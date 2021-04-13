<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Entity\UsernameToken;

class BlowfishUsernameToken extends LoginPasswordUsernameToken
{
    use TraitUseEncryptionUsernameToken;
    use TraitUseBlowfishUsernameToken;

    /**
     * BlowfishUsernameToken constructor.
     * @param string $sUsername
     * @param string $sPassword
     * @param string $sSecret
     */
    public function __construct(string $sUsername='', string $sPassword='', string $sSecret='')
    {
        $this->_setEncryptionInfo('blowfish', $sSecret);
        parent::__construct($sUsername, $sPassword);
    }

    protected function _Compute(): void
    {
        $this->Password = $this->_crypt($this->Encryption, $this->m_sClearPassword, $this->m_sSecret, $this->Nonce, $this->Created);
    }

    /**
     * @inheritDoc
     */
    public function forSerialization(): array
    {
        return [$this->Username, $this->m_sClearPassword, $this->m_sSecret];
    }

    /**
     * @inheritDoc
     */
    public function fromSerialization(array $data): void
    {
        list($this->Username, $this->m_sClearPassword, $this->m_sSecret) = $data;
    }
}