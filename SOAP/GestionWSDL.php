<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 26/11/2015
 * Time: 09:56
 */

namespace NOUT\Bundle\NOUTOnlineBundle\SOAP;


use NOUT\Bundle\NOUTOnlineBundle\Cache\NOUTCache;

class GestionWSDL
{
    /**
     * @var NOUTCache
     */
    protected $__cache;

    /**
     * @var string
     */
    protected $m_sUri;

    /**
     * @var string
     */
    protected $m_sHash;

    /**
     * @var float
     */
    protected $m_dVersion;

    public function __construct(NOUTCache $cache, $sUri)
    {
        $this->__cache = $cache;
        $this->m_sUri = $sUri;
        $this->m_dVersion = 0;

        $fHandle = fopen($sUri, "r");
        if ($fHandle)
        {
            $sDebutWSDL='';
            while (strlen($sDebutWSDL)<1000)
            {
                $sDebutWSDL .= fgets($fHandle, 1000);
            }
            fclose($fHandle);

            $this->m_sHash = md5($sDebutWSDL, false);
            $this->m_dVersion = $this->_getVersion($sDebutWSDL);
        }
    }

    /**
     * @return mixed
     */
    public function load()
    {
        if (!isset($this->__clCache) || empty($this->m_sHash))
            return false;

        if ($this->__clCache->contains($this->m_sHash))
            return $this->__clCache->fetch($this->m_sHash);

        return false;
    }

    /**
     * sauve la wsdl en cache pour usage futur
     */
    public function save($wsdl, $dureeVie)
    {
        if (!isset($this->__clCache) || empty($this->__sVersionWSDL))
            return ;

        $this->__clCache->save($this->m_sHash, $wsdl, $dureeVie);
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


    public function getParamForGetTokenSession($Username)
    {
        if ($this->m_dVersion >= 1548.01)
        {
            $aParamTokenSession = array(
                'Username'=>$Username->Username,
                'Nonce'=>$Username->Nonce,
                'Created'=>$Username->Created,
                'Password'=>array(
                    '!'=>$Username->Password,
                )
            );

            if (!empty($Username->CryptMode))
            {
                $aParamTokenSession['Password']['md5']=$Username->CryptMd5;
                $aParamTokenSession['Password']['encryption']=$Username->CryptMode;
                if (isset($Username->CryptIV))
                {
                    $aParamTokenSession['Password']['iv']=$Username->CryptIV;
                }
                if (isset($Username->CryptKS))
                {
                    $aParamTokenSession['Password']['ks']=$Username->CryptKS;
                }
            }
            return $aParamTokenSession;
        }
        return $Username;
    }


}