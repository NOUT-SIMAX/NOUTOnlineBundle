<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 25/07/14
 * Time: 14:07
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Twig;

use NOUT\Bundle\NOUTOnlineBundle\Entity\ConfigurationDialogue;
use NOUT\Bundle\NOUTOnlineBundle\Service\OnlineServiceFactory;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * This class contains the needed functions
 * - in order to do the query highlighting
 * - get the version and state of noutonline
 */
class NOUTOnlineExtension extends \Twig_Extension
{

	/**
	 * @var OnlineServiceFactory $m_clServiceFactory
	 */
	protected $m_clServiceFactory;

	/**
	 * @var ConfigurationDialogue $m_clConfiguration
	 */
	protected $m_clConfiguration;

	/**
	 * @param OnlineServiceFactory  $factory
	 * @param ConfigurationDialogue $configuration
	 */
	public function __construct(OnlineServiceFactory $factory, ConfigurationDialogue $configuration)
	{

		$this->m_clServiceFactory = $factory;
		$this->m_clConfiguration = $configuration;
	}


	/**
	 * Get the name of the extension
	 *
	 * @return string
	 */
	public function getName()
	{
		return 'nout_online_extension';
	}

	/**
	 * Define our functions
	 *
	 * @return array
	 */
	public function getFilters()
	{
		return array(
			'noutonline_beautify_soap_query' => new \Twig_Filter_Method($this, 'beautifySOAPQuery'),
		);
	}

	/**
	 * Minify the query
	 *
	 * @param string $query
	 *
	 * @return string
	 */
	public function beautifySOAPQuery($query)
	{
		$nPos = strpos($query, '<?xml ');
		if (!$nPos)
			return $query;

		$header = substr($query, 0, $nPos);
		$xml = substr($query, $nPos);

		$doc = new \DOMDocument();
		$doc->preserveWhiteSpace = false;
		$doc->formatOutput = true;
		$doc->loadXML($xml);

		$result = $header.$doc->saveXML();
		return $result;
	}


	public function getFunctions()
	{
		return array(
			'noutonline_version' => new \Twig_Function_Method($this, 'version'),
			'noutonline_is_started' => new \Twig_Function_Method($this, 'isStarted'),
		);
	}

	/**
	 * Get NOUTOnline Version
	 *
	 * @return string
	 */
	public function version()
	{
		$clRest = $this->m_clServiceFactory->clGetRESTProxy($this->m_clConfiguration);
		try
		{
			return $clRest->sGetVersion();
		}
		catch(Exception $e)
		{
			return $e->getMessage();
		}
	}

	/**
	 * Test si NOUTOnline est démarré
	 * @return bool
	 */
	public function isStarted()
	{
		$clRest = $this->m_clServiceFactory->clGetRESTProxy($this->m_clConfiguration);
		return $clRest->bIsStarted();
	}

} 