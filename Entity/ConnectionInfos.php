<?php
namespace NOUT\Bundle\NOUTSessionManagerBundle\Entity;


/**
 * Class ConnectionInfos
 *
 * classe pour les infos de connexion, permet par exemple de generer le formulaire de connexion.
 *
 * @package NOUT\Bundle\NOUTSessionManagerBundle\Entity
 */
class ConnectionInfos
{
    public $m_sLogin;
    public $m_sPass;
    public $m_bExtranet;
    public $m_sExtraLogin;
    public $m_sExtraPass;

    public $m_sErrorMess;
    public $m_iErrorCode;


    public function __construct()
    {
        $this->m_bExtranet = false;
        $this->m_sExtraLogin = '';
        $this->m_sExtraPass ='';
        $this->m_sLogin ='';
        $this->m_sPass ='';
    }
} 