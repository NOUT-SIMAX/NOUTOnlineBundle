<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 02/09/2015
 * Time: 16:06
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Record;

/**
 * Class RecordList, Description d'une liste d'enregistrement
 *
 * @package NOUT\Bundle\NOUTOnlineBundle\Entity\Record
 */
class RecordList extends MultiElement
{
    /**
     * @var RecordCache $m_clRecordCache : Contient les données de la fiche
     */
    protected $m_clRecordCache=null;

    /**
     * @var EnregTableauArray;
     * tableau qui contient les identifiants des enregistrements avec conservation de l'ordre de la réponse
     */
    protected $m_TabEnregTableau=null;

    /**
     * RecordList constructor.
     * @param string            $sTitle
     * @param string            $sIDAction
     * @param string            $sIDForm
     * @param EnregTableauArray $TabIDEnreg
     * @param StructureElement  $clStructElem
     * @param bool              $withGhost
     * @param bool              $possibleReorder
     * @param bool              $activeReorder
     * @param array             $exports
     * @param array             $imports
     * @param array             $tabSort
     */
    public function __construct(string $sTitle,
                                string $sIDAction,
                                string $sIDForm,
                                EnregTableauArray $TabIDEnreg,
                                StructureElement $clStructElem=null,
                                bool $withGhost=false,
                                bool $possibleReorder=false,
                                bool $activeReorder=false,
                                array $exports = array(),
                                array $imports = array(),
                                $tabSort=array())
    {
        parent::__construct($sTitle, $sIDAction, $sIDForm, $clStructElem, $withGhost, $possibleReorder, $activeReorder, $exports, $imports, $tabSort);

        $this->m_TabEnregTableau = $TabIDEnreg;
    }

    /**
     * @param $clRecordCache
     * @return $this
     */
    public function setRecordCache($clRecordCache=null): RecordList
    {
        $this->m_clRecordCache = $clRecordCache;
        return $this;
    }

    /**
     * @return RecordCache
     */
    public function getRecordCache(): RecordCache
    {
        return $this->m_clRecordCache;
    }

    /**
     * @return EnregTableauArray
     */
    public function getTabIDEnreg(): EnregTableauArray
    {
        return $this->m_TabEnregTableau;
    }

}