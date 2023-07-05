<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 18/07/14
 * Time: 18:01
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService;

/**
 * Class ConnectedUser
 * @package NOUT\Bundle\NOUTOnlineBundle\Entity
 * Utilisateur connecté
 */
class ConnectedUser extends FormElement
{
    /** @var null|PwdInfo */
    protected ?PwdInfo $oPwdInfo = null;

    /** @var null|ConnectedUser */
    protected ?ConnectedUser $oExtranet = null;

    /** @var FormElement|null  */
    protected ?FormElement $oResource = null;

    /**
     * @param PwdInfo $pdwInfo
     * @return $this
     */
    public function setPwdInfo(PwdInfo $pdwInfo) : ConnectedUser
    {
        $this->oPwdInfo = $pdwInfo;
        return $this;
    }

    /**
     * @return PwdInfo|null
     */
    public function getPwdInfo() : ?PwdInfo
    {
        return $this->oPwdInfo;
    }

    /**
     * @param ConnectedUser $clExtranet
     * @return $this
     */
    public function setExtranet(ConnectedUser $clExtranet): ConnectedUser
    {
        $this->oExtranet = $clExtranet;
        return $this;
    }

    /**
     * @return ConnectedUser|null
     */
    public function getExtranet() :?ConnectedUser
    {
        return $this->oExtranet;
    }

    public function getResource() : ?FormElement
    {
        return $this->oResource;
    }

    public function setResource(FormElement  $clResource) : ConnectedUser
    {
        $this->oResource = $clResource;
        return $this;
    }
}
