<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Entity\UsernameToken;

class CipherUsernameToken extends LoginPasswordUsernameToken
{
    use TraitWithPassPhraseUsernameToken;
    use TraitUseEncryptionUsernameToken;
    use TraitUseCipherUsernameToken;

    /**
     * BlowfishUsernameToken constructor.
     *
     * @param string $cipher
     * @param string $sUsername
     * @param string $sPassword
     * @param string $sPassPhrase
     */
    public function __construct(string $cipher, string $sUsername='', string $sPassword='', string $sPassPhrase='')
    {
        $this->_setEncryptionMode($cipher);
        $this->_setPassPhrase($sPassPhrase);
        parent::__construct($sUsername, $sPassword);
    }

    protected function _Compute(): void
    {
        $this->Password = $this->_crypt($this->Encryption, $this->m_sSecretPassword, $this->m_sPassPhrase, $this->Nonce, $this->Created);
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
