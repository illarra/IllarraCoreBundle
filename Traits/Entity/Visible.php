<?php

namespace Illarra\CoreBundle\Traits\Entity;

trait Visible
{
    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isVisible;
    
    /**
     * @param boolean $isVisible
     */
    public function setIsVisible($isVisible)
    {
        $this->isVisible = $isVisible;
        
        return $this;
    }
    
    /**
     * @return boolean
     */
    public function getIsVisible()
    {
        return $this->isVisible;
    }
    
    /**
     * @return boolean
     */
    public function isVisible()
    {
        return $this->isVisible;
    }
}
