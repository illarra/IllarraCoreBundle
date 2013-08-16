<?php

namespace Illarra\CoreBundle\EventListener;

use Symfony\Component\Security\Core\Encoder\EncoderFactory;

class PreSaveUserListener
{
    protected $factory;

    public function __construct(EncoderFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function prePersist(\Doctrine\ORM\Event\LifecycleEventArgs $args)
    {
        $this->preSave($args);
    }

    /**
     * @param \Doctrine\ORM\Event\PreUpdateEventArgs $args
     */
    public function preUpdate(\Doctrine\ORM\Event\PreUpdateEventArgs $args)
    {
        $this->preSave($args);
    }

    public function preSave($args)
    {
        $entity = $args->getEntity();

        if (is_a($entity, 'Illarra\CoreBundle\Entity\User')) {
            $encoder = $this->factory->getEncoder($entity);

            if (get_class($args) == 'Doctrine\ORM\Event\LifecycleEventArgs') {
                $password = $encoder->encodePassword($entity->getPassword(), $entity->getSalt());
                $entity->setPassword($password);
            } else if ($args->hasChangedField('password')) {
                $password = $encoder->encodePassword($args->getNewValue('password'), $entity->getSalt());
                $args->setNewValue('password', $password);
            }
        }
    }
}
