<?php
namespace NOUT\Bundle\NOUTOnlineBundle\SOAP;
//WSDLEntity utilsé en paramètres
use NOUT\Bundle\NOUTOnlineBundle\Cache\NOUTCache;
use NOUT\Bundle\NOUTOnlineBundle\DataCollector\NOUTOnlineLogger;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ConfigurationDialogue;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS;
use NOUT\Bundle\NOUTOnlineBundle\Exception\NOUTOnlineException;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\NUSOAP\SOAPTransportHTTP;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\NUSOAP\WSDL;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\AddPJ;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Cancel;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\CancelFolder;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\CancelMessage;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\CheckCreateElement;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\CheckRecipient;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\CloseFolderList;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\CloseMessageList;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ConfirmResponse;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Create;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\CreateFrom;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\CreateMessage;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Delete;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\DeleteFolder;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\DeletePJ;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Display;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\DrillThrough;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Execute;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetCalculation;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetChart;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetColInRecord;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetContentFolder;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetEndAutomatism;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetListMessage;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetPJ;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetPlanningInfo;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetStartAutomatism;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetTableChild;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetTokenSession;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\InitRecordFromAddress;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\InitRecordFromMessage;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ListParams;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Modify;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ModifyFolder;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ModifyMessage;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\PrintParams;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ReorderList;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ReorderSubList;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Request;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\RequestMessage;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\RequestParam;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ResetPasswordFailed;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Search;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SelectForm;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SelectItems;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SelectPrintTemplate;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SendMessage;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SetOrderList;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SetOrderSubList;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\TransformInto;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Update;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\UpdateFolder;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\UpdateMessage;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ValidateFolder;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\WithAutomaticResponse;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ZipPJ;

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
final class OnlineServiceProxy extends ModifiedNuSoapClient
{
	//OPTION HEADER
	const FORMHEAD_UNDECODED_SPECIAL_ELEM_AND_DATE = 16508; //display value a utilise
	const FORMHEAD_UNDECODED_SPECIAL_ELEM = 16638; //display value a utilise
	const FORMHEAD_DB_FORMAT_SPECIAL_ELEM = 0; //display value permettant de récupérer les donnée au format stockés.

    //Definition des variable pour gestion des headers de requete
    private $__aListHeaders = array();
    private $__bCleanHeadersBeforeRequest = true; //sert a savoir si on remet les headers a zero avant une requete

	/**
	 * classe de configuration
	 * @var \NOUT\Bundle\NOUTOnlineBundle\Entity\ConfigurationDialogue
	 */
	private $__ConfigurationDialogue ;

	//logger symfony
	private $__clLogger;

	//pour le cache de la wsdl
	private $__sVersionWSDL;
	private $__clCache;



    /**
     * constructeur permettant d'instancier les classe de communication soap avec les bonne question
     * @param $sEndpoint
     * @param $bWsdl
     * @param $sProxyHost
     * @param $sProxyPort
     * @return unknown_type
     */
    public function __construct(ConfigurationDialogue $clConfig, NOUTOnlineLogger $_clLogger, NOUTCache $cache)
    {
        parent::__construct($clConfig->m_sEndPoint, $clConfig->m_bWsdl,$clConfig->m_sHost,$clConfig->m_nPort);

	    $this->__ConfigurationDialogue = $clConfig;
	    $this->parse_response = false; //la réponse parsée ne nous interresse pas

        $this->forceEndpoint = $clConfig->m_sProtocolPrefix . $clConfig->m_sHost . ':' . $clConfig->m_nPort; //on force l'ip et le port du fichier config
        // on force le timeout a 300s
        $this->timeout = 300;
        $this->response_timeout = 300;
	    $this->__clLogger = $_clLogger;

	    //il faut lire le début de endpoint pour avoir la version de la wsdl
	    $this->__clCache = $cache;
	    if (file_exists($clConfig->m_sEndPoint))
	    {
		    $fHandle = fopen($clConfig->m_sEndPoint, "r");
		    $sDebutWSDL = fgets($fHandle, 250);
		    fclose($fHandle);

		    $this->__sVersionWSDL = md5($sDebutWSDL, false);
	    }
    }


	/**
	 * Méthode qui marque le début du send
	 */
	function __StartSend()
	{
		if (isset($this->__clLogger)) //log des requetes
			$this->__clLogger->startSend();

	}

	/**
	 * Méthode qui marque la fin du send
	 */
	function __StopSend()
	{
		if (isset($this->__clLogger)) //log des requetes
			$this->__clLogger->stopSend();
	}

	/**
	 * charge la wsdl depuis le cache si disponible
	 * @return bool
	 */
	function _loadWSDLFromCache()
	{
		if (!isset($this->__sVersionWSDL) || ($this->__sVersionWSDL == '') || ($this->__sVersionWSDL == null))
			return false;

		if ($this->__clCache->contains($this->__sVersionWSDL))
			return $this->__clCache->fetch($this->__sVersionWSDL);

		return false;
	}

	/**
	 * sauve la wsdl en cache pour usage futur
	 */
	function _saveWSDLInCache()
	{
		if (!isset($this->__sVersionWSDL) || ($this->__sVersionWSDL == '') || ($this->__sVersionWSDL == null))
			return ;

		$this->__clCache->save($this->__sVersionWSDL, $this->wsdl, $this->__ConfigurationDialogue->m_nDureeSession);
	}

	/**
	 * instantiate wsdl object and parse wsdl file,
	 * charge la wsdl depuis le cache s'il existe
	 *
	 * @access	public
	 */
	function loadWSDL() {
		$this->wsdl = $this->_loadWSDLFromCache();
		if (!$this->wsdl)
		{
			$this->wsdl = new WSDL('',$this->proxyhost,$this->proxyport,$this->proxyusername,$this->proxypassword,$this->timeout,$this->response_timeout,$this->curl_options,$this->use_curl);
			$this->wsdl->setCredentials($this->username, $this->password, $this->authtype, $this->certRequest);
			$this->wsdl->fetchWSDL($this->wsdlFile);

			$this->_saveWSDLInCache();
		}

		$this->checkWSDL();
	}

	/**
	 * send the SOAP message
	 *
	 * Note: if the operation has multiple return values
	 * the return value of this method will be an array
	 * of those values.
	 *
	 * @param    string $msg a SOAPx4 soapmsg object
	 * @param    string $soapaction SOAPAction value
	 * @param    integer $timeout set connection timeout in seconds
	 * @param	integer $response_timeout set response timeout in seconds
	 * @return	mixed native PHP types.
	 * @access   private
	 */
	function send($msg, $soapaction = '', $timeout=0, $response_timeout=30) {
		$this->checkCookies();
		// detect transport
		switch(true){
			// http(s)
			case preg_match('/^http/',$this->endpoint):
				if($this->persistentConnection == true && is_object($this->persistentConnection)){
					$http =& $this->persistentConnection;
				} else {
					$http = new SOAPTransportHTTP($this->endpoint, $this->curl_options, $this->use_curl);
					if ($this->persistentConnection) {
						$http->usePersistentConnection();
					}
				}
				$http->setContentType($this->getHTTPContentType(), $this->getHTTPContentTypeCharset());
				$http->setSOAPAction($soapaction);
				if($this->proxyhost && $this->proxyport){
					$http->setProxy($this->proxyhost,$this->proxyport,$this->proxyusername,$this->proxypassword);
				}
				if($this->authtype != '') {
					$http->setCredentials($this->username, $this->password, $this->authtype, array(), $this->certRequest);
				}
				if($this->http_encoding != ''){
					$http->setEncoding($this->http_encoding);
				}

				$this->__StartSend();
				if(preg_match('/^http:/',$this->endpoint)){
					//if(strpos($this->endpoint,'http:')){
					$this->responseData = $http->send($msg,$timeout,$response_timeout,$this->cookies);
				} elseif(preg_match('/^https/',$this->endpoint)){
					//} elseif(strpos($this->endpoint,'https:')){
					//if(phpversion() == '4.3.0-dev'){
					//$response = $http->send($msg,$timeout,$response_timeout);
					//$this->request = $http->outgoing_payload;
					//$this->response = $http->incoming_payload;
					//} else
					$this->responseData = $http->sendHTTPS($msg,$timeout,$response_timeout,$this->cookies);
				} else {
					$this->setError('no http/s in endpoint url');
				}
				$this->__StopSend();

				$this->request = $http->outgoing_payload;
				$this->response = $http->incoming_payload;
				$this->UpdateCookies($http->incoming_cookies);

				// save transport object if using persistent connections
				if ($this->persistentConnection) {
					if (!is_object($this->persistentConnection)) {
						$this->persistentConnection = $http;
					}
				}

				if($err = $http->getError()){
					$this->setError('HTTP Error: '.$err);
					return false;
				} elseif($this->getError()){
					return false;
				} else {
					return $this->parseResponse($http->incoming_headers, $this->responseData);
				}
				break;
			default:
				$this->setError('no transport found, or selected transport is not yet supported!');
				return false;
				break;
		}
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


	/**
	 * @return XMLResponseWS
	 */
	public function getXMLResponseWS()
	{
		//retourne un XMLResponseWS qui permet de manipuler la réponse
		return new XMLResponseWS($this->responseData);
	}


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
	 *
	 * @param string $sOperation
	 * @param array $mParams
	 * @param null $sNamespace
	 * @param null $sSoapAction
	 * @param bool $mHeaders
	 * @param null $mRpcParams
	 * @param string $sStyle
	 * @param string $sUse
	 * @return XMLResponseWS
	 * @throws \Exception
	 */
	public function call($sOperation, $mParams = array(),$sNamespace=null,$sSoapAction=null , $mHeaders = false,$mRpcParams=null,$sStyle='rpc',$sUse='encoded')
    {
	    //petite modif sur le paramètre mParams si tableau vide
	    if (!isset($mParams) || (is_array($mParams) && (count($mParams)==0)))
		    $mParams = '<'.$sOperation.' />';


        //si il le faut, avant toutes chose, on nettoye les header
        if($this->__bCleanHeadersBeforeRequest )
        {
            $this->cleanListHeaders();
        }

        //on rajoute les header
        $this->addMultipleHeaders($mHeaders);


		//TODO: ajouter les header X-SIMAX pour le service.


	    if (isset($this->__aListHeaders['OptionDialogue']) && is_object($this->__aListHeaders['OptionDialogue']))
	    {
		    //on transforme l'objet en tableau associatif
		    $this->__aListHeaders['OptionDialogue'] = (array)$this->__aListHeaders['OptionDialogue'];
	    }


        //si le la partie optiondialogue du header n'est pas passer en param on la crée
        if( ! isset($this->__aListHeaders['OptionDialogue']) )
        {
            $this->__aListHeaders['OptionDialogue'] = array('Readable'=>false);
        }
        if(!isset($this->__aListHeaders['OptionDialogue']['DisplayValue']))
        {
            $this->__aListHeaders['OptionDialogue']['DisplayValue'] = OnlineServiceProxy::FORMHEAD_UNDECODED_SPECIAL_ELEM;
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
            $this->__aListHeaders['OptionDialogue']['LanguageCode'] = $this->__ConfigurationDialogue->m_nLangCode;
        }

        //si on a pas de withFieldStateControl precisé, on le mets à 1 (pour recuperer les controle d'etat de champ)
        if(
            !isset($this->__aListHeaders['OptionDialogue']['WithFieldStateControl']) ||
            is_null($this->__aListHeaders['OptionDialogue']['WithFieldStateControl']) ||
            $this->__aListHeaders['OptionDialogue']['WithFieldStateControl'] == ''
        )
        {
            $this->__aListHeaders['OptionDialogue']['WithFieldStateControl'] = 1;
        }



        //on ajoute l'id application
        $this->__aListHeaders['APIUUID'] = $this->__ConfigurationDialogue->m_sAPIUUID;

	    //
	    if (isset($this->__clLogger)) //log des requetes
		    $this->__clLogger->startQuery();

	    try
	    {
		    //on fait l'appel a la methode mere
		    /*$mResult =  */parent::call($sOperation, $mParams, $sNamespace, $sSoapAction, $this->__aListHeaders, $mRpcParams , null, null);

	    }
	    catch(\Exception $e)
	    {
		    if (isset($this->__clLogger)) //log des requetes
		        $this->__clLogger->stopQuery($this->request, $this->response, $sOperation);

		    throw $e;
	    }



	    if (isset($this->__clLogger)) //log des requetes
		    $this->__clLogger->stopQuery($this->request, $this->response, $sOperation);

	    //on ne veut pas l'objet retourné par NUSOAP qui est un tableau associatif mais un objet qui permet de manipuler la réponse
        return $this->getXMLResponseWS();
    }
    //---



    //------------------------------------------------------------------------------------------
    // Fonction d'appel  direct soap
    //------------------------------------------------------------------------------------------
    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : AddPJ
     *
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return AddPJResponse
     * @access public
     */
    public function addPJ(AddPJ $clWsdlType_AddPJ, $aHeaders = array())
    {
        return $this->call('AddPJ', array($clWsdlType_AddPJ) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline :Cancel
     *
     * @param Cancel $clWsdlType_Cancel
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function cancel(Cancel $clWsdlType_Cancel, $aHeaders = array())
    {
	    return $this->call('Cancel', array($clWsdlType_Cancel) ,  null, null , $aHeaders);
	}
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : CancelFolder
     *
     * @param CancelFolder $clWsdlType_CancelFolder
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function cancelFolder(CancelFolder $clWsdlType_CancelFolder, $aHeaders = array())
    {
	    return $this->call('CancelFolder', array($clWsdlType_CancelFolder) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : CancelMessage
     *
     * @param CancelMessage $clWsdlType_CancelMessage
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function cancelMessage(CancelMessage $clWsdlType_CancelMessage, $aHeaders = array())
    {
	    return $this->call('CancelMessage', array($clWsdlType_CancelMessage) ,  null, null , $aHeaders);
    }
    //----

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : CheckCreateElement
     *
     * @param CheckCreateElement $clWsdlType_CheckCreateElement
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function checkCreateElement(CheckCreateElement $clWsdlType_CheckCreateElement, $aHeaders = array())
    {
	    return $this->call('CheckCreateElement', array($clWsdlType_CheckCreateElement) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : CheckRecipient
     *
     * @param CheckRecipient $clWsdlType_CheckRecipient
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function checkRecipient(CheckRecipient $clWsdlType_CheckRecipient, $aHeaders = array())
    {
	    return $this->call('CheckRecipient', array($clWsdlType_CheckRecipient) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : CloseFolderList
     *
     * @param CloseFolderList $clWsdlType_CloseFolderList
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function closeFolderList(CloseFolderList $clWsdlType_CloseFolderList, $aHeaders = array())
    {
	    return $this->call('CloseFolderList', array($clWsdlType_CloseFolderList) ,  null, null , $aHeaders);
	}
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : CloseMessageList
     *
     * @param CloseMessageList $clWsdlType_CloseMessageList
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function closeMessageList(CloseMessageList $clWsdlType_CloseMessageList, $aHeaders = array())
    {
	    return $this->call('CloseMessageList', array($clWsdlType_CloseMessageList) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : ConfirmResponse
     *
     * @param ConfirmResponse $clWsdlType_ConfirmResponse
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function ConfirmResponse(ConfirmResponse $clWsdlType_ConfirmResponse, $aHeaders = array())
    {
	    return $this->call('ConfirmResponse', array($clWsdlType_ConfirmResponse) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : Create
     *
     * @param Create $clWsdlType_Create
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function create(Create $clWsdlType_Create, $aHeaders = array())
    {
	    return $this->call('Create', array($clWsdlType_Create) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : CreateFolder
     *
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function createFolder($aHeaders = array())
    {
	    return $this->call('CreateFolder', array() ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : CreateFrom
     *
     * @param CreateFrom $clWsdlType_CreateFrom
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function createFrom(CreateFrom $clWsdlType_CreateFrom, $aHeaders = array())
    {
	    return $this->call('CreateFrom', array($clWsdlType_CreateFrom) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : CreateMessage
     *
     * @param CreateMessage $clWsdlType_CreateMessage
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function createMessage(CreateMessage $clWsdlType_CreateMessage, $aHeaders = array())
    {
	    return $this->call('CreateMessage', array($clWsdlType_CreateMessage) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : Delete
     *
     * @param Delete $clWsdlType_Delete
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function delete(Delete $clWsdlType_Delete, $aHeaders = array())
    {
	    return $this->call('Delete', array($clWsdlType_Delete) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : DeleteFolder
     *
     * @param DeleteFolder $clWsdlType_DeleteFolder
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function deleteFolder(DeleteFolder $clWsdlType_DeleteFolder, $aHeaders = array())
    {
	    return $this->call('DeleteFolder', array($clWsdlType_DeleteFolder) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : DeletePJ
     *
     * @param DeletePJ $clWsdlType_DeletePJ
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function deletePj(DeletePJ $clWsdlType_DeletePJ, $aHeaders = array())
    {
	    return $this->call('DeletePJ', array($clWsdlType_DeletePJ) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : Disconnect
     *
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function disconnect($aHeaders = array())
    {
	    return $this->call('Disconnect', array() ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : Display
     *
     * @param Display $clWsdlType_Display
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function display(Display $clWsdlType_Display, $aHeaders = array())
    {
	    return $this->call('Display', array($clWsdlType_Display) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : DrillThrough
     *
     * @param Display DrillThrough
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function drillThrough(DrillThrough $clWsdlType_DrillThrough, $aHeaders = array())
    {
	    return $this->call('DrillThrough', array($clWsdlType_DrillThrough) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : EnterReorderListMode
     *
     * @param Display EnterReorderListMode
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function enterReorderListMode(EnterReorderListMode $clWsdlType_EnterReorderListMode, $aHeaders = array())
    {
	    return $this->call('EnterReorderListMode', array($clWsdlType_EnterReorderListMode) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : Execute
     *
     * @param Execute $clWsdlType_Execute
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function execute(Execute $clWsdlType_Execute, $aHeaders = array())
    {
	    return $this->call('Execute', array($clWsdlType_Execute) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetCalculation
     *
     * @param GetCalculation $clWsdlType_GetCalculation
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function getCalculation(GetCalculation $clWsdlType_GetCalculation, $aHeaders = array())
    {
	    return $this->call('GetCalculation', array((array)$clWsdlType_GetCalculation) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetChart
     *
     * @param GetChart $clWsdlType_GetChart
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function getChart(GetChart  $clWsdlType_GetChart, $aHeaders = array())
    {
	    return $this->call('GetChart', array($clWsdlType_GetChart) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetColInRecord
     *
     * @param GetColInRecord $clWsdlType_GetColInRecord
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function getColInRecord(GetColInRecord $clWsdlType_GetColInRecord, $aHeaders = array())
    {
	    return $this->call('GetColInRecord', array($clWsdlType_GetColInRecord) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetContentFolder
     *
     * @param GetContentFolder $clWsdlType_GetContentFolder
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function getContentFolder(GetContentFolder $clWsdlType_GetContentFolder, $aHeaders = array())
    {
	    return $this->call('GetContentFolder', array($clWsdlType_GetContentFolder) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetEndAutomatism
     *
     * @param GetEndAutomatism $clWsdlType_GetEndAutomatism
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function getEndAutomatism(GetEndAutomatism $clWsdlType_GetEndAutomatism, $aHeaders = array())
    {
	    return $this->call('GetEndAutomatism', array($clWsdlType_GetEndAutomatism) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetFolderList
     *
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function getFolderList($aHeaders = array())
    {
	    return $this->call('GetFolderList', array() ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetLanguages
     *
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function getLanguages($aHeaders)
    {
	    return $this->call('GetLanguages', array() ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetListMessage
     *
     * @param GetListMessage $clWsdlType_GetListMessage
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function getListMessage(GetListMessage $clWsdlType_GetListMessage, $aHeaders = array())
    {
	    return $this->call('GetListMessage', array($clWsdlType_GetListMessage) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline :GetMailServiceStatus
     *
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function getMailServiceStatus($aHeaders = array())
    {
	    return $this->call('GetMailServiceStatus', array() ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetPJ
     *
     * @param GetPJ $clWsdlType_GetPJ
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function getPJ(GetPJ $clWsdlType_GetPJ, $aHeaders = array())
    {
	    return $this->call('GetPJ', array($clWsdlType_GetPJ) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetPlanningInfo
     *
     * @param GetPlanningInfo $clWsdlType_GetPlanningInfo
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function getPlanningInfo(GetPlanningInfo $clWsdlType_GetPlanningInfo, $aHeaders = array())
    {
	    return $this->call('GetPlanningInfo', array($clWsdlType_GetPlanningInfo) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetStartAutomatism
     *
     * @param GetStartAutomatism $clWsdlType_GetStartAutomatism
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function getStartAutomatism(GetStartAutomatism $clWsdlType_GetStartAutomatism, $aHeaders = array())
    {
	    return $this->call('GetStartAutomatism', array($clWsdlType_GetStartAutomatism) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetTableChild
     *
     * @param GetTableChild $clWsdlType_GetTableChild
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function getTableChild(GetTableChild $clWsdlType_GetTableChild, $aHeaders = array())
    {
	    return $this->call('GetTableChild', array($clWsdlType_GetTableChild) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetTemporalAutomatism
     *
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function getTemporalAutomatism($aHeaders = array())
    {
	    return $this->call('GetTemporalAutomatism', array() ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetTokenSession
     *
     * @param GetTokenSession $clWsdlType_GetTokenSession
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function getTokenSession(GetTokenSession $clWsdlType_GetTokenSession, $aHeaders = array())
    {
	    return $this->call('GetTokenSession', array($clWsdlType_GetTokenSession) , null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : HasChanged
     *
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function hasChanged($aHeaders = array())
    {
	    return $this->call('HasChanged', array() ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : InitRecordFromAddress
     *
     * @param InitRecordFromAddress $clWsdlType_InitRecordFromAddress
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function initRecordFromAddress(InitRecordFromAddress $clWsdlType_InitRecordFromAddress, $aHeaders = array())
    {
	    return $this->call('InitRecordFromAddress', array($clWsdlType_InitRecordFromAddress) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : InitRecordFromMessage
     *
     * @param InitRecordFromMessage $clWsdlType_InitRecordFromMessage
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function initRecordFromMessage(InitRecordFromMessage $clWsdlType_InitRecordFromMessage, $aHeaders = array())
    {
	    return $this->call('InitRecordFromMessage', array($clWsdlType_InitRecordFromMessage) ,  null, null , $aHeaders);
    }
    //----

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : List
     *
     * @param List ListParams
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function listAction(ListParams $clWsdlType_List, $aHeaders = array())
    {
	    return $this->call('List', array($clWsdlType_List) ,  null, null , $aHeaders);
    }
    //----

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : Modify
     *
     * @param Modify $clWsdlType_Modify
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function modify(Modify $clWsdlType_Modify, $aHeaders = array())
    {
	    return $this->call('Modify', array($clWsdlType_Modify) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : ModifyFolder
     *
     * @param ModifyFolder $clWsdlType_ModifyFolder
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function modifyFolder(ModifyFolder $clWsdlType_ModifyFolder, $aHeaders = array())
    {
	    return $this->call('ModifyFolder', array($clWsdlType_ModifyFolder) ,  null, null , $aHeaders);
    }
    //---


    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : ModifyMessage
     *
     * @param ModifyMessage $clWsdlType_ModifyMessage
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function modifyMessage(ModifyMessage $clWsdlType_ModifyMessage, $aHeaders = array())
    {
	    return $this->call('ModifyMessage', array($clWsdlType_ModifyMessage) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : Print
     *
     * @param PrintParams $clWsdlType_Print
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function printAction(PrintParams $clWsdlType_Print, $aHeaders = array())
    {
	    return $this->call('Print', array($clWsdlType_Print) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : ReorderList
     *
     * @param ReorderList $clWsdlType_ModifyFolder
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function reorderList(ReorderList $clWsdlType_ReorderList, $aHeaders = array())
    {
	    return $this->call('ReorderList', array($clWsdlType_ReorderList) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : ReorderList
     *
     * @param ReorderSubList $clWsdlType_ModifyFolder
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function reorderSubList(ReorderSubList $clWsdlType_ReorderSubList, $aHeaders = array())
    {
	    return $this->call('ReorderSubList', array($clWsdlType_ReorderSubList) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : Request
     *
     * @param Request $clWsdlType_Request
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function request(Request $clWsdlType_Request, $aHeaders = array())
    {
	    return $this->call('Request', array($clWsdlType_Request) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : RequestMessage
     *
     * @param RequestMessage $clWsdlType_RequestMessage
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function requestMessage(RequestMessage $clWsdlType_RequestMessage, $aHeaders = array())
    {
	    return $this->call('RequestMessage', array($clWsdlType_RequestMessage) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : RequestParam
     *
     * @param RequestParam $clWsdlType_RequestParam
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function requestParam(RequestParam $clWsdlType_RequestParam, $aHeaders = array())
    {
	    return $this->call('RequestParam', array($clWsdlType_RequestParam) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : ResetPasswordFailed
     *
     * @param ResetPasswordFailed $clWsdlType_ResetPasswordFailed
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function resetPasswordFailed(ResetPasswordFailed $clWsdlType_ResetPasswordFailed, $aHeaders = array())
    {
	    return $this->call('ResetPasswordFailed', array($clWsdlType_ResetPasswordFailed) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : Search
     *
     * @param Search $clWsdlType_Search
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function search(Search $clWsdlType_Search, $aHeaders = array())
    {
	    return $this->call('Search', array($clWsdlType_Search) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : SelectForm
     *
     * @param SelectForm $clWsdlType_SelectForm
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function selectForm(SelectForm $clWsdlType_SelectForm, $aHeaders = array())
    {
	    return $this->call('SelectForm', array($clWsdlType_SelectForm) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : SelectItems
     *
     * @param SelectItems $clWsdlType_SelectForm
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function selectItems(SelectItems $clWsdlType_SelectItems, $aHeaders = array())
    {
	    return $this->call('SelectForm', array($clWsdlType_SelectItems) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : SelectPrintTemplate
     *
     * @param SelectPrintTemplate $clWsdlType_SelectPrintTemplate
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function selectPrintTemplate(SelectPrintTemplate $clWsdlType_SelectPrintTemplate, $aHeaders = array())
    {
	    return $this->call('SelectPrintTemplate', array($clWsdlType_SelectPrintTemplate) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : SendMessage
     *
     * @param SendMessage $clWsdlType_SendMessage
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function sendMessage(SendMessage $clWsdlType_SendMessage, $aHeaders = array())
    {
	    return $this->call('SendMessage', array($clWsdlType_SendMessage) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : SetOrderList
     *
     * @param SetOrderList $clWsdlType_SendMessage
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function setOrderList(SetOrderList $clWsdlType_SetOrderList, $aHeaders = array())
    {
	    return $this->call('SetOrderList', array($clWsdlType_SetOrderList) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : SetOrderSubList
     *
     * @param SetOrderSubList $clWsdlType_SendMessage
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function setOrderSubList(SetOrderSubList $clWsdlType_SetOrderSubList, $aHeaders = array())
    {
	    return $this->call('SetOrderSubList', array($clWsdlType_SetOrderSubList) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : TransformInto
     *
     * @param TransformInto $clWsdlType_TransformInto
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function transformInto(TransformInto $clWsdlType_TransformInto, $aHeaders = array())
    {
	    return $this->call('TransformInto', array($clWsdlType_TransformInto) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : Update
     *
     * @param Update $clWsdlType_Update
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function update(Update $clWsdlType_Update, $aHeaders = array())
    {
	    return $this->call('Update', array($clWsdlType_Update) ,  null, null , $aHeaders);
    }
    //----

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : UpdateFolder
     *
     * @param UpdateFolder $clWsdlType_UpdateFolder
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function updateFolder(UpdateFolder $clWsdlType_UpdateFolder, $aHeaders = array())
    {
	    return $this->call('UpdateFolder', array($clWsdlType_UpdateFolder) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : UpdateMessage
     *
     * @param UpdateMessage $clWsdlType_UpdateMessage
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function updateMessage(UpdateMessage $clWsdlType_UpdateMessage, $aHeaders = array())
    {
	    return $this->call('UpdateMessage', array($clWsdlType_UpdateMessage) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : Validate
     *
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function validate($aHeaders = array())
    {
	    return $this->call('Validate', array() ,  null, null , $aHeaders);
    }
    //---


    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : ValidateFolder
     *
     * @param ValidateFolder $clWsdlType_ValidateFolder
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function validateFolder(ValidateFolder $clWsdlType_ValidateFolder, $aHeaders = array())
    {
	    return $this->call('ValidateFolder', array($clWsdlType_ValidateFolder) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : WithAutomaticResponse
     *
     * @param WithAutomaticResponse $clWsdlType_WithAutomaticResponse
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function withAutomaticResponse(WithAutomaticResponse $clWsdlType_WithAutomaticResponse, $aHeaders = array())
    {
	    return $this->call('WithAutomaticResponse', array($clWsdlType_WithAutomaticResponse) ,  null, null , $aHeaders);
    }
    //---

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : ZipPJ
     *
     * @param ZipPJ $clWsdlType_ZipPJ
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     */
    public function zipPJ(ZipPJ $clWsdlType_ZipPJ, $aHeaders = array())
    {
	    return $this->call('ZipPJ', array($clWsdlType_ZipPJ) ,  null, null , $aHeaders);
    }
    //---
}
//***


//-------------------------------------------------------------------------------------------------------------------
// Ensemble de classes utilisé par la classe SimaxOnlineServiceProxy
// Note : les conventions de code peuvent semblé non respecté sur les nom de variables, mais elle corresponde en realite
// au fichier WSDL
//-------------------------------------------------------------------------------------------------------------------


/*
 * definition des classe de constante utile a la communication soap
 *
 */
/*
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
*/

/*
 * definition des classe de constante utile a la communication soap mais non presente dans la WSDL (uniquement dans la doc)
 */
/*
class COutOfWsdlType_CalculEnumForGetCalculation
{
    const Sum = 'sum';
    const Average = 'average';
    const Min = 'min';
    const Max = 'max';
    const Count = 'count';
    const Percent = 'percent';
}


class GetTokenSessionResponse
{
	public $SessionToken; // string
}
//***


class ResetPasswordFailedResponse
{
	public $xml; // string
}
//***


class GetStartAutomatismResponse
{
	public $xml; // string
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

class SelectFormResponse
{
	public $xml; // string
}
//***
class SelectPrintTemplateResponse
{
	public $xml; // string
}
//***
class GetPlanningInfoResponse
{
	public $xml; // string
}
//***

class GetColInRecordResponse
{
	public $xml; // string
}
//***
class DisplayResponse
{
	public $xml; // string
}
//***
class CreateResponse
{
	public $xml; // string
}
//***

class CreateFromResponse
{
	public $xml; // string
}
//***
class TransformIntoResponse
{
	public $xml; // string
}
//***
//***
class ModifyResponse
{
	public $xml; // string
}
//***
class UpdateResponse
{
	public $xml; // string
}
//***
class PrintResponse
{
	public $xml; // string
}
//***
class DeleteResponse
{
	public $xml; // string
}
//***
class ExecuteResponse
{
	public $xml; // string
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
class CancelResponse
{
	public $xml; // string
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
class GetChartResponse
{
	public $xml; // string
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
class ReorderListResponse
{
	public $Value; // string
}
//***
class SetOrderListResponse
{
	public $Value; // string
}
//***
class ReorderSubListResponse
{
	public $Value; // string
}
//***

class SetOrderSubListResponse
{
	public $Value; // string
}
//***
class GetCalculationResponse
{
	public $xml; // string
}
//***
class SearchResponse
{
	public $xml; // string
}
//***
class RequestResponse
{
	public $xml; // string
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
class GetEndAutomatismResponse
{
	public $xml; // string
}
//***
class UpdateFolderResponse
{
	public $xml; // string
}
//***

class GetContentFolderResponse
{
	public $xml; // string
}
//***
class GetTableChildResponse
{
	public $xml; // string
}
//***

class GetFolderListResponse
{
	public $xml; // string
}
//***

class CreateFolderResponse
{
	public $xml; // string
}
//***

class DeletePJResponse
{
	public $xml; // string
}
//***

class GetPJResponse
{
	public $xml; // GetPJResponseType
}
//***

class SendMessageResponse
{
	public $xml; // string
}
//***

class CreateMessageResponse
{
	public $xml; // string
}
//***

class UpdateMessageResponse
{
	public $xml; // string
}
//***


class ModifyMessageResponse
{
	public $xml; // string
}
//***
class GetListMessageResponse
{
	public $xml; // string
}
//***
class RequestMessageResponse
{
	public $xml; // string
}
//***

class ModifyFolderResponse
{
	public $xml; // string
}
//***

class AddPJResponse
{
	public $xml; // string
}
//***

class CheckRecipientResponse
{
	public $xml; // string
}
//***

class ZipPJResponse
{
	public $xml; // string
}
//***

class CheckCreateElementResponse
{
	public $xml; // string
}
//***

class InitRecordFromMessageResponse
{
	public $xml; // string
}
//***
class InitRecordFromAddressResponse
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


class WithAutomaticResponseResponse
{
	public $xml; // string
}
//***
*/