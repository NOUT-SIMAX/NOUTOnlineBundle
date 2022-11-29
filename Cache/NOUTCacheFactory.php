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
     * @param string $namespace
     * @param string $prefix
     * @param string $dirprefix
     * @return NOUTApcuCache|NOUTFileCache
     */
    public function getCache(string $namespace, string $prefix, string $dirprefix) : NOUTCacheProvider
    {
        if (isset($_SERVER) && isset($_SERVER['HTTP_HOST'])){
            $server_name = explode(':', $_SERVER['HTTP_HOST'])[0];
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