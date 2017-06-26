<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 03/11/2016
 * Time: 15:02
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Cache;


use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Yaml;

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


    /**
     * @var string
     */
    private $__cacheinfofile;


    /**
     * @var Stopwatch
     */
    private $__stopwatch;

    function __construct(Router $router, $cachedir, $configdir, Stopwatch $stopwatch=null)
    {
        $this->__router = $router;
        $this->__cachedir = $cachedir;
        $this->__cacheinfofile = $configdir.'/cache_info.yml';
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

            //dump de la config
            $dumper = new Dumper();
            $sYaml = $dumper->dump(array('server_name'=>$server_name), 5);
            file_put_contents($this->__cacheinfofile, $sYaml);
        }
        else {
            $aParseYaml = file_exists($this->__cacheinfofile) ? Yaml::parse($this->__cacheinfofile) : array();
            if (isset($aParseYaml['server_name'])){
                $server_name=$aParseYaml['server_name'];
            }
            else {
                $server_name='';
            }
        }
        $baseurl = $server_name.$this->__router->generate('index');

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