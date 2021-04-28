<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Entity\UsernameToken;


abstract class LoginPasswordUsernameToken extends UsernameToken
{
    /**
     * @var string
     */
    protected $m_sSecretPassword;

    /**
     * LoginPasswordUsernameToken constructor.
     * @param string $sUsername
     * @param string $sPassword
     */
    public function __construct(string $sUsername='', string $sPassword='')
    {
        $this->m_sSecretPassword = $sPassword;

        parent::__construct($sUsername);
    }

    /**
     * @inheritDoc
     */
    public function bIsValid() : bool
    {
        return !empty($this->Username);
    }

    /**
     * @param string $password
     */
    protected function _setClearPassword(string $password) : void
    {
        $this->m_sSecretPassword = $password;
    }
}