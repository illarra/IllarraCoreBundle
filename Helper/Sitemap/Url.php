<?php

namespace Illarra\CoreBundle\Helper\Sitemap;

class Url
{
    protected $loc;
    protected $priority = 0.5;
    
    public function __construct($url)
    {
        $this->loc = $url;
    }
    
    /**
     * @param string $loc
     * @return Url 
     */
    public function setLoc($loc)
    {
        $this->loc = $loc;
        
        return $this;
    }
    
    /**
     * @return string 
     */
    public function getLoc()
    {
        return $this->loc;
    }
    
    /**
     * @param integer $priority
     * @return Url 
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
        
        return $this;
    }
    
    /**
     * @return integer 
     */
    public function getPriority()
    {
        return $this->priority;
    }
}
