<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Record;


class IHMWindows
{

    /** @var string : contient la mini desc de l'enregistrement */
    protected string $sTitle ='';

    /** @var string : identifiant du formulaire */
    protected string $nIDTableau ='';

    /** @var StructureElement|null */
    protected ?StructureElement $clStructElem;

    public function __construct(string $sTitle, string $sIDTableau, StructureElement $clStruct = null)
    {
        $this->sTitle         = $sTitle;
        $this->nIDTableau     = $sIDTableau;
        $this->clStructElem = $clStruct;
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
    public function getIDTableau(): string
    {
        return $this->nIDTableau;
    }

    /**
     * @return int
     */
    public function getTableInfoConfiguration() : int
    {
        return is_null($this->clStructElem) ? 0 : $this->clStructElem->getTableInfoConfiguration();
    }

    public function getIDIcon() : string
    {
        return is_null($this->clStructElem) ? '' : $this->clStructElem->getIDIcon();
    }

    /**
     * @return StructureElement
     */
    public function getStructElem(): ?StructureElement
    {
        return $this->clStructElem;
    }

    /**
     * @param $idColonne
     * @return StructureColonne|null
     */
    public function getStructColonne($idColonne): ?StructureColonne
    {
        if ($this->clStructElem instanceof StructureElement){
            return $this->clStructElem->getStructureColonne($idColonne);
        }
        return null;
    }
}
