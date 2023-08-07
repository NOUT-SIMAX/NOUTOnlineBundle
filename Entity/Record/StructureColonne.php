<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 04/08/14
 * Time: 11:00
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Record;

use NOUT\Bundle\NOUTOnlineBundle\Entity\Langage\Langage;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Langage\LangageParametre;
use NOUT\Bundle\NOUTOnlineBundle\Entity\NOUTOnlineVersion;
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
    protected $m_aTabOptions;

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

    /** @var bool */
    protected $m_bFixed = false;

    /** @var string  */
    protected $m_sDefaultVal = '';

    /**
     * retourne le type de l'élément
     * @param \SimpleXMLElement $clAttribNOUT
     * @return string
     */
    public static function s_getTypeColonne(\SimpleXMLElement $clAttribNOUT): string
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

        $this->m_aTabOptions          = array();
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
        $this->m_bFixed       = (isset($clAttribXS['fixed']) && ((string) $clAttribXS['fixed'] == 'true')); //xs:fixed="true"
        $this->m_sDefaultVal  = isset($clAttribXS['default']) ? (string) $clAttribXS['default']  : '';

        foreach ($clAttribNOUT as $sAttribName => $ndAttrib)
        {
            $this->m_aTabOptions[$sAttribName] = (string) $ndAttrib;
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
        return $this->m_aTabOptions;
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
            if ($this->isOption(self::OPTION_Modele_Search) || ($this->m_nIDColonne == LangageParametre::RechercheGlobal))
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
     * @param NOUTOnlineVersion|null $NOUTOnlineVersion
     * @return integer
     */
    public function eGetBuddyTypeMulticolonne($isParamcard, ?NOUTOnlineVersion $NOUTOnlineVersion=null): int
    {
        // Règles fusion fiche et règles paramètres sont différentes
        // à séparer avec if .. else

        // Si on est en mode filtres + liste
        if($isParamcard)
        {
            // Permet de faire le whole et la fusion avec le champ de recherche
            if (($this->m_nIDColonne == LangageParametre::RechercheGlobal) || $this->isOption(self::OPTION_Modele_Search))
            {
                return self::BUDDYTYPE_Search;
            }

        }

        if ($this->isMultilineText($NOUTOnlineVersion) || ($this->m_eTypeElement == self::TM_ListeElem))
        {
            return self::BUDDYTYPE_Multi;
        }

        return self::BUDDYTYPE_Mono;
    }

    /**
     * pour savoir si prend toute la place quand tout seul
     * @param $isParamcard
     * @param NOUTOnlineVersion|null $NOUTOnlineVersion
     * @return integer
     */
    public function isWholeIfAlone($isParamcard, ?NOUTOnlineVersion $NOUTOnlineVersion=null)
    {
        // Règles fusion fiche et règles paramètres sont différentes
        // à séparer avec if .. else

        // Si on est en mode filtres + liste
        if($isParamcard)
        {
            if (($this->m_nIDColonne == LangageParametre::RechercheGlobal) || $this->isOption(self::OPTION_Modele_Search))
            {
                return true;
            }
        }

        if ($this->isMultilineText($NOUTOnlineVersion) || ($this->m_eTypeElement == self::TM_ListeElem))
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
     * @param NOUTOnlineVersion|null $NOUTOnlineVersion
     * @return int
     */
    public function canGrow(?NOUTOnlineVersion $NOUTOnlineVersion=null) : int
    {
        $canGrow = 0;
        if ($this->m_eTypeElement == self::TM_ListeElem){
            $canGrow |= 0x10;
        }
        if ($this->isMultilineText($NOUTOnlineVersion) && !$this->isOption(StructureColonne::OPTION_LineAff)){
            $canGrow |= 0x01;
        }
        return $canGrow;
    }

    /**
     * @param NOUTOnlineVersion|null $NOUTOnlineVersion
     * @return bool
     */
    public function needH(?NOUTOnlineVersion $NOUTOnlineVersion=null) : bool
    {
        return $this->isMultilineText($NOUTOnlineVersion);
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
        //quelque cas particulier ou on force le retour
        if ($sOption == self::OPTION_Modele_ComboBox)
        {
            $isPredefRequest = $this->m_aTabOptions[self::OPTION_PredefinedRequest] ?? false;
            if ($isPredefRequest)
            {
                return true;
            }
        }

        if (!isset($this->m_aTabOptions[$sOption]))
        {
            return false;
        }

        if (!empty($this->m_aTabOptions[$sOption]))
        {
            $val = $this->m_aTabOptions[$sOption];
            if (is_numeric($val)){
                return ($val+0) != 0;
            }

            if (is_bool($val)){
                return $val;
            }

            return true;
        }

        return false;
    }


    /**
     * @return int
     */
    public function getState() : int
    {
        return self::s_nGetState(
            $this->isOption(StructureColonne::OPTION_Hidden),
            $this->isOption(StructureColonne::OPTION_Disabled),
            $this->isOption(StructureColonne::OPTION_ReadOnly),
            $this->isOption(StructureColonne::OPTION_ReadWithoutModify)
        );
    }

    /**
     * @param bool $bHidden
     * @param bool $bDisabled
     * @param bool $bReadOnly
     * @param bool $bReadWithoutModify
     * @return int
     */
    static public function s_nGetState(bool $bHidden, bool $bDisabled, bool $bReadOnly, bool $bReadWithoutModify) : int
    {
        if ($bHidden){
            return Langage::eSTATE_Invisible;
        }

        if ($bDisabled)
        {
            return Langage::eSTATE_Grise;
        }

        if ($bReadOnly)
        {
            return $bReadWithoutModify ? Langage::eSTATE_LectureSeuleSansModifie : Langage::eSTATE_LectureSeuleAvecModifie;
        }
        return Langage::eSTATE_Editable;
    }



    /**
     * @param $sOption
     * @param $default
     * @return string|null
     */
    public function getOption($sOption, $default=null): ?string
    {
        if (!isset($this->m_aTabOptions[$sOption]))
        {
            return $default;
        }

        return $this->m_aTabOptions[$sOption];
    }

    public function getDisplayMode() : array
    {
        $sString = $this->m_aTabOptions[self::OPTION_DisplayMode] ?? XMLResponseWS::DISPLAYMODE_List;
        return explode('|', $sString);
    }

    /**
     * vrai si le champ est un texte multiligne
     * @return bool
     */
    public function isMultilineText(?NOUTOnlineVersion $NOUTOnlineVersion=null): bool
    {
        if (!$this->_isText()) {
            return false;
        }

        if ($this->m_eTypeElement == self::TM_HTML){
            return true;
        }

        $restriction = !is_null($NOUTOnlineVersion) && $NOUTOnlineVersion->isVersionSup($NOUTOnlineVersion::SUPPORT_RESTRICTION_WHITESPACE)
            ? ColonneRestriction::R_WHITESPACE
            : ColonneRestriction::R_MAXLENGTH
        ;

        if (!is_null($this->m_clRestriction) && $this->m_clRestriction->hasTypeRestriction($restriction)){
            //texte avec restriction => n'est PAS texte multiligne
            return false;
        }

        return !$this->_isLongTextMonoline();
    }

    /**
     * vrai si le champ est un texte monoligne
     * @return bool
     */
    public function isMonolineText(?NOUTOnlineVersion $NOUTOnlineVersion=null): bool
    {
        if (!$this->_isText() || ($this->m_eTypeElement == self::TM_HTML)){
            return false;
        }

        $restriction = !is_null($NOUTOnlineVersion) && $NOUTOnlineVersion->isVersionSup($NOUTOnlineVersion::SUPPORT_RESTRICTION_WHITESPACE)
            ? ColonneRestriction::R_WHITESPACE
            : ColonneRestriction::R_MAXLENGTH
        ;

        if (   !is_null($this->m_clRestriction)
            && $this->m_clRestriction->hasTypeRestriction($restriction)) {
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


    /**
     * @return array|mixed|string|null
     */
    public function getMaxLength()
    {
        if (!$this->_isText()) {
            return null;
        }

        if (!is_null($this->m_clRestriction) ){
            $maxLength = $this->m_clRestriction->hasTypeRestriction(ColonneRestriction::R_MAXLENGTH)
                ? (int)$this->m_clRestriction->getRestriction(ColonneRestriction::R_MAXLENGTH)
                : 0;
            $length = $this->m_clRestriction->hasTypeRestriction(ColonneRestriction::R_LENGTH)
                ? (int)$this->m_clRestriction->getRestriction(ColonneRestriction::R_LENGTH)
                : 0;

            return max($maxLength, $length);
        }

        return null;
    }


    protected function _isText(): bool
    {
        return ($this->m_eTypeElement == self::TM_Texte) || ($this->m_eTypeElement == self::TM_HTML);
    }

    /**
     * @return bool
     */
    public function isFixed(): bool
    {
        return $this->m_bFixed;
    }

    /**
     * @return string
     */
    public function getDefaultVal(): string
    {
        return $this->m_sDefaultVal;
    }

    const TM_Invalide = null;

    //type simple
    const TM_Booleen       = 'xs:boolean';
    const TM_Entier        = 'xs:integer';
    const TM_Texte         = 'xs:string';
    const TM_DateHeure     = 'xs:dateTime';
    const TM_Date          = 'xs:date';
    const TM_Heure         = 'xs:time';
    const TM_Reel          = 'xs:float';
    const TM_Monetaire     = 'xs:decimal';
    const TM_Duree         = 'simax-duration';
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
    const TM_TextImage  = 'simax-text-image';


    const OPTION_Name        = 'name';
    const OPTION_TypeElement = 'typeElement';

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

    const OPTION_Hidden            = "hidden"; // Namespace déjà géré
    const OPTION_ContainerCol      = "containerCol"; // Namespace déjà géré
    const OPTION_ReadOnly          = "readOnly";
    const OPTION_Disabled          = "disabled";
    const OPTION_ReadWithoutModify = "readWithoutModify";

    const OPTION_ConfigurationFlags = "configurationFlags";
    const OPTION_RealFormID         = "realFormID";
    const OPTION_RealFormName       = "realFormName";

    const OPTION_Required  = "required";
    const OPTION_Transform = "transform";
    const OPTION_Crypted   = "crypted";
    const OPTION_Help      = "help";
    const OPTION_Type      = "type";

    // Attributs pour element d'un tableau et sous-liste
    const OPTION_LinkedTableXml         = "linkedTableXml";
    const OPTION_LinkedTableID          = "linkedTableID";
    const OPTION_TableInfoConfiguration = "tableInfoConfiguration";
    // Attributs pour element d'un tableau
    const OPTION_NoGroupList        = "notGroupList";
    const OPTION_WithBtnOrdre       = "withBtnOrder";
    const OPTION_WithoutDetail      = "withoutDetail";
    const OPTION_WithoutEdit        = "withoutEdit";
    const OPTION_WithoutSearch      = "withoutSearch";
    const OPTION_WithoutCreate      = "withoutCreate";
    const OPTION_Resource           = "resource";
    const OPTION_MultiResource      = "resourceMulti";
    const OPTION_PredefinedRequest  = 'predefinedRequest';

    const OPTION_WithoutDownload    = "withoutDownload";

    // Attributs pour les sous-listes
    const OPTION_Relation      = "withAddAndRemove";    // bestGroupeRelation
    const OPTION_Relation11    = "withModifyAndRemove"; // bEstRelation11
    const OPTION_UniqueElement = "uniqueElement";
    const OPTION_SelectLink    = "selectLink";

    // Attributs pour les textes
    const OPTION_TextBoxSize   = "textBoxSize";
    const OPTION_LineAff       = "lineAff";
    const OPTION_LineAffMax    = "lineAffMax";

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
    const OPTION_ListMode           = 'listMode';
    const OPTION_IDButtonAction     = 'idButtonAction';
    const OPTION_StateMin           = 'stateMin';

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
    const OPTION_MimeType               = "typeMime";
    const OPTION_Editable               = "editable";
    const OPTION_CanvasWidth            = "canvasWidth";
    const OPTION_CanvasHeight           = "canvasHeight";
    const OPTION_WithWatermark          = "watermark";
    const OPTION_WatermarkText          = "watermarkText";
    const OPTION_WatermarkColor         = "watermarkColor";
    const OPTION_WatermarkAngle         = "watermarkAngle";


    public static function s_GetModeleOption(): array
    {
        return array(
            self::OPTION_Modele_Barcode,
            self::OPTION_Modele_CreditCard,
            self::OPTION_Modele_PhoneNumber,
            self::OPTION_Modele_IpAddress,
            self::OPTION_Modele_SocialSecurity,
            self::OPTION_Modele_BankDetails,
            self::OPTION_Modele_Directory,
            self::OPTION_Modele_PostalCode,
            self::OPTION_Modele_City,
            self::OPTION_Modele_InputMask,
            self::OPTION_Modele_WithSecond,
            self::OPTION_Modele_PositionVideo,
            self::OPTION_Modele_IDColLinked,
            self::OPTION_Modele_Company,
            self::OPTION_Modele_Latitude,
            self::OPTION_Modele_Longitude,
            self::OPTION_Modele_Search,
        );
    }

    // Attributs liés au modele
    const OPTION_Modele_Barcode          = "barCode";
    const OPTION_Modele_PhoneNumber      = "phoneNumber";
    const OPTION_Modele_IpAddress        = "ipAddress";
    const OPTION_Modele_CreditCard       = "creditCard";
    const OPTION_Modele_SocialSecurity   = "socialSecurity";
    const OPTION_Modele_BankDetails      = "bankDetails";
    const OPTION_Modele_Directory        = "directory";
    const OPTION_Modele_PostalCode       = "postalCode";
    const OPTION_Modele_City             = "City";
    const OPTION_Modele_InputMask        = "inputMask";
    const OPTION_Modele_WithSecond       = "withSecond";
    const OPTION_Modele_PositionVideo    = "videoPosition";
    const OPTION_Modele_IDColLinked      = "columnLinked";
    const OPTION_Modele_Company          = "siret";
    const OPTION_Modele_Latitude         = "latitude";
    const OPTION_Modele_Longitude        = "longitude";
    const OPTION_Modele_Search           = "search";
    const OPTION_Modele_ComboBox         = "comboBox";
    const OPTION_Modele_SyntaxColor      = "syntaxColor";
    const OPTION_Modele_Formula          = "formula";
    const OPTION_Modele_LineNumber       = "lineNumber";
    const OPTION_Modele_Multilanguage    = "multiLanguage";
    //Si ajout au dessus, rajouter dans la méthode s_GetModeleOption

    // Attributs de transformation
    const OPTION_Transform_Color            = "colorRGB";
    const OPTION_Transform_Uppercase        = "uppercase";
    const OPTION_Transform_Lowercase        = "lowercase";
    const OPTION_Transform_FirstUppercase   = "firstUppercase";
    const OPTION_Transform_Url              = "url";
    const OPTION_Transform_Video            = "video";
    const OPTION_Transform_Secret           = "secret";
    const OPTION_Transform_Email            = "email";


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
    const BTNSUB_Annuler            = 1; //remplace le bouton 'annuler'
    const BTNSUB_Enregistrer        = 2; //remplace le bouton 'enregistrer'
    const BTNSUB_Imprimer           = 3; //remplace le bouton 'imprimer'

    //---------------------------------------------------
    //les flags pour la configuration
    const FC_Visible            = 0x0000000000000002;        // Visible par parametrage
    const FC_AImprimer          = 0x0000000000000004;        // A imprimer
    const FC_Obligatoire        = 0x0000000000000008;        // Valeur obligatoire
    const FC_Unique             = 0x0000000000000010;        // Valeur unique
    const FC_VisibleUtilisateur = 0x0000000000000020;        // Visible demande par l'utilisateur
    const FC_TriableDS          = 0x0000000000000040;        // le DS peut trier
    const FC_TriableRequete     = 0x0000000400000000;        // le DS peut trier
    const FC_CommenceParDS      = 0x0000000000000080;        // le DS peut faire un 'commence par'
    const FC_Intitule           = 0x0000000000001000;        // repris dans l'intitulé
    const OBS_FC_Vignette       = 0x0000000000000800;        // la valeur de la colonne est affichée en mode vignette
    const FC_FicheInvisible     = 0x0000000002000000;        // invisible en fiche
    const FC_DroitInvisible     = 0x0000002000000000;        // droit Invisible sur la colonne ça permet de filtrer par exemple pour les listes images
    const FC_LectureSeule       = 0x0000010000000000;        // en lecture seulement (par droit; ctrl etat; ...)

    const FC_ListeInvisible        = 0x0000000000000001;        // Detail (pas en liste)
    const FC_ListeImportant        = 0x0000000100000000;        // Important (non detail; affichage non obligatoire)
    const FC_ListeALaDemande       = 0x0000000800000000;        // A la demande
    const FC_ListeBoutonSurLaLigne = 0x0000008000000000;        // pour les boutons en ligne
    //const FC_FlagListeParSeparateur = 0x0000001000000000;        // le FC_ListeXxxxx a ete herite par le separateur
    const FC_MaskFlagListe         = 0x0000008900000001;                // FC_ListeXxxxxx

    const FC_AvecCtrlEtat      = 0x0000100000000000;
    const FC_AvecCtrlEtatListe = 0x0000200000000000;        // se combine avec FC_AvecCtrlEtat si actif en liste

    const FC_MaskFlagAPlatFiche = 0x0000302D020018FF;
    const FC_MaskFlagAPlatTable = 0x0000302902000027;        // flag repercute depuis un separateur pour recup a Plat

    const FC_ListeOrdonne       = 0x0000000000100000;        // la liste est ordonnee (soit par colonne ordre soit par groupe)
    const FC_ListeNonGroupe     = 0x0000000000002000;
    const FC_ListeGroupe        = 0x0000000000004000;
    const FC_Relation11         = 0x0000000000008000;
    const FC_Relation01         = 0x0000000000800000;
    const FC_AEvaluer           = 0x0000000000200000;        // la valeur est a evaluer (formule; ...) = ILangageModele::OPT_AEvaluer
    const FC_InitUniqParFormule = 0x0000000004000000;
    const FC_CalculModifiable   = 0x0000000000080000;        // le calcul est modifiable (saisie directe dans le tableau croise)
    const FC_ModifDirectListe   = 0x0000004000000000;        // autorise la modification directe en mode liste
    const FC_Arborescence       = 0x0000400000000000;        // c'est l'arborescence

    const FC_CalculAuto         = 0x0000000000000100;        // c'est un calcul ajoute par le DS
    const FC_Bouton             = 0x0000000000000200;        // c'est un bouton
    const FC_Separateur         = 0x0000000000000400;        // c'est un separateur
    const FC_Donnee             = 0x0000000000010000;        // c'est une donnee
    const FC_CalculStocke       = 0x0000000000020000;        // c'est un calcul stocke (non recalcule)
    const FC_CalculRecalcule    = 0x0000000000040000;        // c'est un calcul recalcule
    const FC_TexteImage         = 0x0000020000000000;        // c'est une colonne texte ou image
    const FC_TypeColonneInconnu = 0x0000040000000000;        // c'est un type inconnu à ignorer
    const FC_MaskType           = 0x0000060000070700;
    const FC_MaskNonLangage     = 0x0000060000000700;
    const FC_MaskSansValeur     = 0x0000040000000600;

    const FC_DonneeEnLectureIHM      = 0x0000000200000000;        // colonne donnee en lecture seule par modèle; peut etre doublon avec FC_LectureSeule
    const FC_DonneeEcritureAnticipee = 0x0000001000000000;

    const FC_Virtuel       = 0x0000000000400000;        // ajout interne par le DS
    const FC_IDTemporaire  = 0x0000000001000000;        // l'id de cette colonne est regenere
    const FC_ToujoursDsXML = 0x0000000008000000;        // il faut toujours le mettre dans XML

    const FC_Separateur_MaskNiveau = 0x0000000070000000;

    const FC_SC_DansFactory = 0x0000000080000000;        // interne pour FactoryStructureColonne
    
    
}
