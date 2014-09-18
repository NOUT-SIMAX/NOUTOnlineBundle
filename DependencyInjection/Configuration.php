<?php

namespace NOUT\Bundle\NOUTOnlineBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('nout_online');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

	    $rootNode
		    ->children()
		        ->scalarNode('debug')
		            ->defaultValue(false)
		            ->validate()
		                ->ifNotInArray(array(true, false))
		                ->thenInvalid('la valeur "%s" n\'est pas valide, les valeurs acceptées sont : true, false')
		            ->end()
		        ->end()
		    ->end()
	    ;

	    // Here you should define the parameters that are allowed to
	    // configure your bundle. See the documentation linked above for
	    // more information on that topic.
	    return $treeBuilder;





        return $treeBuilder;
    }
}
