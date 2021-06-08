<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 26/11/2015
 * Time: 09:56
 */

namespace NOUT\Bundle\NOUTOnlineBundle\SOAP;


use NOUT\Bundle\NOUTOnlineBundle\Cache\NOUTCacheProvider;
use NOUT\Bundle\NOUTOnlineBundle\Entity\UsernameToken\UsernameToken;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\OnlineError;
use Symfony\Contracts\Translation\TranslatorInterface;

class GestionWSDL
{
    /**
     * @var NOUTCacheProvider
     */
    protected $__cache;

    /**
     * @var TranslatorInterface
     */
    protected $__translator;

    /** @var double */
    protected $m_sNOVersion = '';

    public function __construct(TranslatorInterface $translator)
    {
        $this->__translator = $translator;

    }

    public function initCache(NOUTCacheProvider $cache)
    {
        $this->__cache = $cache;
    }

    /**
     * @param $sUri
     * @throws SOAPException
     */
    public function init($sNOVersionUri)
    {
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
        curl_close($curl);
    }

    /**
     * @return mixed
     */
    public function load()
    {
        if (!isset($this->__cache) || empty($this->m_sNOVersion)) {
            return false;
        }


        if ($this->__cache->contains(array('wsdl', $this->m_sNOVersion)))
        {
            return $this->__cache->fetch(array('wsdl', $this->m_sNOVersion));
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
        if (!isset($this->__cache) || empty($this->m_sNOVersion))
        {
            return ;
        }

        $this->__cache->save(array('wsdl', $this->m_sNOVersion), $wsdl, $dureeVie);
    }

    public function bGere($options)
    {
        switch($options)
        {
            case self::OPT_MenuVisible:
            {
                return floatval($this->m_sNOVersion) >= 1550.01;
            }
        }
        return false;
    }

    const OPT_MenuVisible = 'menu_visible';
}