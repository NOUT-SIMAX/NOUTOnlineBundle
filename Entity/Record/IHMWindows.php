<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Record;


class IHMWindows
{

    /** @var string : contient la mini desc de l'enregistrement */
    protected $m_sTitle='';

    /** @var string : identifiant du formulaire */
    protected $m_nIDTableau='';

    /** @var StructureElement */
    protected $m_clStructElem;

    public function __construct(string $sTitle, string $sIDTableau, StructureElement $clStruct = null)
    {
        $this->m_sTitle       = $sTitle;
        $this->m_nIDTableau   = $sIDTableau;
        $this->m_clStructElem = $clStruct;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->m_sTitle;
    }

    /**
     * @return string
     */
    public function getIDTableau(): string
    {
        return $this->m_nIDTableau;
    }

    /**
     * @return StructureElement
     */
    public function getStructElem(): ?StructureElement
    {
        return $this->m_clStructElem;
    }

    /**
     * @param $idColonne
     * @return StructureColonne|null
     */
    public function getStructColonne($idColonne): ?StructureColonne
    {
        if ($this->m_clStructElem instanceof StructureElement){
            return $this->m_clStructElem->getStructureColonne($idColonne);
        }
        return null;
    }
}