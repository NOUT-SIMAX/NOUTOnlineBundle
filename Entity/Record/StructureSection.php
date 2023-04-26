<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 03/08/2015
 * Time: 17:06
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Record;


use NOUT\Bundle\NOUTOnlineBundle\Entity\NOUTOnlineVersion;

class StructureSection extends StructureColonne
{
    /**
     * @var array tableau des colonnes filles
     */
    protected array $m_aTabStructureColonne;

    /**
     * @var array map idcolonne => colonnes filles
     */
    protected array $m_aMapIDColonne2Colonne;


    /**
     * @param                   $sID
     * @param \SimpleXMLElement $clAttribNOUT
     * @param \SimpleXMLElement $clAttribXS
     */
    public function __construct($sID, \SimpleXMLElement $clAttribNOUT, \SimpleXMLElement $clAttribXS)
    {
        parent::__construct($sID, $clAttribNOUT, $clAttribXS);

        $this->m_aTabStructureColonne = array();
        $this->m_aMapIDColonne2Colonne = array();

        $this->m_eTypeElement = self::TM_Separateur;
    }

    /**
     * @return StructureColonne[]
     */
    public function getTabStructureColonne(): array
    {
        return $this->m_aTabStructureColonne;
    }

    /**
     * @return StructureColonne[]
     */
    public function getMapStructureColonneAplat(): array
    {
        return $this->m_aMapIDColonne2Colonne;
    }

    /**
     * @param StructureColonne $clColonne
     * @return $this
     */
    public function addColonne(StructureColonne $clColonne): StructureSection
    {
        $this->m_aTabStructureColonne[]=$clColonne;
        $this->m_aMapIDColonne2Colonne[$clColonne->getIDColonne()]=$clColonne;
        return $this;
    }

    /**
     * @return StructureSection[]
     */
    public function getSubSections(): array
    {
        $sections = array();
        foreach($this->m_aTabStructureColonne as $column)
        {
            if($column instanceof StructureSection) {
                $sections[] = $column;
            }
        }
        return $sections;
    }

    /**
     * @return StructureBouton[]
     */
    public function getButtons(): array
    {
        $buttons = array();
        foreach($this->m_aTabStructureColonne as $column)
        {
            if($column instanceof StructureBouton){
                $buttons[] = $column;
            }
        }
        return $buttons;
    }

    /**
     * @return StructureElement[]
     */
    public function getElements(): array
    {
        $elements = array();
        foreach($this->m_aTabStructureColonne as $column)
        {
            if($column instanceof StructureElement){
                $elements[] = $column;
            }
        }
        return $elements;
    }

    /**
     * @return StructureDonnee[]
     */
    public function getData(): array
    {
        $data = array();
        foreach($this->m_aTabStructureColonne as $column)
        {
            if($column instanceof StructureDonnee) {
                $data[] = $column;
            }
        }
        return $data;
    }

    /**
     * @param NOUTOnlineVersion|null $NOUTOnlineVersion
     * @return bool
     */
    public function canGrow(?NOUTOnlineVersion $NOUTOnlineVersion=null) : int
    {
        $canGrow = 0;
        foreach($this->m_aMapIDColonne2Colonne as $column)
        {
            /** @var StructureColonne $column */
            $canGrow |= $column->canGrow($NOUTOnlineVersion);
        }
        return $canGrow;
    }


    /**
     * @param NOUTOnlineVersion|null $NOUTOnlineVersion
     * @return bool
     */
    public function needH(?NOUTOnlineVersion $NOUTOnlineVersion=null) : bool
    {
        $canGrow = false;
        $allH100 = true;
        foreach($this->m_aMapIDColonne2Colonne as $column)
        {
            /** @var StructureColonne $column */
            if ($column->canGrow($NOUTOnlineVersion))
            {
                $canGrow = true;
                if (!$column->needH($NOUTOnlineVersion)){
                    $allH100 = false;
                    break;
                }
            }
        }
        return $canGrow && $allH100;
    }


    const LEVEL_FICHE   = 0;
    const LEVEL_PRINCIPAL = 1;
    const LEVEL_SECONDAIRE = 2;
    const LEVEL_SEQUENCE = 3;

    const MODE_1COlONNE = 1;
    const MODE_2COlONNE = 2;
    const MODE_3COlONNE = 3;
    const MODE_4COlONNE = 4;
    const MODE_5COlONNE = 5;

    const SENS_HORIZONTAL = 1;
    const SENS_VERTICAL = 2;
}
