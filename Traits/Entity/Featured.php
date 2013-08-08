<?php

namespace Illarra\CoreBundle\Traits\Entity;

trait Featured
{
    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $isFeatured;
    
    /**
     * @param boolean $isFeatured
     */
    public function setIsFeatured($isFeatured)
    {
        $this->isFeatured = $isFeatured;
        
        return $this;
    }
    
    /**
     * @return boolean
     */
    public function getIsFeatured()
    {
        return $this->isFeatured;
    }
    
    /**
     * @return boolean
     */
    public function isFeatured()
    {
        return $this->isFeatured;
    }
}
