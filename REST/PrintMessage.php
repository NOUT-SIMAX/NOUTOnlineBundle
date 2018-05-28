<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 28/10/2016
 * Time: 11:51
 */

namespace NOUT\Bundle\NOUTOnlineBundle\REST;

class PrintMessage extends AbstractRestEntity
{
    private $idMessage;

    public function __construct($idMessage, $host, $actionParams = array())
    {
        $this->idMessage = $idMessage;
        parent::__construct($host);
    }

    public function setAllowedOptions() {
        $this->allowed_options = array();
    }

    public function getRouteParams()
    {
        return array(
           0 => $this->idMessage,
        );
    }

    public function getRouteName()
    {
        return 'PrintMessage';
    }
}