<?php

namespace Illarra\CoreBundle\Menu;

use Illarra\CoreBundle\Event\ConfigureSitemapEvent;
use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class SitemapBuilder extends ContainerAware
{
    public function indexMenu(FactoryInterface $factory, $options)
    {
        $menu = $factory->createItem('illarra_core_sitemap_index', $options);
        
        $this->container->get('event_dispatcher')->dispatch(ConfigureSitemapEvent::CONFIGURE, new ConfigureSitemapEvent($factory, $menu));
        
        return $menu;
    }
}
