<?php

namespace Illarra\CoreBundle\Traits\Entity;

trait Counter
{
    /**
     * @var array
     *
     * @ORM\Column(type="array", nullable=true)
     */
    private $counter = [];
    
    /**
     * @abstract
     * @return array
     */
    abstract public function getCounterFields();
    
    /**
     * @param string $field
     * @param integer $field
     * @return array
     */
    public function setCounterValue($field, $value)
    {
        $this->checkCounter();
        $this->counter[$field] = (int) $value;
        
        return $this;
    }
    
    /**
     * @param string $field
     * @return integer
     */
    public function getCounterValue($field)
    {
        $this->checkCounter();
        
        return (int) $this->counter[$field];
    }
    
    /**
     * @return array
     */
    public function getCounter()
    {
        $this->checkCounter();
        
        return $this->counter;
    }
    
    public function checkCounter()
    {
        foreach ($this->getCounterFields() as $field) {
            if (!isset($this->counter[$field])) $this->counter[$field] = 0;
        }
    }
}
