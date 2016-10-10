<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 26/11/2015
 * Time: 09:56
 */

namespace NOUT\Bundle\NOUTOnlineBundle\SOAP;


use NOUT\Bundle\NOUTOnlineBundle\Cache\NOUTCache;
use NOUT\Bundle\NOUTOnlineBundle\Entity\OASIS\UsernameToken;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\OnlineError;

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

        try {
            $fHandle = @fopen($sUri, "r");
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
            else
            {
                throw new SOAPException('NOUTOnline ne répond pas', OnlineError::ERR_NOUTONLINE_OFF);
            }
        }
        catch (\Exception $e){
            //TODO trad
            throw new SOAPException('NOUTOnline ne répond pas', OnlineError::ERR_NOUTONLINE_OFF);
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


    public function getParamForGetTokenSession(UsernameToken $Username)
    {
        if (($this->m_dVersion >= 1548.01) && $Username->bCrypted())
        {
            $aParamTokenSession = array(
                'Username'=>$Username->Username,
                'Nonce'=>$Username->Nonce,
                'Created'=>$Username->Created,
                'Password'=>$Username->Password,
            );

            if ($Username->bCrypted())
            {
                $aParamTokenSession['Encryption']['!']=$Username->getMode();
                $aParamTokenSession['Encryption']['md5']=$Username->CryptMd5;
                if (isset($Username->CryptIV))
                {
                    $aParamTokenSession['Encryption']['iv']=$Username->CryptIV;
                }
                if (isset($Username->CryptKS))
                {
                    $aParamTokenSession['Encryption']['ks']=$Username->CryptKS;
                }
            }
            return $aParamTokenSession;
        }
        return $Username;
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