<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Entity\UsernameToken;


use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Encryption;

trait TraitUseEncryptionUsernameToken
{
    /**
     * @var string
     */
    protected $m_sSecret;

    /**
     * initialise correctement le secret
     * @param string $sMode
     * @param string $sSecret
     */
    protected function _setEncryptionInfo(string $sMode, string $sSecret)
    {
        $this->Encryption = new Encryption();
        $this->Encryption->_ = $sMode;

        $this->m_sSecret = utf8_decode(trim(str_replace("\r", "", $sSecret)));
    }
}