<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 02/08/2016
 * Time: 11:36
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity;


class InfoIHM
{
    public $aMenu;
    public $aBigIcon;
    public $aToolbar;

    public function __construct(){
        $this->aMenu = array();
        $this->aBigIcon = array();
        $this->aToolbar = array();
    }
}