<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Entity\UsernameToken;


abstract class LoginPasswordUsernameToken extends UsernameToken
{
    /**
     * @var string
     */
    protected $m_sClearPassword;

    /**
     * LoginPasswordUsernameToken constructor.
     * @param string $sUsername
     * @param string $sPassword
     */
    public function __construct(string $sUsername='', string $sPassword='')
    {
        $this->m_sClearPassword = $sPassword;

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
    public function setClearPassword(string $password)
    {
        $this->m_sClearPassword = $password;
        return $this;
    }
}