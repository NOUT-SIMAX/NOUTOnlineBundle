<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Entity\UsernameToken;


class PartialOASISUsernameToken extends LoginPasswordUsernameToken
{
    use TraitUseBlowfishUsernameToken;
    use TraitWithPassPhraseUsernameToken;

    /**
     * PartialOASISUsernameToken constructor.
     * @param string $sUsername
     * @param string $pwd
     * @param string $iv
     * @param int    $ks
     * @param string $sPassPhrase
     */
    public function __construct(string $sUsername='', string $pwd='', string $iv='', int $ks=0, string $sPassPhrase='')
    {
        if (!empty($pwd)){
            $this->_setPassPhrase($sPassPhrase);
            $pwd = $this->_decryptConnectedUser($pwd, $iv, $ks, $this->m_sPassPhrase);
        }
        parent::__construct($sUsername, $pwd);
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