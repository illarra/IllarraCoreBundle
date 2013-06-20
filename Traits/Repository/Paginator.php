<?php

namespace Illarra\CoreBundle\Traits\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

trait Paginator
{
    protected $environment          = null;
    protected $entitiesCount        = null;
    protected $entitiesPerPage      = 0;
    protected $pages                = 1;
    protected $entityIsTranslatable = null;
    
    /**
     * @abstract
     * @return array
     */
    abstract public function getOrderFields();
    
    /**
     * @param \Doctrine\ORM\QueryBuilder $qb
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function updateQueryForEnvironment($qb) { return $qb; }
    
    public function countPages()
    {
        return ceil($this->getEntitiesCount() / $this->getEntitiesPerPage());
    }
    
    public function countEntities()
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
            ->select('count(e.id)')
            ->from($this->getEntityName(), 'e')
        ;
        
        $qb = $this->updateQueryForEnvironment($qb);
        //$qb = $this->filterQueryByLabel($qb);
        
        return $qb->getQuery()->getSingleScalarResult();
    }
    
    public function setEnvironment($environment)
    {
        $this->environment = $environment;
        
        return $this;
    }
    
    public function getEnvironment()
    {
        return $this->environment;
    }
    
    public function getEntitiesCount()
    {
        if (is_null($this->entitiesCount)) {
            $this->entitiesCount = $this->countEntities();
        }
        
        return $this->entitiesCount;
    }
    
    public function setEntitiesPerPage($entitiesPerPage)
    {
        $this->entitiesPerPage = $entitiesPerPage;
        
        return $this;
    }
    
    public function getEntitiesPerPage()
    {
        return $this->entitiesPerPage;
    }
    
    public function getPages()
    {
        if (0 == $this->getEntitiesPerPage()) {
            $this->pages = 1;
        } else {
            $this->pages = ($this->countPages() > 0) ? $this->countPages() : 1;
        }
        
        return $this->pages;
    }
    
    public function getMaxResults()
    {
        return (0 == $this->getEntitiesPerPage()) ? null : $this->getEntitiesPerPage();
    }
    
    public function getOffset($page)
    {
        return (1 == $page) ? null : (($page - 1) * $this->getEntitiesPerPage());
    }
    
    public function entityIsTranslatable()
    {
        if (is_null($this->entityIsTranslatable)) {
            $this->entityIsTranslatable = class_exists($this->getEntityName().'Translation');
        }
        
        return $this->entityIsTranslatable;
    }
    
    public function findAllOrdered($page = 1)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
            ->select('e')
            ->from($this->getEntityName(), 'e')
        ;
        
        if ($this->entityIsTranslatable()) {
            $fetchJoinCollection = true;
            $qb
                ->addSelect('et')
                ->innerJoin('e.translations', 'et')
            ;
        } else {
            $fetchJoinCollection = false;
        }
        
        $qb = $this->updateQueryWithOrder($qb);
        $qb = $this->updateQueryForEnvironment($qb);
        //$qb = $this->filterQueryByLabel($qb);
        
        $query = $qb->getQuery()
            ->setFirstResult($this->getOffset($page))
            ->setMaxResults($this->getMaxResults())
        ;
        
        return new DoctrinePaginator($query, $fetchJoinCollection);
    }
    
    public function updateQueryWithOrder($qb)
    {
        $fields = [];
        
        foreach ($this->getOrderFields() as $f) {
            $e = explode(' ', trim($f));
            if (!isset($e[1])) $e[] = 'ASC';
            
            if (property_exists($this->getEntityName(), $e[0])) {
                $fields[] = 'e.'.$e[0].' '.strtoupper($e[1]);
            } else if ($this->entityIsTranslatable() && property_exists($this->getEntityName().'Translation', $e[0])) {
                $fields[] = 'et.'.$e[0].' '.strtoupper($e[1]);
            }
        }
        
        if (!empty($fields)) $qb->add('orderBy', implode(', ', $fields));
        
        return $qb;
    }
    
    /*
     * HELPERS
     */
    
    public function findAllWithTranslations()
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
            ->select('e')
            ->from($this->getEntityName(), 'e')
            ->addSelect('et')->innerJoin('e.translations', 'et')
        ;
        
        $qb = $this->updateQueryWithOrder($qb);
        $qb = $this->updateQueryForEnvironment($qb);
        
        return $qb->getQuery()->getResult();
    }
}
