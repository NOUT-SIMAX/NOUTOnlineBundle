<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Entity\UsernameToken;


use NOUT\Bundle\NOUTOnlineBundle\Security\EncryptionType;

class OASISUsernameToken extends LoginPasswordUsernameToken
{
    /** @var EncryptionType|null  */
    protected $m_clEncryptionType;
    /**
     * LoginPasswordUsernameToken constructor.
     * @param string $sUsername
     * @param string $sPassword
     * @param EncryptionType|null $clEncryption le système d'encryption du mot de passe dans SIMAXs
     */
    public function __construct(string $sUsername='', string $sPassword='', EncryptionType $clEncryption=null)
    {
        $this->m_clEncryptionType=$clEncryption;
        parent::__construct($sUsername, $sPassword);
    }

    /**
     * Crypte les différents éléments
     */
    public function _Compute() : void
    {
        $sSecurePassword = $this->m_clEncryptionType->sGetPassword($this->m_sSecretPassword);
        $this->Password = base64_encode(sha1($this->Nonce.$this->Created.$sSecurePassword, true));
    }

    /**
     * @inheritDoc
     */
    public function forSerialization(): array
    {
        return [
            $this->Username,
            $this->m_sSecretPassword,
            $this->m_clEncryptionType ? $this->m_clEncryptionType->forSerialization() : null,
        ];
    }

    /**
     * @inheritDoc
     */
    public function fromSerialization(array $data): void
    {
        if (count($data) > 2){
            list($this->Username, $this->m_sSecretPassword, $dataForEncrytion) = $data;
            $this->m_clEncryptionType = new EncryptionType(EncryptionType::MD5, EncryptionType::OPT_EmptyNoHash, false);
            if (!is_null($dataForEncrytion)){
                $this->m_clEncryptionType->fromSerialization($dataForEncrytion);
            }
        }
        else {
            list($this->Username, $this->m_sSecretPassword) = $data;
        }
    }
}
