<?php
/**
 * Created by PhpStorm.
 * User: Chris
 * Date: 12/08/2016
 * Time: 15:03
 */

namespace NOUT\Bundle\SessionManagerBundle\Entity;


class ConfigExtranet
{
    /**
     * @var boolean
     */
    protected $m_bModeExtranet;

    /**
     * @var string
     */
    protected $m_sUser;

    /**
     * @var string
     */
    protected $m_sPassword;

    /**
     * @var string
     */
    protected $m_sForm;

    /**
     * ConfigExtranet constructor.
     * @param bool $bModeExtranet
     * @param string $sUser
     * @param string $sPassword
     * @param string $sForm
     */
    public function __construct($aConfigExtra)
    {
        $this->m_bModeExtranet  = $aConfigExtra['actif'];
        $this->m_sUser          = $aConfigExtra['user'];
        $this->m_sPassword      = $aConfigExtra['password'];
        $this->m_sForm          = $aConfigExtra['form'];
    }


    /**
     * @return boolean
     */
    public function isExtranet()
    {
        return $this->m_bModeExtranet;
    }

    /**
     * @param boolean $bModeExtranet
     */
    public function setModeExtranet($bModeExtranet)
    {
        $this->m_bModeExtranet = $bModeExtranet;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->m_sUser;
    }

    /**
     * @param string $sUser
     */
    public function setUser($sUser)
    {
        $this->m_sUser = $sUser;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->m_sPassword;
    }

    /**
     * @param string $sPassword
     */
    public function setPassword($sPassword)
    {
        $this->m_sPassword = $sPassword;
    }

    /**
     * @return string
     */
    public function getForm()
    {
        return $this->m_sForm;
    }

    /**
     * @param string $sForm
     */
    public function setForm($sForm)
    {
        $this->m_sForm = $sForm;
    }

}