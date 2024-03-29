<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Entity\UsernameToken;

use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Encryption;

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
    public function __construct(string $cipher='', string $sUsername='', string $sPassword='', string $sPassPhrase='')
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
        return [
            'username' => $this->Username,
            'password' => $this->m_sSecretPassword,
            'passphrase' => $this->m_sPassPhrase,
            'cipher' => $this->Encryption instanceof Encryption ?  $this->Encryption->_ : $this->Encryption['!'] ,
        ];
    }

    /**
     * @inheritDoc
     */
    public function fromSerialization(array $data): void
    {
        if (array_key_exists('username', $data)){
            $this->Username = $data['username'];
            $this->m_sSecretPassword = $data['password'];
            $this->m_sPassPhrase = $data['passphrase'];
            if (is_array($this->Encryption)){
                $this->Encryption['_'] = $data['cipher'];
            }
            else {
                $this->Encryption->_ = $data['cipher'];
            }
        }
        else {
            list($this->Username, $this->m_sSecretPassword, $this->m_sPassPhrase) = $data;
            if ($this->Encryption instanceof Encryption){
                $this->Encryption->_ = 'blowfish';
            }
            else {
                $this->Encryption['!'] = 'blowfish';
            }
        }
    }
}
