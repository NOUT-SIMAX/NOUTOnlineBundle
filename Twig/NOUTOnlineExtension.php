<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 25/07/14
 * Time: 14:07
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Twig;

use NOUT\Bundle\NOUTOnlineBundle\Entity\ConfigurationDialogue;
use NOUT\Bundle\NOUTOnlineBundle\Entity\NOUTOnlineState;
use NOUT\Bundle\NOUTOnlineBundle\Security\Authentication\Token\NOUTToken;
use NOUT\Bundle\NOUTOnlineBundle\Security\Authentication\Token\TokenWithNOUTOnlineVersionInterface;
use NOUT\Bundle\NOUTOnlineBundle\Service\OnlineServiceFactory;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
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
     * @var OnlineServiceFactory
     */
    protected $m_clServiceFactory;

    /**
     * @var ConfigurationDialogue
     */
    protected $m_clConfiguration;

    /** @var string */
    protected $m_sVersionMin;

    /** @var string */
    protected $m_sVersionMultilanguage;

    /**
     * @var TokenInterface|null
     */
    protected $m_oToken;

    /**
     * @param OnlineServiceFactory  $factory
     * @param ConfigurationDialogue $configuration
     * @param array                 $aVersionsMin
     */
    public function __construct(TokenStorageInterface $tokenStorage, OnlineServiceFactory $factory, ConfigurationDialogue $configuration, array $aVersionsMin)
    {
        $this->m_clServiceFactory = $factory;
        $this->m_clConfiguration = $configuration;
        $this->m_sVersionMin = $aVersionsMin['site'];
        $this->m_sVersionMultilanguage = $aVersionsMin['multilanguage'];
        $this->m_oToken = $tokenStorage->getToken();
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
        if ($nPos === false) {
            return $query;
        }

        $header = substr($query, 0, $nPos);
        $xml = substr($query, $nPos);

        $doc = new \DOMDocument();
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;
        $doc->loadXML($xml);

        $result = $header . $doc->saveXML();
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
        if (strncmp(trim($query), '<?xml ', strlen('<?xml ')) == 0) {
            return 'markup';
        }

        if ((strncmp(trim($query), 'http://', strlen('http://')) == 0)
            || (strncmp(trim($query), 'https://', strlen('https://')) == 0)) {
            return 'http';
        }

        json_decode($query);
        if (json_last_error() == JSON_ERROR_NONE) {
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
            new TwigFunction('noutonline_is_versionsup', array($this, 'isVersionSup')),
            new TwigFunction('noutonline_support', array($this, 'support')),
            new TwigFunction('noutonline_get_language_query', array($this, 'getLanguageQuery')),
            new TwigFunction('noutonline_beautify_xml', array($this, 'beautifyXML')),
            new TwigFunction('noutonline_beautify_json', array($this, 'beautifyJSON')),
            new TwigFunction('noutonline_is_simaxstarter', array($this, 'isSIMAXStarter')),
        );
    }

    /**
     * Get NOUTOnline State
     * @return NOUTOnlineState
     * @throws \Exception
     */
    public function state(): NOUTOnlineState
    {
        if ($this->m_oToken instanceof TokenWithNOUTOnlineVersionInterface) {
            $ret = $this->m_oToken->clGetNOUTOnlineState($this->m_sVersionMin);
        } else {
            $clRest = $this->m_clServiceFactory->clGetRESTProxy($this->m_clConfiguration);
            $ret = $clRest->clGetNOUTOnlineState($this->m_sVersionMin);
        }

        return $ret;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function isSIMAXStarter(): bool
    {
        $state = $this->state();
        return $state->isSIMAXStarter;
    }

    /**
     * @param $version
     * @return bool
     */
    public function isVersionSup($version): bool
    {
        if (!$this->m_oToken instanceof NOUTToken) {
            return false;
        }
        return $this->m_oToken->isVersionSup($version, true);
    }

    /**
     * @param string $property
     * @return bool
     */
    public function support(string $property): bool
    {
        if (!$this->m_oToken instanceof NOUTToken) {
            return false;
        }
        switch ($property) {
            case 'multilanguage':
            {
                return $this->m_oToken->isVersionSup($this->m_sVersionMultilanguage, true);
            }
        }
        return false;
    }

    /**
     * Get NOUTOnline Version
     *
     * @return string
     * @throws \Exception
     */
    public function version(): string
    {
        $state = $this->state();
        return $state->version;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function isVersionMin(): bool
    {
        $state = $this->state();
        return $state->isRecent;
    }

    /**
     * Test si NOUTOnline est démarré
     * @return bool
     * @throws \Exception
     */
    public function isStarted(): string
    {
        $state = $this->state();
        return $state->isStarted;
    }

}
