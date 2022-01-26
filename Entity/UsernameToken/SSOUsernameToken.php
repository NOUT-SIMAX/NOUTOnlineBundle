<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Entity\UsernameToken;

class SSOUsernameToken extends UsernameToken
{
    use TraitWithPassPhraseUsernameToken;
    use TraitUseEncryptionUsernameToken;
    use TraitUseBlowfishUsernameToken;

    protected $m_sEmail='';
    protected $m_sId='';

    /**
     * SSOUsernameToken constructor.
     * @param string $email
     * @param string $id
     * @param string $sPassPhrase
     */
    public function __construct(?string $email='', ?string $id='', ?string $sPassPhrase='')
    {
        $this->m_sEmail = $email ?? '';
        $this->m_sId = $id ?? '';
        $this->_setEncryptionMode('sso');
        $this->_setPassPhrase($sPassPhrase ?? '');
        parent::__construct();
    }

    protected function _setClearPassword(string $password): void { /*pas de password à stocker*/ }

    /**
     * @inheritDoc
     */
    public function bIsValid() : bool
    {
        return !empty($this->m_sEmail) || !empty($this->m_sId);
    }

    /**
     * @inheritDoc
     */
    protected function _Compute(): void
    {
        $this->Password = $this->_crypt($this->Encryption, json_encode([$this->m_sEmail, $this->m_sId]), $this->m_sPassPhrase, $this->Nonce, $this->Created);
    }

    /**
     * @inheritDoc
     */
    public function forSerialization(): array
    {
        return [$this->m_sEmail, $this->m_sId, $this->m_sPassPhrase];
    }

    /**
     * @inheritDoc
     */
    public function fromSerialization(array $data): void
    {
        list($this->m_sEmail, $this->m_sId, $this->m_sPassPhrase) = $data;
    }
}