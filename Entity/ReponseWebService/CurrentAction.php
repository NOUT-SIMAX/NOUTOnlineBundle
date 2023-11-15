<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 18/07/14
 * Time: 17:30
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService;

/**
 * Class ResponseHeaderAction
 * @package NOUT\Bundle\NOUTOnlineBundle\Entity
 * contient les informations de l'action en cours
 */
class CurrentAction
{
    /**
     * @var string
     */
    protected string $sID='';

    /**
     * @var string
     */

    protected string $sTitle ='';
    /**
     * @var int
     */
    protected int $nIDTypeAction =0;

    /**
     * @var string
     */
    protected string $nIDForm ='';

    /**
     * @var string
     */
    protected string $sUserConfirmation='';

    /** @var bool  */
    protected bool $bIsConfiguration = false;

    /**
     * CurrentAction constructor.
     * @param \SimpleXMLElement $clAction
     */
    public function initFromXML(?\SimpleXMLElement $clAction) : CurrentAction
    {
        if (is_null($clAction)){
            return $this;
        }

        $this->sID             = (string) $clAction;
        $this->sTitle          = (string)$clAction['title'];
        $this->nIDTypeAction = (int)$clAction['typeAction'];
        $this->nIDForm            = (string)$clAction['actionForm'];
        $this->sUserConfirmation   = (string)$clAction['userConfirmation'];
        $this->bIsConfiguration   = ((int) ($clAction['isConfiguration'] ?? 0)) != 0;
        return $this;
    }

    /**
     * CurrentAction constructor.
     * @param \SimpleXMLElement $clAction
     */
    public function initFromJSON(\stdClass $clAction) : CurrentAction
    {
        $this->sID               = (string)$clAction->id;
        $this->sTitle            = (string)$clAction->title;
        $this->nIDTypeAction     = (int)$clAction->typeAction;
        $this->nIDForm           = (string)$clAction->actionForm;
        $this->sUserConfirmation = property_exists($clAction, 'userConfirmation') ? (string)$clAction->userConfirmation : '';
        $this->bIsConfiguration  = property_exists($clAction, 'isConfiguration') && ((int)($clAction->isConfiguration ?? 0)) != 0;
        return $this;
    }

    /**
     * @return int
     */
    public function getIDTypeAction(): int
    {
        return $this->nIDTypeAction;
    }

    /**
     * @return string
     */
    public function getID(): string
    {
        return $this->sID;
    }


    /**
     * @return string
     */
    public function getIDForm(): string
    {
        return $this->nIDForm;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->sTitle;
    }

    /**
     * @return string
     */
    public function getUserConfirmation(): string
    {
        return $this->sUserConfirmation;
    }

    /**
     * @return bool
     */
    public function isConfiguration(): bool
    {
        return $this->bIsConfiguration;
    }

}
