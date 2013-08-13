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
        $menu->addChild('user.menu', ['route' => 'admin_illarra_user_index']);
        
        $this->container->get('event_dispatcher')->dispatch(ConfigureAdminMenuEvent::CONFIGURE, new ConfigureAdminMenuEvent($factory, $menu));
        
        return $menu;
    }
}
