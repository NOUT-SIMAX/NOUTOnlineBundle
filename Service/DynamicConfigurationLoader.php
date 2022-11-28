<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Service;

use NOUT\Bundle\NOUTOnlineBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;

class DynamicConfigurationLoader
{

    /** @var array|null  */
    protected $_config = null;

    /** @var bool|array  */
    protected $_multitenantNotFound = false;

    /** @var string  */
    protected $_hostname = '';

    /**
     * ConfigurationLoader constructor.
     * @param array  $aMultitenant
     * @param string $sConfigDir
     * @param array  $defaultConfig
     * @param string $sHostname
     */
    public function __construct(array $aMultitenant, string $sConfigDir, array $defaultConfig, string $sHostname)
    {
        if (!$aMultitenant['actif']){
            $this->_config = $defaultConfig;
        }
        else {
            $this->_hostname = $sHostname;

            //il faut faire en fonction du hostname
            if (($sHostname=='localhost') && array_key_exists('HTTP_HOST', $_SERVER)){
                $sHostname=str_replace('www.', '', explode(':',$_SERVER['HTTP_HOST'])[0] );
            }

            $filepath = '';
            $filepathyaml = $sConfigDir.'/'.$sHostname.'.yaml';
            $filepathyml = $sConfigDir.'/'.$sHostname.'.yml';
            if (file_exists($filepathyaml)){
                $filepath = $filepathyaml;
            }
            elseif(file_exists($filepathyml)){
                $filepath = $filepathyml;
            }

            if (!empty($filepath)){
                $config_readed = Yaml::parseFile($filepath);

                $configuration = new Configuration();
                $tree_builder = $configuration->getConfigTreeBuilder();
                $processor = new Processor();

                $this->_config = $processor->process($tree_builder->buildTree(), $config_readed);
            }
            else {
                $this->_multitenantNotFound = [
                    'yaml' => [ 'path'=>$filepathyaml, 'exists' => file_exists($filepathyaml)],
                    'yml' => [ 'path'=>$filepathyml, 'exists' => file_exists($filepathyml)],
                ];
            }

        }
    }

    /**
     * @param string $name
     * @param null   $default
     * @return mixed
     */
    public function getParameter(string $name, $default=null)
    {
        if (is_null($this->_config) || !isset($this->_config[$name])){
            return $default;
        }
        return $this->_config[$name];
    }

    /**
     * @return bool
     */
    public function isMultitenantNotFound(): bool
    {
        return $this->_multitenantNotFound!==false;
    }

    /**
     * @return array
     */
    public function getMultitenantNotFound(): array
    {
        return $this->_multitenantNotFound;
    }

    /**
     * @return string
     */
    public function getHostname(): string
    {
        return $this->_hostname;
    }

}