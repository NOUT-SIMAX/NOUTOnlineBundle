<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Entity\UsernameToken;


use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Encryption;

trait TraitUseEncryptionUsernameToken
{

    /**
     * initialise correctement le secret
     * @param string $sMode
     */
    protected function _setEncryptionMode(string $sMode)
    {
        $this->Encryption = new Encryption();
        $this->Encryption->_ = $sMode;
    }
}