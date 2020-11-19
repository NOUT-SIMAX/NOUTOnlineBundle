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

    /**
     * @var string
     */
    protected $m_sUri = '';

    /**
     * @var string
     */
    protected $m_sHash = '';

    /**
     * @var float
     */
    protected $m_dVersion = 0;

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
    public function initUri($sUri)
    {
        //initialisation de curl
        $curl = curl_init($sUri);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT , 2);

        //autres options
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //Demande du contenu du fichier
        curl_setopt($curl, CURLOPT_HEADER, 0); // Demande des headers

        //---------------------------
        //execution
        $output = curl_exec($curl);
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
        $sDebutWSDL=substr($output, 0, 1000);
        $this->m_sHash = md5($sDebutWSDL, false);
        $this->m_dVersion = $this->_getVersion($sDebutWSDL);
    }

    /**
     * @return mixed
     */
    public function load()
    {
        if (!isset($this->__cache) || empty($this->m_sHash))
        {
            return false;
        }

        if ($this->__cache->contains(array('wsdl', $this->m_sHash)))
        {
            return $this->__cache->fetch(array('wsdl', $this->m_sHash));
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
        if (!isset($this->__cache) || empty($this->m_sHash))
        {
            return ;
        }

        $this->__cache->save(array('wsdl', $this->m_sHash), $wsdl, $dureeVie);
    }

    /**
     * récupère le numéro de version de la wsdl
     * @param $sDebutWSDL
     * @return float
     */
    protected function _getVersion($sDebutWSDL)
    {
        //xmlns:serviceversion="http://www.nout.fr/wsdl/noutonline/1548.01"
        $nPos=strpos($sDebutWSDL, 'xmlns:serviceversion="');
        if ($nPos===FALSE)
        {
            return 0;
        }

        $nPos+=strlen('xmlns:serviceversion="');

        $nFin = strpos($sDebutWSDL, '"', $nPos);

        $sValeur = substr($sDebutWSDL, $nPos, $nFin-$nPos);

        $nPos=strrpos($sValeur, '/');
        $sValeur = substr($sValeur, $nPos+1);

        return floatval($sValeur);
    }

    /**
     * @return float
     */
    public function getVersion()
    {
        return $this->m_dVersion;
    }

    public function bGere($options)
    {
        switch($options)
        {
            case self::OPT_MenuVisible:
            {
                return $this->m_dVersion >= 1550.01;
            }
        }
        return false;
    }

    const OPT_MenuVisible = 'menu_visible';
}