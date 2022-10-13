<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 04/08/14
 * Time: 11:00
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Record;

use NOUT\Bundle\NOUTOnlineBundle\Entity\Langage;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS;

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
     * restriction sur la colonne
     * @var ColonneRestriction|null
     */
	protected $m_clRestriction;


	/**
	 * retourne le type de l'élément
	 * @param \SimpleXMLElement $clAttribNOUT
	 * @return string
	 */
	static function s_getTypeColonne(\SimpleXMLElement $clAttribNOUT): string
    {
		return (string) $clAttribNOUT[self::OPTION_TypeElement];
	}

    /**
     * StructureColonne constructor.
     * @param                   $sID
     * @param \SimpleXMLElement $clAttribNOUT
     * @param \SimpleXMLElement $clAttribXS
     */
	public function __construct($sID, \SimpleXMLElement $clAttribNOUT, \SimpleXMLElement $clAttribXS)
	{
		$this->m_nIDColonne   = $sID;
		$this->m_sLibelle     = '';
		$this->m_eTypeElement =  '';

		$this->m_TabOptions          = array();
		$this->m_clStructureElemLie  = null;
		$this->m_clRestriction       = null;

		$this->_InitInfoColonne($clAttribNOUT, $clAttribXS);
	}

    /**
     * @param \SimpleXMLElement $clAttribNOUT
     * @param \SimpleXMLElement $clAttribXS
     */
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
	public function setStructureElementLie(StructureElement $clStructElem): StructureColonne
    {
		$this->m_clStructureElemLie = $clStructElem;
		return $this;
	}

    /**
     * @return StructureElement|null
     */
    public function getStructureElementLie(): ?StructureElement
    {
        return $this->m_clStructureElemLie;
    }

	/**
     * @return array
     */
    public function getTabOptions(): array
    {
        return $this->m_TabOptions;
    }


    /**
     * pour savoir si fusion de colonne pour le multi-colonne
     * @param $isParamcard
     * @return integer
     */
    public function eGetFusionTypeMulticolonne($isParamcard): int
    {
		// Règles fusion fiche et règles paramètres sont différentes
		// à séparer avec if .. else

        // Si on est en mode filtres + liste
        if($isParamcard)
        {

            // Pour mettre ensemble toutes les dates
            if ($this->m_eTypeElement == self::TM_Date)
            {
                return self::FUSIONTYPE_Dates;
            }

            // Pour mettre ensemble le champ recherche et recherche globale
            if ($this->isOption(self::OPTION_Modele_Search) || ($this->m_nIDColonne == Langage::PA_Recherche_Global))
            {
                return self::FUSIONTYPE_Search;
            }
        }
        else
        {
            // Pour mettre ensemble tous les boutons
            if ($this->m_eTypeElement == self::TM_Bouton)
            {
                return self::FUSIONTYPE_Bouton;
            }

            // Pour mettre ensemble la ville et le code postal
            if ($this->isOption(self::OPTION_Modele_City) || $this->isOption(self::OPTION_Modele_PostalCode))
            {
                return self::FUSIONTYPE_VilleCP;
            }
        }

        return self::FUSIONTYPE_Aucun;
    }

    /**
     * pour savoir si cote a cote en multicolonne
     * @param $isParamcard
     * @return integer
     */
    public function eGetBuddyTypeMulticolonne($isParamcard): int
    {
        // Règles fusion fiche et règles paramètres sont différentes
		// à séparer avec if .. else

        // Si on est en mode filtres + liste
        if($isParamcard)
        {
            // Permet de faire le whole et la fusion avec le champ de recherche
            if (($this->m_nIDColonne == Langage::PA_Recherche_Global) || $this->isOption(self::OPTION_Modele_Search))
            {
                return self::BUDDYTYPE_Search;
            }

        }

        if ($this->isMultilineText() || ($this->m_eTypeElement == self::TM_ListeElem))
        {
            return self::BUDDYTYPE_Multi;
        }

        return self::BUDDYTYPE_Mono;
    }

    /**
     * pour savoir si prend toute la place quand tout seul
     * @param $isParamcard
     * @return integer
     */
    public function isWholeIfAlone($isParamcard)
    {
        // Règles fusion fiche et règles paramètres sont différentes
		// à séparer avec if .. else

        // Si on est en mode filtres + liste
        if($isParamcard)
        {
            if (($this->m_nIDColonne == Langage::PA_Recherche_Global) || $this->isOption(self::OPTION_Modele_Search))
            {
                return true;
            }
        }

        if ($this->isMultilineText() || ($this->m_eTypeElement == self::TM_ListeElem))
        {
            return true;
        }

        return false;
    }


	public function bEstTypeSimple(): bool
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

    public function bAvecValeur(): bool
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
            self::TM_HTML,
        );

        return in_array($this->m_eTypeElement, $aTypeSimple);
    }


	/**
	 * @return mixed
	 */
	public function getIDColonne(): string
    {
		return $this->m_nIDColonne;
	}

	/**
	 * @return string
	 */
	public function getLibelle(): string
    {
		return $this->m_sLibelle;
	}

	/**
	 * @param ColonneRestriction $clRestriction
	 * @return $this
	 */
	public function setRestriction(ColonneRestriction $clRestriction): StructureColonne
    {
		$this->m_clRestriction = $clRestriction;

		return $this;
	}

    /**
     * @return ColonneRestriction|null
     */
    public function clGetRestriction(): ?ColonneRestriction
    {
        return $this->m_clRestriction;
    }

    /**
     * @param $type
     * @return array
     */
    public function getRestriction($type) :array
    {
        if ($this->m_clRestriction){
            return $this->m_clRestriction->getRestriction($type);
        }
        return array();
    }

    /**
     * @param $type
     * @return array|string
     */
    public function getIconRestriction($type)
    {
        if ($this->m_clRestriction){
            return $this->m_clRestriction->getIconRestriction($type);
        }
        return array();
    }

	/**
	 * @return string
	 */
	public function getTypeElement(): string
    {
		return $this->m_eTypeElement;
	}

    /**
     * @return int
     */
	public function canGrow() : int
    {
        $canGrow = 0;
        if ($this->m_eTypeElement == self::TM_ListeElem){
            $canGrow |= 0x10;
        }
        if ($this->isMultilineText() && !$this->isOption(StructureColonne::OPTION_LineAff)){
            $canGrow |= 0x01;
        }
        return $canGrow;
    }

    /**
     * @return bool
     */
    public function needH() : bool
    {
        return $this->isMultilineText();
    }

    /**
     * @param $eTypeElement string
     * @return $this
     */
	public function setTypeElement(string $eTypeElement): StructureColonne
    {
		$this->m_eTypeElement = $eTypeElement;

		return $this;
	}


    /**
     * @param $sOption
     * @return bool
     */
	public function isOption($sOption): bool
    {
		if (!isset($this->m_TabOptions[$sOption]))
		{
			return false;
		}

        if (!empty($this->m_TabOptions[$sOption]))
        {
            $val = $this->m_TabOptions[$sOption];
            if (is_numeric($val)){
                return ($val+0) != 0;
            }

            if (is_bool($val)){
                return $val;
            }

            return true;
        }

        if (    ($sOption==self::OPTION_ReadOnly)
            &&  isset($this->m_TabOptions[self::OPTION_Modele_Directory])
            && !empty($this->m_TabOptions[self::OPTION_Modele_Directory]))
        {
            return true; //dans le cas d'un modèle directory, on force en readonly
        }

        return false;
	}

	/**
	 * @param $sOption
     * @param $default
	 * @return string|null
	 */
	public function getOption($sOption, $default=null): ?string
    {
		if (!isset($this->m_TabOptions[$sOption]))
		{
			return $default;
		}

		return $this->m_TabOptions[$sOption];
	}

	public function getDisplayMode() : array
    {
        $sString = $this->m_TabOptions[self::OPTION_DisplayMode] ?? XMLResponseWS::DISPLAYMODE_List;
        return explode('|', $sString);
    }

    /**
     * vrai si le champ est un texte multiligne
     * @return bool
     */
    public function isMultilineText(): bool
    {
        if (!$this->_isText()) {
            return false;
        }

        if ($this->m_eTypeElement == self::TM_HTML){
            return true;
        }

        if (!is_null($this->m_clRestriction) && $this->m_clRestriction->hasTypeRestriction(ColonneRestriction::R_MAXLENGTH)){
            //texte avec restriction => n'est PAS texte multiligne
            return false;
        }

        return !$this->_isLongTextMonoline();
    }

    /**
     * @return array|mixed|string|null
     */
    public function getMaxLength()
    {
        if (!$this->_isText()) {
            return null;
        }

        if (!is_null($this->m_clRestriction) && $this->m_clRestriction->hasTypeRestriction(ColonneRestriction::R_MAXLENGTH)){
            //texte avec restriction => n'est PAS texte multiligne
            return $this->m_clRestriction->getRestriction(ColonneRestriction::R_MAXLENGTH);
        }

        return null;
    }


    /**
     * vrai si le champ est un texte monoligne
     * @return bool
     */
    public function isMonolineText(): bool
    {
        if (!$this->_isText() || ($this->m_eTypeElement == self::TM_HTML)){
            return false;
        }

        if (   !is_null($this->m_clRestriction) && $this->m_clRestriction->hasTypeRestriction(ColonneRestriction::R_MAXLENGTH)) {
            //texte avec restriction => texte monoligne
            return true;
        }

        return $this->_isLongTextMonoline();
    }

    /**
     * vrai si c'est un texte qui est monoligne, même si pas restriction sur la longueur du champ
     * @return bool
     */
    protected function _isLongTextMonoline(): bool
    {

        // Texte multi-ligne (car pas de restriction de nombre de caractères)
        //certain modèle sont transformé en monoligne :
        switch($this->getOption(self::OPTION_Transform))
        {
            case self::OPTION_Transform_Url:
            {
                return true;
            }
        }

        if ($this->isOption(self::OPTION_Modele_Directory)) {
            return true;
        }

        return false;
    }

    protected function _isText(): bool
    {
        return ($this->m_eTypeElement == self::TM_Texte) || ($this->m_eTypeElement == self::TM_HTML);
    }

	//////////////////////////////////////////
	// POUR LE MOTEUR DE FORMULAIRE PAR DEFAUT
	//////////////////////////////////////////

    /**
     * @return string
     *
     * Retourne une liste de classe en fonction des options du modèle
     */
    public function getFormClass(): string
    {
        if ($this->isOption(self::OPTION_Modele_PostalCode)){
            return 'codepostal ';
        }

        return '';
    }


	const TM_Invalide = null;

	//type simple
	const TM_Booleen   	= 'xs:boolean';
	const TM_Entier    	= 'xs:integer';
	const TM_Texte     	= 'xs:string';
	const TM_DateHeure 	= 'xs:dateTime';
	const TM_Date      	= 'xs:date';
	const TM_Heure     	= 'xs:time';
	const TM_Reel      	= 'xs:float';
	const TM_Monetaire 	= 'xs:decimal';
    const TM_Duree 		= 'simax-duration';
    //n'existe dans le xsd, pour cohérence de code
    const TM_TexteMultiLigne = 'xs:multilinestring';

	//type complexe
    const TM_Fichier    = 'xs:base64Binary';
	const TM_Tableau    = 'simax-element';
	const TM_ListeElem  = 'simax-list';
	const TM_Separateur = 'simax-section';
	const TM_Bouton     = 'simax-button';
	const TM_Combo      = 'simax-choice';
    const TM_HTML       = 'simax-html';
    const TM_CalculAuto = 'simax-autoComputed';


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
    const OPTION_DisplayMode = 'displayMode';
    const OPTION_LevelCol    = 'levelCol';

    const OPTION_Hidden = "hidden"; // Namespace déjà géré
    const OPTION_ContainerCol = "containerCol"; // Namespace déjà géré
    const OPTION_ReadOnly = "readOnly";
    const OPTION_Disabled = "disabled";

	const OPTION_Required = "required";
	const OPTION_Transform = "transform";
	const OPTION_Crypted = "crypted";
    const OPTION_Help = "help";

    // Attributs pour element d'un tableau et sous-liste
    const OPTION_LinkedTableXml = "linkedTableXml";
    const OPTION_LinkedTableID  = "linkedTableID";
	// Attributs pour element d'un tableau
    const OPTION_NoGroupList    = "notGroupList";
	const OPTION_WithBtnOrdre   = "withBtnOrder";
	const OPTION_WithoutDetail  = "withoutDetail";
    const OPTION_WithoutEdit    = "withoutEdit";
	const OPTION_WithoutSearch  = "withoutSearch";
	const OPTION_WithoutCreate  = "withoutCreate";
	const OPTION_Resource       = "resource";
	const OPTION_MultiResource  = "resourceMulti";

	// Attributs pour les sous-listes
	const OPTION_Relation      = "withAddAndRemove";    // bestGroupeRelation
	const OPTION_Relation11    = "withModifyAndRemove"; // bEstRelation11
	const OPTION_UniqueElement = "uniqueElement";
	const OPTION_SelectLink    = "selectLink";

	// Attributs pour les textes
	const OPTION_TextBoxSize   = "textBoxSize";
	const OPTION_LineAff       = "lineAff";

	// Attributs pour les listes en général
	const OPTION_WithPlanning = "withPlanning";
	const OPTION_WithGhost    = "withGhost";
	const OPTION_TableType    = "tableType";

	const OPTION_TableType_ListTable  = "list";
	const OPTION_TableType_PivotTable = "pivotTable";
	const OPTION_TableType_ViewTable  = "view";

	// Attributs pour les boutons
	const OPTION_IDAction           = "idAction";
	const OPTION_IDBouton           = "idButton";
	const OPTION_Sentence           = "sentence";
	const OPTION_TypeAction         = "actionType";
	const OPTION_IDTypeAction       = "actionTypeID";
	const OPTION_TypeSelection      = "typeSelection";
	const OPTION_Icone              = "icon";
	const OPTION_WithValidation     = "withValidation";
    const OPTION_Substitution       = "substitution";
	const OPTION_IDColToUpdate      = "columnToUpdate";
	const OPTION_IDColSelection     = "columnSelection";
	const OPTION_ColumnAssignation  = "columnAssignation";
	const OPTION_DisplayOnLine      = "displayOnLine";

	// Attributs des separateurs
	const OPTION_ModeMultiC      = "multiColumnMode";
	const OPTION_SensMultiC      = "multiColumnWay";
	const OPTION_SideBySide      = "sideBySide";
	const OPTION_Width           = "width";
	const OPTION_SectionComputed = "sectionComputed";
	const OPTION_SectionLevel    = "sectionLevel";
	const OPTION_BackgroundColor = "backgroundColor";

	// Attributs pour liste deroulante
	const OPTION_AttributID = "id";

	// Attributs pour les fichiers
	const OPTION_MimeType				= "typeMime";
	const OPTION_Editable				= "editable";
	const OPTION_CanvasWidth            = "canvasWidth";
	const OPTION_CanvasHeight           = "canvasHeight";
	const OPTION_WithWatermark          = "watermark";
	const OPTION_WatermarkText          = "watermarkText";
	const OPTION_WatermarkColor         = "watermarkColor";
	const OPTION_WatermarkAngle         = "watermarkAngle";


	static public function s_GetModeleOption(): array
    {
		return array(
            self::OPTION_Modele_Barcode         ,
            self::OPTION_Modele_CreditCard      ,
            self::OPTION_Modele_PhoneNumber     ,
            self::OPTION_Modele_IpAddress       ,
			self::OPTION_Modele_SocialSecurity	,
			self::OPTION_Modele_BankDetails		,
			self::OPTION_Modele_Directory		,
			self::OPTION_Modele_PostalCode		,
			self::OPTION_Modele_City			,
			self::OPTION_Modele_InputMask		,
			self::OPTION_Modele_WithSecond		,
			self::OPTION_Modele_PositionVideo	,
			self::OPTION_Modele_IDColLinked		,
			self::OPTION_Modele_Company			,
			self::OPTION_Modele_Latitude		,
			self::OPTION_Modele_Longitude		,
			self::OPTION_Modele_Search  		,
		);
	}

	// Attributs liés au modele
    const OPTION_Modele_Barcode         = "barCode";
    const OPTION_Modele_PhoneNumber		= "phoneNumber";
    const OPTION_Modele_IpAddress		= "ipAddress";
	const OPTION_Modele_CreditCard    	= "creditCard";
	const OPTION_Modele_SocialSecurity	= "socialSecurity";
	const OPTION_Modele_BankDetails		= "bankDetails";
	const OPTION_Modele_Directory		= "directory";
	const OPTION_Modele_PostalCode		= "postalCode";
	const OPTION_Modele_City			= "City";
	const OPTION_Modele_InputMask		= "inputMask";
	const OPTION_Modele_WithSecond		= "withSecond";
	const OPTION_Modele_PositionVideo	= "videoPosition";
	const OPTION_Modele_IDColLinked		= "columnLinked";
	const OPTION_Modele_Company			= "siret";
	const OPTION_Modele_Latitude		= "latitude";
	const OPTION_Modele_Longitude		= "longitude";
    const OPTION_Modele_Search  		= "search";
    const OPTION_Modele_ComboBox  		= "comboBox";
    const OPTION_Modele_SyntaxColor		= "syntaxColor";
    const OPTION_Modele_Formula 		= "formula";
    const OPTION_Modele_LineNumber 		= "lineNumber";
    const OPTION_Modele_Multilanguage	= "multiLanguage";
	//Si ajout au dessus, rajouter dans la méthode s_GetModeleOption

	// Attributs de transformation
	const OPTION_Transform_Color			= "colorRGB";
	const OPTION_Transform_Uppercase		= "uppercase";
	const OPTION_Transform_Lowercase		= "lowercase";
	const OPTION_Transform_FirstUppercase	= "firstUppercase";
	const OPTION_Transform_Url				= "url";
	const OPTION_Transform_Video			= "video";
	const OPTION_Transform_Secret			= "secret";
	const OPTION_Transform_Email			= "email";


    // Constantes pour les fusions
    const FUSIONTYPE_Aucun          = 0;
    const FUSIONTYPE_Bouton         = 1;
    const FUSIONTYPE_VilleCP        = 2;
    const FUSIONTYPE_Dates          = 3;  // Par exemple pour date début - date fin
    const FUSIONTYPE_Search         = 11; // Pour pouvoir faire la fusion du champ recherche avec GlobalSearch

    const BUDDYTYPE_Mono            = 0;
    const BUDDYTYPE_Multi           = 1;
    const BUDDYTYPE_Search          = 2;

    //Constantes pour la validation des boutons
    const BTNVAL_Avant              = 1; //on enregistre avant
    const BTNVAL_Apres              = 2; //on enregistre après
    const BTNVAL_Question           = 3; //on pose la question à l'utilisateur
    const BTNSUB_Annuler            = 1; //remplace le bouton annuler
    const BTNSUB_Enregistrer        = 2; //remplace le bouton enregistrer
    const BTNSUB_Imprimer           = 3; //remplace le bouton imprimer

}
