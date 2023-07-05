<?php
/**
 * Created by PhpStorm
 * User: ninon
 * Date: 05/07/2023 15:46
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService;

class FormElement
{
    /** @var string  */
    protected string $nElementID;
    /** @var string  */
    protected string $sElementTitle;
    /** @var string  */
    protected string $nFormID;
    /** @var string  */
    protected string $sFormTitle;


    /**
     * @param $nElementID    : identifiant de l'utilisateur
     * @param $sElementTitle : minidesc de l'utilisateur
     * @param $nFormID       : identifiant du tableau réel de l'utilisateur
     * @param $sFormTitle    : minidesc du tableau réel de l'utilisateur
     */
    public function __construct($nElementID, $sElementTitle, $nFormID, $sFormTitle)
    {
        $this->nElementID    = (string) $nElementID;
        $this->sElementTitle = (string) $sElementTitle;
        $this->nFormID  = (string) $nFormID;
        $this->sFormTitle = (string) $sFormTitle;
    }

    /**
     * @return string
     */
    public function getFormID(): string
    {
        return $this->nFormID;
    }

    /**
     * @return string
     */
    public function getElementID(): string
    {
        return $this->nElementID;
    }

    /**
     * @return string
     */
    public function getFormTitle(): string
    {
        return $this->sFormTitle;
    }

    /**
     * @return string
     */
    public function getElementTitle(): string
    {
        return $this->sElementTitle;
    }


    /**
     * pour la serialisation
     * @return array
     */
    public function forSerialization() : array
    {
        return [$this->nFormID, $this->sFormTitle, $this->nElementID, $this->sElementTitle];
    }

    /**
     * pour l'init suivant à la deserialization
     * @param array $data
     */
    public function fromSerialization(array $data) : void
    {
        list($this->nFormID, $this->sFormTitle, $this->nElementID, $this->sElementTitle) = $data;
    }

}
