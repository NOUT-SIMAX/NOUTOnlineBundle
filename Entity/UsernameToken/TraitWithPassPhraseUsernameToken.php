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
        $this->m_sPassPhrase = mb_convert_encoding(trim(str_replace("\r", "", $sPassPhrase)), 'Windows-1252', 'UTF-8');
    }
}
