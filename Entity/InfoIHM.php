<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 02/08/2016
 * Time: 11:36
 */

namespace NOUT\Bundle\ContextsBundle\Entity\Menu;


class InfoMenu 
{
    public $aMenu;
    public $aBigIcon;

    public function __construct(){
        $this->aMenu = array();
        $this->aBigIcon = array();
    }
}