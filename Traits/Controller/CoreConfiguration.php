<?php

namespace Illarra\CoreBundle\Traits\Controller;

trait CoreConfiguration
{
    /**
     * @return string
     */
    public function getDefaultLocale()
    {
        return $this->container->getParameter('illarra_core.default_locale');
    }
    
    /**
     * @return array
     */
    public function getAvailableLocales()
    {
        return $this->container->getParameter('illarra_core.available_locales');
    }
    
    /**
     * @return array
     */
    public function getActiveLocales()
    {
        return $this->container->getParameter('illarra_core.active_locales');
    }
    
    /**
     * @return array
     */
    public function getEntitiesPerPageInAdmin()
    {
        return $this->container->getParameter('illarra_core.admin.entities_per_page');
    }
}
