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
        $this->_setClearPassword($sPassword);

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
        if (empty($password)){
            $this->m_sSecretPassword = '';
            return ;
        }

        //on triche pour l'euro
        $this->m_sSecretPassword = mb_convert_encoding($password, 'Windows-1252', 'UTF-8');

    }
}
