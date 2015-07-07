<?php

namespace NOUT\Bundle\NOUTOnlineBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Translation\Translator;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{

	public function __construct()
	{
	}


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
		    ->addDefaultsIfNotSet()
		    ->children()
		        ->booleanNode('debug')->end()
			    ->enumNode('protocole')
	                ->values(array('http://', 'https://'))
				    ->defaultValue('http://')
			    ->end()
			    ->scalarNode('address')
				    ->defaultValue('127.0.0.1')
	                ->cannotBeEmpty()
				    ->validate()
					    ->ifTrue(function ($ip) { return !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6); })
					    ->thenInvalid('%s should be an IPv4 or IPv6 address') //la valeur "%s" n'est pas valide pour le paramètre address, la valeur doit être une adresse IPv4 ou IPv6
				    ->end()
			    ->end()
			    ->integerNode('port')
		            ->cannotBeEmpty()
				    ->defaultValue('8052')
			    ->end()
		        ->scalarNode('apiuuid')
		        ->end()
		    ->end()
	    ;

	    return $treeBuilder;
    }
}
