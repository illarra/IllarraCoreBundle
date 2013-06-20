<?php

namespace Illarra\CoreBundle\Menu;

use Illarra\CoreBundle\Event\ConfigureAdminMenuEvent;
use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class AdminBuilder extends ContainerAware
{
    public function mainMenu(FactoryInterface $factory, $options)
    {
        $menu = $factory->createItem('illarra_core_admin_main', $options);
        
        $this->container->get('event_dispatcher')->dispatch(ConfigureAdminMenuEvent::CONFIGURE, new ConfigureAdminMenuEvent($factory, $menu));
        
        return $menu;
    }
}
