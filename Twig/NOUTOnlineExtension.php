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
    protected OnlineServiceFactory $clServiceFactory;

    /**
     * @var ConfigurationDialogue
     */
    protected ConfigurationDialogue $clConfiguration;

    /** @var string */
    protected string $sVersionMin;

    /** @var string */
    protected string $sVersionMultilanguage;

    /** @var TokenStorageInterface  */
    protected TokenStorageInterface $clTokenStorage;

    /**
     * @param TokenStorageInterface $tokenStorage
     * @param OnlineServiceFactory  $factory
     * @param ConfigurationDialogue $configuration
     * @param array                 $aVersionsMin
     */
    public function __construct(TokenStorageInterface $tokenStorage, OnlineServiceFactory $factory, ConfigurationDialogue $configuration, array $aVersionsMin)
    {
        $this->clServiceFactory = $factory;
        $this->clConfiguration  = $configuration;
        $this->sVersionMin    = $aVersionsMin['site'];
        $this->sVersionMultilanguage = $aVersionsMin['multilanguage'];
        $this->clTokenStorage = $tokenStorage;
    }


    /**
     * Get the name of the extension
     *
     * @return string
     */
    public function getName() : string
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
    public function beautifyXML(string $query) : string
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

        return $header . $doc->saveXML();
    }

    /**
     * @param string $query
     * @return string
     */
    public function beautifyJSON(string $query) : string
    {
        $oTemp = json_decode($query);
        return json_encode($oTemp, JSON_PRETTY_PRINT);
    }

    /**
     * @param string $query
     *
     * @return string
     */
    public function getLanguageQuery(string $query) : string
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
        $oToken = $this->clTokenStorage->getToken();
        if ($oToken instanceof TokenWithNOUTOnlineVersionInterface) {
            $ret = $oToken->clGetNOUTOnlineState($this->sVersionMin);
        } else {
            $clRest = $this->clServiceFactory->clGetRESTProxy($this->clConfiguration);
            $ret = $clRest->clGetNOUTOnlineState($this->sVersionMin);
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
        $oToken = $this->clTokenStorage->getToken();
        if (!$oToken instanceof NOUTToken) {
            return false;
        }
        return $oToken->isVersionSup($version);
    }

    /**
     * @param string $property
     * @return bool
     */
    public function support(string $property): bool
    {
        $oToken = $this->clTokenStorage->getToken();
        if (!$oToken instanceof NOUTToken) {
            return false;
        }
        if ($property == 'multilanguage'){
            return $oToken->isVersionSup($this->sVersionMultilanguage);
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
    public function isStarted(): bool
    {
        $state = $this->state();
        return $state->isStarted;
    }

}
