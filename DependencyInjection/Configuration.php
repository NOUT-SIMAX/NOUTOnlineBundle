<?php

namespace NOUT\Bundle\NOUTOnlineBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;


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

        $treeBuilder = new TreeBuilder('nout_online');
        $rootNode = $treeBuilder->getRootNode();

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
                    ->defaultValue(8052)
                ->end()//integerNode('port')

                ->integerNode('soap_socket_timeout')
                    ->info('Timeout de socket pour SOAP')
                    ->defaultValue(-1) //SOAPProxy::SOCKET_TIMEOUT
                ->end()//integerNode('soap_socket_timeout')

                ->scalarNode('apiuuid')
                    ->info('Identifiant du site pour la vérification par application de NOUTOnline.')
                    ->defaultValue('')
                ->end()
                ->booleanNode('log')
                    ->info('Indique s\'il faut logger les requetes a NOUTOnline.')
                    ->defaultValue(false)
                ->end()
                ->append($this->addAuthNode())
                ->append($this->addExtranetNode())
                ->append($this->addAnonymeNode())
            ->end()//children
        ;
        return $treeBuilder;
    }

    public function addAuthNode()
    {
        $builder = new TreeBuilder('auth');
        $node = $builder->getRootNode();

        $node
            ->addDefaultsIfNotSet()
            ->children()
            ->enumNode('mode')
            ->info("mode d'authentification pour NOUTOnline")
            ->values(array('OASIS', 'base64'))
            ->defaultValue('OASIS')
            ->end()
            ->scalarNode('secret')
            ->defaultValue("// ManipDonnees.h : classe abstraite pour la manipulation des données\n///////////////////////////////////////////////////////////////////////////////////////\n\n#pragma once\n\n#include InterfaceMAXEx.h\n#include IPasserelle.h\n#include IMaxBDDPasserelle.h\n\n#define  OR_IDENTIQUE\t 0x00080000\t\t// HLitRechercheXXX... à l'ident\n#define  OR_COMMENCEPAR\t 0x00001000\t\t// HLitRechercheXXX... générique")
            ->end()
            ->end()
        ;
        return $node;
    }


    public function addExtranetNode()
    {
        $builder = new TreeBuilder('extranet');
        $node = $builder->getRootNode();

        $node
            ->addDefaultsIfNotSet()
            ->children()
            ->booleanNode('actif')->defaultValue(false)->end()
            ->scalarNode('user')->defaultValue('')->end()
            ->scalarNode('password')->defaultValue('')->end()
            ->scalarNode('form')->defaultValue('')->end()
            ->end()
        ;
        return $node;
    }


    public function addAnonymeNode()
    {
        $builder = new TreeBuilder('anonyme');
        $node = $builder->getRootNode();

        $node
            ->addDefaultsIfNotSet()
            ->children()
            ->booleanNode('actif')->defaultValue(false)->end()
            ->scalarNode('user')->defaultValue('')->end()
            ->scalarNode('password')->defaultValue('')->end()
            ->end()
        ;
        return $node;
    }
}
