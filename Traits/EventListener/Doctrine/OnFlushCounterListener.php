<?php

namespace Illarra\CoreBundle\Traits\EventListener\Doctrine;

use Doctrine\ORM\Event\OnFlushEventArgs;

trait OnFlushCounterListener
{
    protected $entities = ['insert' => [], 'delete' => []];
    
    /**
     * @abstract
     * @return string
     */
    abstract public function getOwnerEntityClass();
    
    /**
     * @abstract
     * @return array
     */
    abstract public function getManyToOneTargetEntitiesClassAndField();
    
    /**
     * @abstract
     * @return array
     */
    abstract public function getManyToManyTargetEntitiesClassAndField();
    
    /**
     * @abstract
     * @return string
     */
    abstract public function getCounterFieldInTargetEntities();
    
    /**
     * @param \Doctrine\ORM\Event\OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $em  = $args->getEntityManager();
        $uow = $em->getUnitOfWork();
        
        $this->processManyToOneWorks($uow);
        $this->processManyToManyWorks($uow);
        
        foreach ($this->entities as $action => $entities) {
            foreach ($entities as $entity) {
                if ($entity) {
                    $this->updateValue($entity, $action);
                    
                    $classMetadata = $em->getClassMetadata(get_class($entity));
                    $uow->computeChangeSet($classMetadata, $entity);
                }
            }
        }
    }
    
    public function processManyToOneWorks($uow)
    {
        foreach ($this->getManyToOneTargetEntitiesClassAndField() as $class => $field) {
            $getter = 'get'.$field;
            
            foreach ($uow->getScheduledEntityInsertions() as $entity) {
                if ($this->isOwner($entity)) {
                    $this->entities['insert'][] = $entity->$getter();
                }
            }
            
            foreach ($uow->getScheduledEntityUpdates() as $entity) {
                if ($this->isOwner($entity)) {
                    $changeset = $uow->getEntityChangeSet($entity);
                    
                    if (isset($changeset[$field])) {
                        $this->entities['delete'][] = $changeset[$field][0];
                        $this->entities['insert'][] = $changeset[$field][1];
                    }
                }
            }
            
            foreach ($uow->getScheduledEntityDeletions() as $entity) {
                if ($this->isOwner($entity)) {
                    $this->entities['delete'][] = $entity->$getter();
                }
            }
        }
    }
    
    public function processManyToManyWorks($uow)
    {
        foreach ($this->getManyToManyTargetEntitiesClassAndField() as $class => $field) {
            $getter = 'get'.$field;
            
            foreach ($uow->getScheduledEntityDeletions() as $entity) {
                if ($this->isOwner($entity)) {
                    foreach ($entity->$getter() as $e) {
                        $this->entities['delete'][] = $e;
                    }
                }
            }
            
            foreach ($uow->getScheduledCollectionUpdates() as $collection) {
                if ($this->isOwner($collection->getOwner())) {
                    foreach ($collection->getInsertDiff() as $entity) {
                        if ($this->isInstanceOfClass($entity, $class)) {
                            $this->entities['insert'][] = $entity;
                        }
                    }
                
                    foreach ($collection->getDeleteDiff() as $entity) {
                        if ($this->isInstanceOfClass($entity, $class)) {
                            $this->entities['delete'][] = $entity;
                        }
                    }
                }
            }
        }
    }
    
    public function updateValue($entity, $action)
    {
        $values = ['insert' => 1, 'delete' => -1];
        $field  = $this->getCounterFieldInTargetEntities();
        
        $entity->setCounterValue($field, $entity->getCounterValue($field) + $values[$action]);
    }
    
    public function isOwner($entity)
    {
        return $this->isInstanceOfClass($entity, $this->getOwnerEntityClass());
    }
    
    public function isInstanceOfClass($entity, $class)
    {
        return join('', array_slice(explode('\\', get_class($entity)), -1)) == $class;
    }
}
