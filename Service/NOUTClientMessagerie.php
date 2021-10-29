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
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS;
use NOUT\Bundle\NOUTOnlineBundle\REST\HTTPResponse;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\OnlineServiceProxy as SOAPProxy;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\AddPJ;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\CreateMessage;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\DataPJType;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\DeletePJ;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Execute;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetContentFolder;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetPJ;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ModifyMessage;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\RequestMessage;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SendMessage;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SpecialParamListType;

use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\UpdateColumnMessageValueInBatch;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\UpdateMessage;


/**
 * Class NOUTClient
 * @package NOUT\Bundle\NOUTOnlineBundle\Service
 */
class NOUTClientMessagerie extends NOUTClientBase
{

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
}