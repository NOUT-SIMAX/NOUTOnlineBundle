<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Service;


use NOUT\Bundle\NOUTOnlineBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;

class DynamicConfigurationLoader
{

    /** @var array  */
    protected $_config;

    /**
     * ConfigurationLoader constructor.
     * @param string $sConfigDir
     * @param array  $config
     */
    public function __construct(bool $bMultitenant, string $sConfigDir, array $defaultConfig, string $sHostname)
    {
        if (!$bMultitenant){
            $this->_config = $defaultConfig;
        }
        else {
            //il faut faire en fonction du hostname
            if (($sHostname=='localhost') && in_array('SERVER_NAME', $_SERVER)){
                $sHostname=str_replace('www.', '', $_SERVER['SERVER_NAME']);
            }

            $filepath = $sConfigDir.'/'.$sHostname.'.yaml';
            if (file_exists($filepath)){
                $config_readed = Yaml::parseFile($filepath);

                $configuration = new Configuration();
                $tree_builder = $configuration->getConfigTreeBuilder();
                $processor = new Processor();

                $this->_config = $processor->process($tree_builder->buildTree(), $config_readed);
            }
            else {
                $this->_config = $defaultConfig;
            }

        }
    }

    public function getParameter($name)
    {
        return $this->_config[$name];
    }

}