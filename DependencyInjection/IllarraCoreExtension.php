<?php

namespace Illarra\CoreBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class IllarraCoreExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        
        // Set parameters
        $container->setParameter('illarra_core.locales', $config['locales']);
        $container->setParameter('illarra_core.default_locale', $config['default_locale']);
        $container->setParameter('illarra_core.available_locales', $config['available_locales']);
        $container->setParameter('illarra_core.active_locales', $config['active_locales']);
        $container->setParameter('illarra_core.admin.entities_per_page', $config['admin']['entities_per_page']);
        $container->setParameter('illarra_core.admin.locale', $config['admin']['locale']);
    }
}
