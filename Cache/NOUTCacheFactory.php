<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 03/11/2016
 * Time: 15:02
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Cache;


use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Yaml;


class NOUTCacheFactory
{

    /**
     * @var UrlGeneratorInterface
     */
    private $__router;

    /**
     * @var string
     */
    private $__cachedir;

    /**
     * @var Stopwatch
     */
    private $__stopwatch;

    function __construct(UrlGeneratorInterface $router, $cachedir, Stopwatch $stopwatch=null)
    {
        $this->__router = $router;
        $this->__cachedir = $cachedir;
        $this->__stopwatch = $stopwatch;
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
        if (isset($_SERVER) && isset($_SERVER['SERVER_NAME'])){
            $server_name = $_SERVER['SERVER_NAME'];
            $server_path = substr(str_replace(dirname($_SERVER['PHP_SELF']), '\\', '/'),0,-1);

        }
        else {
            $server_name='';
            $server_path='';
        }
        $indexurl = $this->__router->generate('index');
        $baseurl = $server_name.$server_path.$indexurl;

        if (extension_loaded('apc') || extension_loaded('apcu'))
        {
            $cache = new NOUTApcuCache($this->__stopwatch);
            $cache->setNamespace($namespace, $baseurl.$prefix);
        }
        elseif (extension_loaded('xcache'))
        {
            $cache = new NOUTXCacheCache($this->__stopwatch);
            $cache->setNamespace($namespace, $baseurl.$prefix);
        }
        else
        {
            $cache = new NOUTFileCache($this->__stopwatch);

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