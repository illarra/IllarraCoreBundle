<?php

namespace Illarra\CoreBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class IllarraCoreExtension extends Extension implements PrependExtensionInterface
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
        $container->setParameter(
            'illarra_core.locales',
            array_merge(
                [
                    'ca' => 'Català',
                    'de' => 'Deutsch',
                    'en' => 'English',
                    'es' => 'Castellano',
                    'eo' => 'Esperanto',
                    'eu' => 'Euskara',
                    'fr' => 'Française',
                    'gl' => 'Galego',
                    'it' => 'Italiano',
                    'nl' => 'Nederlands',
                    'pt' => 'Português',
                ],
                $config['locales']
            )
        );
        $container->setParameter('illarra_core.default_locale', $config['default_locale']);
        $container->setParameter('illarra_core.available_locales', $config['available_locales']);
        $container->setParameter('illarra_core.active_locales', $config['active_locales']);
        $container->setParameter('illarra_core.admin.entities_per_page', $config['admin']['entities_per_page']);
        $container->setParameter('illarra_core.admin.locale', $config['admin']['locale']);
    }

    public function prepend(ContainerBuilder $container)
    {
        // get all Bundles
        $bundles = $container->getParameter('kernel.bundles');

        // Configure SonataAdminBundle
        if (isset($bundles['SonataAdminBundle'])) {
            $container->prependExtensionConfig('sonata_admin', [
                'templates' => [
                    // default global templates
                    'layout'    => 'SonataAdminBundle::standard_layout.html.twig',
                    'ajax'      => 'SonataAdminBundle::ajax_layout.html.twig',
                    'dashboard' => 'SonataAdminBundle:Core:dashboard.html.twig',

                    // default values of actions templates, they should extend global templates
                    'list'               => 'SonataAdminBundle:CRUD:list.html.twig',
                    'show'               => 'SonataAdminBundle:CRUD:show.html.twig',
                    'edit'               => 'SonataAdminBundle:CRUD:edit.html.twig',
                    'history'            => 'SonataAdminBundle:CRUD:history.html.twig',
                    'preview'            => 'SonataAdminBundle:CRUD:preview.html.twig',
                    'delete'             => 'SonataAdminBundle:CRUD:delete.html.twig',
                    'batch'              => 'SonataAdminBundle:CRUD:list__batch.html.twig',
                    'batch_confirmation' => 'SonataAdminBundle:CRUD:batch_confirmation.html.twig',

                    // list related templates
                    'inner_list_row'  => 'SonataAdminBundle:CRUD:list_inner_row.html.twig',
                    'base_list_field' => 'SonataAdminBundle:CRUD:base_list_field.html.twig',

                    // default values of helper templates
                    'short_object_description' => 'SonataAdminBundle:Helper:short-object-description.html.twig',

                    // default values of block templates, they should extend the base_block template
                    'list_block' => 'SonataAdminBundle:Block:block_admin_list.html.twig',
                ]
            ]);
        }

        if (isset($bundles['SonataDoctrineORMAdminBundle'])) {
            $container->prependExtensionConfig('sonata_doctrine_orm_admin', [
                'templates' => [
                    'form' => [
                        'IllarraCoreBundle:Sonata:Form/fields.html.twig',
                    ],
                ]
            ]);
        }
    }
}
