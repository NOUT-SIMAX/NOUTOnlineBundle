<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 26/11/2015
 * Time: 09:56
 */

namespace NOUT\Bundle\NOUTOnlineBundle\SOAP;


use NOUT\Bundle\NOUTOnlineBundle\Cache\NOUTCacheFactory;
use NOUT\Bundle\NOUTOnlineBundle\Cache\NOUTCacheProvider;
use NOUT\Bundle\NOUTOnlineBundle\Entity\NOUTOnlineVersion;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\OnlineError;
use NOUT\Bundle\NOUTOnlineBundle\Service\NOUTClientCache;
use Symfony\Contracts\Translation\TranslatorInterface;

class GestionWSDL
{
    /** @var NOUTCacheProvider|null */
    protected ?NOUTCacheProvider $clCacheNOUTOnline=null;

    /** @var NOUTCacheFactory  */
    protected NOUTCacheFactory $clCacheFactory;

    /** @var TranslatorInterface */
    protected TranslatorInterface $clTtranslator;

    /** @var string */
    protected $sNOVersion = '';

    public function __construct(TranslatorInterface $translator, NOUTCacheFactory $cacheFactory)
    {
        $this->clTtranslator = $translator;
        $this->clCacheFactory = $cacheFactory;
    }

    /**
     * @param $sUri
     * @throws SOAPException
     */
    public function init($sNOVersionUri, ?NOUTOnlineVersion $clNOUTOnlineVersion)
    {
        if (!empty($this->sNOVersion)){
            return ;
        }

        if (!is_null($clNOUTOnlineVersion))
        {
            $version = $clNOUTOnlineVersion->get();
            if (is_string($version) && preg_match('/^(?:\d{2}\.\d{2}\.)?\d{4}\.\d{2}$/', $version))
            {
                $this->sNOVersion = $version;
                $this->clCacheNOUTOnline =  $this->clCacheFactory->getCache($this->sNOVersion, NOUTClientCache::SOUSREPCACHE_NOUTONLINE, NOUTClientCache::REPCACHE);
                return ;
            }
        }


        //initialisation de curl
        $curl = curl_init($sNOVersionUri);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT , 2);

        //autres options
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //Demande du contenu du fichier
        curl_setopt($curl, CURLOPT_HEADER, 0); // Demande des headers

        //---------------------------
        //execution
        $this->sNOVersion = curl_exec($curl);
        $curlErrno = curl_errno($curl);
        if($curlErrno){
            switch ($curlErrno)
            {
                case CURLE_OPERATION_TIMEDOUT:
                {
                    $localip = curl_getinfo($curl, CURLINFO_LOCAL_IP);
                    $primaryip = curl_getinfo($curl, CURLINFO_PRIMARY_IP);
                    curl_close($curl);
                    throw new SOAPException($this->clTtranslator->trans('noutonline.wsdl.timeout_message', ['message'=>$localip]), OnlineError::ERR_NOUTONLINE_OFF);
                }

                default:
                {
                    $mess = curl_error($curl);
                    curl_close($curl);
                    throw new SOAPException($mess);
                }
            }
        }

        $this->clCacheNOUTOnline = $this->clCacheFactory->getCache($this->sNOVersion, NOUTClientCache::SOUSREPCACHE_NOUTONLINE, NOUTClientCache::REPCACHE);
        curl_close($curl);
    }

    /**
     * @return mixed
     */
    public function load()
    {
        if (!isset($this->clCacheNOUTOnline)) {
            return false;
        }

        if ($this->clCacheNOUTOnline->contains(array('wsdl')))
        {
            return $this->clCacheNOUTOnline->fetch(array('wsdl'));
        }

        return false;
    }

    /**
     * sauve la wsdl en cache pour usage futur
     * @param $dureeVie
     * @param $wsdl
     */
    public function save($wsdl, $dureeVie)
    {
        if (!isset($this->clCacheNOUTOnline))
        {
            return ;
        }

        $this->clCacheNOUTOnline->save(array('wsdl'), $wsdl, $dureeVie);
    }
}
