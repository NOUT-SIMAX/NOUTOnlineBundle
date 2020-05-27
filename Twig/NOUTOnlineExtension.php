<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 25/07/14
 * Time: 14:07
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Twig;

use NOUT\Bundle\NOUTOnlineBundle\Entity\ConfigurationDialogue;
use NOUT\Bundle\NOUTOnlineBundle\Entity\NOUTOnlineVersion;
use NOUT\Bundle\NOUTOnlineBundle\REST\OnlineServiceProxy;
use NOUT\Bundle\NOUTOnlineBundle\Service\OnlineServiceFactory;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\SOAPException;
use Symfony\Component\Config\Definition\Exception\Exception;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * This class contains the needed functions
 * - in order to do the query highlighting
 * - get the version and state of noutonline
 */
class NOUTOnlineExtension extends AbstractExtension
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
     * @var string $m_sVersionMin
     */
	protected $m_sVersionMin;

	/**
	 * @param OnlineServiceFactory  $factory
	 * @param ConfigurationDialogue $configuration
     * @param string $sVersionMin
	 */
	public function __construct(OnlineServiceFactory $factory, ConfigurationDialogue $configuration, $sVersionMin)
	{

		$this->m_clServiceFactory = $factory;
		$this->m_clConfiguration = $configuration;
		$this->m_sVersionMin = $sVersionMin;
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
            new TwigFilter('noutonline_beautify_xml', array($this, 'beautifyXML')),
            new TwigFilter('noutonline_beautify_json', array($this, 'beautifyJSON')),
		);
	}

	/**
	 * @param string $query
	 * @return string
	 */
	public function beautifyXML($query)
	{
		$nPos = strpos($query, '<?xml ');
		if ($nPos===false)
        {
            return $query;
        }

		$header = substr($query, 0, $nPos);
		$xml = substr($query, $nPos);

		$doc = new \DOMDocument();
		$doc->preserveWhiteSpace = false;
		$doc->formatOutput = true;
		$doc->loadXML($xml);

		$result = $header.$doc->saveXML();
		return $result;
	}

    /**
     * @param string $query
     * @return string
     */
    public function beautifyJSON($query)
    {
        $oTemp = json_decode($query);
        return json_encode($oTemp, JSON_PRETTY_PRINT);
    }


    public function getLanguageQuery($query)
    {
        if (strncmp(trim($query), '<?xml ', strlen('<?xml ')) == 0)
        {
            return 'markup';
        }

        if (    (strncmp(trim($query), 'http://', strlen('http://')) == 0)
            ||  (strncmp(trim($query), 'https://', strlen('https://')) == 0))
        {
            return 'http';
        }

        $oTemp = json_decode($query);
        if (json_last_error()==JSON_ERROR_NONE)
        {
            return 'json';
        }

        return 'txt';

    }



    public function getFunctions()
	{
		return array(
			 new TwigFunction('noutonline_state', array($this, 'state')),
			 new TwigFunction('noutonline_version', array($this, 'version')),
			 new TwigFunction('noutonline_is_started', array($this, 'isStarted')),
             new TwigFunction('noutonline_is_versionmin', array($this, 'isVersionMin')),
             new TwigFunction('noutonline_get_language_query', array($this, 'getLanguageQuery')),
		);
	}

    /**
     * Get NOUTOnline State
     * @return \stdClass
     */
	public function state()
    {
        /** @var OnlineServiceProxy $clRest */

        $ret = new  \stdClass();
        $ret->isStarted = false;
        $ret->error = '';
        $ret->version = '';
        $ret->isVersionMin = false;

        try
        {
            $clRest = $this->m_clServiceFactory->clGetRESTProxy($this->m_clConfiguration);
            $ret->isStarted = $clRest->bIsStarted();

            $clVersion = $clRest->clGetVersion();

            $ret->version = $clVersion->get();
            $ret->isVersionMin = $clVersion->isVersionSup($this->m_sVersionMin, true);
        }
        catch (\Exception $e)
        {
            if ($e instanceof SOAPException){
                $ret->isStarted = true;
            }
            $ret->error = $e->getMessage();
        }

        return $ret;
    }

	/**
	 * Get NOUTOnline Version
	 *
	 * @return string
	 */
	public function version()
	{
        /** @var OnlineServiceProxy $clRest */
		$clRest = $this->m_clServiceFactory->clGetRESTProxy($this->m_clConfiguration);
		try
		{
			return $clRest->clGetVersion()->get();
		}
		catch(\Exception $e)
		{
			return $e->getMessage();
		}
	}

    /**
     * @return bool
     */
	public function isVersionMin()
    {
        /** @var OnlineServiceProxy $clRest */
        $clRest = $this->m_clServiceFactory->clGetRESTProxy($this->m_clConfiguration);
        try
        {
            /** @var NOUTOnlineVersion $clVersion */
            $clVersion = $clRest->clGetVersion();
        }
        catch(\Exception $e)
        {
            return false;
        }

        return $clVersion->isVersionSup($this->m_sVersionMin, true);
    }

	/**
	 * Test si NOUTOnline est démarré
	 * @return bool
	 */
	public function isStarted()
	{
        /** @var OnlineServiceProxy $clRest */
		$clRest = $this->m_clServiceFactory->clGetRESTProxy($this->m_clConfiguration);
        return $clRest->bIsStarted();
	}

} 