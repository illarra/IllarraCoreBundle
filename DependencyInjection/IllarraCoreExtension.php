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
                    'layout'    => 'IllarraCoreBundle:Sonata:standard_layout.html.twig',
                    // 'ajax'      => 'IllarraCoreBundle:Sonata:ajax_layout.html.twig',
                    // 'dashboard' => 'IllarraCoreBundle:Sonata:Core/dashboard.html.twig',

                    // default values of actions templates, they should extend global templates
                    // 'list'               => 'IllarraCoreBundle:Sonata:CRUD/list.html.twig',
                    // 'show'               => 'IllarraCoreBundle:Sonata:CRUD/show.html.twig',
                    // 'edit'               => 'IllarraCoreBundle:Sonata:CRUD/edit.html.twig',
                    // 'history'            => 'IllarraCoreBundle:Sonata:CRUD/history.html.twig',
                    // 'preview'            => 'IllarraCoreBundle:Sonata:CRUD/preview.html.twig',
                    // 'delete'             => 'IllarraCoreBundle:Sonata:CRUD/delete.html.twig',
                    // 'batch'              => 'IllarraCoreBundle:Sonata:CRUD/list__batch.html.twig',
                    // 'batch_confirmation' => 'IllarraCoreBundle:Sonata:CRUD/batch_confirmation.html.twig',

                    // list related templates
                    // 'inner_list_row'  => 'IllarraCoreBundle:Sonata:CRUD/list_inner_row.html.twig',
                    // 'base_list_field' => 'IllarraCoreBundle:Sonata:CRUD/base_list_field.html.twig',

                    // default values of helper templates
                    // 'short_object_description' => 'IllarraCoreBundle:Sonata:Helper/short-object-description.html.twig',

                    // default values of block templates, they should extend the base_block template
                    // 'list_block' => 'IllarraCoreBundle:Sonata:Block/block_admin_list.html.twig',
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

        $container->prependExtensionConfig('assetic', [
            'bundles' => [
                'IllarraCoreBundle',
            ]
        ]);
    }
}
