<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Entity\UsernameToken;


class NonceCreatedSecretUsernamePassword extends UsernameToken
{
    use TraitUseEncryptionUsernameToken;
    use TraitWithPassPhraseUsernameToken;

    /**
     * Base64UsernameToken constructor.
     * @param string $sPassPhrase
     */
    public function __construct(string $sPassPhrase='')
    {
        $this->_setEncryptionMode('base64');
        $this->_setPassPhrase($sPassPhrase);
        parent::__construct('');
    }

    protected function _setClearPassword(string $password) : void {}

    /**
     * Crypte les différents éléments
     */
    public function _Compute() : void
    {
        $this->Encryption->md5 = base64_encode(md5($this->m_sPassPhrase.$this->Nonce.$this->Created, true));
    }

    /**
     * @inheritDoc
     */
    public function forSerialization(): array
    {
        return [$this->m_sPassPhrase];
    }

    /**
     * @inheritDoc
     */
    public function fromSerialization(array $data): void
    {
        list($this->m_sPassPhrase) = $data;
    }
    /**
     * @inheritDoc
     */
    public function bIsValid() : bool
    {
        return true;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function sToRest() : string
    {
        $this->Compute(); //on fait le compute

        $sBottom = 'nonce='.urlencode(utf8_decode($this->Nonce));
        $sBottom .= '&created='.urlencode(utf8_decode($this->Created));

        if (!empty($this->Encryption))
        {
            $sBottom .= '&md5=' . urlencode($this->Encryption->md5);
        }
        return $sBottom;
    }
}