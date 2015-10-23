<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 04/08/14
 * Time: 11:00
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Record;

abstract class StructureColonne
{
	/**
	 * @var string identifiant de la colonne
	 */
	protected $m_nIDColonne;
	/**
	 * @var string nom de la colonne
	 */
	protected $m_sLibelle;
	/**
	 * @var string type modèle de la colonne
	 */
	protected $m_eTypeElement;

	/**
	 * @var array options du champ
	 */
	protected $m_TabOptions;

	/**
	 * structure élément du formulaire lié
	 * @var StructureElement|null
	 */
	protected $m_clStructureElemLie;


	/**
	 * retourne le type de l'élément
	 * @param \SimpleXMLElement $clAttribNOUT
	 * @return string
	 */
	static function s_getTypeColonne(\SimpleXMLElement $clAttribNOUT)
	{
		return (string) $clAttribNOUT[self::OPTION_TypeElement];
	}

	public function __construct($sID, \SimpleXMLElement $clAttribNOUT, \SimpleXMLElement $clAttribXS)
	{
		$this->m_nIDColonne   = $sID;
		$this->m_sLibelle     = '';
		$this->m_eTypeElement =  '';

		$this->m_TabOptions          = array();
		$this->m_clStructureElemLie  = null;

		$this->_InitInfoColonne($clAttribNOUT, $clAttribXS);
	}

	protected function _InitInfoColonne(\SimpleXMLElement $clAttribNOUT, \SimpleXMLElement $clAttribXS)
	{
		$this->m_sLibelle     = (string) $clAttribNOUT[self::OPTION_Name];
		$this->m_eTypeElement = (string) $clAttribNOUT[self::OPTION_TypeElement];

		foreach ($clAttribNOUT as $sAttribName => $ndAttrib)
		{
			$this->m_TabOptions[$sAttribName] = (string) $ndAttrib;
		}
	}

	/**
	 * @param StructureElement $clStructElem
	 * @return $this
	 */
	public function setStructureElementLie(StructureElement $clStructElem)
	{
		$this->m_clStructureElemLie = $clStructElem;
		return $this;
	}

    /**
     * @return array|null
     */
    public function getStructureElementLie()
    {
        return $this->m_clStructureElemLie;
    }

	/**
     * @return StructureElement|null
     */
    public function getTabOptions()
    {
        return $this->m_TabOptions;
    }



    /**
     * pour savoir si fusion de colonne pour le multi-colonne
     * @return bool
     */
    public function eGetFusionTypeMulticolonne()
    {
        if ($this->m_eTypeElement == self::TM_Bouton)
            return self::FUSIONTYPE_Bouton;

        if ($this->isOption(self::OPTION_Modele_City) || $this->isOption(self::OPTION_Modele_PostalCode))
            return self::FUSIONTYPE_VilleCP;

        return self::FUSIONTYPE_Aucun;
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

    public function bAvecValeur()
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
            self::TM_TexteMultiLigne,
            self::TM_Tableau,
            self::TM_ListeElem,
            self::TM_Combo,
            self::TM_Fichier,
        );

        return in_array($this->m_eTypeElement, $aTypeSimple);
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


    /**
     * @param $sOption
     * @return bool
     */
	public function isOption($sOption)
	{
		if (!isset($this->m_TabOptions[$sOption]))
		{
			return false;
		}

		return !empty($this->m_TabOptions[$sOption]);
	}

	/**
	 * @param $sOption
	 * @return string|null
	 */
	public function getOption($sOption)
	{
		if (!isset($this->m_TabOptions[$sOption]))
		{
			return null;
		}

		return $this->m_TabOptions[$sOption];
	}

	//////////////////////////////////////////
	// POUR LE MOTEUR DE FORMULAIRE PAR DEFAUT
	//////////////////////////////////////////


	/**
	 * @return string
	 *
	 * Retourne le type de formulaire Symfony correspondant
	 */
	public function getFormType()
	{
		switch ($this->m_eTypeElement)
		{
			case self::TM_Entier :
			{
				if ($this->getOption(self::OPTION_Transform) == self::OPTION_Transform_Color)
				{
					return 'simax_color';
				}
				else
				{
					return str_replace(array(':', '-'), array('_', '_'), $this->m_eTypeElement);
				}

			}
			case self::TM_Texte :
			{
				if (!is_null($this->m_clRestriction) &&
					($this->m_clRestriction->hasTypeRestriction(ColonneRestriction::R_MAXLENGTH))
				)
				{
					// Texte mono-ligne
					return str_replace(array(':', '-'), array('_', '_'), self::TM_Texte);
				}
				else
				{
					// Texte multi-ligne (car pas de restriction de nombre de caractères)
					return str_replace(array(':', '-'), array('_', '_'), self::TM_TexteMultiLigne);
				}
			}
			default : // Correspond au != self::TM_Texte
			{
				return str_replace(array(':', '-'), array('_', '_'), $this->m_eTypeElement);
			}
		}
	}


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
    const TM_TexteMultiLigne = 'xs:multilinestring';

	//type complexe
	const TM_Tableau    = 'simax-element';
	const TM_ListeElem  = 'simax-list';
	const TM_Separateur = 'simax-section';
	const TM_Bouton     = 'simax-button';
	const TM_Combo      = 'simax-choice';
	const TM_Fichier    = 'xs:base64Binary';


    const OPTION_Name       = 'name';
    const OPTION_TypeElement= 'typeElement';

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
	const OPTION_Transform = "transform";


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
	const OPTION_Modele_PhoneNumber		= "phoneNumber";
	const OPTION_Modele_CreditCard    	= "creditCard";
	const OPTION_Modele_SocialSecurity	= "socialSecurity";
	const OPTION_Modele_BankDetails		= "bankDetails";
	const OPTION_Modele_Directory		= "directory";
	const OPTION_Modele_PostalCode		= "postalCode";
	const OPTION_Modele_City			= "city";
	const OPTION_Modele_InputMask		= "inputMask";
	const OPTION_Modele_WithSecond		= "withSecond";
	const OPTION_Modele_PositionVideo	= "videoPosition";
	const OPTION_Modele_IDColLinked		= "columnLinked";
	const OPTION_Transform_Color		= "colorRGB";

    const FUSIONTYPE_Aucun = 0;
    const FUSIONTYPE_Bouton = 1;
    const FUSIONTYPE_VilleCP = 2;
}
