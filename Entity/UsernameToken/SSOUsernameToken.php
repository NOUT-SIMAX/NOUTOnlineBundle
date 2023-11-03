<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Entity\UsernameToken;

class SSOUsernameToken extends UsernameToken
{
    use TraitWithPassPhraseUsernameToken;
    use TraitUseEncryptionUsernameToken;
    use TraitUseCipherUsernameToken;

    protected string $sEmail ='';
    protected string $sId    ='';

    /**
     * SSOUsernameToken constructor.
     * @param string $email
     * @param string $id
     * @param string $sPassPhrase
     */
    public function __construct(?string $email='', ?string $id='', ?string $sPassPhrase='', string $sEncryptionMode = 'sso_bf')
    {
        $this->sEmail = $email ?? '';
        $this->sId    = $id ?? '';
        $this->_setEncryptionMode($sEncryptionMode);
        $this->_setPassPhrase($sPassPhrase ?? '');
        parent::__construct();
    }

    protected function _setClearPassword(string $password): void { /*pas de password à stocker*/ }

    /**
     * @inheritDoc
     */
    public function bIsValid() : bool
    {
        return !empty($this->sEmail) || !empty($this->sId);
    }

    /**
     * @inheritDoc
     */
    protected function _Compute(): void
    {
        $this->Password = $this->_crypt($this->Encryption, json_encode([$this->sEmail, $this->sId]), $this->m_sPassPhrase, $this->Nonce, $this->Created);
    }

    /**
     * @inheritDoc
     */
    public function forSerialization(): array
    {
        return [$this->sEmail, $this->sId, $this->m_sPassPhrase];
    }

    /**
     * @inheritDoc
     */
    public function fromSerialization(array $data): void
    {
        list($this->sEmail, $this->sId, $this->m_sPassPhrase) = $data;
    }
}
