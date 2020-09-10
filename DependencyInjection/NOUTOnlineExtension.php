<?php

namespace NOUT\Bundle\NOUTOnlineBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class NOUTOnlineExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

	    foreach($config as $key=>$value)
        {
            $container->setParameter('nout_online.' . $key, $value);
        }
    }

    private function _get_full_namespace($filename) {
        $lines = file($filename, FILE_IGNORE_NEW_LINES);
        $grep = preg_grep('/^namespace /', $lines);
        $namespaceLine = array_shift($grep);
        $match = array();
        preg_match('/^namespace (.*);$/', $namespaceLine, $match);
        $fullNamespace = array_pop($match);

        return $fullNamespace;
    }

    private function _get_classname($filename) {

        return pathinfo($filename, PATHINFO_FILENAME);
    }

}
