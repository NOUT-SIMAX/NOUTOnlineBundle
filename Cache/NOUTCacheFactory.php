<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 03/11/2016
 * Time: 15:02
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Cache;



use Symfony\Bundle\FrameworkBundle\Routing\Router;

class NOUTCacheFactory
{

    /**
     * @var Router
     */
    private $__router;

    /**
     * @var string
     */
    private $__cachedir;

    function __construct(Router $router, $cachedir)
    {
        $this->__router = $router;
        $this->__cachedir = $cachedir;
    }


    /**
     * renvoi le bon cache en fonction des extensions qui sont chargées
     * @param $namespace
     * @param $prefix
     * @param $dirprefix
     * @return NOUTApcuCache|NOUTFileCache|NOUTXCacheCache
     */
    public function getCache($namespace, $prefix, $dirprefix)
    {
        $baseurl = $_SERVER['SERVER_NAME'].$this->__router->generate('index');

        if (extension_loaded('apc') || extension_loaded('apcu'))
        {
            $cache = new NOUTApcuCache();
            $cache->setNamespace($namespace, $baseurl.$prefix);
        }
        elseif (extension_loaded('xcache'))
        {
            $cache = new NOUTXCacheCache();
            $cache->setNamespace($namespace, $baseurl.$prefix);
        }
        else
        {
            $cache = new NOUTFileCache();

            $cachedir = $this->__cachedir;
            if (!empty($dirprefix)){
                $cachedir.='/'.$dirprefix;
            }
            if (!empty($prefix)){
                $cachedir.='/'.$prefix;
            }
            $cache->setNamespace($namespace, $cachedir);
        }

        return $cache;
    }
}