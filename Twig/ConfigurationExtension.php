<?php

namespace Illarra\CoreBundle\Twig;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ConfigurationExtension extends \Twig_Extension
{
    private $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    public function getGlobals()
    {
        return array(
            'locales'           => $this->container->getParameter('illarra_core.locales'),
            'default_locale'    => $this->container->getParameter('illarra_core.default_locale'),
            'available_locales' => $this->container->getParameter('illarra_core.available_locales'),
            'active_locales'    => $this->container->getParameter('illarra_core.active_locales'),
        );
    }
    
    public function getName()
    {
        return 'configuration_extension';
    }
}
