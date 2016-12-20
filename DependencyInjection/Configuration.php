<?php

namespace NOUT\Bundle\SessionManagerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root(0);

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
            ->append($this->addCustomizationNode())
            ->end()
        ;

        return $treeBuilder;
    }


    public function addCustomizationNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('customization');

        $node
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('title')->defaultValue('')->end()
            ->scalarNode('logo_file')->defaultValue('')->end()
            ->scalarNode('css_file')->defaultValue('')->end()
            ->end()
        ;
        return $node;
    }

}
