<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Entity\UsernameToken;


class OASISUsernameToken extends LoginPasswordUsernameToken
{
    protected $m_sTypeEncryption;
    /**
     * LoginPasswordUsernameToken constructor.
     * @param string $sUsername
     * @param string $sPassword
     * @param string $sType (custom_md5 désigne aussi bien la méthode pour les mots de passe classique que la gestion pour
     *                      les mots de passe plaintext pour l'extranet)
     */
    public function __construct(string $sUsername='', string $sPassword='', string $sType='custom_md5')
    {
        $this->m_sTypeEncryption=$sType;
        parent::__construct($sUsername, $sPassword);
    }

    /**
     * Crypte les différents éléments
     */
    public function _Compute() : void
    {
        switch ($this->m_sTypeEncryption)
        {
            default:
            case 'plaintext':
            case 'custom_md5':
                //la méthode d'encryption pour les mots de passe classique et la gestion pour les mots de passe plaintext pour l'extranet
                if (!empty($this->m_sSecretPassword)){
                    $sSecurePassword = base64_encode(md5($this->m_sSecretPassword, true));
                }
                else{
                    $sSecurePassword = 'AAAAAAAAAAAAAAAAAAAAAA==';
                }
                break;
            case 'md5':
                $sSecurePassword = base64_encode(hash('md5', $this->m_sSecretPassword, true));
                break;
            case 'sha-1':
                $sSecurePassword = base64_encode(hash('sha1', $this->m_sSecretPassword, true));
                break;
            case 'sha-256':
                $sSecurePassword = base64_encode(hash('sha256', $this->m_sSecretPassword, true));
                break;
        }
        $this->Password = base64_encode(sha1($this->Nonce.$this->Created.$sSecurePassword, true));
    }

    /**
     * @inheritDoc
     */
    public function forSerialization(): array
    {
        return [$this->Username, $this->m_sSecretPassword];
    }

    /**
     * @inheritDoc
     */
    public function fromSerialization(array $data): void
    {
        list($this->Username, $this->m_sSecretPassword) = $data;
    }
}