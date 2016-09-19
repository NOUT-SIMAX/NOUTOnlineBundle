<?php

namespace NOUT\Bundle\NOUTOnlineBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Translation\Translator;


/*
 * Fichier utilisé pour parser les variables de noutonline.yml
 */

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
                //->enumNode('protocole')
                ->scalarNode('protocole')
                    ->info('Protocole pour NOUTOnline.')
                    //->values(array('http://', 'https://'))
                    ->defaultValue('http://')
                ->end()//enumNode('protocole')

                ->scalarNode('address')
                    ->info('Adresse IP de NOUTOnline.')
                    ->defaultValue('127.0.0.1')
                    ->cannotBeEmpty()
                ->end()//scalarNode('adresse')

                ->integerNode('port')
                    ->info('Port du web service de NOUTOnline.')
                    ->cannotBeEmpty()
                    ->defaultValue('8052')
                ->end()//integerNode('port')

                ->scalarNode('apiuuid')
                    ->info('Identifiant du site pour la vérification par application de NOUTOnline.')
                    ->defaultValue('')
                ->end()
                ->booleanNode('log')
                    ->info('Indique s\'il faut logger les requetes a NOUTOnline.')
                    ->defaultValue(true)
                ->end()
            ->end()//children
        ;
        return $treeBuilder;
    }
}
