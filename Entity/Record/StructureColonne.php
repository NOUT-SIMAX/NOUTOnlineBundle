<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 04/08/14
 * Time: 11:00
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Record;

class StructureColonne
{
	protected $m_nIDColonne;
	protected $m_sLibelle;
	protected $m_eTypeElement;

	protected $m_bRequired;

	protected $m_TabStructureColonne;
	protected $m_clRestriction;

	protected $m_TabOptions;


	public function __construct($sID, \SimpleXMLElement $clAttribNOUT, \SimpleXMLElement $clAttribXS)
	{
		$this->m_nIDColonne   = $sID;
		$this->m_sLibelle     = '';
		$this->m_eTypeElement =  '';

		$this->m_bRequired = false;

		$this->m_TabStructureColonne = array();
		$this->m_TabOptions          = array();
		$this->m_clRestriction       = null;

		$this->_InitInfoColonne($clAttribNOUT, $clAttribXS);
	}

	protected function _InitInfoColonne(\SimpleXMLElement $clAttribNOUT, \SimpleXMLElement $clAttribXS)
	{
		$this->m_sLibelle     = (string) $clAttribNOUT['name'];
		$this->m_eTypeElement = (string) $clAttribNOUT['typeElement'];

		$this->m_bRequired = (isset($clAttribXS['use']) && ((string) $clAttribXS['use'] === 'required')); //xs:use="required"

		foreach ($clAttribNOUT as $sAttribName => $ndAttrib)
		{
			$this->m_TabOptions[$sAttribName] = (string) $ndAttrib;
		}
	}

	public function bEstTypeSimple()
	{
		$aTypeSimple = array(
			self::TM_Booleen,
			self::TM_Entier,
			self::TM_Texte,
			self::TM_DateHeure,
			self::TM_Date,
			self::TM_Heure,
			self::TM_Reel,
			self::TM_Monetaire,
		);

		return in_array($this->m_eTypeElement, $aTypeSimple);
	}


	/**
	 * @return array
	 */
	public function getTabStructureColonne()
	{
		return $this->m_TabStructureColonne;
	}


	/**
	 * @return null|ColonneRestriction
	 */
	public function getRestriction()
	{
		return $this->m_clRestriction;
	}

	/**
	 * @return mixed
	 */
	public function getIDColonne()
	{
		return $this->m_nIDColonne;
	}

	/**
	 * @return string
	 */
	public function getLibelle()
	{
		return $this->m_sLibelle;
	}

	/**
	 * @param ColonneRestriction $clRestriction
	 * @return $this
	 */
	public function setRestriction(ColonneRestriction $clRestriction)
	{
		$this->m_clRestriction = $clRestriction;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getTypeElement()
	{
		return $this->m_eTypeElement;
	}

	/**
	 * @param $eTypeElement string
	 * @return $this
	 */
	public function setTypeElement($eTypeElement)
	{
		$this->m_eTypeElement = $eTypeElement;

		return $this;
	}




	public function isOption($sOption)
	{
		//les options qui viennent de membres
		switch ($sOption)
		{
		case self::OPTION_Required:
			return $this->m_bRequired;
		}


		if (!isset($this->m_TabOptions[$sOption]))
		{
			return false;
		}

		return !empty($this->m_TabOptions[$sOption]);
	}

	//////////////////////////////////////////
	// POUR LE MOTEUR DE FORMULAIRE PAR DEFAUT
	//////////////////////////////////////////


	/**
	 * @return string
	 */
	public function getFormType()
	{
        if ($this->m_eTypeElement != self::TM_Texte)
        {
            return str_replace(':', '_', $this->m_eTypeElement);
        }
        //dans le cas d'un texte, il faut vérifier s'il y a pas des restrictions
        if (    is_null($this->m_clRestriction)
            || !$this->m_clRestriction->isTypeRestriction(ColonneRestriction::R_MAXLENGTH))
        {
            return str_replace(':', '_', self::TM_TexteLong);
        }

        return str_replace(':', '_', self::TM_Texte);
	}

	/**
	 * @return array
	 */
	public function getFormOption()
	{
		$aOptions = array(
			'label'     => $this->m_sLibelle,
			'read_only' => $this->isOption(self::OPTION_ReadOnly),
			'required'  => $this->m_bRequired,
			'disabled'  => $this->isOption(self::OPTION_Disabled),
		);
	}

	/**
	 *
	 */


	const TM_Invalide = null;

	//type simple
	const TM_Booleen   = 'xs:boolean';
	const TM_Entier    = 'xs:integer';
	const TM_Texte     = 'xs:string';
	const TM_DateHeure = 'xs:dateTime';
	const TM_Date      = 'xs:date';
	const TM_Heure     = 'xs:time';
	const TM_Reel      = 'xs:float';
	const TM_Monetaire = 'xs:decimal';
    //n'existe dans le xsd, pour cohérence de code
    const TM_TexteLong = 'xs:longstring';

	//type complexe
	const TM_Tableau    = 'simax-element';
	const TM_ListeElem  = 'simax-list';
	const TM_Separateur = 'simax-section';
	const TM_Bouton     = 'simax-button';
	const TM_Combo      = 'simax-choice';
	const TM_Fichier    = 'xs:base64Binary';


	// attributs communs à toutes les colonnes
	const OPTION_Detail      = 'detail';
	const OPTION_Printed     = 'printed';
	const OPTION_Computed    = 'computed';
	const OPTION_Titled      = 'titled';         //repris dans l'intitulé
	const OPTION_Sort        = 'sort';
	const OPTION_Link        = 'link';
	const OPTION_LinkControl = 'linkControl';    // pour les colonnes (controles de validité)

	const OPTION_Hidden   = "hidden";
	const OPTION_ReadOnly = "readOnly";
	const OPTION_Disabled = "disabled";

	const OPTION_Required = "required";


	// Attributs pour element d'un tableau
	const OPTION_LinkedTableXml = "linkedTableXml";
	const OPTION_LinkedTableID  = "linkedTableID";
	const OPTION_WithBtnOrdre   = "withBtnOrder";
	const OPTION_WithoutDetail  = "withoutDetail";
	const OPTION_WithoutSearch  = "withoutSearch";
	const OPTION_WithoutCreate  = "withoutCreate";
	const OPTION_Resource       = "resource";
	const OPTION_MultiResource  = "resourceMulti";

	// Attributs pour les sous-listes
	const OPTION_Relation      = "withAddAndRemove";    // bestGroupeRelation
	const OPTION_Relation11    = "withModifyAndRemove"; // bEstRelation11
	const OPTION_UniqueElement = "uniqueElement";

	// Attributs pour les listes en général
	const OPTION_WithPlanning = "withPlanning";
	const OPTION_WithGhost    = "withGhost";
	const OPTION_TableType    = "tableType";

	const OPTION_TableType_ListTable  = "list";
	const OPTION_TableType_PivotTable = "pivotTable";
	const OPTION_TableType_ViewTable  = "view";

	// Attributs pour les boutons
	const OPTION_IDAction       = "idAction";
	const OPTION_IDBouton       = "idButton";
	const OPTION_Sentence       = "sentence";
	const OPTION_TypeAction     = "actionType";
	const OPTION_TypeSelection  = "typeSelection";
	const OPTION_Icone          = "icon";
	const OPTION_WithValidation = "withValidation";
	const OPTION_IDColToUpdate  = "columnToUpdate";
	const OPTION_IDColSelection = "columnSelection";

	// Attributs des separateurs
	const OPTION_ModeMultiC      = "multiColumnMode";
	const OPTION_SensMultiC      = "multiColumnWay";
	const OPTION_SectionComputed = "sectionComputed";
	const OPTION_SectionLevel    = "sectionLevel";
	const OPTION_BackgroundColor = "backgroundColor";

	// Attributs pour liste deroulante
	const OPTION_AttributID = "id";

	// Attributs liés au modele
	const OPTION_Modele_PhoneNumber   = "phoneNumber";
	const OPTION_Modele_Directory     = "directory";
	const OPTION_Modele_PostalCode    = "postalCode";
	const OPTION_Modele_City          = "City";
	const OPTION_Modele_InputMask     = "inputMask";
	const OPTION_Modele_WithSecond    = "withSecond";
	const OPTION_Modele_PositionVideo = "videoPosition";
	const OPTION_Modele_IDColLinked   = "columnLinked";
}
