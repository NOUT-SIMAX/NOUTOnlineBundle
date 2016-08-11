<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 02/09/2015
 * Time: 16:34
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService;


use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\RecordList;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureElement;

class ParserList extends Parser
{
    /**
     * @var ParserRecordList
     */
    protected $m_clParserList;

    /**
     * @var ParserRecordList
     */
    protected $m_clParserParam;

    public function __construct()
    {
        $this->m_clParserList = new ParserRecordList();
        $this->m_clParserParam = new ParserRecordList();
    }

    /**
     * @return array|\NOUT\Bundle\NOUTOnlineBundle\Entity\Record\EnregTableauArray
     */
    public function GetTabEnregTableau()
    {
        return $this->m_clParserList->GetTabEnregTableau();
    }

    /**
     * @param $sIDForm
     * @param $sIDEnreg
     * @return \NOUT\Bundle\NOUTOnlineBundle\Entity\Record\Record|null
     */
    public function getRecordFromID($sIDForm, $sIDEnreg)
    {
        return $this->m_clParserList->getRecordFromID($sIDForm, $sIDEnreg);
    }

    /**
     * Parse la liste
     * Ne doit pas être trop volumineuse
     * @param XMLResponseWS $clReponseXML
     */
    public function ParseList(XMLResponseWS $clReponseXML)
    {

        $ndSchema    = $clReponseXML->getNodeSchema();

        if (isset($ndSchema))
        {
            $this->m_clParserList->ParseXSD($ndSchema, StructureElement::NV_XSD_List);
        }

        $ndXML = $clReponseXML->getNodeXML();
        $this->m_clParserList->ParseXML($ndXML, $clReponseXML->clGetForm()->getID(), StructureElement::NV_XSD_List);
    }

    /**
     * Parse les paramètres
     * @param XMLResponseWS $clReponseXML
     */
    public function ParseParam(XMLResponseWS $clReponseXML)
    {
        $ndSchema    = $clReponseXML->getNodeXSDParam();

        // Ne pas utiliser isSet
        if (!is_null($ndSchema))
        {
            $this->m_clParserParam->ParseXSD($ndSchema, StructureElement::NV_XSD_Enreg);
        }

        $ndXML = $clReponseXML->getNodeXMLParam();

        // Ne pas utiliser isSet
        if (!is_null($ndXML))
        {
            $this->m_clParserParam->ParseXML($ndXML, $clReponseXML->clGetAction()->getIDForm(), StructureElement::NV_XSD_Enreg);
        }
    }

    /**
     * @param XMLResponseWS $clReponseXML
     * @return RecordList
     */
    public function getList(XMLResponseWS $clReponseXML)
    {
        // Appel depuis le testController
        // GetRecord sur non-objet = ERREUR

        $sIDForm = $clReponseXML->clGetForm()->getID();
        $sIDFormAction = $clReponseXML->clGetAction()->getIDForm();
        $sIDAction = $clReponseXML->clGetAction()->getID();
        $sTitre = $clReponseXML->clGetAction()->getTitle();


        // Contrôle des variables OK
//        dump($sIDForm);
//        dump($sIDFormAction);
//        dump($sIDAction);
//        dump($sTitre);


        $clStructElem = $this->m_clParserList->getStructureElem($sIDForm, StructureElement::NV_XSD_List);


        // Instance d'une nouvelle clList avec toutes les données précédentes
        $clList = new RecordList($sTitre, $sIDAction, $sIDForm, $this->m_clParserList->m_TabEnregTableau, $clStructElem);
        $clList->setDefaultDisplayMode($clReponseXML->sGetDefaultDisplayMode());
        $clList->setTabPossibleDisplayMode($clReponseXML->GetTabPossibleDisplayMode());

        // Paramètres pour la clList
        $clList->setParam($this->m_clParserParam->getRecordFromID($sIDFormAction, $sIDAction));

        // TODO


        // Instance de ParserRecordList->getRecord()
        // Faire un getter du RecordCache du parserRecordList (get(get())
        // $this->m_clParserParam->

        // Deux méthodes dans ParserRecordList
        // GetFullCache et GetRecord
        // $this->m_clParserList est un ParserRecordList


        // Données pour la clList
        //// Il faut donner le cache en paramètre
        $clList->setRecordCache($this->m_clParserList->getFullCache());

        return $clList;
    }

}