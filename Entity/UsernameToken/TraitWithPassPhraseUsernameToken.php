<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Entity\UsernameToken;

trait TraitWithPassPhraseUsernameToken
{
    /**
     * @var string
     */
    protected $m_sPassPhrase;

    /**
     * initialise correctement le secret
     * @param string $sPassPhrase
     */
    protected function _setPassPhrase(string $sPassPhrase)
    {
        $this->m_sPassPhrase = utf8_decode(trim(str_replace("\r", "", $sPassPhrase)));
    }
}
