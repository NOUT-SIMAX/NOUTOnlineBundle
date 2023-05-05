<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 27/11/14
 * Time: 11:25
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Service;

use NOUT\Bundle\NOUTOnlineBundle\Entity\ActionResult;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ParametersManagement;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Langage;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\ColListType;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\Condition\CondColumn;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\Condition\Condition;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\Condition\CondType;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\Condition\CondValue;

use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\CondListType\CondListType;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\Factory\CondListTypeFactory;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\Operator\Operator;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\Record;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS;
use NOUT\Bundle\NOUTOnlineBundle\Entity\UserExists\UserExists;
use NOUT\Bundle\NOUTOnlineBundle\REST\HTTPResponse;
use NOUT\Bundle\NOUTOnlineBundle\REST\OnlineServiceProxy as RESTProxy;
use NOUT\Bundle\NOUTOnlineBundle\Security\EncryptionType;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\OnlineServiceProxy as SOAPProxy;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ButtonAction;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Cancel;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ConfirmResponse;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Create;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\CreateFrom;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\DataType;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Delete;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Display;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\DrillThrough;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Execute;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Export;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetChart;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetStartAutomatism;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetSubListContent;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Import;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ListParams;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Merge;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Modify;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Request;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Search;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SelectChoice;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SelectForm;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SelectItems;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SelectPrintTemplate;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SetOrderList;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SetOrderSubList;

use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Update;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\UpdateFilter;
use NOUT\Bundle\NOUTOnlineBundle\NOUTException\NOUTValidationException;
use Symfony\Component\HttpFoundation\File\UploadedFile;


/**
 * Class NOUTClient
 * @package NOUT\Bundle\NOUTOnlineBundle\Service
 */
class NOUTClient extends NOUTClientBase
{

    /**
     * @param string       $table
     * @param CondListType $condList
     * @param array        $colList
     * @param array|null   $tabHeaderSuppl
     * @return XMLResponseWS
     * @throws \Exception
     */
    protected function _oNewRequest(string $table, CondListType $condList, array $colList, ?array $tabHeaderSuppl=null) : XMLResponseWS
    {
        $clParamRequest = new Request();
        $clParamRequest->ColList = new ColListType($colList);
        $clParamRequest->Table = $table;
        $clParamRequest->CondList = $condList;
        $clParamRequest->MaxResult = self::MaxEnregs;
        return $this->m_clSOAPProxy->request($clParamRequest, $this->_aGetTabHeader($tabHeaderSuppl));
    }

    /**
     * Execute une action via son id
     * @param array      $tabParamQuery
     * @param array|null $aTabHeaderQuery
     * @param string     $sIDAction
     * @param int        $final
     * @param string     $sIDContext
     * @return ActionResult
     * @throws \Exception
     */
    public function oExecIDAction(string $sIDAction, string $sIDContext, array $tabParamQuery, ?array $aTabHeaderQuery = null, int $final = 0) : ActionResult
    {
        // Les paramètres du header sont passés par array

        //--------------------------------------------------------------------------------------------
        // Paramètres
        $clParam = $this->_oGetParam(Execute::class, $tabParamQuery);
        $clParam->ID = $sIDAction;             // identifiant de l'action (String)
        $clParam->Final = $final;

        //--------------------------------------------------------------------------------------------
        // Headers
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($aTabHeaderQuery, $sIDContext);

        //--------------------------------------------------------------------------------------------
        // L'action
        return $this->_oExecute($clParam, $aTabHeaderSuppl);
    }

    /**
     * @param array $tabParamQuery
     * @param array|null $tabHeaderQuery
     * @return ActionResult
     * @throws \Exception
     */
    public function oExecute(array $tabParamQuery, ?array $tabHeaderQuery=null) : ActionResult
    {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($tabHeaderQuery);
        $clParam = $this->_oGetParam(Execute::class, $tabParamQuery);

        return $this->_oExecute($clParam, $aTabHeaderSuppl);
    }

    /**
     * Execute une action via sa phrase
     * @param array      $tabParamQuery
     * @param string     $sPhrase
     * @param string     $sIDContexte
     * @param array|null $aTabHeaderQuery
     * @return ActionResult
     * @throws \Exception
     */
    public function oExecSentence(string $sPhrase, string $sIDContexte, array $tabParamQuery, ?array $aTabHeaderQuery=null) : ActionResult
    {
        //--------------------------------------------------------------------------------------------
        // Création de $clParamExecute
        $clParam = $this->_oGetParam(Execute::class, $tabParamQuery);
        $clParam->Sentence = $sPhrase;

        //--------------------------------------------------------------------------------------------
        // Headers
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($aTabHeaderQuery, $sIDContexte);

        return $this->_oExecute($clParam, $aTabHeaderSuppl);
    }

    /**
     * @param $login
     * @return UserExists
     * @throws \Exception
     */
    public function clUserExists($login) : UserExists
    {
        if (is_null($this->m_clRESTProxy)){
            throw $this->m_eProxyException;
        }

        return $this->m_clRESTProxy->clGetUserExists($login);
    }

    /**
     * @param $login
     * @param $form
     * @param $defaultEncryption
     * @return UserExists
     * @throws \Exception
     */
    public function clExtranetUserExists($login, $form, $defaultEncryption) : UserExists
    {
        return $this->m_clRESTProxy->clGetExtranetUserExists($login, $form, $defaultEncryption);
    }

    /**
     * @param $loginIntra
     * @param $loginExtra
     * @param $formExtra
     * @param $defaultExtraEncrypt
     * @return array
     */
    public function aGetInfoForCnxExtraAction($loginIntra, $loginExtra, $formExtra, $defaultExtraEncrypt) : array
    {
        //il faut commencer par vérifier si l'utilisateur extranet existe
        try{
            $clExtraExists = $this->clExtranetUserExists($loginExtra, $formExtra, $defaultExtraEncrypt);
        }
        catch (\Exception $e){
            $clExtraExists = new UserExists(UserExists::TYPEUTIL_NONE, null, null, $defaultExtraEncrypt);
        }
        try{
            $clIntraExists = $this->clUserExists($loginIntra);
        }
        catch (\Exception $e){
            $clIntraExists = new UserExists(UserExists::TYPEUTIL_NONE, null, null, null);
        }
        return [$clExtraExists, $clIntraExists];
    }

    /**
     * @param string         $sLoginExtranet
     * @param string         $sPwdExtra
     * @param EncryptionType $clHashExtra
     * @param int            $codeLangue
     * @param string         $sLoginIntranet
     * @param string         $sPwdIntra
     * @param EncryptionType $clHashIntra
     * @param string         $sFormulaireExtranet
     * @param bool           $bFromLogin
     * @return ActionResult
     * @throws \Exception
     */
    public function oConnexionExtranet(string $sLoginExtranet, string $sPwdExtra, EncryptionType $clHashExtra, int $codeLangue, string $sLoginIntranet, string $sPwdIntra, EncryptionType $clHashIntra, string $sFormulaireExtranet, bool $bFromLogin) : ActionResult
    {
        $clParam = new Execute();
        $clParam->ID = Langage::ACTION_ConnexionExtranet;

        //il faut encoder le mot de passe simax
        $sEncodedIntranet = $clHashIntra->sGetPassword($sPwdIntra, true);
        //et le mot de passe extranet
        $sEncodedExtranet = $clHashExtra->sGetPassword($sPwdExtra, true);

        $clParam->ParamXML = ParametersManagement::s_sStringifyParamXML([
            Langage::PA_ConnexionExtranet_Extranet_Pseudo => $sLoginExtranet,
            Langage::PA_ConnexionExtranet_Extranet_Mdp    => $sEncodedExtranet,
            Langage::PA_ConnexionExtranet_Intranet_Pseudo => $sLoginIntranet,
            Langage::PA_ConnexionExtranet_Intranet_Mdp    => $sEncodedIntranet,
            Langage::PA_ConnexionExtranet_Formulaire      => $sFormulaireExtranet,
            Langage::PA_ConnexionExtranet_CodeLangue      => $codeLangue,
            Langage::PA_ConnexionExtranet_FromLogin       => $bFromLogin ? 1 : 0,
        ]);

        $oRet = $this->_oExecute($clParam, []);

        //ici il faut invalider le cache
        //$this->m_clCache
        return $oRet;
    }

    /**
     * @param array      $tabParamQuery
     * @param string     $sIDTableau
     * @param string     $sIDContexte
     * @param array|null $aTabHeaderQuery
     * @return ActionResult
     * @throws \Exception
     */
    public function oExecList(array $tabParamQuery, string $sIDTableau, string $sIDContexte = '', ?array $aTabHeaderQuery=null) : ActionResult
    {
        //paramètre de l'action liste
        $clParam = $this->_oGetParam(ListParams::class, $tabParamQuery);
        $clParam->Table = $sIDTableau;

        //--------------------------------------------------------------------------------------------
        // Headers
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($aTabHeaderQuery, $sIDContexte);

        $clReponseXML = $this->m_clSOAPProxy->listAction($clParam, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param string     $tableID
     * @param string     $contextID
     * @param array|null $aTabHeaderQuery
     * @return ActionResult
     * @throws \Exception
     */
    public function oExecListRequest(string $tableID, string $contextID = '', ?array $aTabHeaderQuery=null) : ActionResult
    {
        return $this->_oExecRequestOnIDTableau($tableID, $contextID, Langage::TABL_Requete, Langage::COL_REQUETE_IDTableau, [], $aTabHeaderQuery);
    }

    /**
     * @param string     $tableID
     * @param string     $contextID
     * @param string     $requestTableId
     * @param string     $requestColId
     * @param array      $colList
     * @param array|null $aTabHeaderQuery
     * @return ActionResult
     * @throws \Exception
     */
    protected function _oExecRequestOnIDTableau(string $tableID, string $contextID, string $requestTableId, string $requestColId, array $colList, ?array $aTabHeaderQuery=null) : ActionResult
    {
        $condition = new Condition(
            new CondColumn($requestColId),
            new CondType(CondType::COND_EQUAL),
            new CondValue($tableID));
        $condList = CondListTypeFactory::create($condition);

        //--------------------------------------------------------------------------------------------
        // Headers
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($aTabHeaderQuery, $contextID);

        $clReponseXML = $this->_oNewRequest(
            $requestTableId,
            $condList,
            $colList,
            $aTabHeaderSuppl);

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param string     $contextID
     * @param array|null $aTabHeaderQuery
     * @return ActionResult
     * @throws \Exception
     */
    public function oExecListCalculation(string $contextID, ?array $aTabHeaderQuery=null) : ActionResult
    {
        //--------------------------------------------------------------------------------------------
        // Headers
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($aTabHeaderQuery, $contextID);

        $clReponseXML = $this->m_clSOAPProxy->getEndListCalculation($this->_aGetTabHeader($aTabHeaderSuppl));

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param string     $contextID
     * @param array|null $aTabHeaderQuery
     * @return ActionResult
     * @throws \Exception
     */
    public function oGetDefaultExportAction(string $contextID, ?array $aTabHeaderQuery=null) : ActionResult
    {
        //--------------------------------------------------------------------------------------------
        // Headers
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($aTabHeaderQuery, $contextID);

        //----------------------------------------------------------------------------------
        $aTabColonne = array();
        $default_export_action = new Condition(
            new CondColumn(Langage::COL_ACTION_IDAction),
            new CondType(CondType::COND_EQUAL),
            new CondValue(Langage::ACTION_Export)
        );
        $has_rights = new Condition(
            new CondColumn(Langage::COL_ACTION_IDAction),
            new CondType(CondType::COND_WITHRIGHT),
            new CondValue('1')
        );

        $operator = new Operator(Operator::OP_AND);
        $operator->addCondition($default_export_action)
            ->addCondition($has_rights);

        $condList = CondListTypeFactory::create($operator);

        $clReponseXML = $this->_oNewRequest(Langage::TABL_Action,
            $condList,
            $aTabColonne,
            $aTabHeaderSuppl);

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param string     $tableID
     * @param string     $contextID
     * @param array|null $aTabHeaderQuery
     * @return ActionResult
     * @throws \Exception
     */
    public function oGetExportsList(string $tableID, string $contextID, ?array $aTabHeaderQuery=null) : ActionResult
    {
        return $this->_oExecRequestOnIDTableau($tableID, $contextID, Langage::TABL_Export, Langage::COL_EXPORT_IDTableau, [Langage::COL_EXPORT_Libelle], $aTabHeaderQuery);
    }

    /**
     * @param string $tableID
     * @param string $contextID
     * @return ActionResult
     * @throws \Exception
     */
    public function oGetImportsList(string $tableID, string $contextID) : ActionResult
    {
        return $this->_oExecRequestOnIDTableau($tableID, $contextID, Langage::TABL_Import, Langage::COL_IMPORT_Formulaire, [Langage::COL_IMPORT_Libelle]);
    }

    /**
     * @param string     $contextID
     * @param string|null     $tableId
     * @param string|null     $actionId
     * @param string|null     $exportId
     * @param string|null     $format
     * @param string|null     $module
     * @param string|null     $colType
     * @param string|null     $items
     * @param array|null $aTabHeaderQuery
     * @return ActionResult
     * @throws \Exception
     */
    public function oExport(string $contextID, ?string $tableId, ?string $actionId, ?string $exportId, ?string $format, ?string $module, ?string $colType, ?string $items, ?array $aTabHeaderQuery=null) : ActionResult
    {
        //--------------------------------------------------------------------------------------------
        // Headers
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($aTabHeaderQuery, $contextID);

        $export = new Export();
        $export->Table = $tableId;
        $export->ID = $actionId;
        $export->Export = $exportId;
        $export->Format = $format;
        $export->Module = $module;
        $export->ColType = $colType;
        $export->items = $items;

        $clReponseXML = $this->m_clSOAPProxy->export($export, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param string            $tableId
     * @param string            $actionId
     * @param string|null       $importId
     * @param UploadedFile|null $file
     * @param array|null        $aTabHeaderQuery
     * @return ActionResult
     * @throws \Exception
     */
    public function oImport(string $tableId, string $actionId, ?string $importId, ?UploadedFile $file = null, ?array $aTabHeaderQuery=null) : ActionResult
    {
        //--------------------------------------------------------------------------------------------
        // Headers
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($aTabHeaderQuery);

        //------------------------------------------------------------------------------------------------------
        $data = null;

        if($file instanceof UploadedFile) {
            $encoding = 'base64';
            $filename = $file->getClientOriginalName();
            $size = $file->getSize();
            $fileData = base64_encode(stream_get_contents(fopen($file->getRealPath(), 'rb')));

            $data = new DataType();
            $data->filename = $filename;
            $data->encoding = $encoding;
            $data->size = $size;
            $data->_ = $fileData;
        }
        $import = new Import();

        $import->Table = $tableId;
        $import->ID = $actionId;
        $import->Import = $importId;
        $import->File = $data;

        $clReponseXML = $this->m_clSOAPProxy->import($import, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param string     $tableID
     * @param string     $contextID
     * @param string     $eTypeAction
     * @param array|null $aTabHeaderQuery
     * @return ActionResult
     * @throws \Exception
     */
    protected function _oRequestImportExportActions(string $tableID, string $contextID, string $eTypeAction, ?array $aTabHeaderQuery=null) : ActionResult
    {
        //--------------------------------------------------------------------------------------------
        // Headers
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($aTabHeaderQuery, $contextID);

        //--------------------------------------------------------------------------------------------
        $colList = array(Langage::COL_ACTION_Libelle);
        $table_actions = new Condition(
            new CondColumn(Langage::COL_ACTION_IDTableau),
            new CondType(CondType::COND_EQUAL),
            new CondValue($tableID)
        );
        $has_rights = new Condition(
            new CondColumn(Langage::COL_ACTION_IDAction),
            new CondType(CondType::COND_WITHRIGHT),
            new CondValue(1)
        );
        $type_actions = new Condition(
            new CondColumn(Langage::COL_ACTION_TypeAction),
            new CondType(CondType::COND_EQUAL),
            new CondValue($eTypeAction)
        );
        $operator = new Operator(Operator::OP_AND);
        $operator->addCondition($table_actions)
            ->addCondition($has_rights)
            ->addCondition($type_actions);

        $condList = CondListTypeFactory::create($operator);

        //----------------------------------
        $clReponseXML = $this->_oNewRequest(
            Langage::TABL_Action,
            $condList,
            $colList,
            $aTabHeaderSuppl);

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param string     $tableID
     * @param string     $contextID
     * @param array|null $aTabHeaderQuery
     * @return ActionResult
     * @throws \Exception
     */
    public function oGetExportsActions(string $tableID, string $contextID, ?array $aTabHeaderQuery=null) : ActionResult
    {
        return $this->_oRequestImportExportActions($tableID, $contextID, Langage::eTYPEACTION_Exporter, $aTabHeaderQuery);
    }

    /**
     * @param string     $tableID
     * @param string     $contextID
     * @param array|null $aTabHeaderQuery
     * @return ActionResult
     * @throws \Exception
     */
    public function oGetImportsActions(string $tableID, string $contextID, ?array $aTabHeaderQuery=null) : ActionResult
    {
        return $this->_oRequestImportExportActions($tableID, $contextID, Langage::eTYPEACTION_Importer, $aTabHeaderQuery);
    }

    /**
     * Affichage d'une liste via l'action recherche
     * @param array      $tabParamQuery
     * @param string     $sIDTableau
     * @param string     $contextID
     * @param array|null $aTabHeaderQuery
     * @return ActionResult
     * @throws \Exception
     */
    public function oExecSearch(array $tabParamQuery, string $sIDTableau, string $contextID = '', ?array $aTabHeaderQuery=null) : ActionResult
    {
        //--------------------------------------------------------------------------------------------
        // Headers
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($aTabHeaderQuery, $contextID);

        //-----------------------------
        //paramètre de l'action liste
        $clParam = $this->_oGetParam(Search::class, $tabParamQuery);
        $clParam->Table = $sIDTableau;

        $clReponseXML = $this->m_clSOAPProxy->search($clParam, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param Execute $clParamExecute
     * @param array $aTabHeaderSuppl
     * @return ActionResult
     * @throws \Exception
     */
    protected function _oExecute(Execute $clParamExecute, array $aTabHeaderSuppl) : ActionResult
    {
        $clReponseXML = $this->m_clSOAPProxy->execute($clParamExecute, $this->_aGetTabHeader($aTabHeaderSuppl));

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * Execute une action via son id
     * @param array      $tabParamQuery
     * @param string     $idcolonne
     * @param Record     $clRecord
     * @param array|null $aTabHeaderQuery
     * @param string     $idcontexte
     * @return ActionResult
     * @throws \Exception
     */
    public function oGetSublistContent(Record $clRecord, string $idcolonne, string $idcontexte, array $tabParamQuery, ?array $aTabHeaderQuery=null) : ActionResult
    {
        //test des valeurs des paramètres
        $this->_TestParametre(self::TP_NotEmpty, '$idColumn', $idcolonne, null);

        //paramètre de l'action liste
        $clParam = $this->_oGetParam(GetSubListContent::class, $tabParamQuery);
        $clParam->Record = $clRecord->getIDEnreg();
        $clParam->Column = $idcolonne;

        //--------------------------------------------------------------------------------------------
        // Headers
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($aTabHeaderQuery, $idcontexte);

        $clReponseXML = $this->m_clSOAPProxy->getSubListContent($clParam, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param string     $idenreg
     * @param string     $idcolonne
     * @param string     $idContext
     * @param array      $tabParamQuery
     * @param array|null $tabHeaderQuery
     * @return ActionResult
     * @throws \Exception
     */
    public function oDrillthrough(string $idenreg, string $idcolonne, string $idContext, array $tabParamQuery, ?array $tabHeaderQuery=null) : ActionResult
    {
        $clParam = $this->_oGetParam(DrillThrough::class, $tabParamQuery);
        $clParam->Record = $idenreg;
        $clParam->Column = $idcolonne;

        //--------------------------------------------------------------------------------------------
        // Headers
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($tabHeaderQuery, $idContext);

        $clReponseXML = $this->m_clSOAPProxy->drillThrough($clParam, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param array      $tabParamQuery
     * @param array|null $tabHeaderQuery
     * @param string     $idContexte
     * @return ActionResult
     * @throws \Exception
     */
    public function oGetChart(string $idContexte, array $tabParamQuery, ?array $tabHeaderQuery=null) : ActionResult
    {
        $getChart = $this->_oGetParam(GetChart::class, $tabParamQuery);
        $getChart->Width = 5000;
        $getChart->Height = 5000;
        $getChart->DPI = 92;

        //--------------------------------------------------------------------------------------------
        // Headers
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($tabHeaderQuery, $idContexte);

        $clReponseXML = $this->m_clSOAPProxy->getChart($getChart, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param array|null $tabHeaderQuery
     * @param string     $column
     * @param string     $items
     * @param string     $idContext
     * @return array
     * @throws NOUTValidationException|\Exception
     */
    public function oSetSublistOrder(string $column, string $items, string $idContext, ?array $tabHeaderQuery=null) : array
    {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($tabHeaderQuery, $idContext);

        $setSublistOrder = new SetOrderSubList();
        $setSublistOrder->items = $items;
        $setSublistOrder->column = $column;

        $clXMLResponse = $this->m_clSOAPProxy->setOrderSubList($setSublistOrder, $this->_aGetTabHeader($aTabHeaderSuppl));

        if($clXMLResponse->sGetReturnType() === XMLResponseWS::RETURNTYPE_VALUE) {
            return explode('|', trim($clXMLResponse->getValue(), '|'));
        }
        else {
            throw new NOUTValidationException("No valid ReturnType");
        }
    }

    /**
     * @param array|null $tabHeaderQuery
     * @param string     $items
     * @param string     $idContext
     * @return array
     * @throws NOUTValidationException|\Exception
     */
    public function oSetFullListOrder(string $items, string $idContext, ?array $tabHeaderQuery=null) : array
    {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($tabHeaderQuery, $idContext);

        $setSublistOrder = new SetOrderList();
        $setSublistOrder->items = $items;

        $clXMLResponse = $this->m_clSOAPProxy->setOrderList($setSublistOrder, $this->_aGetTabHeader($aTabHeaderSuppl));

        if($clXMLResponse->sGetReturnType() === XMLResponseWS::RETURNTYPE_VALUE) {
            return explode('|', trim($clXMLResponse->getValue(), '|'));
        }
        else {
            throw new NOUTValidationException("No valid ReturnType");
        }
    }


    /**
     * @param string $sIDContexte
     * @param Record $clRecord
     * @param int    $autovalidate
     * @param bool   $bComplete
     * @param string $idihm
     * @return ActionResult
     * @throws \Exception
     */
    public function oUpdate(string $sIDContexte, string $idihm, Record $clRecord, int $autovalidate = SOAPProxy::AUTOVALIDATE_None, bool $bComplete=false) : ActionResult
    {

        //test des valeurs des paramètres
        $this->_TestParametre(self::TP_InArray, '$autovalidate', $autovalidate, array(SOAPProxy::AUTOVALIDATE_None, SOAPProxy::AUTOVALIDATE_Cancel, SOAPProxy::AUTOVALIDATE_Validate));
        $this->_TestParametre(self::TP_NotEmpty, '$sIDContexte', $sIDContexte, null);

        //header
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl(null, $sIDContexte, $autovalidate);

        //paramètre
        $sIDForm = $clRecord->getIDTableau();
        $sIDEnreg = $clRecord->getIDEnreg();

        $clParamUpdate              = new Update();
        $clParamUpdate->Table       = $sIDForm;
        $clParamUpdate->ParamXML    = ParametersManagement::s_sStringifyParamXML([$sIDForm=>$sIDEnreg]);

        //m_clRecordSerializer->getRecordUpdateData fait la gestion des fichiers
        $clParamUpdate->UpdateData = $this->m_clRecordSerializer->getRecordUpdateData($clRecord, $sIDContexte, $idihm);
        $clParamUpdate->Complete = $bComplete ? 1 : 0;

        $clReponseXML = $this->m_clSOAPProxy->update($clParamUpdate, $this->_aGetTabHeader($aTabHeaderSuppl));

        $oRet = $this->_oGetActionResultFromXMLResponse($clReponseXML);

        if ($autovalidate == SOAPProxy::AUTOVALIDATE_None)
        {
            //c'est un update tout bête sans validation normalement on à le même enregistrement en entrée et en sortie
            $clRecortRes = $oRet->getData();
            if ($clRecord->getIDEnreg() != $clRecortRes->getIDEnreg())
            {
                throw new \Exception("l'update n'a pas retourné le bon enregistrement");
            }

            //on met à jour l'enregistrement d'origine à partir de celui renvoyé par NOUTOnline
            $clRecord->updateFromRecord($clRecortRes);
            $oRet->setData($clRecord);
        }

        return $oRet;
    }


    /**
     * @param string $sIDContexte
     * @param Record $clRecord
     * @param string $idihm
     * @return ActionResult
     * @throws \Exception
     */
    public function oUpdateFilter(string $sIDContexte, string $idihm, Record $clRecord) : ActionResult
    {
        //test des valeurs des paramètres
        $this->_TestParametre(self::TP_NotEmpty, '$sIDContexte', $sIDContexte, null);

        //header
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl(null, $sIDContexte);

        //paramètre
        $clParamUpdate              = new UpdateFilter();
        //m_clRecordSerializer->getRecordUpdateData fait la gestion des fichiers
        $clParamUpdate->ID = $clRecord->getIDEnreg();
        $clParamUpdate->UpdateData = $this->m_clRecordSerializer->getRecordUpdateData($clRecord, $sIDContexte, $idihm, true);

        $clReponseXML = $this->m_clSOAPProxy->updateFilter($clParamUpdate, $this->_aGetTabHeader($aTabHeaderSuppl));

        $oRet = $this->_oGetActionResultFromXMLResponse($clReponseXML);

        //c'est un update tout bête sans validation normalement on à le même enregistrement en entrée et en sortie
        $clRecortRes = $oRet->getData();
        if ($clRecord->getIDEnreg() != $clRecortRes->getIDEnreg())
        {
            throw new \Exception("l'update n'a pas retourné le bon enregistrement");
        }

        //on met à jour l'enregistrement d'origine à partir de celui renvoyé par NOUTOnline
        $clRecord->updateFromRecord($clRecortRes);
        $oRet->setData($clRecord);

        return $oRet;
    }


    /**
     * @param string $idContext
     * @param string $items
     * @param string $CallingColumn
     * @param Record $clRecord
     * @return ActionResult
     * @throws \Exception
     */
    public function oSelectItems(string $idContext, string $items, string $CallingColumn, Record $clRecord) : ActionResult
    {
        //header
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl(null, $idContext);

        $clParamSelectItems                 = new SelectItems();
        $clParamSelectItems->items          = $items;
        $clParamSelectItems->CallingColumn  = $CallingColumn;

        $clReponseXML = $this->m_clSOAPProxy->selectItems($clParamSelectItems, $this->_aGetTabHeader($aTabHeaderSuppl));

        $oRet = $this->_oGetActionResultFromXMLResponse($clReponseXML);

        //c'est un update tout bête sans validation normalement on à le même enregistrement en entrée et en sortie
        $clRecortRes = $oRet->getData();
        if ($clRecord->getIDEnreg() != $clRecortRes->getIDEnreg())
        {
            throw new \Exception("l'update n'a pas retourné le bon enregistrement");
        }

        //on met à jour l'enregistrement d'origine à partir de celui renvoyé par NOUTOnline
        $clRecord->updateFromRecord($clRecortRes);
        $oRet->setData($clRecord);

        return $oRet;
    }

    /**
     * @param string $sIDContexte
     * @param string $idButton
     * @param array|null $ColumnSelection
     * @param Record|null $dataRecord
     * @return ActionResult
     * @throws \Exception
     */
    public function oButtonAction(string $sIDContexte, string $idButton, ?array $ColumnSelection, Record $dataRecord = null) : ActionResult
    {
        //test des valeurs des paramètres
        $this->_TestParametre(self::TP_NotEmpty, '$sIDContexte', $sIDContexte, null);

        //header
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl(null, $sIDContexte);

        //paramètre
        $clParam                = new ButtonAction();
        $clParam->CallingColumn = $idButton;
        $clParam->ColumnSelection = $ColumnSelection;


        $clReponseXML       = $this->m_clSOAPProxy->buttonAction($clParam, $this->_aGetTabHeader($aTabHeaderSuppl));

        $oRet = $this->_oGetActionResultFromXMLResponse($clReponseXML);
        if($dataRecord !== null) {
            $clRecortRes = $oRet->getData();
            if ($dataRecord->getIDEnreg() != $clRecortRes->getIDEnreg())
            {
                throw new \Exception("l'action du bouton n'a pas retourné le bon enregistrement");
            }
            $dataRecord->updateFromRecord($clRecortRes);
            $oRet->setData($dataRecord);
        }

        return $oRet;
    }

    /**
     * Valide l'action courante du contexte
     * @param string $sIDContexte
     * @return ActionResult
     * @throws \Exception
     */
    public function oValidate(string $sIDContexte) : ActionResult
    {
        $this->_TestParametre(self::TP_NotEmpty, '$sIDContexte', $sIDContexte, null);

        //header
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl(null, $sIDContexte);

        $clReponseXML = $this->m_clSOAPProxy->validate($this->_aGetTabHeader($aTabHeaderSuppl));

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * Valide l'action courante du contexte
     * @param string $sIDContexte
     * @param int    $final
     * @param string $form
     * @param string $record
     * @return ActionResult
     * @throws \Exception
     */
    public function oCreateFrom(string $sIDContexte, string $form, string $record, int $final) :ActionResult
    {
        //paramètre de l'action liste
        $clCreateFrom = new CreateFrom();
        $clCreateFrom->ElemSrc = $record;
        $clCreateFrom->Table = $form;
        $clCreateFrom->TableSrc = $form;
        $clCreateFrom->Final = $final;

        //header
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl(null, $sIDContexte);

        $clReponseXML = $this->m_clSOAPProxy->createFrom($clCreateFrom, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param string $sIDContexte
     * @param string $form
     * @param string $dstRecord
     * @param string $srcRecords
     * @return ActionResult
     * @throws \Exception
     */
    public function oMerge(string $sIDContexte, string $form, string $dstRecord, string $srcRecords) :ActionResult
    {
        //paramètre de l'action liste
        $clMerge = new  Merge();
        $clMerge->ElemSrc = $srcRecords;
        $clMerge->Table = $form;
        $clMerge->ElemDest= $dstRecord;

        //header
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl(null, $sIDContexte);

        $clReponseXML = $this->m_clSOAPProxy->merge($clMerge, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }


    /**
     * annulation
     * @param string $sIDContexte
     * @param bool $bAll tout le contexte
     * @param bool $bByUser action utilisateur
     * @return ActionResult
     * @throws \Exception
     */
    public function oCancel(string $sIDContexte, bool $bAll = false, bool $bByUser = true) :ActionResult
    {
        $this->_TestParametre(self::TP_NotEmpty, '$sIDContexte', $sIDContexte, null);

        //header
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl(null, $sIDContexte);

        $clParamCancel = new Cancel();
        $clParamCancel->Context = $bAll ? 1 : 0;
        $clParamCancel->ByUser = $bByUser ? 1 : 0;

        $clReponseXML = $this->m_clSOAPProxy->cancel($clParamCancel, $this->_aGetTabHeader($aTabHeaderSuppl));

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }


    /**
     * @param string $sIDContexte
     * @param string $ResponseValue
     * @return ActionResult
     * @throws \Exception
     */
    public function oConfirmResponse(string $sIDContexte, string $ResponseValue) :ActionResult
    {
        //header
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl(null, $sIDContexte);

        $oConfirmResponse = new ConfirmResponse();
        $oConfirmResponse->TypeConfirmation = $ResponseValue;

        $clReponseXML = $this->m_clSOAPProxy->ConfirmResponse($oConfirmResponse, $this->_aGetTabHeader($aTabHeaderSuppl));

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    // ------------------------------------------------------------------------------------
    // pour les Elements liés et les sous-listes

    /**
     * @param array  $tabParamQuery
     * @param string $sIDFormulaire
     * @param string $sIDContexte
     * @return ActionResult
     * @throws \Exception
     */
    public function oSelectElem(array $tabParamQuery, string $sIDFormulaire, string $sIDContexte) :ActionResult
    {
        $this->_TestParametre(self::TP_NotEmpty, '$sIDContexte', $sIDContexte, null);

        //header
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl(null, $sIDContexte);

//        public $ParamXML; 			// string
//        public $SpecialParamList; 	// SpecialParamListType
//        public $Checksum; 			// integer
//        public $DisplayMode; 		    // DisplayModeParamEnum
        $clParam = $this->_oGetParam(Search::class, $tabParamQuery);
        // Ajout des paramètres
        $clParam->Table = $sIDFormulaire;


        $clReponseXML = $this->m_clSOAPProxy->search($clParam, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param array  $tabParamQuery
     * @param string $sIDFormulaire
     * @param string $sIDContexte
     * @return ActionResult
     * @throws \Exception
     */
    public function oCreateElem(array $tabParamQuery, string $sIDFormulaire, string $sIDContexte) :ActionResult
    {
        $this->_TestParametre(self::TP_NotEmpty, '$sIDContexte', $sIDContexte, null);

        //header
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl(null, $sIDContexte);

        $clParam = $this->_oGetParam(Create::class, $tabParamQuery);
        $clParam->Table = $sIDFormulaire;


        $clReponseXML = $this->m_clSOAPProxy->create($clParam, $this->_aGetTabHeader($aTabHeaderSuppl));

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }


    /**
     * @param array  $tabParamQuery
     * @param string $sIDFormulaire
     * @param string $sIDEnreg
     * @param string $sIDContexte
     * @return ActionResult
     * @throws \Exception
     */
    public function oDeleteElem(array $tabParamQuery, string $sIDFormulaire, string $sIDEnreg, string $sIDContexte) :ActionResult
    {
        $this->_TestParametre(self::TP_NotEmpty, '$sIDContexte', $sIDContexte, null);

        //header
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl(null, $sIDContexte);

        $clParam = $this->_oGetParam(Delete::class, $tabParamQuery);
        $clParam->Table = $sIDFormulaire;
        $clParam->ParamXML = ParametersManagement::s_sStringifyParamXML([$sIDFormulaire=>$sIDEnreg]);

        $clReponseXML = $this->m_clSOAPProxy->delete($clParam, $this->_aGetTabHeader($aTabHeaderSuppl));

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param array          $tabParamQuery
     * @param string         $sIDContexte
     * @param \stdClass|null $updateData
     * @param string         $idenreg
     * @param string         $idformulaire
     * @param int            $autovalidate
     * @return ActionResult
     * @throws \Exception
     */
    public function oModifyElem(array $tabParamQuery, string $sIDContexte, string $idformulaire, string $idenreg, \stdClass $updateData = null, int $autovalidate = SOAPProxy::AUTOVALIDATE_None) :ActionResult
    {
        $this->_TestParametre(self::TP_NotEmpty, '$sIDContexte', $sIDContexte, null);
        //header
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl(null, $sIDContexte, $autovalidate);

        $clParamModify = $this->_oGetParam(Modify::class, $tabParamQuery);
        $clParamModify->Table = $idformulaire;
        $clParamModify->ParamXML .= ParametersManagement::s_sStringifyParamXML([$idformulaire=>$idenreg]);

        if(!is_null($updateData)) {
            $aColMultiLangue = (isset($updateData->isMultiLanguage) && boolval($updateData->isMultiLanguage)) ? [$updateData->idColumn] : null;
            $clParamModify->UpdateData = ParametersManagement::s_sStringifyUpdateData($idformulaire, [$updateData->idColumn=>$updateData->val], $aColMultiLangue);
        }

        $clReponseXML = $this->m_clSOAPProxy->modify($clParamModify, $this->_aGetTabHeader($aTabHeaderSuppl));

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param array  $tabParamQuery
     * @param string $sIDContexte
     * @param string $idenreg
     * @param string $idformulaire
     * @return ActionResult
     * @throws \Exception
     */
    public function oDisplayElem(array $tabParamQuery, string $sIDContexte, string $idformulaire, string $idenreg) :ActionResult
    {
        $this->_TestParametre(self::TP_NotEmpty, '$sIDContexte', $sIDContexte, null);

        //header
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl(null, $sIDContexte);

        $clParamDisplay = $this->_oGetParam(Display::class, $tabParamQuery);
        $clParamDisplay->Table = $idformulaire;
        $clParamDisplay->ParamXML = ParametersManagement::s_sStringifyParamXML([$idformulaire=>$idenreg]);

        $clReponseXML = $this->m_clSOAPProxy->display($clParamDisplay, $this->_aGetTabHeader($aTabHeaderSuppl));

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param string $sIDFormulaire
     * @param string $sIDContexte
     * @return ActionResult
     * @throws \Exception
     */
    public function oSelectAmbiguous(string $sIDFormulaire, string $sIDContexte) :ActionResult
    {
        $this->_TestParametre(self::TP_NotEmpty, '$sIDFormulaire', $sIDFormulaire, null);

        //header
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl(null, $sIDContexte);

        // Paramètres obligatoires
        $clParamSelect = new SelectForm();
        $clParamSelect->Form = $sIDFormulaire;

        $clReponseXML = $this->m_clSOAPProxy->selectForm($clParamSelect, $this->_aGetTabHeader($aTabHeaderSuppl)); // Deuxième paramètre = array

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }


    /**
     * @param string $sIDTemplate
     * @param string $sIDContexte
     * @return ActionResult
     * @throws \Exception
     */
    public function oSelectTemplate(string $sIDTemplate, string $sIDContexte) :ActionResult
    {
        $this->_TestParametre(self::TP_NotEmpty, '$sIDTemplate', $sIDTemplate, null);

        //header
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl(null, $sIDContexte);

        // Paramètres obligatoires
        $clParamSelect = new SelectPrintTemplate();
        $clParamSelect->Template = $sIDTemplate;

        $clReponseXML = $this->m_clSOAPProxy->selectPrintTemplate($clParamSelect, $this->_aGetTabHeader($aTabHeaderSuppl)); // Deuxième paramètre = array

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param string $sIDChoice
     * @param string $sIDContexte
     * @return ActionResult
     * @throws \Exception
     */
    public function oSelectChoice(string $sIDChoice, string $sIDContexte) :ActionResult
    {
        $this->_TestParametre(self::TP_NotEmpty, '$sIDChoice', $sIDChoice, null);

        //header
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl(null, $sIDContexte);

        $clParamSelect = new SelectChoice();
        $clParamSelect->Choice = $sIDChoice;

        $clReponseXML = $this->m_clSOAPProxy->selectChoice($clParamSelect, $this->_aGetTabHeader($aTabHeaderSuppl));

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @return ActionResult
     * @throws \Exception
     */
    public function oGetStartAutomatism() :ActionResult
    {
        // Informations d'authentification
        /*
        $token          = $this->_oGetToken();
        $sessionToken   = $token->getSessionToken();
        $usernameToken  = $this->_oGetUsernameToken($token);
        */

        $clParamStartAutomatism = new GetStartAutomatism();

        // Paramètres : GetStartAutomatism $clWsdlType_GetStartAutomatism, $aHeaders = array()
        $clReponseXML = $this->m_clSOAPProxy->getStartAutomatism($clParamStartAutomatism, $this->_aGetTabHeader([]));

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }


    // Fin Elements liés et les sous-listes
    // ------------------------------------------------------------------------------------

    /**
     * @param string $idContext
     * @param string $startTime
     * @param string $endTime
     * @return ActionResult
     * @throws \Exception
     */
    public function getSchedulerInfo(string $idContext, string $startTime, string $endTime) : ActionResult
    {
        $aTabParam = array(
            RESTProxy::PARAM_StartTime  => $startTime,
            RESTProxy::PARAM_EndTime    => $endTime,
        );

        $clIdentification = $this->_clGetIdentificationREST($idContext, false);

        $sRet = $this->m_clRESTProxy->oGetSchedulerInfo($aTabParam, $clIdentification);

        $clActionResult = new ActionResult(null);
        $clActionResult->setData($sRet);

        return $clActionResult;
    }

    /**
     * @param string $idContext
     * @param string $startTime
     * @param string $endTime
     * @param string $idForm
     * @param string $idEnreg
     * @param string $idColumn
     * @return ActionResult
     * @throws \Exception
     */
    public function getSchedulerCardInfo(string $idContext, string $idForm, string $idEnreg, string $idColumn, string $startTime, string $endTime) :ActionResult
    {
        $aTabParam = array(
            RESTProxy::PARAM_StartTime  => $startTime,
            RESTProxy::PARAM_EndTime    => $endTime,
        );

        $clIdentification = $this->_clGetIdentificationREST($idContext, false);

        $sRet = $this->m_clRESTProxy->oGetSchedulerCardInfo($idForm, $idEnreg, $idColumn, $aTabParam, $clIdentification);

        $clActionResult = new ActionResult(null);
        $clActionResult->setData($sRet);

        return $clActionResult;
    }

    /**
     * @param string $idcontext
     * @param string $idformulaire
     * @param string $idcallingcolumn
     * @param string $query
     * @return ActionResult
     * @throws \Exception
     */
    public function getSuggest(string $idcontext, string $idformulaire, string $idcallingcolumn, string $query) : ActionResult
    {
        $oSuggestData = $this->_getSuggest($idcontext, $idformulaire, $idcallingcolumn, $query);

        $clActionResult = new ActionResult(null);
        $clActionResult->setData($oSuggestData);

        // Modifier des données au besoin..
        //
        return $clActionResult;
    }

    /**
     * @param string $idcontext
     * @param string $idformulaire
     * @param string $idcallingcolumn
     * @param string $query
     * @return HTTPResponse
     * @throws \Exception
     */
    private function _getSuggest(string $idcontext, string $idformulaire, string $idcallingcolumn, string $query) : HTTPResponse
    {
        // Création des options
        $aTabOption = array();
        $aTabParam = array(RESTProxy::PARAM_CallingColumn => $idcallingcolumn);

        $clIdentification = $this->_clGetIdentificationREST($idcontext, true);

        return $this->m_clRESTProxy->oGetSuggestFromQuery(
            $idformulaire,
            $query,
            $aTabParam,
            $aTabOption,
            $clIdentification
        );
    }

    /**
     * @param HTTPResponse $HTTPResponse
     * @return ActionResult
     */
    private function _oGetJSONActionResultFromHTTPResponse(HTTPResponse $HTTPResponse) : ActionResult
    {
        $clActionResult = new ActionResult(null);
        $oInfo = json_decode($HTTPResponse->content);
        $clActionResult->setData($oInfo);
        return $clActionResult;
    }

    /**
     * @param string $idcontext
     * @param string $idformulaire
     * @param string $idenreg
     * @param string $idcallingcolumn
     * @param string $formula
     * @return ActionResult
     * @throws \Exception
     */
    public function oVerifyFormula(string $idcontext, string $idformulaire, string $idenreg, string $idcallingcolumn, string $formula) : ActionResult
    {
        $clIdentification = $this->_clGetIdentificationREST($idcontext, true);
        $httpresponse = $this->m_clRESTProxy->oVerifyFormula(
            $idformulaire,
            $idenreg,
            $idcallingcolumn,
            $formula,
            $clIdentification
        );
        return $this->_oGetJSONActionResultFromHTTPResponse($httpresponse);
    }

    /**
     * @param string $idcontext
     * @param string $idformulaire
     * @param string $idenreg
     * @param string $idcallingcolumn
     * @param string $formula
     * @return ActionResult
     * @throws \Exception

     */
    public function oVerifyIndentation(string $idcontext, string $idformulaire, string $idenreg, string $idcallingcolumn, string $formula) : ActionResult
    {
        $clIdentification = $this->_clGetIdentificationREST($idcontext, true);
        $httpresponse = $this->m_clRESTProxy->oVerifyIndentation(
            $idformulaire,
            $idenreg,
            $idcallingcolumn,
            $formula,
            $clIdentification
        );
        return $this->_oGetJSONActionResultFromHTTPResponse($httpresponse);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function oGetConfigurationDropdownParams(string $idcontext) : ActionResult
    {
        $clIdentification = $this->_clGetIdentificationREST($idcontext, true);

        $httpresponse = $this->m_clRESTProxy->oGetConfigurationDropdownParams($clIdentification);

        return $this->_oGetJSONActionResultFromHTTPResponse($httpresponse);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function oGetConfigurationDropdownColumns(string $idcontext) : ActionResult
    {
        $clIdentification = $this->_clGetIdentificationREST($idcontext, true);

        $httpresponse = $this->m_clRESTProxy->oGetConfigurationDropdownColumns($clIdentification);

        return $this->_oGetJSONActionResultFromHTTPResponse($httpresponse);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function oApplyConfiguration() : ActionResult
    {
        $clIdentification = $this->_clGetIdentificationREST('', true);

        $httpresponse = $this->m_clRESTProxy->oApplyConfiguration($clIdentification);

        return $this->_oGetJSONActionResultFromHTTPResponse($httpresponse);
    }

}