<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 27/11/14
 * Time: 11:25
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Service;

use NOUT\Bundle\NOUTOnlineBundle\Entity\ActionResult;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ActionResultCache;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Header\OptionDialogue;
use NOUT\Bundle\NOUTOnlineBundle\Entity\NOUTFileInfo;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ParametersManagement;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Langage;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\RecordList;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS;
use NOUT\Bundle\NOUTOnlineBundle\REST\HTTPResponse;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\OnlineServiceProxy as SOAPProxy;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\AddPJ;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\CreateMessage;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\DataPJType;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\DeletePJ;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\DisplayRedoMessage;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\DisplayUndoMessage;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Execute;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetContentFolder;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetPJ;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetRedoList;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetUndoList;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ModifyMessage;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Redo;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\RequestMessage;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SendMessage;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SpecialParamListType;

use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Undo;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\UpdateColumnMessageValueInBatch;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\UpdateMessage;


/**
 * Class NOUTClient
 * @package NOUT\Bundle\NOUTOnlineBundle\Service
 */
class NOUTClientMessagerie extends NOUTClientBase
{

    /**
     * @param XMLResponseWS $clReponseXML
     * @param ActionResult  $clActionResult
     * @param string        $ReturnType
     * @param string        $idForm
     * @throws \Exception
     */
    protected function __oGetActionResultFromXMLResponse(XMLResponseWS $clReponseXML, ActionResult $clActionResult, string $ReturnType, string $idForm)
    {
        $aPtrFct = array(
            XMLResponseWS::VIRTUALRETURNTYPE_MAILSERVICERECORD_PJ => function() use($clReponseXML, $clActionResult) { $this->_oGetPJ($clReponseXML, $clActionResult); },
        );

        if (!array_key_exists($ReturnType, $aPtrFct)){
            parent::__GetActionResultFromXMLResponse($clReponseXML, $clActionResult, $ReturnType, $idForm);
            return ;
        }

        $fct = $aPtrFct[$ReturnType];
        if (!is_null($fct)){
            //on applique la fonction
            $fct();
        }
    }

    /**
     * @param XMLResponseWS $clReponseXML
     * @param ActionResult  $clActionResult
     */
    protected function _oGetPJ(XMLResponseWS $clReponseXML, ActionResult $clActionResult)
    {
        $clData = $clReponseXML->getFile();
        $clActionResult->setData($clData);
    }

    /**
     * @param XMLResponseWS $clReponseXML
     * @param ActionResult  $clActionResult
     * @param string        $idForm
     * @throws \Exception
     */
    protected function _oGetList(XMLResponseWS $clReponseXML, ActionResult $clActionResult, ?string $idForm)
    {
        parent::_oGetList($clReponseXML, $clActionResult, $idForm);

        $clFolderCount = $clReponseXML->clGetFolderCount();
        if ($clFolderCount){
            $clActionResult->setFolderCount($clFolderCount);
        }
    }

    /**
     * @param array|null $requestHeaders
     * @param array $requestParams
     * @return ActionResult
     * @throws \Exception
     */
    public function oGetFolderList(array $requestParams, ?array $requestHeaders=null) : ActionResult
    {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($requestHeaders);

        $clReponseXML = $this->m_clSOAPProxy->getFolderList($this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML, Langage::TABL_Messagerie_Dossier);
    }

    /**
     * @param array      $requestParams
     * @param array|null $requestHeaders
     * @param string     $folderID
     * @return ActionResult
     * @throws \Exception
     */
    public function oGetFolderContent(string $folderID, array $requestParams, ?array $requestHeaders=null): ActionResult
    {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($requestHeaders);

        $folderContent = new GetContentFolder();
        $folderContent->SpecialParamList = $requestParams[self::PARAM_SPECIALPARAMLIST];
        $folderContent->IDFolder = $folderID;

        $clReponseXML = $this->m_clSOAPProxy->getContentFolder($folderContent, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML, Langage::TABL_Messagerie_Message);
    }

    /**
     * @param array      $requestParams
     * @param array|null $requestHeaders
     * @return ActionResult
     * @throws \Exception
     */
    public function oGetMessageRequest(array $requestParams, ?array $requestHeaders=null) : ActionResult
    {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($requestHeaders);

        $requestMessage = new RequestMessage();
        $requestMessage->SpecialParamList = $requestParams[self::PARAM_SPECIALPARAMLIST];
        $requestMessage->StartDate = $requestParams[self::PARAMMESS_StartDate];
        $requestMessage->EndDate = $requestParams[self::PARAMMESS_EndDate];
        $requestMessage->Filter = $requestParams[self::PARAMMESS_Filter];

        $clReponseXML = $this->m_clSOAPProxy->getRequestMessage($requestMessage, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML, Langage::TABL_Messagerie_Message);
    }

    /**
     * @param string $idmessage
     * @param int    $autovalidate
     * @param array  $updateData
     * @return ActionResult
     * @throws \Exception
     */
    public function oUpdateMessage(string $idmessage, array $updateData, int $autovalidate = SOAPProxy::AUTOVALIDATE_None) : ActionResult
    {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl([], null, $autovalidate);
        //$aTabHeaderSuppl[SOAPProxy::HEADER_OptionDialogue]->{SOAPProxy::HEADER_OptionDialogue_ListContentAsync} = 0;

        $updateMessage = new UpdateMessage();
        $updateMessage->IDMessage=$idmessage;
        $updateMessage->UpdateData = ParametersManagement::s_sStringifyUpdateData(Langage::TABL_Messagerie_Message, $updateData);

        $clReponseXML = $this->m_clSOAPProxy->updateMessage($updateMessage, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML, Langage::TABL_Messagerie_Message);
    }

    /**
     * @param array|null $requestHeaders
     * @param string     $messages
     * @param string     $column
     * @param string     $value
     * @param int        $autovalidate
     * @return ActionResult
     * @throws \Exception
     */
    public function oUpdateMessages(string $messages, string $column, string $value, int $autovalidate, ?array $requestHeaders=null) : ActionResult
    {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($requestHeaders, null, $autovalidate);

        $updateMessages = new UpdateColumnMessageValueInBatch();
        $updateMessages->IDMessage = $messages;
        $updateMessages->Column = $column;
        $updateMessages->Value = $value;

        $clReponseXML =  $this->m_clSOAPProxy->updateMessages($updateMessages, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML, Langage::TABL_Messagerie_Message);
    }

    /**
     * @param array|null $requestHeaders
     * @param string     $type
     * @param string     $originalMessage
     * @param string     $templateId
     * @return ActionResult
     * @throws \Exception
     */
    public function oCreateMessage(string $type, string $originalMessage, string $templateId, ?array $requestHeaders=null) : ActionResult
    {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($requestHeaders);

        $message = new CreateMessage();
        $message->CreateType = $type;
        $message->IDAnswerType = $templateId;
        if($originalMessage !== 'undefined')
            $message->IDMessage = $originalMessage;

        $clReponseXML=$this->m_clSOAPProxy->createMessage($message, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML, Langage::TABL_Messagerie_Message);
    }

    /**
     * @return ActionResult
     * @throws \Exception
     */
    public function oGetReplyTemplates() : ActionResult
    {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl(null, null, SOAPProxy::AUTOVALIDATE_Cancel);

        $specialParamList = new SpecialParamListType();
        $specialParamList->First = 0;
        $specialParamList->Length = 200;
        $specialParamList->WithEndCalculation = 0;

        $execaction = new Execute();
        $execaction->ID = Langage::ACTION_RechercherReponseType;
        $execaction->SpecialParamList = $specialParamList;

        $clReponseXML = $this->m_clSOAPProxy->execute($execaction, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @return \stdClass
     * @throws \Exception
     */
    public function oGetUndoRedoInitFilter() : \stdClass
    {
        $oUndoRedoFilter = $this->fetchFromCache(NOUTClientCache::CACHE_IHM, "undoredo_filter");
        if (isset($oUndoRedoFilter) && ($oUndoRedoFilter !== false)){
            return $oUndoRedoFilter; //on a déjà les infos, elles sont fixe par rapport au langage
        }
        //apiuser et on annule tout de suite
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl([SOAPProxy::HEADER_APIUser => 1], null, SOAPProxy::AUTOVALIDATE_Cancel);

        //il faut construire le résultat,

        // on commence par la liste des types d'action
        $listTypeAction = new Execute();
        $listTypeAction->ID = Langage::ACTION_ListeChoix;
        $listTypeAction->ParamXML='<id_'.Langage::PA_ListeChoix_Modele.'>'.Langage::MT_TypeDAction.'</id_'.Langage::PA_ListeChoix_Modele.'>';


        $clReponseXML = $this->m_clSOAPProxy->execute($listTypeAction, $this->_aGetTabHeader($aTabHeaderSuppl));
        $oRetTypeAction = $this->_oGetActionResultFromXMLResponse($clReponseXML);


        //on fait aussi la liste des formulaires
        $listFormulaire = new Execute();
        $listFormulaire->ID = Langage::ACTION_ListeFormulaire;
        $listFormulaire->ParamXML='<id_'.Langage::PA_ListeFormulaire_SousModule.'></id_'.Langage::PA_ListeFormulaire_SousModule.'>';


        $clReponseXML = $this->m_clSOAPProxy->execute($listFormulaire, $this->_aGetTabHeader($aTabHeaderSuppl));
        $oRetFormulaire = $this->_oGetActionResultFromXMLResponse($clReponseXML);

        $oRet = new \stdClass();

        /** @var RecordList $clListTypeAction */
        $clListTypeAction = $oRetTypeAction->getData();
        $oRet->typeActions = $clListTypeAction->toIDTitleArray();

        /** @var RecordList $clListFormulaire */
        $clListFormulaire = $oRetFormulaire->getData();
        $oRet->forms = $clListFormulaire->toIDTitleArray();

        //on sauve en cache
        $this->_saveInCache(NOUTClientCache::CACHE_IHM, "undoredo_filter", $oRet);

        return $oRet;
    }


    /**
     * @return \stdClass
     * @throws \Exception
     */
    public function oGetUndoRedoUserList() : \stdClass
    {
        //apiuser et on annule tout de suite
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl([SOAPProxy::HEADER_APIUser => 1], null, SOAPProxy::AUTOVALIDATE_Cancel);

        //il faut construire le résultat,

        // on commence par la liste des types d'action
        $listUtilisateur = new Execute();
        $listUtilisateur->ID = Langage::ACTION_ListeUtilisateur;

        $clReponseXML = $this->m_clSOAPProxy->execute($listUtilisateur, $this->_aGetTabHeader($aTabHeaderSuppl));
        $oRetUtilisateur = $this->_oGetActionResultFromXMLResponse($clReponseXML);

        $oRet = new \stdClass();

        /** @var RecordList $clListUtilisateur */
        $clListUtilisateur = $oRetUtilisateur->getData();
        $oRet->users = $clListUtilisateur->toIDTitleArray();

        return $oRet;
    }


    /**
     * @param string     $messageID
     * @param array|null $requestParams
     * @param array|null $requestHeaders
     * @return ActionResult
     * @throws \Exception
     */
    public function oReadMessage(string $messageID, ?array $requestParams, ?array $requestHeaders=null) :ActionResult
    {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($requestHeaders);

        //$asyncProp = SOAPProxy::HEADER_OptionDialogue_ListContentAsync;
        //$aTabHeaderSuppl[SOAPProxy::HEADER_OptionDialogue]->$asyncProp = 0;

        $message = new ModifyMessage();
        $message->IDMessage = $messageID;
        $clReponseXML = $this->m_clSOAPProxy->modifyMessage($message, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML, Langage::TABL_Messagerie_Message);
    }

    /**
     * @param array|null $requestHeaders
     * @param string     $messageID
     * @return ActionResult
     * @throws \Exception
     */
    public function oSendMessage(string $messageID, ?array $requestHeaders=null) : ActionResult
    {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($requestHeaders);

        //$asyncProp = SOAPProxy::HEADER_OptionDialogue_ListContentAsync;
        //$aTabHeaderSuppl[SOAPProxy::HEADER_OptionDialogue]->$asyncProp = 0;

        $message = new SendMessage();
        $message->IDMessage = $messageID;
        $clReponseXML = $this->m_clSOAPProxy->sendMessage($message, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML, Langage::TABL_Messagerie_Message);
    }

    /**
     * @param array|null $requestHeaders
     * @param string     $messageId
     * @param string     $attachmentId
     * @return ActionResult
     * @throws \Exception
     */
    public function oGetAttachment(string $messageId, string $attachmentId, ?array $requestHeaders=null) : ActionResult
    {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($requestHeaders);

        $getPJ = new GetPJ();
        $getPJ->IDMessage = $messageId;
        $getPJ->IDPJ = $attachmentId;

        $clReponseXML =  $this->m_clSOAPProxy->getPJ($getPJ, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML, null, XMLResponseWS::VIRTUALRETURNTYPE_MAILSERVICERECORD_PJ);
    }

    /**
     * @param string $messageId
     * @return HTTPResponse
     * @throws \Exception
     */
    public function oPrintMessage(string $messageId) : HTTPResponse
    {
        $clIdentification = $this->_clGetIdentificationREST(null, false);
        return $this->m_clRESTProxy->oPrintMessage($messageId, $clIdentification);
    }

    /**
     * @param array|null $requestHeaders
     * @param DataPJType $PJType
     * @param string     $messageId
     * @return ActionResult
     * @throws \Exception
     */
    public function oAddAttachment(string $messageId, DataPJType $PJType, ?array $requestHeaders=null) : ActionResult
    {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($requestHeaders);

        $addPJ = new AddPJ();
        $addPJ->IDMessage = $messageId;
        $addPJ->DataPJ = $PJType;

        $clReponseXML = $this->m_clSOAPProxy->addPJ($addPJ, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML, Langage::TABL_Messagerie_Message);
    }

    /**
     * @param array|null $requestHeaders
     * @param string     $messageId
     * @param string     $attachmentId
     * @return ActionResult
     * @throws \Exception
     */
    public function oDeleteAttachment(string $messageId, string $attachmentId, ?array $requestHeaders=null) : ActionResult
    {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($requestHeaders);

        $deletePJ = new DeletePJ();
        $deletePJ->IDMessage = $messageId;
        $deletePJ->IDPJ = $attachmentId;

        $clReponseXML = $this->m_clSOAPProxy->deletePj($deletePJ, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML, Langage::TABL_Messagerie_Message);
    }

    /**
     * @param string $compteID
     * @return string
     * @throws \Exception
     */
    public function sGetSignature(string $compteID) : string
    {
        $clIdentification = $this->_clGetIdentificationREST('', false);

        $aTabParam=[];
        $aTabOption=[];

        $oRetHTTP = $this->m_clRESTProxy->oGetColInRecord(Langage::TABL_CompteEmail, $compteID, Langage::COL_COMPTEEMAIL_Signature, $aTabParam, $aTabOption, $clIdentification);

        return $oRetHTTP->content;
    }

    /**
     * @param string $compteID
     * @param string $sType
     * @param bool   $withOriginalMessage
     * @return bool
     * @throws \Exception
     */
    public function bGetSiAjouteSignature(string $compteID, string $sType, bool $withOriginalMessage) : bool
    {
        if ($sType == CreateMessage::CREATE_TYPE_ANSWER_TYPE){
            return false; //par défaut on ajoute pas la signature sur un reponse type
        }

        $nIDCol=(($sType==CreateMessage::CREATE_TYPE_EMPTY) || (($sType==CreateMessage::CREATE_TYPE_ANSWER_TYPE) && !$withOriginalMessage))
            ? Langage::COL_COMPTEEMAIL_SignatureNouveau
            : Langage::COL_COMPTEEMAIL_SignatureRepondre;

        $clIdentification = $this->_clGetIdentificationREST('', false);

        $aTabParam=[];
        $aTabOption=['displayvalue' => 0];

        $oRetHTTP = $this->m_clRESTProxy->oGetColInRecord(Langage::TABL_CompteEmail, $compteID, $nIDCol, $aTabParam, $aTabOption, $clIdentification);
        $sRes= $oRetHTTP->content;

        return ($sRes==="Oui") || ($sRes==="Vrai") || intval($sRes) <> 0;
    }

    /**
     * @param string       $messageId
     * @param string       $attachmentId
     * @param NOUTFileInfo $pj
     * @return ActionResult
     */
    public function savePJInCache(string $messageId, string $attachmentId, NOUTFileInfo $pj) : ActionResult
    {
        $name = $this->m_clCache->saveMessagePJ($messageId, $attachmentId, $pj);

        $clActionResult = new ActionResult(null);
        $clActionResult->setData($name);

        //gestion du cache
        $clActionResult->setTypeCache(ActionResultCache::TYPECACHE_Private);

        return $clActionResult;
    }

    /**
     * @param string $messageId
     * @param string $attachmentId
     * @return ActionResult
     */
    public function getPJInCache(string $messageId, string $attachmentId): ActionResult
    {
        $data = $this->m_clCache->fetchMessagePJ($messageId, $attachmentId);

        $clActionResult = new ActionResult(null);
        $clActionResult->setData($data);

        //gestion du cache
        $clActionResult->setTypeCache(ActionResultCache::TYPECACHE_Private);
        return $clActionResult;
    }

    /**
     * @param array      $requestParams
     * @param array|null $requestHeaders
     * @return ActionResult
     * @throws \Exception
     */
    public function oGetUndoList(array $requestParams, ?array $requestHeaders=null) : ActionResult
    {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($requestHeaders);

        $getUndoListParam = new GetUndoList();
        $getUndoListParam->SpecialParamList = $requestParams[self::PARAM_SPECIALPARAMLIST];
        $getUndoListParam->StartDate = $requestParams[self::PARAMMESS_StartDate];
        $getUndoListParam->EndDate = $requestParams[self::PARAMMESS_EndDate];
        $getUndoListParam->ActionType = '';
        $getUndoListParam->DoneBy = '';
        $getUndoListParam->Form = '';
        $getUndoListParam->OtherCriteria = '';

        $clReponseXML = $this->m_clSOAPProxy->getUndoList($getUndoListParam, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML, Langage::TABL_Messagerie_Message);
    }


    /**
     * @param array      $requestParams
     * @param array|null $requestHeaders
     * @return ActionResult
     * @throws \Exception
     */
    public function oGetRedoList(array $requestParams, ?array $requestHeaders=null) : ActionResult
    {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($requestHeaders);

        $getRedoListParam = new GetRedoList();
        $getRedoListParam->SpecialParamList = $requestParams[self::PARAM_SPECIALPARAMLIST];
        $getRedoListParam->StartDate = $requestParams[self::PARAMMESS_StartDate];
        $getRedoListParam->EndDate = $requestParams[self::PARAMMESS_EndDate];
        $getRedoListParam->ActionType = '';
        $getRedoListParam->DoneBy = '';
        $getRedoListParam->Form = '';
        $getRedoListParam->OtherCriteria = '';

        $clReponseXML = $this->m_clSOAPProxy->getRedoList($getRedoListParam, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML, Langage::TABL_Messagerie_Message);
    }

    /**
     * @param string     $idMessage
     * @param array|null $requestHeaders
     * @return ActionResult
     * @throws \Exception
     */
    public function oDisplayUndoMessage(string $idMessage, ?array $requestHeaders=null) : ActionResult
    {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($requestHeaders);
        $param = new DisplayUndoMessage();
        $param->IDMessage = $idMessage;

        $clReponseXML = $this->m_clSOAPProxy->displayUndoMessage($param, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML, Langage::TABL_Messagerie_Message);
    }


    /**
     * @param string     $idMessage
     * @param array|null $requestHeaders
     * @return ActionResult
     * @throws \Exception
     */
    public function oDisplayRedoMessage(string $idMessage, ?array $requestHeaders=null) : ActionResult
    {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($requestHeaders);
        $param = new DisplayRedoMessage();
        $param->IDMessage = $idMessage;

        $clReponseXML = $this->m_clSOAPProxy->displayRedoMessage($param, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML, Langage::TABL_Messagerie_Message);
    }

    /**
     * @param string     $idMessage
     * @param array|null $requestHeaders
     * @return ActionResult
     * @throws \Exception
     */
    public function oUndo(string $idMessage, ?array $requestHeaders=null) : ActionResult
    {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($requestHeaders);
        $param = new Undo();
        $param->IDMessage = $idMessage;

        $clReponseXML = $this->m_clSOAPProxy->undo($param, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML, Langage::TABL_Messagerie_Message);
    }

    /**
     * @param string     $idMessage
     * @param array|null $requestHeaders
     * @return ActionResult
     * @throws \Exception
     */
    public function oRedo(string $idMessage, ?array $requestHeaders=null) : ActionResult
    {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($requestHeaders);
        $param = new Redo();
        $param->IDMessage = $idMessage;

        $clReponseXML = $this->m_clSOAPProxy->redo($param, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML, Langage::TABL_Messagerie_Message);
    }

    const PARAMMESS_StartDate   = 'StartDate';
    const PARAMMESS_EndDate     = 'EndDate';
    const PARAMMESS_Filter      = 'Filter';
}