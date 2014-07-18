<?php
namespace NOUT\Bundle\NOUTOnlineBundle\SOAP;

/**
 * Classe finale permettant la consomation du service web de simaxOnline de facon simplifié.
 * elle utilise la classe ModifiedNuSoapClient : ModifiedNuSoapClient.php afin de permettre une connexion simple en
 * document litteral et avec gestion des erreur soap au format 1.2
 *
 * Note : cette classe utilise de nombreuses classe secondaire, permettant de definir le format d'echange simplement.
 *
 * pour plus d'info
 * @see ModifiedNuSoapClient
 *
 * @final
 */
final class SimaxOnlineServiceProxy extends ModifiedNuSoapClient
{

    //Definition des variable pour gestion des headers de requete
    private $__aListHeaders = array();
    private $__bCleanHeadersBeforeRequest = true; //sert a savoir si on remet les headers a zero avant une requete


    /**
     * constructeur permettant d'instancier les classe de communication soap avec les bonne question
     * @param $sEndpoint
     * @param $bWsdl
     * @param $sProxyHost
     * @param $sProxyPort
     * @return unknown_type
     */
    public function SimaxOnlineServiceProxy($sEndpoint,$bWsdl = false,$sProxyHost = false,$sProxyPort = false, $sProtocolPrefix = 'http://')
    {
        parent::ModifiedNuSoapClient($sEndpoint,$bWsdl,$sProxyHost,$sProxyPort);
        $this->forceEndpoint = $sProtocolPrefix . $sProxyHost . ':' . $sProxyPort; //on force l'ip et le port du fichier config
        // on force le timeout a 300s
        $this->timeout = 300;
        $this->response_timeout = 300;
    }
    //---

    //------------------------------------------------------------------------------------------
    // Fonctions de gestion des headers de requete :
    //------------------------------------------------------------------------------------------

    /**
     * par default, les header sont nettoyé  avant la contstruction de la requete, suite a l'appel de cette fonction il ne le sont plus
     * @param void
     * @return void
     * @access public
     */
    public function desactiveAutoCleanHeadersBeforeRequest()
    {
        $this->__bCleanHeadersBeforeRequest = false;
    }
    //---

    /**
     * reactive le nettoyage automatique des headers avant requete.
     * @return unknown_type
     */
    public function reactivateAutoCleanHeadersBeforeRequest()
    {
        $this->__bCleanHeadersBeforeRequest = true;
    }
    //---

    /**
     * fonction permettant de remettre la liste des headers a vide
     *
     * @param void
     * @return void
     * @access public
     */
    public function cleanListHeaders()
    {
        $this->__aListHeaders = array();
    }
    //---

    /**
     * fonction permettant d'ajouter plusieur headers de requete à la fois, grace a un tableau
     * @param array $aHeaders la liste des headers a ajouter sous force de tableau associatif (nom header => valeur)
     * @return void
     * @access public
     */
    public function addMultipleHeaders($aHeaders)
    {
        if(is_array($aHeaders))
        {
            foreach ($aHeaders as $sKey => $mValue)
            {
                $this->__aListHeaders[$sKey] = $mValue;
            }
        }
    }
    //---


    /**
     * fonction permettant d'ajouter un header de requete
     * @param string $sName le nom du header a ajouter
     * @param mixed $mValue la valeur que l'on souhaite ajouter au header.
     * @return void
     * @access public
     */
    public function addHeader($sName = '', $mValue = '')
    {
        $this->__aListHeaders[$sName] = $mValue;
    }
    //---

    //------------------------------------------------------------------------------------------
    // Redefinition methode call
    //------------------------------------------------------------------------------------------

    /**
     * redefinition de la methode call de maniére a gérer les headers obligatoire de la communication
     * avec le service simax de simaxOnline
     *
     * @see core/soap/ModifiedNuSoapClient#call($sOperation, $mParams, $mHeaders)
     *
     * @access public
     *
     * //note : $sStyle et $sUse sont des parametre inutile il ne sont la que pour permettre la surchage de methode sans modification de la signature.
     */
    public function call($sOperation, $mParams = array(),$sNamespace=null,$sSoapAction=null , $mHeaders = false,$mRpcParams=null,$sStyle='rpc',$sUse='encoded')
    {
        //si il le faut, avant toutes chose, on nettoye les header
        if($this->__bCleanHeadersBeforeRequest )
        {
            $this->cleanListHeaders();
        }

        //on rajoute les header
        $this->addMultipleHeaders($mHeaders);

        //on ajoute le tokenSessin + info de connexion dans tous les cas sauf dans le cas d'un getTokenSession
        if($sOperation != 'GetTokenSession')
        {
            $this->addMultipleHeaders( getUsernameTokenFromSession() );


            $aSessionToken=getSessionToken();
            if (is_array($aSessionToken))
            {
                $this->addMultipleHeaders($aSessionToken);
            }
        }

        //si le la partie optiondialogue du header n'est pas passer en param on la cr��
        if( ! isset($this->__aListHeaders['OptionDialogue']) )
        {
            $this->__aListHeaders['OptionDialogue'] = array('Readable'=>false);
        }
        if(!isset($this->__aListHeaders['OptionDialogue']['DisplayValue']))
        {
            $this->__aListHeaders['OptionDialogue']['DisplayValue'] = FORMHEAD_UNDECODED_SPECIAL_ELEM;
        }

        //Si on a pas encore d'encodingType, on le met a 0
        if(
            !isset($this->__aListHeaders['OptionDialogue']['EncodingOutput']) ||
            is_null($this->__aListHeaders['OptionDialogue']['EncodingOutput']) ||
            $this->__aListHeaders['OptionDialogue']['EncodingOutput'] == ''
        )
        {
            $this->__aListHeaders['OptionDialogue']['EncodingOutput'] = 0;
        }


        //on ajoute le bon code langue.
        if(
            !isset($this->__aListHeaders['OptionDialogue']['LanguageCode']) ||
            is_null($this->__aListHeaders['OptionDialogue']['LanguageCode']) ||
            $this->__aListHeaders['OptionDialogue']['LanguageCode'] == ''
        )
        {
            $iLocalDefiner = clLocalDefiner::getInstance();
            $this->__aListHeaders['OptionDialogue']['LanguageCode'] = $iLocalDefiner->getLangCode();
        }

        //si on a pas de withFieldStateControl precis�, on le mets a� 1 (pour recuperer les controle d'etat de champ)
        if(
            !isset($this->__aListHeaders['OptionDialogue']['WithFieldStateControl']) ||
            is_null($headers['OptionDialogue']['WithFieldStateControl']) ||
            $headers['OptionDialogue']['WithFieldStateControl'] == ''
        )
        {
            $this->__aListHeaders['OptionDialogue']['WithFieldStateControl'] = 1;
        }


        $clConfigManager = clConfigFileManager::getInstance();

        //on ajoute l'id application
        $this->__aListHeaders['APIUUID'] = $clConfigManager->oGlobalConfig->getValue(CStaticConfigFileIdGest::CONFIG_OPT_SERVICE_APPLI_ID);

        //on fait l'appel a la methode mere
        $mResult =  parent::call($sOperation, $mParams, $sNamespace, $sSoapAction, $this->__aListHeaders, $mRpcParams , null, null);

        return $mResult;
    }
    //---



    //------------------------------------------------------------------------------------------
    // Fonction d'appel  direct soap
    //------------------------------------------------------------------------------------------

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetTokenSession
     *
     * @param CWsdlType_GetTokenSession $clWsdlType_GetTokenSession
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_GetTokenSessionResponse
     * @access public
     */
    public function fSoapAction_GetTokenSession(CWsdlType_GetTokenSession $clWsdlType_GetTokenSession, $aHeaders = array())
    {
        $clWsdlType_GetTokenSessionResponse = $this->call('GetTokenSession', array($clWsdlType_GetTokenSession) , null, null , $aHeaders);

        if($this->fault)
        {
            return setError($this->getError());
        }
        if($err = $this->getError())
        {
            return setError($err);
        }

        return $clWsdlType_GetTokenSessionResponse;
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetLanguages
     *
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_GetLanguagesResponse
     * @access public
     */
    public function fSoapAction_GetLanguages()
    {
        $clWsdlType_GetLanguagesResponse = $this->call('GetLanguages', array() ,  null, null , $aHeaders);

        return $clWsdlType_GetLanguagesResponse;
    }


    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : ResetPasswordFailed
     *
     * @param CWsdlType_ResetPasswordFailed $clWsdlType_ResetPasswordFailed
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_ResetPasswordFailedResponse
     * @access public
     */
    public function fSoapAction_ResetPasswordFailed(CWsdlType_ResetPasswordFailed $clWsdlType_ResetPasswordFailed, $aHeaders = array())
    {
        $clWsdlType_ResetPasswordFailedResponse = $this->call('ResetPasswordFailed', array($clWsdlType_ResetPasswordFailed) ,  null, null , $aHeaders);

        return $clWsdlType_ResetPasswordFailedResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetStartAutomatism
     *
     * @param CWsdlType_GetStartAutomatism $clWsdlType_GetStartAutomatism
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_GetStartAutomatismResponse
     * @access public
     */
    public function fSoapAction_GetStartAutomatism(CWsdlType_GetStartAutomatism $clWsdlType_GetStartAutomatism, $aHeaders = array())
    {
        $clWsdlType_GetStartAutomatismResponse = $this->call('GetStartAutomatism', array($clWsdlType_GetStartAutomatism) ,  null, null , $aHeaders);

        return $clWsdlType_GetStartAutomatismResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : ConfirmResponse
     *
     * @param CWsdlType_ConfirmResponse $clWsdlType_ConfirmResponse
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_ConfirmResponseResponse
     * @access public
     */
    public function fSoapAction_ConfirmResponse(CWsdlType_ConfirmResponse $clWsdlType_ConfirmResponse, $aHeaders = array())
    {
        $clWsdlType_ConfirmResponseResponse = $this->call('ConfirmResponse', array($clWsdlType_ConfirmResponse) ,  null, null , $aHeaders);

        return $clWsdlType_ConfirmResponseResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : HasChanged
     *
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_HasChangedResponse
     * @access public
     */
    public function fSoapAction_HasChanged($aHeaders = array())
    {
        $clWsdlType_HasChangedResponse = $this->call('HasChanged', array() ,  null, null , $aHeaders);

        return $clWsdlType_HasChangedResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : SelectForm
     *
     * @param CWsdlType_SelectForm $clWsdlType_SelectForm
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_SelectFormResponse
     * @access public
     */
    public function fSoapAction_SelectForm(CWsdlType_SelectForm $clWsdlType_SelectForm, $aHeaders = array())
    {
        $clWsdlType_SelectFormResponse = $this->call('SelectForm', array($clWsdlType_SelectForm) ,  null, null , $aHeaders);

        return $clWsdlType_SelectFormResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : SelectPrintTemplate
     *
     * @param CWsdlType_SelectPrintTemplate $clWsdlType_SelectPrintTemplate
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_SelectPrintTemplateResponse
     * @access public
     */
    public function fSoapAction_SelectPrintTemplate(CWsdlType_SelectPrintTemplate $clWsdlType_SelectPrintTemplate, $aHeaders = array())
    {
        $clWsdlType_SelectPrintTemplateResponse = $this->call('SelectPrintTemplate', array($clWsdlType_SelectPrintTemplate) ,  null, null , $aHeaders);

        return $clWsdlType_SelectPrintTemplateResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetPlanningInfo
     *
     * @param CWsdlType_GetPlanningInfo $clWsdlType_GetPlanningInfo
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_GetPlanningInfoResponse
     * @access public
     */
    public function fSoapAction_GetPlanningInfo(CWsdlType_GetPlanningInfo $clWsdlType_GetPlanningInfo, $aHeaders = array())
    {
        $clWsdlType_GetPlanningInfoResponse = $this->call('GetPlanningInfo', array($clWsdlType_GetPlanningInfo) ,  null, null , $aHeaders);

        return $clWsdlType_GetPlanningInfoResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetColInRecord
     *
     * @param CWsdlType_GetColInRecord $clWsdlType_GetColInRecord
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_GetColInRecordResponse
     * @access public
     */
    public function fSoapAction_GetColInRecord(CWsdlType_GetColInRecord $clWsdlType_GetColInRecord, $aHeaders = array())
    {
        $clWsdlType_GetColInRecordResponse = $this->call('GetColInRecord', array($clWsdlType_GetColInRecord) ,  null, null , $aHeaders);

        return $clWsdlType_GetColInRecordResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : Display
     *
     * @param CWsdlType_Display $clWsdlType_Display
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_DisplayResponse
     * @access public
     */
    public function fSoapAction_Display(CWsdlType_Display $clWsdlType_Display, $aHeaders = array())
    {
        $clWsdlType_DisplayResponse = $this->call('Display', array($clWsdlType_Display) ,  null, null , $aHeaders);

        return $clWsdlType_DisplayResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : Create
     *
     * @param CWsdlType_Create $clWsdlType_Create
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_CreateResponse
     * @access public
     */
    public function fSoapAction_Create(CWsdlType_Create $clWsdlType_Create, $aHeaders = array())
    {
        $clWsdlType_CreateResponse = $this->call('Create', array($clWsdlType_Create) ,  null, null , $aHeaders);

        return $clWsdlType_CreateResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : CreateFrom
     *
     * @param CWsdlType_CreateFrom $clWsdlType_CreateFrom
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_CreateFromResponse
     * @access public
     */
    public function fSoapAction_CreateFrom(CWsdlType_CreateFrom $clWsdlType_CreateFrom, $aHeaders = array())
    {
        $clWsdlType_CreateFromResponse = $this->call('CreateFrom', array($clWsdlType_CreateFrom) ,  null, null , $aHeaders);

        return $clWsdlType_CreateFromResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : TransformInto
     *
     * @param CWsdlType_TransformInto $clWsdlType_TransformInto
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_TransformIntoResponse
     * @access public
     */
    public function fSoapAction_TransformInto(CWsdlType_TransformInto $clWsdlType_TransformInto, $aHeaders = array())
    {
        $clWsdlType_TransformIntoResponse = $this->call('TransformInto', array($clWsdlType_TransformInto) ,  null, null , $aHeaders);

        return $clWsdlType_TransformIntoResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : Modify
     *
     * @param CWsdlType_Modify $clWsdlType_Modify
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_ModifyResponse
     * @access public
     */
    public function fSoapAction_Modify(CWsdlType_Modify $clWsdlType_Modify, $aHeaders = array())
    {
        $clWsdlType_ModifyResponse = $this->call('Modify', array($clWsdlType_Modify) ,  null, null , $aHeaders);

        return $clWsdlType_ModifyResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : Update
     *
     * @param CWsdlType_Update $clWsdlType_Update
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_UpdateResponse
     * @access public
     */
    public function fSoapAction_Update(CWsdlType_Update $clWsdlType_Update, $aHeaders = array())
    {
        $clWsdlType_UpdateResponse = $this->call('Update', array($clWsdlType_Update) ,  null, null , $aHeaders);

        return $clWsdlType_UpdateResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : Print
     *
     * @param CWsdlType_Print $clWsdlType_Print
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_PrintResponse
     * @access public
     */
    public function fSoapAction_Print(CWsdlType_Print $clWsdlType_Print, $aHeaders = array())
    {
        $clWsdlType_PrintResponse = $this->call('Print', array($clWsdlType_Print) ,  null, null , $aHeaders);

        return $clWsdlType_PrintResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : Delete
     *
     * @param CWsdlType_Delete $clWsdlType_Delete
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_DeleteResponse
     * @access public
     */
    public function fSoapAction_Delete(CWsdlType_Delete $clWsdlType_Delete, $aHeaders = array())
    {
        $clWsdlType_DeleteResponse = $this->call('Delete', array($clWsdlType_Delete) ,  null, null , $aHeaders);

        return $clWsdlType_DeleteResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : Execute
     *
     * @param CWsdlType_Execute $clWsdlType_Execute
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_ExecuteResponse
     * @access public
     */
    public function fSoapAction_Execute(CWsdlType_Execute $clWsdlType_Execute, $aHeaders = array())
    {
        $clWsdlType_ExecuteResponse = $this->call('Execute', array($clWsdlType_Execute) ,  null, null , $aHeaders);

        return $clWsdlType_ExecuteResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : Validate
     *
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_ValidateResponse
     * @access public
     */
    public function fSoapAction_Validate($aHeaders = array())
    {
        $clWsdlType_ValidateResponse = $this->call('Validate', array() ,  null, null , $aHeaders);

        return $clWsdlType_ValidateResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline :Cancel
     *
     * @param CWsdlType_Cancel $clWsdlType_Cancel
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_CancelResponse
     * @access public
     */
    public function fSoapAction_Cancel(CWsdlType_Cancel $clWsdlType_Cancel, $aHeaders = array())
    {
        $clWsdlType_CancelResponse = $this->call('Cancel', array($clWsdlType_Cancel) ,  null, null , $aHeaders);

        return $clWsdlType_CancelResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : List
     *
     * @param CWsdlType_List $clWsdlType_List
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_ListResponse
     * @access public
     */
    public function fSoapAction_List(CWsdlType_List $clWsdlType_List, $aHeaders = array())
    {
        $clWsdlType_ListResponse = $this->call('List', array($clWsdlType_List) ,  null, null , $aHeaders);

        return $clWsdlType_ListResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetChart
     *
     * @param CWsdlType_GetChart $clWsdlType_GetChart
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_GetChartResponse
     * @access public
     */
    public function fSoapAction_GetChart(CWsdlType_GetChart  $clWsdlType_GetChart, $aHeaders = array())
    {
        $clWsdlType_GetChartResponse = $this->call('GetChart', array($clWsdlType_GetChart) ,  null, null , $aHeaders);

        return $clWsdlType_GetChartResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetCalculation
     *
     * @param CWsdlType_GetCalculation $clWsdlType_GetCalculation
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_GetCalculationResponse
     * @access public
     */
    public function fSoapAction_GetCalculation(CWsdlType_GetCalculation $clWsdlType_GetCalculation, $aHeaders = array())
    {
        $clWsdlType_GetCalculationResponse = $this->call('GetCalculation', array($clWsdlType_GetCalculation) ,  null, null , $aHeaders);

        return $clWsdlType_GetCalculationResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : Search
     *
     * @param CWsdlType_Search $clWsdlType_Search
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_SearchResponse
     * @access public
     */
    public function fSoapAction_Search(CWsdlType_Search $clWsdlType_Search, $aHeaders = array())
    {
        $clWsdlType_SearchResponse = $this->call('Search', array($clWsdlType_Search) ,  null, null , $aHeaders);

        return $clWsdlType_SearchResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : Request
     *
     * @param CWsdlType_Request $clWsdlType_Request
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_RequestResponse
     * @access public
     */
    public function fSoapAction_Request(CWsdlType_Request $clWsdlType_Request, $aHeaders = array())
    {
        $clWsdlType_RequestResponse = $this->call('Request', array($clWsdlType_Request) ,  null, null , $aHeaders);

        return $clWsdlType_RequestResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : RequestParam
     *
     * @param CWsdlType_RequestParam $clWsdlType_RequestParam
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_RequestParamResponse
     * @access public
     */
    public function fSoapAction_RequestParam(CWsdlType_RequestParam $clWsdlType_RequestParam, $aHeaders = array())
    {
        $clWsdlType_RequestParamResponse = $this->call('RequestParam', array($clWsdlType_RequestParam) ,  null, null , $aHeaders);

        return $clWsdlType_RequestParamResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetTemporalAutomatism
     *
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_GetTemporalAutomatismResponse
     * @access public
     */
    public function fSoapAction_GetTemporalAutomatism($aHeaders = array())
    {
        $clWsdlType_GetTemporalAutomatismResponse = $this->call('GetTemporalAutomatism', array() ,  null, null , $aHeaders);

        return $clWsdlType_GetTemporalAutomatismResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetEndAutomatism
     *
     * @param CWsdlType_GetEndAutomatism $clWsdlType_GetEndAutomatism
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_GetEndAutomatismResponse
     * @access public
     */
    public function fSoapAction_GetEndAutomatism(CWsdlType_GetEndAutomatism $clWsdlType_GetEndAutomatism, $aHeaders = array())
    {
        $clWsdlType_GetEndAutomatismResponse = $this->call('GetEndAutomatism', array($clWsdlType_GetEndAutomatism) ,  null, null , $aHeaders);

        return $clWsdlType_GetEndAutomatismResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : Disconnect
     *
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return void la reponse attendu est vide.
     * @access public
     */
    public function fSoapAction_Disconnect($aHeaders = array())
    {
        $mResp = $this->call('Disconnect', array() ,  null, null , $aHeaders);

        return $mResp;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetTableChild
     *
     * @param CWsdlType_GetTableChild $clWsdlType_GetTableChild
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_GetTableChildResponse
     * @access public
     */
    public function fSoapAction_GetTableChild(CWsdlType_GetTableChild $clWsdlType_GetTableChild, $aHeaders = array())
    {
        $clWsdlType_GetTableChildResponse = $this->call('GetTableChild', array($clWsdlType_GetTableChild) ,  null, null , $aHeaders);

        return $clWsdlType_GetTableChildResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetContentFolder
     *
     * @param CWsdlType_GetContentFolder $clWsdlType_GetContentFolder
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_GetContentFolderResponse
     * @access public
     */
    public function fSoapAction_GetContentFolder(CWsdlType_GetContentFolder $clWsdlType_GetContentFolder, $aHeaders = array())
    {
        $clWsdlType_GetContentFolderResponse = $this->call('GetContentFolder', array($clWsdlType_GetContentFolder) ,  null, null , $aHeaders);

        return $clWsdlType_GetContentFolderResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : ModifyFolder
     *
     * @param CWsdlType_ModifyFolder $clWsdlType_ModifyFolder
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_ModifyFolderResponse
     * @access public
     */
    public function fSoapAction_ModifyFolder(CWsdlType_ModifyFolder $clWsdlType_ModifyFolder, $aHeaders = array())
    {
        $clWsdlType_ModifyFolderResponse = $this->call('ModifyFolder', array($clWsdlType_ModifyFolder) ,  null, null , $aHeaders);

        return $clWsdlType_ModifyFolderResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : UpdateFolder
     *
     * @param CWsdlType_UpdateFolder $clWsdlType_UpdateFolder
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_UpdateFolderResponse
     * @access public
     */
    public function fSoapAction_UpdateFolder(CWsdlType_UpdateFolder $clWsdlType_UpdateFolder, $aHeaders = array())
    {
        $clWsdlType_UpdateFolderResponse = $this->call('UpdateFolder', array($clWsdlType_UpdateFolder) ,  null, null , $aHeaders);

        return $clWsdlType_UpdateFolderResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : CreateFolder
     *
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_CreateFolderResponse
     * @access public
     */
    public function fSoapAction_CreateFolder($aHeaders = array())
    {
        $clWsdlType_CreateFolderResponse = $this->call('CreateFolder', array() ,  null, null , $aHeaders);

        return $clWsdlType_CreateFolderResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : DeleteFolder
     *
     * @param CWsdlType_DeleteFolder $clWsdlType_DeleteFolder
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return void
     * @access public
     */
    public function fSoapAction_DeleteFolder(CWsdlType_DeleteFolder $clWsdlType_DeleteFolder, $aHeaders = array())
    {
        $mResp = $this->call('DeleteFolder', array($clWsdlType_DeleteFolder) ,  null, null , $aHeaders);

        return $mResp;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : ValidateFolder
     *
     * @param CWsdlType_ValidateFolder $clWsdlType_ValidateFolder
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return void la reponse attendu est vide.
     * @access public
     */
    public function fSoapAction_ValidateFolder(CWsdlType_ValidateFolder $clWsdlType_ValidateFolder, $aHeaders = array())
    {
        $mResp = $this->call('ValidateFolder', array($clWsdlType_ValidateFolder) ,  null, null , $aHeaders);

        return $mResp;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : CancelFolder
     *
     * @param CWsdlType_CancelFolder $clWsdlType_CancelFolder
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return void la reponse attendu est vide.
     * @access public
     */
    public function fSoapAction_CancelFolder(CWsdlType_CancelFolder $clWsdlType_CancelFolder, $aHeaders = array())
    {
        $mResp = $this->call('CancelFolder', array($clWsdlType_CancelFolder) ,  null, null , $aHeaders);

        return $mResp;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetFolderList
     *
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_GetFolderListResponse
     * @access public
     */
    public function fSoapAction_GetFolderList($aHeaders = array())
    {
        $clWsdlType_GetFolderListResponse = $this->call('GetFolderList', array() ,  null, null , $aHeaders);

        return $clWsdlType_GetFolderListResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : CloseFolderList
     *
     * @param CWsdlType_CloseFolderList $clWsdlType_CloseFolderList
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return void la reponse attendu est vide.
     * @access public
     */
    public function fSoapAction_CloseFolderList(CWsdlType_CloseFolderList $clWsdlType_CloseFolderList, $aHeaders = array())
    {
        $mResp = $this->call('CloseFolderList', array($clWsdlType_CloseFolderList) ,  null, null , $aHeaders);

        return $mResp;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : RequestMessage
     *
     * @param CWsdlType_RequestMessage $clWsdlType_RequestMessage
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_RequestMessageResponse
     * @access public
     */
    public function fSoapAction_RequestMessage(CWsdlType_RequestMessage $clWsdlType_RequestMessage, $aHeaders = array())
    {
        $clWsdlType_RequestMessageResponse = $this->call('RequestMessage', array($clWsdlType_RequestMessage) ,  null, null , $aHeaders);

        return $clWsdlType_RequestMessageResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetListMessage
     *
     * @param CWsdlType_GetListMessage $clWsdlType_GetListMessage
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_GetListMessageResponse
     * @access public
     */
    public function fSoapAction_GetListMessage(CWsdlType_GetListMessage $clWsdlType_GetListMessage, $aHeaders = array())
    {
        $clWsdlType_GetListMessageResponse = $this->call('GetListMessage', array($clWsdlType_GetListMessage) ,  null, null , $aHeaders);

        return $clWsdlType_GetListMessageResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : CloseMessageList
     *
     * @param CWsdlType_CloseMessageList $clWsdlType_CloseMessageList
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return void la reponse attendu est vide.
     * @access public
     */
    public function fSoapAction_CloseMessageList(CWsdlType_CloseMessageList $clWsdlType_CloseMessageList, $aHeaders = array())
    {
        $mResp = $this->call('CloseMessageList', array($clWsdlType_CloseMessageList) ,  null, null , $aHeaders);

        return $mResp;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : ModifyMessage
     *
     * @param CWsdlType_ModifyMessage $clWsdlType_ModifyMessage
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_ModifyMessageResponse
     * @access public
     */
    public function fSoapAction_ModifyMessage(CWsdlType_ModifyMessage $clWsdlType_ModifyMessage, $aHeaders = array())
    {
        $clWsdlType_ModifyMessageResponse = $this->call('ModifyMessage', array($clWsdlType_ModifyMessage) ,  null, null , $aHeaders);

        return $clWsdlType_ModifyMessageResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : UpdateMessage
     *
     * @param CWsdlType_UpdateMessage $clWsdlType_UpdateMessage
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_UpdateMessageResponse
     * @access public
     */
    public function fSoapAction_UpdateMessage(CWsdlType_UpdateMessage $clWsdlType_UpdateMessage, $aHeaders = array())
    {
        $clWsdlType_UpdateMessageResponse = $this->call('UpdateMessage', array($clWsdlType_UpdateMessage) ,  null, null , $aHeaders);

        return $clWsdlType_UpdateMessageResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : CreateMessage
     *
     * @param CWsdlType_CreateMessage $clWsdlType_CreateMessage
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_CreateMessageResponse
     * @access public
     */
    public function fSoapAction_CreateMessage(CWsdlType_CreateMessage $clWsdlType_CreateMessage, $aHeaders = array())
    {
        $clWsdlType_CreateMessageResponse = $this->call('CreateMessage', array($clWsdlType_CreateMessage) ,  null, null , $aHeaders);

        return $clWsdlType_CreateMessageResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : SendMessage
     *
     * @param CWsdlType_SendMessage $clWsdlType_SendMessage
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_SendMessageResponse
     * @access public
     */
    public function fSoapAction_SendMessage(CWsdlType_SendMessage $clWsdlType_SendMessage, $aHeaders = array())
    {
        $clWsdlType_SendMessageResponse = $this->call('SendMessage', array($clWsdlType_SendMessage) ,  null, null , $aHeaders);

        return $clWsdlType_SendMessageResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : CancelMessage
     *
     * @param CWsdlType_CancelMessage $clWsdlType_CancelMessage
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return void la reponse attendu est vide.
     * @access public
     */
    public function fSoapAction_CancelMessage(CWsdlType_CancelMessage $clWsdlType_CancelMessage, $aHeaders = array())
    {
        $mResp = $this->call('CancelMessage', array($clWsdlType_CancelMessage) ,  null, null , $aHeaders);

        return $mResp;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : InitRecordFromMessage
     *
     * @param CWsdlType_InitRecordFromMessage $clWsdlType_InitRecordFromMessage
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_InitRecordFromMessageResponse
     * @access public
     */
    public function fSoapAction_InitRecordFromMessage(CWsdlType_InitRecordFromMessage $clWsdlType_InitRecordFromMessage, $aHeaders = array())
    {
        $clWsdlType_InitRecordFromMessageResponse = $this->call('InitRecordFromMessage', array($clWsdlType_InitRecordFromMessage) ,  null, null , $aHeaders);

        return $clWsdlType_InitRecordFromMessageResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : InitRecordFromAddress
     *
     * @param CWsdlType_InitRecordFromAddress $clWsdlType_InitRecordFromAddress
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_InitRecordFromAddressResponse
     * @access public
     */
    public function fSoapAction_InitRecordFromAddress(CWsdlType_InitRecordFromAddress $clWsdlType_InitRecordFromAddress, $aHeaders = array())
    {
        $clWsdlType_InitRecordFromAddressResponse = $this->call('InitRecordFromAddress', array($clWsdlType_InitRecordFromAddress) ,  null, null , $aHeaders);

        return $clWsdlType_InitRecordFromAddressResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetPJ
     *
     * @param CWsdlType_GetPJ $clWsdlType_GetPJ
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_GetPJResponse
     * @access public
     */
    public function fSoapAction_GetPJ(CWsdlType_GetPJ $clWsdlType_GetPJ, $aHeaders = array())
    {
        $clWsdlType_GetPJResponse = $this->call('GetPJ', array($clWsdlType_GetPJ) ,  null, null , $aHeaders);

        return $clWsdlType_GetPJResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : DeletePJ
     *
     * @param CWsdlType_DeletePJ $clWsdlType_DeletePJ
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_DeletePJResponse
     * @access public
     */
    public function fSoapAction_DeletePJ(CWsdlType_DeletePJ $clWsdlType_DeletePJ, $aHeaders = array())
    {
        $clWsdlType_DeletePJResponse = $this->call('DeletePJ', array($clWsdlType_DeletePJ) ,  null, null , $aHeaders);

        return $clWsdlType_DeletePJResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : AddPJ
     *
     * @param CWsdlType_AddPJ $clWsdlType_AddPJ
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_AddPJResponse
     * @access public
     */
    public function fSoapAction_AddPJ(CWsdlType_AddPJ $clWsdlType_AddPJ, $aHeaders = array())
    {
        $clWsdlType_AddPJResponse = $this->call('AddPJ', array($clWsdlType_AddPJ) ,  null, null , $aHeaders);

        return $clWsdlType_AddPJResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : ZipPJ
     *
     * @param CWsdlType_ZipPJ $clWsdlType_ZipPJ
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_ZipPJResponse
     * @access public
     */
    public function fSoapAction_ZipPJ(CWsdlType_ZipPJ $clWsdlType_ZipPJ, $aHeaders = array())
    {
        $clWsdlType_ZipPJResponse = $this->call('ZipPJ', array($clWsdlType_ZipPJ) ,  null, null , $aHeaders);

        return $clWsdlType_ZipPJResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : CheckRecipient
     *
     * @param CWsdlType_CheckRecipient $clWsdlType_CheckRecipient
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_CheckRecipientResponse
     * @access public
     */
    public function fSoapAction_CheckRecipient(CWsdlType_CheckRecipient $clWsdlType_CheckRecipient, $aHeaders = array())
    {
        $clWsdlType_CheckRecipientResponse = $this->call('CheckRecipient', array($clWsdlType_CheckRecipient) ,  null, null , $aHeaders);

        return $clWsdlType_CheckRecipientResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : CheckCreateElement
     *
     * @param CWsdlType_CheckCreateElement $clWsdlType_CheckCreateElement
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_CheckCreateElementResponse
     * @access public
     */
    public function fSoapAction_CheckCreateElement(CWsdlType_CheckCreateElement $clWsdlType_CheckCreateElement, $aHeaders = array())
    {
        $clWsdlType_CheckCreateElementResponse = $this->call('CheckCreateElement', array($clWsdlType_CheckCreateElement) ,  null, null , $aHeaders);

        return $clWsdlType_CheckCreateElementResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline :GetMailServiceStatus
     *
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_GetMailServiceStatusResponse
     * @access public
     */
    public function fSoapAction_GetMailServiceStatus($aHeaders = array())
    {
        $clWsdlType_GetMailServiceStatusResponse = $this->call('GetMailServiceStatus', array() ,  null, null , $aHeaders);

        return $clWsdlType_GetMailServiceStatusResponse;
    }

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : WithAutomaticResponse
     *
     * @param CWsdlType_WithAutomaticResponse $clWsdlType_WithAutomaticResponse
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return CWsdlType_WithAutomaticResponseResponse
     * @access public
     */
    public function fSoapAction_WithAutomaticResponse(CWsdlType_WithAutomaticResponse $clWsdlType_WithAutomaticResponse, $aHeaders = array())
    {
        $clWsdlType_WithAutomaticResponseResponse = $this->call('WithAutomaticResponse', array($clWsdlType_WithAutomaticResponse) ,  null, null , $aHeaders);

        return $clWsdlType_WithAutomaticResponseResponse;
    }
}
//***


//-------------------------------------------------------------------------------------------------------------------
// Ensemble de classes utilisé par la classe SimaxOnlineServiceProxy
// Note : les conventions de code peuvent semblé non respecté sur les nom de variables, mais elle corresponde en realite
// au fichier WSDL
//-------------------------------------------------------------------------------------------------------------------
class LanguageCodeList
{
    public $LanguageCode; // integer
}
//***



class UsernameTokenType
{
    public $Username; // string
    public $Password; // string
    public $Nonce; // string
    public $Created; // string
}
//***

class ExtranetUserType
{
    public $UsernameToken; // UsernameTokenType
    public $Form; // string
}
//***

class DefaultClientLanguageCodeType
{
}
//***

class GetTokenSession
{
    public $UsernameToken; // UsernameTokenType
    public $ExtranetUser; // ExtranetUserType
    public $DefaultClientLanguageCode; // DefaultClientLanguageCodeType
}
//***

class GetTokenSessionResponse
{
    public $SessionToken; // string
}
//***

class ResetPasswordFailed
{
    public $Login; // string
}
//***

class ResetPasswordFailedResponse
{
    public $xml; // string
}
//***

class GetStartAutomatism
{
    public $SpecialParamList; // SpecialParamListType
}
//***

class GetStartAutomatismResponse
{
    public $xml; // string
}
//***

class ConfirmResponse
{
    public $TypeConfirmation; // integer
}
//***

class ConfirmResponseResponse
{
    public $xml; // string
}
//***

class HasChangedResponse
{
    public $Value; // integer
}
//***

class SelectForm
{
    public $Form; // string
}
//***

class SelectFormResponse
{
    public $xml; // string
}
//***

class SelectPrintTemplate
{
    public $Template; // string
}
//***

class SelectPrintTemplateResponse
{
    public $xml; // string
}
//***

class GetPlanningInfo
{
    public $Resource; // string
    public $StartTime; // string
    public $EndTime; // string
    public $Table; // string
    public $ID; // string
    public $ParamXML; // string
}
//***

class GetPlanningInfoResponse
{
    public $xml; // string
}
//***

class GetColInRecord
{
    public $Column; // string
    public $Record; // string
    public $Encoding; // string
    public $MimeType; // string
    public $TransColor; // string
    public $WantContent; // string
    public $ColorFrom; // string
    public $ColorTo; // string
    public $Width; // string
    public $Height; // string
}
//***

class GetColInRecordResponse
{
    public $xml; // string
}
//***

class Display
{
    public $Table; // string
    public $ParamXML; // string
}
//***

class DisplayResponse
{
    public $xml; // string
}
//***

class Create
{
    public $Table; // string
    public $ParamXML; // string
    public $IDMessage; // string
    public $CallingColumn; // string
}
//***

class CreateResponse
{
    public $xml; // string
}
//***

class CreateFrom
{
    public $Table; // string
    public $TableSrc; // string
    public $ElemSrc; // string
}
//***

class CreateFromResponse
{
    public $xml; // string
}
//***

class TransformInto
{
    public $Table; // string
    public $TableSrc; // string
    public $ElemSrc; // string
}
//***

class TransformIntoResponse
{
    public $xml; // string
}
//***

class Modify
{
    public $Table; // string
    public $ParamXML; // string
}
//***

class ModifyResponse
{
    public $xml; // string
}
//***

class Update
{
    public $Table; // string
    public $ParamXML; // string
    public $Complete; // integer
    public $UpdateData; // string
}
//***

class UpdateResponse
{
    public $xml; // string
}
//***

class Print
{
    public $Table; // string
    public $ParamXML; // string
    public $ListMode; // integer
}
//***

class PrintResponse
{
    public $xml; // string
}
//***

class Delete
{
    public $Table; // string
    public $ParamXML; // string
}
//***

class DeleteResponse
{
    public $xml; // string
}
//***

class SortType
{
    public $_; // string
    public $asc; // string
}
//***

class SpecialParamListType
{
    public $First; // integer
    public $Length; // integer
    public $WithBreakRow; // integer
    public $WithEndCalculation; // integer
    public $ChangePage; // integer
    public $Sort1; // SortType
    public $Sort2; // SortType
    public $Sort3; // SortType
}
//***

class Execute
{
    public $ID; // string
    public $Sentence; // string
    public $ParamXML; // string
    public $SpecialParamList; // SpecialParamListType
    public $Checksum; // integer
    public $CallingColumn; // string
    public $DisplayMode; // DisplayModeParamEnum
}
//***

class ExecuteResponse
{
    public $xml; // string
}
//***

class DrillThrough
{
    public $Record; // string
    public $Column; // string
    public $SpecialParamList; // SpecialParamListType
}
//***

class DrillThroughResponse
{
    public $xml; // string
}
//***

class ValidateResponse
{
    public $xml; // string
}
//***

class ListeColType
{
    public $Column; // string
}
//***

class Cancel
{
    public $Context; // integer
    public $ByUser; // integer
}
//***

class CancelResponse
{
    public $xml; // string
}
//***

class List
{
    public $Table; // string
    public $ParamXML; // string
    public $SpecialParamList; // SpecialParamListType
    public $CallingColumn; // string
    public $NoCache; // integer
    public $Checksum; // integer
    public $DisplayMode; // DisplayModeParamEnum
}
//***

class ListResponse
{
    public $xml; // string
}
//***

class GetLanguagesResponse
{
    public $xml; // LanguageCodeList
}
//***


class GetChart
{
    public $Height; // integer
    public $Width; // integer
    public $DPI; // integer
    public $Index; // integer
    public $ParamXML; // string
    public $Table; // string
    public $Axes; // string
    public $Calculation; // CalculationTypeEnum
    public $OnlyData; // integer
}
//***

class GetChartResponse
{
    public $xml; // string
}
//***

class SelectItems
{
    public $items; // string
}
//***

class SelectItemsResponse
{
    public $xml; // string
}
//***

class EnterReorderListModeResponse
{
    public $Value; // integer
}
//***

class ReorderList
{
    public $items; // string
    public $moveType; // string
    public $scale; // integer
}
//***

class ReorderListResponse
{
    public $Value; // string
}
//***

class SetOrderList
{
    public $items; // string
    public $offset; // integer
}
//***

class SetOrderListResponse
{
    public $Value; // string
}
//***

class ReorderSubList
{
    public $column; // string
    public $items; // string
    public $moveType; // string
    public $scale; // integer
}
//***

class ReorderSubListResponse
{
    public $Value; // string
}
//***

class SetOrderSubList
{
    public $column; // string
    public $items; // string
    public $offset; // integer
}
//***

class SetOrderSubListResponse
{
    public $Value; // string
}
//***

class CalculationListType
{
    public $Calculation; // string
}
//***

class ColListType
{
    public $Col; // string
}
//***

class GetCalculation
{
    public $ColList; // ColListType
    public $CalculationList; // CalculationListType
}
//***

class GetCalculationResponse
{
    public $xml; // string
}
//***

class Search
{
    public $Table; // string
    public $ParamXML; // string
    public $SpecialParamList; // SpecialParamListType
    public $CallingColumn; // string
    public $Checksum; // integer
    public $DisplayMode; // DisplayModeParamEnum
}
//***

class SearchResponse
{
    public $xml; // string
}
//***

class Request
{
    public $Table; // string
    public $CallingColumn; // string
    public $ColList; // ColListType
    public $CondList; // string
    public $MaxResult; // integer
    public $Sort1; // SortType
    public $Sort2; // SortType
    public $Sort3; // SortType
}
//***

class RequestResponse
{
    public $xml; // string
}
//***

class RequestParam
{
    public $Table; // string
    public $CallingColumn; // string
    public $ColList; // ColListType
    public $CondList; // string
    public $MaxResult; // integer
}
//***

class RequestParamResponse
{
    public $xml; // string
}
//***

class GetTemporalAutomatismResponse
{
    public $xml; // string
}
//***

class GetEndAutomatism
{
    public $SpecialParamList; // SpecialParamListType
}
//***

class GetEndAutomatismResponse
{
    public $xml; // string
}
//***

class GetTableChild
{
    public $Table; // string
    public $Recursive; // string
    public $ReadOnly; // string
}
//***

class GetTableChildResponse
{
    public $xml; // string
}
//***

class GetContentFolder
{
    public $IDFolder; // string
    public $SpecialParamList; // SpecialParamListType
}
//***

class GetContentFolderResponse
{
    public $xml; // string
}
//***

class GetFolderListResponse
{
    public $xml; // string
}
//***

class ModifyFolder
{
    public $IDFolder; // string
}
//***

class ModifyFolderResponse
{
    public $xml; // string
}
//***

class UpdateFolder
{
    public $IDFolder; // string
    public $UpdateData; // string
}
//***

class UpdateFolderResponse
{
    public $xml; // string
}
//***

class CreateFolderResponse
{
    public $xml; // string
}
//***

class DeleteFolder
{
    public $IDFolder; // string
}
//***

class ValidateFolder
{
    public $IDFolder; // string
}
//***

class CancelFolder
{
    public $IDFolder; // string
}
//***

class CloseFolderList
{
    public $IDList; // string
}
//***

class FilterType
{
    public $Way; // WayEnum
    public $State; // StateEnum
    public $Inner; // integer
    public $Email; // integer
    public $Spam; // integer
    public $Max; // integer
    public $From; // string
    public $Containing; // string
}
//***

class RequestMessage
{
    public $StartDate; // string
    public $EndDate; // string
    public $Filter; // FilterType
    public $SpecialParamList; // SpecialParamListType
}
//***

class RequestMessageResponse
{
    public $xml; // string
}
//***

class GetListMessage
{
    public $MessageType; // MessageTypeEnum
    public $StartDate; // string
    public $EndDate; // string
    public $UserMessagerie; // string
    public $Filter; // FilterType
    public $SpecialParamList; // SpecialParamListType
}
//***

class GetListMessageResponse
{
    public $xml; // string
}
//***

class CloseMessageList
{
    public $IDList; // string
}
//***

class ModifyMessage
{
    public $IDMessage; // string
}
//***

class ModifyMessageResponse
{
    public $xml; // string
}
//***

class UpdateMessage
{
    public $IDMessage; // string
    public $UpdateData; // string
}
//***

class UpdateMessageResponse
{
    public $xml; // string
}
//***

class CreateMessage
{
    public $CreateType; // CreateTypeEnum
    public $IDMessage; // string
    public $IDAnswerType; // string
}
//***

class CreateMessageResponse
{
    public $xml; // string
}
//***

class SendMessage
{
    public $IDMessage; // string
}
//***

class SendMessageResponse
{
    public $xml; // string
}
//***

class CancelMessage
{
    public $IDMessage; // string
}
//***

class GetPJ
{
    public $IDMessage; // string
    public $IDPJ; // string
}
//***

class GetPJResponseType
{
    public $Data; // string
}
//***

class GetPJResponse
{
    public $xml; // GetPJResponseType
}
//***

class DeletePJ
{
    public $IDMessage; // string
    public $IDPJ; // string
}
//***

class DeletePJResponse
{
    public $xml; // string
}
//***

class DataPJType
{
    public $_; // string
    public $encoding; // string
    public $filename; // string
    public $size; // integer
}
//***

class AddPJ
{
    public $IDMessage; // string
    public $DataPJ; // DataPJType
}
//***

class AddPJResponse
{
    public $xml; // string
}
//***

class CheckRecipient
{
    public $IDMessage; // string
}
//***

class CheckRecipientResponse
{
    public $xml; // string
}
//***

class ZipPJ
{
    public $IDMessage; // string
}
//***

class ZipPJResponse
{
    public $xml; // string
}
//***

class CheckCreateElement
{
    public $IDMessage; // string
}
//***

class CheckCreateElementResponse
{
    public $xml; // string
}
//***

class InitRecordFromMessage
{
    public $Table; // string
    public $Record; // string
    public $IDMessage; // string
}
//***

class InitRecordFromMessageResponse
{
    public $xml; // string
}
//***

class InitRecordFromAddress
{
    public $Table; // string
    public $Record; // string
    public $Address; // string
}
//***

class InitRecordFromAddressResponse
{
    public $xml; // string
}
//***

class LastUnReadType
{
    public $xml; // string
}
//***

class GetMailServiceStatusResponse
{
    public $UnRead; // integer
    public $Receive; // integer
    public $LastUnRead; // LastUnReadType
}
//***

class WithAutomaticResponse
{
    public $IDMessage; // string
    public $ResponseType; // integer
    public $SendAutomaticMessage; // integer
}
//***

class WithAutomaticResponseResponse
{
    public $xml; // string
}
//***

class APIUUID
{
}
//***

class UsernameToken
{
    public $Username; // string
    public $Password; // string
    public $Nonce; // string
    public $Created; // string
}
//***

class SessionToken
{
}
//***

class APIUser
{
}
//***

class CustomerInfos
{
}
//***

class ActionContext
{
}
//***

class Column
{
}
//***

class OptionDialogue
{
    public $Readable; // integer
    public $EncodingOutput; // integer
    public $ReturnValue; // integer
    public $ReturnXSD; // integer
    public $HTTPForceReturn; // integer
    public $Ghost; // integer
    public $DefaultPagination; // integer
    public $DisplayValue; // integer
    public $LanguageCode; // integer
    public $WithFieldStateControl; // integer
    public $ListContentAsync; // integer
}
//***

class AutoValidate
{
}
//***

class RecipientCheck
{
    public $To; // string
    public $Cc; // string
    public $Cci; // string
}
//***

class XSDSchema
{
}
//***

class SessionLanguageCode
{
}
//***

class PossibleDisplayMode
{
}
//***

class Action
{
    public $_; // string
    public $title; // string
    public $typeaction; // string
    public $typereturn; // string
}
//***

class Form
{
    public $_; // string
    public $title; // string
    public $checksum; // string
    public $typeform; // string
    public $withBtnOrderPossible; // integer
    public $sort1; // string
    public $sort2; // string
    public $sort3; // string
    public $sort1asc; // string
    public $sort2asc; // string
    public $sort3asc; // string
}
//***

class Element
{
    public $_; // string
    public $title; // string
}
//***

class Filter
{
    public $xml; // string
    public $schema; // string
}
//***

class Count
{
    public $NbFiltered; // integer
    public $NbTotal; // integer
    public $NbCalculation; // integer
    public $NbLine; // integer
}
//***

class PlanningFilter
{
    public $Resource; // string
    public $Table; // string
    public $StartTime; // string
    public $EndTime; // string
}
//***

class ValidateError
{
    public $Message; // string
    public $ListCol; // ListeColType
}
//***

class ConnectedUser
{
    public $Form; // string
    public $Element; // string
}
//***

class ConnectedExtranet
{
    public $Form; // string
    public $Element; // string
}
//***

class NextCall
{
}
//***

/*
 * definition des classe de constante utile a la communication soap
 *
 */

class CalculationTypeEnum
{
    const Sum = 'Sum';
    const Count = 'Count';
    const Max = 'Max';
    const Min = 'Min';
    const Average = 'Average';
}
//***

class WayEnum
{
    const Received = 'Received';
    const Sent = 'Sent';
    const All = 'All';
}
//***

class StateEnum
{
    const Processed = 'Processed';
    const Unprocessed = 'Unprocessed';
    const All = 'All';
}
//***

class MessageTypeEnum
{
    const Annulation = 'Annulation';
    const Cancellation = 'Cancellation';
    const Email = 'Email';
}
//***

class CreateTypeEnum
{
    const _Empty = 'Empty';
    const Forward = 'Forward';
    const Answer = 'Answer';
    const Answer_all = 'Answer all';
    const Answer_type = 'Answer type';
}
//***

class PJSizeCheck
{
    const value_0 = '0';
    const value_1 = '1';
    const value_2 = '2';
}
//***

class ReturnType
{
    const _Empty = 'Empty';
    const Report = 'Report';
    const Value = 'Value';
    const Record = 'Record';
    const _List = 'List';
    const Thumbnail = 'Thumbnail';
    const AmbiguousAction = 'AmbiguousAction';
    const Identification = 'Identification';
    const XSD = 'XSD';
    const MessageBox = 'MessageBox';
    const ValidateAction = 'ValidateAction';
    const Planning = 'Planning';
    const RequestFilter = 'RequestFilter';
    const MailServiceRecord = 'MailServiceRecord';
    const MailServiceList = 'MailServiceList';
    const GlobalSearch = 'GlobalSearch';
    const ListCalculation = 'ListCalculation';
    const PrintTemplate = 'PrintTemplate';
    const MailServiceStatus = 'MailServiceStatus';
    const ValidateRecord = 'ValidateRecord';
    const WithAutomaticResponse = 'WithAutomaticResponse';
    const Chart = 'Chart';
}
//***

class DisplayModeEnum
{
    const _List = 'List';
    const Chart = 'Chart';
}
//***

class DisplayModeParamEnum
{
    const _List = 'List';
    const Chart = 'Chart';
    const Planning = 'Planning';
    const Thumbnail = 'Thumbnail';
}
//***


/*
 * definition des classe de constante utile a la communication soap mais non presente dans la WSDL (uniquement dans la doc)
 */
class COutOfWsdlType_CalculEnumForGetCalculation
{
    const Sum = 'sum';
    const Average = 'average';
    const Min = 'min';
    const Max = 'max';
    const Count = 'count';
    const Percent = 'percent';
}