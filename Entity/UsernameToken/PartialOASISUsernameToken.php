<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Entity\UsernameToken;


use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\PwdInfo;

class PartialOASISUsernameToken extends LoginPasswordUsernameToken
{
    use TraitUseBlowfishUsernameToken;
    use TraitWithPassPhraseUsernameToken;

    /**
     * PartialOASISUsernameToken constructor.
     *
     * @param string $sUsername
     * @param PwdInfo|null $pwdInfo
     * @param string $sPassPhrase
     */
    public function __construct(string $sUsername='', ?PwdInfo $pwdInfo=null, string $sPassPhrase='')
    {
        if (!is_null($pwdInfo)){
            $this->_setPassPhrase($sPassPhrase);
            $pwd = $this->_decryptConnectedUser($pwdInfo, $this->m_sPassPhrase);
        }
        parent::__construct($sUsername);
    }


    /**
     * Crypte les différents éléments
     */
    public function _Compute() : void
    {
        $this->Password = base64_encode(sha1($this->Nonce.$this->Created.$this->m_sSecretPassword, true));
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
