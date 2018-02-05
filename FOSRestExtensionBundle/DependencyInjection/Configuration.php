<?php

namespace SC\FOSRestExtensionBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('scfos_rest_extension');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        $rootNode
            ->children()
                ->arrayNode("query_filters")
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode("class")
                            ->end()
                            ->arrayNode("fields")
                                ->scalarPrototype()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode("api_connection_processor")
                    ->children()
                        ->scalarNode("pattern")
                            ->defaultValue("^/")
                        ->end()
                        ->arrayNode("custom_methods")
                            ->scalarPrototype()
                            ->end()
                        ->end()
                        ->arrayNode("methods")
                            ->children()
                                ->booleanNode("update_last_login")
                                    ->defaultFalse()
                                ->end()
                                ->booleanNode("count_connections")
                                    ->defaultFalse()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode("patch_processor")
                    ->children()
                        ->arrayNode("banning")
                            ->arrayPrototype()
                                ->scalarPrototype()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
