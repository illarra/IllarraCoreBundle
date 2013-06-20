<?php

namespace Illarra\CoreBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('illarra_core');
        
        $rootNode
            ->children()
                ->arrayNode('locales')
                    ->info('Nice list of locales.')
                    ->requiresAtLeastOneElement()
                    ->prototype('scalar')
                    ->end()
                ->end()
                ->scalarNode('default_locale')
                    ->isRequired()
                ->end()
                ->arrayNode('available_locales')
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function($v) { return preg_split('/\s*,\s*/', $v); })
                    ->end()
                    ->info('Available locales: they are used in the Admin area.')
                    ->requiresAtLeastOneElement()
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('active_locales')
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function($v) { return preg_split('/\s*,\s*/', $v); })
                    ->end()
                    ->info('Active locales: they are used in the "prod" environment.')
                    ->requiresAtLeastOneElement()
                    ->prototype('scalar')->end()
                ->end()
            ->end()
        ;
        
        $this->addAdminSection($rootNode);
        
        return $treeBuilder;
    }
    
    private function addAdminSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('admin')
                    ->addDefaultsIfNotSet()
                    ->info('Admin module configuration.')
                    ->children()
                        ->scalarNode('entities_per_page')
                            ->defaultValue(15)
                            ->info('Number of entities to show in Admin lists.')
                        ->end()
                        ->scalarNode('locale')
                            ->defaultValue('es')
                            ->info('UI locale to be used in the Admin area.')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
