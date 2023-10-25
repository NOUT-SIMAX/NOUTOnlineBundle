<?php
/**
 * Created by PhpStorm
 * User: ninon
 * Date: 24/10/2023 15:24
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService;

use NOUT\Bundle\NOUTOnlineBundle\Entity\NOUTFileInfo;

interface IResponseWS
{
    /**
     * retourne vrai si le retour est une erreur
     */
    public function bIsFault(): bool;
    /**
     * @return mixed, false si pas une erreur, un tableau d'erreur SIMAX si c'est une erreur
     */
    public function getTabError(): ?array;
    /**
     * @return int
     */
    public function getNumError(): int;

    /**
     * @return int
     */
    public function getCatError(): int;
    /**
     * @return string
     */
    public function getMessError(): string;

    /**
     * renvoi le type de retour
     * @return string
     */
    public function sGetReturnType(): string;

    /**
     * renvoi l'identifiant du contexte d'action
     * @return string
     */
    public function sGetActionContext(): string;
    /**
     * renvoi l'identifiant du contexte d'action
     * @return string
     */
    //public function sGetContextToValidateOnClose(): string;

    /**
     * renvoi les id des contextes qu'on doit fermer
     * @return array
     */
    public function aGetActionContextToClose(): array;


    /**
     * @return MessageBox
     */
    //public function clGetMessageBox(): MessageBox;

    /**
     * @return string
     */
    //public function getData(): string;

    /**
     * @return NOUTFileInfo
     */
    //public function getFile(): ?NOUTFileInfo;

    /**
     * @return CurrentAction : action en cours
     */
    public function clGetAction(): CurrentAction;

    /**
     * @return CurrentRequest : action en cours
     */
   // public function clGetRequest(): ?CurrentRequest;


    /**
     * @return String title of the document
     */
    //public function clGetTitle(): string;

    /**
     * @return String
     */
    //public function sGetIDIHM(): ?string;


    /**
     * @return String[]
     */
    //public function aGetIDIHMToClose(): ?array;

    /**
     * utilisateur actuellement connecté
     * @return ConnectedUser
     */
    //public function clGetConnectedUser(): ConnectedUser;
    /**
     * @return Form
     */
    //public function clGetForm(): Form;

    /**
     * @return Element|null
     */
    //public function clGetElement(): ?Element;

    /**
     * @return Count
     */
    //public function clGetCount(): Count;

    /**
     * @return FolderCount
     */
    //public function clGetFolderCount(): FolderCount;

    /**
     * @return null|array
     */
    //public function GetTabPossibleDisplayMode(): ?array;

    /**
     * @return string|null
     */
    //public function sGetDefaultGraphType() : ?string;

    /**
     * @return null|string
     */
    //public function sGetDefaultDisplayMode(): ?string;


    /**
     * @return string|null
     */
    //public function getValue(): ?string;


    /**
     * @return ValidateError|null
     */
    //public function getValidateError(): ?ValidateError;


    /**
     * récupère le token session dans la réponse XML
     * @return string
     */
    //public function sGetTokenSession(): string;

    /**
     * @return int
     */
    //public function nGetSessionLanguageCode():int;

    /**
     * @return int
     */
    //public function nGetNumberOfChart(): int;


    /**
     * @return string
     */
    public function sGetReport(): string;


    /**
     * returne le tableau des codes langues disponibles
     * @return array
     */
    //public function GetTabLanguages(): array;


    //réponse générique
    const RETURNTYPE_ERROR          = 'Error';
    const RETURNTYPE_EMPTY          = 'Empty';
    const RETURNTYPE_DONOTHING      = 'DoNothing';
    const RETURNTYPE_REPORT         = 'Report';
    const RETURNTYPE_VALUE          = 'Value';
    const RETURNTYPE_REQUESTFILTER  = 'RequestFilter';
    const RETURNTYPE_CHART          = 'Chart';
    const RETURNTYPE_NUMBEROFCHART  = 'NumberOfChart';

    //retourne des enregistrements
    const RETURNTYPE_RECORD         = 'Record';
    const RETURNTYPE_LIST           = 'List';
    const RETURNTYPE_THUMBNAIL      = 'Thumbnail';
    const RETURNTYPE_DATATREE       = 'Datatree';

    //réponse particulière
    const RETURNTYPE_XSD                = 'XSD';
    const RETURNTYPE_IDENTIFICATION     = 'Identification';
    const RETURNTYPE_PLANNING           = 'Planning'; // Vieux planning
    const RETURNTYPE_SCHEDULER          = 'Scheduler'; // Nouveau planning
    const RETURNTYPE_GLOBALSEARCH       = 'GlobalSearch';
    const RETURNTYPE_LISTCALCULATION    = 'ListCalculation';
    const RETURNTYPE_EXCEPTION          = 'Exception';

    //réponse intermédiaire
    const RETURNTYPE_AMBIGUOUSCREATION  = 'AmbiguousAction';
    const RETURNTYPE_MESSAGEBOX         = 'MessageBox';
    const RETURNTYPE_VALIDATEACTION     = 'ValidateAction';
    const RETURNTYPE_VALIDATERECORD     = 'ValidateEnreg';
    const RETURNTYPE_PRINTTEMPLATE      = 'PrintTemplate';
    const RETURNTYPE_CHOICE             = 'Choice';

    //réponse de messagerie
    const RETURNTYPE_MAILSERVICERECORD      = 'MailServiceRecord';
    const RETURNTYPE_MAILSERVICELIST        = 'MailServiceList';
    const RETURNTYPE_MAILSERVICESTATUS      = 'MailServiceStatus';
    const RETURNTYPE_WITHAUTOMATICRESPONSE  = 'WithAutomaticResponse';
    const RETURNTYPE_MAILSERVICEIDLIST      = 'MailServiceIDList';

    //types virtuels pour traitement spéciaux
    const VIRTUALRETURNTYPE_AFFICHEMESSAGE = 'MessageDisplay';
    const VIRTUALRETURNTYPE_FILE = "File";
    const VIRTUALRETURNTYPE_FILE_PREVIEW = "FilePreview";
    const VIRTUALRETURNTYPE_CASCADE = 'Cascade';
    const VIRTUALRETURNTYPE_CASCADE_INPUT = 'CascadeInput';
    const VIRTUALRETURNTYPE_CASCADE_VALIDATE = 'CascadeValidate';
    const VIRTUALRETURNTYPE_MAILSERVICERECORD_PJ = 'MailServiceRecordPJ';

    //les différents types d'affichage pour les listes
    const DISPLAYMODE_List = 'List';
    const DISPLAYMODE_Planning = 'Planning';
    const DISPLAYMODE_DataTree = 'DataTree';
    const DISPLAYMODE_Thumbnail = 'Thumbnail';
    const DISPLAYMODE_Chart = 'Chart';
    const DISPLAYMODE_Flowchart = 'Flowchart';
    const DISPLAYMODE_Gantt = 'Gantt';
    const DISPLAYMODE_Map = 'Map';
}
