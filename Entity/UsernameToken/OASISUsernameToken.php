<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Entity\UsernameToken;


class OASISUsernameToken extends LoginPasswordUsernameToken
{
    /**
     * Crypte les différents éléments
     */
    public function _Compute() : void
    {
        if (!empty($this->m_sSecretPassword)){
            $sSecurePassword = base64_encode(md5($this->m_sSecretPassword, true));
        }
        else{
            $sSecurePassword = 'AAAAAAAAAAAAAAAAAAAAAA==';
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