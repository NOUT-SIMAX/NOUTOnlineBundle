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
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\OnlineError;
use NOUT\Bundle\NOUTOnlineBundle\Security\Authentication\Token\NOUTToken;
use NOUT\Bundle\NOUTOnlineBundle\Service\NOUTClientCache;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class GestionWSDL
{
    /** @var NOUTCacheProvider|null */
    protected $m_clCacheNOUTOnline=null;

    /** @var NOUTCacheFactory  */
    protected $__cacheFactory;

    /** @var TranslatorInterface */
    protected $__translator;

    /** @var string */
    protected $m_sNOVersion = '';

    public function __construct(TranslatorInterface $translator, NOUTCacheFactory $cacheFactory, TokenStorageInterface $tokenStorage)
    {
        $this->__translator = $translator;
        $this->__cacheFactory = $cacheFactory;

        $oSecurityToken = $tokenStorage->getToken();
        if ($oSecurityToken instanceof NOUTToken){
            $this->m_sNOVersion = $oSecurityToken->getVersionNO();
            $this->m_clCacheNOUTOnline =  $cacheFactory->getCache($this->m_sNOVersion, NOUTClientCache::SOUSREPCACHE_NOUTONLINE, NOUTClientCache::REPCACHE);
        }
    }

    /**
     * @param $sUri
     * @throws SOAPException
     */
    public function init($sNOVersionUri)
    {
        if (!empty($this->m_sNOVersion)){
            return ;
        }

        //initialisation de curl
        $curl = curl_init($sNOVersionUri);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT , 2);

        //autres options
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //Demande du contenu du fichier
        curl_setopt($curl, CURLOPT_HEADER, 0); // Demande des headers

        //---------------------------
        //execution
        $this->m_sNOVersion = curl_exec($curl);
        $curl_errno = curl_errno($curl);
        if($curl_errno){
            switch ($curl_errno)
            {
                case CURLE_OPERATION_TIMEDOUT:
                {
                    $localip = curl_getinfo($curl, CURLINFO_LOCAL_IP);
                    $primaryip = curl_getinfo($curl, CURLINFO_PRIMARY_IP);
                    curl_close($curl);
                    throw new SOAPException($this->__translator->trans('noutonline.wsdl.timeout_message', ['message'=>$localip]), OnlineError::ERR_NOUTONLINE_OFF);
                }

                default:
                {
                    $mess = curl_error($curl);
                    curl_close($curl);
                    throw new SOAPException($mess);
                }
            }
        }

        $this->m_clCacheNOUTOnline = $this->__cacheFactory->getCache($this->m_sNOVersion, NOUTClientCache::SOUSREPCACHE_NOUTONLINE, NOUTClientCache::REPCACHE);
        curl_close($curl);
    }

    /**
     * @return mixed
     */
    public function load()
    {
        if (!isset($this->m_clCacheNOUTOnline)) {
            return false;
        }

        if ($this->m_clCacheNOUTOnline->contains(array('wsdl')))
        {
            return $this->m_clCacheNOUTOnline->fetch(array('wsdl'));
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
        if (!isset($this->m_clCacheNOUTOnline))
        {
            return ;
        }

        $this->m_clCacheNOUTOnline->save(array('wsdl'), $wsdl, $dureeVie);
    }
}