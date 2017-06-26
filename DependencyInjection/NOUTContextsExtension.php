<?php

namespace NOUT\Bundle\ContextsBundle\DependencyInjection;

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
class NOUTContextsExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        if (!$container->getParameter('kernel.debug') || ($container->getParameter('kernel.environment')!='dev'))
        {

            /**
             * Symfony creates a big classes.php file in the cache directory to aggregate the contents of the PHP classes that are used in every request.
             * This reduces the I/O operations and increases the application performance.
             * Your bundles can also add their own classes into this file thanks to the addClassesToCompile() method.
             * Define the classes to compile as an array of their fully qualified class names
             *
             * Beware that this technique can't be used in some cases:
             * - When classes contain annotations, such as controllers with @Route annotations and entities with @ORM or @Assert annotations,
             *      because the file location retrieved from PHP reflection changes;
             * - When classes use the __DIR__ and __FILE__ constants, because their values will change when loading these classes from the classes.php file.
             */

            $finder = new Finder();
            $finder->files()->in(array(
                                     __DIR__.'/../Entity',
                                     __DIR__.'/../Service',
                                 ))->name('*.php');

            $aClasses = array();

            foreach ($finder as $file)
            {
                $filename = $file->getRealPath();
                $aClasses[] = $this->_get_full_namespace($filename).'\\'.$this->_get_classname($filename);
            }

            $this->addClassesToCompile($aClasses);
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
