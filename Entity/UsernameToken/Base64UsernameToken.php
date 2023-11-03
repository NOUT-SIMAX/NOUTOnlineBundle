<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Entity\UsernameToken;


class Base64UsernameToken extends LoginPasswordUsernameToken
{
    use TraitUseEncryptionUsernameToken;
    use TraitWithPassPhraseUsernameToken;

    /**
     * Base64UsernameToken constructor.
     * @param string $sUsername
     * @param string $sPassword
     * @param string $sPassPhrase
     */
    public function __construct(string $sUsername='', string $sPassword='', string $sPassPhrase='')
    {
        $this->_setEncryptionMode('base64');
        $this->_setPassPhrase($sPassPhrase);
        parent::__construct($sUsername, $sPassword);
    }

    /**
     * Crypte les différents éléments
     */
    public function _Compute() : void
    {
        $sSecret = base64_encode(md5($this->Nonce.$this->m_sPassPhrase.$this->Created, true));
        $this->Password = base64_encode($sSecret.$this->m_sSecretPassword);
        $this->Encryption->md5 = base64_encode(md5($this->m_sPassPhrase.$this->Nonce.$this->Created, true));
    }

    /**
     * @inheritDoc
     */
    public function forSerialization(): array
    {
        return [$this->Username, $this->m_sSecretPassword, $this->m_sPassPhrase];
    }

    /**
     * @inheritDoc
     */
    public function fromSerialization(array $data): void
    {
        list($this->Username, $this->m_sSecretPassword, $this->m_sPassPhrase) = $data;
    }
}
