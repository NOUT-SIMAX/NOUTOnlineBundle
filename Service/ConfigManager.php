<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 30/07/2015
 * Time: 14:39
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Service;


use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class ConfigManager
{
	/**
	 * @var string
	 */
	private $m_sConfigFile;

	/**
	 * @var string
	 */
	private $m_sConfigDist;


	private $m_clConfig;

	/**
	 * @param $sConfigDir
	 * @param $sNomFichier
	 */
	public function __construct($sConfigDir, $sNomFichier)
	{
		$this->m_sConfigFile=$sConfigDir.'/'.$sNomFichier.'.yml';
		$this->m_sConfigDist=$sConfigDir.'/'.$sNomFichier.'.yml.dist';

		if (!file_exists($this->m_sConfigFile))
		{
			if (!file_exists($this->m_sConfigDist))
			{
				throw new \Exception("Le fichier de configuration $this->m_sConfigFile est manquant");
			}

			copy($this->m_sConfigDist, $this->m_sConfigFile);
		}

		$oTreeBuilder = $this->_oGetTreeBuilder();
		$oNodeInterface = $oTreeBuilder->buildTree();

		$aYamlConfig = Yaml::parse($this->m_sConfigFile);

		$oProcessor = new Processor();
		$this->m_clConfig = $oProcessor->process($oNodeInterface, $aYamlConfig);
	}


	/**
	 * retourne l'arbre de validation
	 * @return TreeBuilder
	 */
	private function _oGetTreeBuilder()
	{
		$treeBuilder = new TreeBuilder();

		$rootNode = $treeBuilder->root('nout_online');

		// Here you should define the parameters that are allowed to
		// configure your bundle. See the documentation linked above for
		// more information on that topic.
		$rootNode
			->addDefaultsIfNotSet()
			->children()
				->enumNode('protocole')
					->values(array('http://', 'https://'))
					->defaultValue('http://')
				->end()
				->scalarNode('address')
					->defaultValue('127.0.0.1')
					->cannotBeEmpty()
					->validate()
						->ifTrue(
							function ($ip)
							{
								if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6))
									return false;



								return !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6);
							}
						)
						->thenInvalid('%s should be an IPv4 or IPv6 address') //la valeur "%s" n'est pas valide pour le paramètre address, la valeur doit être une adresse IPv4 ou IPv6
					->end()
				->end()
				->integerNode('port')
					->cannotBeEmpty()
					->defaultValue('8052')
				->end()
				->scalarNode('apiuuid')->end()
			->end()
		;
		return $treeBuilder;
	}


	/**
	 * @param string $sName
	 * @return mixed
	 * @throws \Exception
	 */
	public function getParameter($sName)
	{
		if (empty($this->m_clConfig))
		{
			throw new \Exception("Une erreur est survenue pendant le chargement du fichier de configuration $this->m_sConfigFile");
		}

		return $this->m_clConfig[$sName];
	}

	/**
	 * @param string $sName
	 * @return mixed
	 * @throws \Exception
	 */
	public function setParameter($sName, $Value)
	{
		if (empty($this->m_clConfig) || !isset($this->m_clConfig[$sName]))
		{
			throw new \Exception("Une erreur est survenue pendant le chargement du fichier de configuration $this->m_sConfigFile");
		}

		switch($sName)
		{
			case 'port':
				$Value=(int)$Value;
				break;
		}

		$this->m_clConfig[$sName]=$Value;

		//dump de la config
		$dumper = new Dumper();
		$yaml = $dumper->dump(array('nout_online'=>$this->m_clConfig), 4);
		file_put_contents($this->m_sConfigFile, $yaml);

		return $this;
	}


}