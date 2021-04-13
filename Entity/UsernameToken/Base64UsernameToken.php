<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Entity\UsernameToken;


class Base64UsernameToken extends LoginPasswordUsernameToken
{
    use TraitUseEncryptionUsernameToken;

    /**
     * Base64UsernameToken constructor.
     * @param string $sUsername
     * @param string $sPassword
     * @param string $sSecret
     */
    public function __construct(string $sUsername='', string $sPassword='', string $sSecret='')
    {
        $this->_setEncryptionInfo('base64', $sSecret);
        parent::__construct($sUsername, $sPassword);
    }

    /**
     * Crypte les différents éléments
     */
    public function _Compute() : void
    {
        $sSecret = base64_encode(md5($this->Nonce.$this->m_sSecret.$this->Created, true));
        $this->Password = base64_encode($sSecret.$this->m_sClearPassword);
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