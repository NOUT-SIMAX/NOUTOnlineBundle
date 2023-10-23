<?php

namespace NOUT\Bundle\NOUTOnlineBundle\SOAP;
//WSDLEntity utilsé en paramètres
use NOUT\Bundle\NOUTOnlineBundle\DataCollector\NOUTOnlineLogger;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ConfigurationDialogue;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Header\OptionDialogue;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS;
use NOUT\Bundle\NOUTOnlineBundle\Entity\UsernameToken\UsernameToken;
use NOUT\Bundle\NOUTOnlineBundle\Security\Authentication\Token\NOUTToken;
use NOUT\Bundle\NOUTOnlineBundle\Service\ClientInformation;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\NUSOAP\SOAPTransportHTTP;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\NUSOAP\WSDL;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\AddPJ;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ButtonAction;
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
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\DeleteMessage;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\DeletePJ;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Display;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\DisplayRedoMessage;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\DisplayUndoMessage;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\DrillThrough;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Execute;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ExecuteWithoutIHM;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetCalculation;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetChart;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetColInRecord;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetContentFolder;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetEndAutomatism;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetListIDMessFromFolder;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetListIDMessFromRequest;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetListMessage;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetMessagesFromListID;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetPJ;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetPlanningInfo;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetRedoList;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetRedoListID;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetStartAutomatism;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetSubListContent;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetTableChild;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetTokenSession;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetUndoList;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetUndoListID;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\InitRecordFromAddress;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\InitRecordFromMessage;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ListParams;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Merge;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Modify;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ModifyFolder;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ModifyMessage;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\PrintParams;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Redo;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ReorderList;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ReorderSubList;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Request;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\RequestMessage;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\RequestParam;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ResetPasswordFailed;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Search;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SelectChoice;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SelectForm;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SelectItems;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SelectPrintTemplate;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SendMessage;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SetOrderList;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SetOrderSubList;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\TransformInto;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Undo;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Update;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\UpdateColumnMessageValueInBatch;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\UpdateFilter;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\UpdateFolder;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\UpdateMessage;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ValidateFolder;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\WithAutomaticResponse;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ZipPJ;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Stopwatch\Stopwatch;

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
final class OnlineServiceProxy extends ModifiedNusoapClient
{
    /**
     * Definition des variable pour gestion des headers de requete
     * @var array
     */
    private array $aListHeaders = [];

    /**
     * sert a savoir si on remet les headers a zero avant une requete
     * @var bool
     */
    private bool $bCleanHeadersBeforeRequest = true;

    /**
     * classe de configuration
     * @var ConfigurationDialogue
     */
    private ConfigurationDialogue $clConfigurationDialogue;

    /**
     * logger symfony
     * @var NOUTOnlineLogger
     */
    private NOUTOnlineLogger $clLogger;

    /**
     * @var Stopwatch
     */
    private Stopwatch $clStopwatch;

    /**
     * @var GestionWSDL
     */
    private GestionWSDL $clGestionWSDL;

    /**
     * @var ClientInformation
     */
    private ClientInformation $clInfoClient;

    /**
     * Constructeur permettant d'instancier les classe de communication soap avec les bonne question
     *
     * @param ClientInformation     $clientInfo
     * @param ConfigurationDialogue $clConfig
     * @param NOUTOnlineLogger      $_clLogger
     * @param Stopwatch|null        $stopwatch
     * @param GestionWSDL           $clGestionWSDL
     * @param int                   $soapSocketTimeout
     * @param TokenStorageInterface $tokenStorage
     * @throws \Exception
     */
    public function __construct(
        ClientInformation     $clientInfo,
        ConfigurationDialogue $clConfig,
        NOUTOnlineLogger      $_clLogger,
        GestionWSDL           $clGestionWSDL,
        TokenStorageInterface $tokenStorage,
        Stopwatch             $stopwatch = null,
                              $soapSocketTimeout = self::SOCKET_TIMEOUT
    )
    {
        parent::__construct($clConfig->getWSDLUri(), $clConfig->getWsdl(), $clConfig->getHost(), $clConfig->getPort());

        $this->clConfigurationDialogue = $clConfig;
        $this->clInfoClient            = $clientInfo;

        $this->forceEndpoint = $clConfig->getProtocolPrefix() . $clConfig->getHost() . ':' . $clConfig->getPort(); //on force l'ip et le port du fichier config
        // on force le timeout a 300s
        $this->timeout          = 300;
        $this->response_timeout = $soapSocketTimeout;
        $this->clLogger         = $_clLogger;
        $this->clStopwatch = $stopwatch;

        //il faut lire le début de endpoint pour avoir la version de la wsdl
        $token = $tokenStorage->getToken();
        $clNOUTOnlineVersion = null;
        if ($token instanceof NOUTToken && !empty($token->nGetIDUser()))
        {
            //uniquement si connecté
            $clNOUTOnlineVersion = $token->clGetNOUTOnlineVersion();
        }

        $this->clGestionWSDL = $clGestionWSDL;
        $this->clGestionWSDL->init($clConfig->getNOVersionUri(), $clNOUTOnlineVersion);
    }


    /**
     * Méthode qui marque le début du send
     */
    private function __StartSend()
    {
        if (isset($this->clLogger)) { //log des requetes
            $this->clLogger->startSend();
        }

        if (isset($this->clStopwatch)) {
            $this->clStopwatch->start('NOUT\Bundle\NOUTOnlineBundle\SOAP\OnlineServiceProxy::send');
        }
    }

    /**
     * Méthode qui marque la fin du send
     */
    private function __StopSend()
    {
        if (isset($this->clStopwatch)) {
            $this->clStopwatch->stop('NOUT\Bundle\NOUTOnlineBundle\SOAP\OnlineServiceProxy::send');
        }

        if (isset($this->clLogger)) { //log des requetes
            $this->clLogger->stopSend();
        }
    }

    /**
     * Charge la wsdl depuis le cache si disponible
     * @return bool
     */
    protected function _loadWSDLFromCache()
    {
        return $this->clGestionWSDL->load();
    }

    /**
     * Sauve la wsdl en cache pour usage futur
     */
    protected function _saveWSDLInCache()
    {
        $this->clGestionWSDL->save($this->wsdl, $this->clConfigurationDialogue->getDureeSession());
    }

    /**
     * instantiate wsdl object and parse wsdl file,
     * charge la wsdl depuis le cache s'il existe
     *
     * @access    public
     */
    public function loadWSDL()
    {
        $this->wsdl = $this->_loadWSDLFromCache();
        if (!$this->wsdl || !empty($this->wsdl->error_str)) {
            $this->wsdl = new WSDL('', $this->proxyhost, $this->proxyport, $this->proxyusername, $this->proxypassword, $this->timeout, $this->response_timeout, $this->curl_options, $this->use_curl);
            $this->wsdl->setCredentials($this->username, $this->password, $this->authtype, $this->certRequest);
            $this->wsdl->fetchWSDL($this->wsdlFile);

            $this->_saveWSDLInCache();
        }

        $this->checkWSDL();
    }


    /**
     * Ajoute-les headers spécifique SIMAX
     * @param $http
     */
    protected function _setHTTPHeader($http)
    {
        $http->setHeader(ConfigurationDialogue::HTTP_SIMAX_CLIENT_IP, $this->clInfoClient->getIP());
        $http->setHeader(ConfigurationDialogue::HTTP_SIMAX_CLIENT, $this->clConfigurationDialogue->getSociete());
        $http->setHeader(ConfigurationDialogue::HTTP_SIMAX_CLIENT_Version, $this->clConfigurationDialogue->getVersion());
    }

    /**
     * send the SOAP message
     * Note: if the operation has multiple return values
     * the return value of this method will be an array
     * of those values.
     *
     * @param string  $msg             a SOAPx4 soapmsg object
     * @param string  $soapaction      SOAPAction value
     * @param integer $timeout         set connection timeout in seconds
     * @param integer $responseTimeout set response timeout in seconds
     *
     * @return    mixed native PHP types.
     * @access   private
     */
    public function send($msg, $soapaction = '', $timeout = 0, $responseTimeout = 30)
    {
        $this->checkCookies();
        // detect transport
        if (!preg_match('/^http/', $this->endpoint))
        {
            //ne matche pas
            $this->setError('no transport found, or selected transport is not yet supported!');
            return false;
        }

        if ($this->persistentConnection && is_object($this->persistentConnection)) {
            $http =& $this->persistentConnection;
        } else {
            $http = new SOAPTransportHTTP($this->endpoint, $this->curl_options, $this->use_curl);
            if ($this->persistentConnection) {
                $http->usePersistentConnection();
            }
        }
        $http->setContentType($this->getHTTPContentType(), $this->getHTTPContentTypeCharset());
        $http->setSOAPAction($soapaction);
        if ($this->proxyhost && $this->proxyport) {
            $http->setProxy($this->proxyhost, $this->proxyport, $this->proxyusername, $this->proxypassword);
        }
        if ($this->authtype != '') {
            $http->setCredentials($this->username, $this->password, $this->authtype, array(), $this->certRequest);
        }
        if ($this->http_encoding != '') {
            $http->setEncoding($this->http_encoding);
        }

        $this->_setHTTPHeader($http);
        $this->__StartSend();
        if (preg_match('/^http:/', $this->endpoint)) {
            //if(strpos($this->endpoint,'http:')){
            $this->responseData = $http->send($msg, $timeout, $responseTimeout, $this->cookies);
        } elseif (preg_match('/^https/', $this->endpoint)) {
            //} elseif(strpos($this->endpoint,'https:')){
            //if(phpversion() == '4.3.0-dev'){
            //$response = $http->send($msg,$timeout,$response_timeout);
            //$this->request = $http->outgoing_payload;
            //$this->response = $http->incoming_payload;
            //} else
            $this->responseData = $http->sendHTTPS($msg, $timeout, $responseTimeout, $this->cookies);
        } else {
            $this->setError('no http/s in endpoint url');
        }
        $this->__StopSend();

        $this->request = $http->outgoing_payload;
        $this->response = $http->incoming_payload;
        $this->UpdateCookies($http->incoming_cookies);

        // save transport object if using persistent connections
        if ($this->persistentConnection && !is_object($this->persistentConnection)) {
            $this->persistentConnection = $http;
        }

        if ($err = $http->getError()) {
            $this->setError('HTTP Error: ' . $err);
            return false;
        } elseif ($this->getError()) {
            return false;
        } else {
            return $this->_parseResponse($http->incoming_headers, $this->responseData, $http->response_status_line);
        }
    }

    /**
     * Surchargé pour modification des contenus type de retour "application/soap+xml"
     *
     * processes SOAP message returned from server
     *
     * @param array  $headers The HTTP headers
     * @param string $data    unprocessed response data from server
     * @return    mixed    value of the message, decoded into a PHP type
     * @access   public
     *
     * * @see lib/NUSOAPClient#parseResponse
     */
    public function _parseResponse(array $headers, $data, $responseStatusLine)
    {
        if (strncmp($responseStatusLine, 'HTTP/1.1 500 ', strlen('HTTP/1.1 500 ')) != 0) {
            return $this->response;
        }

        return parent::parseResponse($headers, $data);
    }
    


    

    //------------------------------------------------------------------------------------------
    // Fonctions de gestion des headers de requete :
    //------------------------------------------------------------------------------------------

    /**
     * Par default, les header sont nettoyé  avant la contstruction de la requete, suite a l'appel de cette fonction il ne le sont plus
     * @param void
     * @return void
     * @access public
     */
    public function desactiveAutoCleanHeadersBeforeRequest()
    {
        $this->bCleanHeadersBeforeRequest = false;
    }
    

    /**
     * Reactive le nettoyage automatique des headers avant requete.
     */
    public function reactivateAutoCleanHeadersBeforeRequest()
    {
        $this->bCleanHeadersBeforeRequest = true;
    }
    

    /**
     * Fonction permettant de remettre la liste des headers a vide
     *
     * @param void
     * @return void
     * @access public
     */
    public function cleanListHeaders()
    {
        $this->aListHeaders = array();
    }
    

    /**
     * Fonction permettant d'ajouter plusieur headers de requete à la fois, grace a un tableau
     * @param array $aHeaders La liste des headers à ajouter sous force de tableau associatif (nom header => valeur)
     * @return void
     * @access public
     */
    public function addMultipleHeaders($aHeaders)
    {
        if (is_array($aHeaders)) {
            foreach ($aHeaders as $sKey => $mValue) {
                $this->aListHeaders[$sKey] = $mValue;
            }
        }
    }
    


    /**
     * Fonction permettant d'ajouter un header de requete
     * @param string $sName  le nom du header a ajouter
     * @param mixed  $mValue la valeur que l'on souhaite ajouter au header.
     * @return void
     * @access public
     */
    public function addHeader(string $sName = '', $mValue = '')
    {
        $this->aListHeaders[$sName] = $mValue;
    }
    

    //------------------------------------------------------------------------------------------
    // Redefinition methode call
    //------------------------------------------------------------------------------------------
    /**
     * Redefinition de la methode call de maniére a gérer les headers obligatoire de la communication
     * avec le service simax de simaxOnline
     *
     * @see    core/soap/ModifiedNuSoapClient#call($sOperation, $mParams, $mHeaders)
     *
     * @access public
     *
     * //note : $sStyle et $sUse sont des parametre inutile il ne sont la que pour permettre la surchage de methode sans modification de la signature.
     *
     * @param string $sOperation
     * @param array  $mParams
     * @param null   $sNamespace
     * @param null   $sSoapAction
     * @param mixed  $mHeaders
     * @param null   $mRpcParams
     * @param string $sStyle
     * @param string $sUse
     * @return XMLResponseWS
     * @throws \Exception
     */
    public function call($sOperation, $mParams = array(), $sNamespace = null, $sSoapAction = null, $mHeaders = false, $mRpcParams = null, $sStyle = 'rpc', $sUse = 'encoded')
    {
        //petite modif sur le paramètre mParams si tableau vide
        if (empty($mParams)) {
            $mParams = '<' . $sOperation . ' />';
        }

        //s'il le faut, avant toute chose, on nettoye les header
        if ($this->bCleanHeadersBeforeRequest) {
            $this->cleanListHeaders();
        }

        //on rajoute les header
        $this->addMultipleHeaders($mHeaders);

        if (isset($this->aListHeaders[self::HEADER_UsernameToken]) && $this->aListHeaders[self::HEADER_UsernameToken] instanceof UsernameToken) {
            $this->aListHeaders[self::HEADER_UsernameToken]->Compute();
        }

        if (!isset($this->aListHeaders[self::HEADER_OptionDialogue]) || is_object($this->aListHeaders[self::HEADER_OptionDialogue])) {
            if (!isset($this->aListHeaders[self::HEADER_OptionDialogue]))//si le la partie optiondialogue du header n'est pas passer en param on la crée
            {
                $this->aListHeaders[self::HEADER_OptionDialogue] = new OptionDialogue();
                $this->aListHeaders[self::HEADER_OptionDialogue]->InitDefault($this->clConfigurationDialogue->getVersionDialoguePref());
            }

            //on transforme l'objet en tableau associatif
            $this->aListHeaders[self::HEADER_OptionDialogue] = (array)$this->aListHeaders[self::HEADER_OptionDialogue];
        }

        if (is_null($this->aListHeaders[self::HEADER_OptionDialogue][self::HEADER_OptionDialogue_DisplayValue])) {
            $this->aListHeaders[self::HEADER_OptionDialogue][self::HEADER_OptionDialogue_DisplayValue] = OptionDialogue::DISPLAY_No_ID;
        }

        //on ajoute le bon code langue.
        if (is_null($this->aListHeaders[self::HEADER_OptionDialogue][self::HEADER_OptionDialogue_LanguageCode])) {
            $this->aListHeaders[self::HEADER_OptionDialogue][self::HEADER_OptionDialogue_LanguageCode] = $this->clConfigurationDialogue->getLangCode();
        }

        //si on a pas de withFieldStateControl precisé, on le mets à 1 (pour recuperer les controle d'etat de champ)
        if (is_null($this->aListHeaders[self::HEADER_OptionDialogue][self::HEADER_WithFieldStateControl])) {
            $this->aListHeaders[self::HEADER_OptionDialogue][self::HEADER_WithFieldStateControl] = 1;
        }

        //si on a pas de ListContentAsync precisé, on le mets à 1 (pour forcer le chargement des sous-listes en ajax)
        if (is_null($this->aListHeaders[self::HEADER_OptionDialogue][self::HEADER_OptionDialogue_ListContentAsync])) {
            $this->aListHeaders[self::HEADER_OptionDialogue][self::HEADER_OptionDialogue_ListContentAsync] = 1;
        }

        //on ajoute l'id application
        $this->aListHeaders[self::HEADER_APIUUID] = $this->clConfigurationDialogue->getAPIUUID();


        if (array_key_exists(self::HEADER_UsernameToken, $this->aListHeaders)) {
            $this->aListHeaders[self::HEADER_UsernameToken]->transformForSOAP();
        }


        //ajoute au log s'il faut
        $this->__startLogQuery();

        try {
            //$old_time = ini_get('max_execution_time');
            ini_set('max_execution_time', 0);
            set_time_limit(0);

            //on fait l'appel a la methode mere
            /*$mResult =  */
            parent::call($sOperation, $mParams, $sNamespace, $sSoapAction, $this->aListHeaders, $mRpcParams, null, null);

            //set_time_limit($old_time);
        } catch (SOAPException $e) {
            $sError = empty($this->response) ? $this->error_str : $this->response;
            $this->__stopLogQuery($sOperation, $sError, true);

            throw $e;
        }

        //ajoute au log s'il faut
        $this->__stopLogQuery($sOperation, $this->response, false);

        //on ne veut pas l'objet retourné par NUSOAP qui est un tableau associatif mais un objet qui permet de manipuler la réponse
        return $this->getXMLResponseWS();
    }

    

    protected function __startLogQuery()
    {
        if (isset($this->clLogger)) //log des requetes
        {
            $this->clLogger->startQuery();
        }

        if (isset($this->clStopwatch)) {
            $this->clStopwatch->start('NOUT\Bundle\NOUTOnlineBundle\SOAP\OnlineServiceProxy::call');
        }
    }

    protected function __stopLogQuery($operation, $reponse, $bError)
    {
        if (isset($this->clStopwatch)) {
            $this->clStopwatch->stop('NOUT\Bundle\NOUTOnlineBundle\SOAP\OnlineServiceProxy::call');
        }

        if (isset($this->clLogger)) //log des requetes
        {
            $extra = array();
            if (isset($this->aListHeaders[self::HEADER_SessionToken])) {
                $extra[NOUTOnlineLogger::EXTRA_TokenSession] = $this->aListHeaders[self::HEADER_SessionToken];
            }
            if (isset($this->aListHeaders[self::HEADER_ActionContext])) {
                $extra[NOUTOnlineLogger::EXTRA_ActionContext] = $this->aListHeaders[self::HEADER_ActionContext];
            }

            if ($bError) {
                $this->clLogger->stopSOAPQueryError($this->request, $reponse, $operation, $extra);
            }
            else {
                $this->clLogger->stopSOAPQuery($this->request, $reponse, $operation, $extra);
            }
        }
    }


    /**
     * @return XMLResponseWS
     * @throws SOAPException
     */
    public function getXMLResponseWS() : XMLResponseWS
    {
        if (empty($this->responseData)) {
            throw new SOAPException('La réponse du service est vide (' . $this->error_str . ')');
        }

        //retourne un XMLResponseWS qui permet de manipuler la réponse
        return new XMLResponseWS($this->responseData);
    }

    //------------------------------------------------------------------------------------------
    // Fonction d'appel  direct soap
    //------------------------------------------------------------------------------------------
    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : AddPJ
     *
     * @param AddPJ $clWsdlTypeAddPJ
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     *
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function addPJ(AddPJ $clWsdlTypeAddPJ, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('AddPJ', array($clWsdlTypeAddPJ), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline :Cancel
     *
     * @param Cancel $clWsdlTypeCancel
     * @param array  $aHeaders tableau d'headers a ajouter a la requete
     *
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function cancel(Cancel $clWsdlTypeCancel, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('Cancel', array($clWsdlTypeCancel), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : CancelFolder
     *
     * @param CancelFolder $clWsdlTypeCancelFolder
     * @param array        $aHeaders tableau d'headers a ajouter a la requete
     *
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function cancelFolder(CancelFolder $clWsdlTypeCancelFolder, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('CancelFolder', array($clWsdlTypeCancelFolder), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : CancelMessage
     *
     * @param CancelMessage $clWsdlTypeCancelMessage
     * @param array         $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function cancelMessage(CancelMessage $clWsdlTypeCancelMessage, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('CancelMessage', array($clWsdlTypeCancelMessage), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : CheckCreateElement
     *
     * @param CheckCreateElement $clWsdlTypeCheckCreateElement
     * @param array              $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function checkCreateElement(CheckCreateElement $clWsdlTypeCheckCreateElement, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('CheckCreateElement', array($clWsdlTypeCheckCreateElement), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : CheckRecipient
     *
     * @param CheckRecipient $clWsdlTypeCheckRecipient
     * @param array          $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function checkRecipient(CheckRecipient $clWsdlTypeCheckRecipient, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('CheckRecipient', array($clWsdlTypeCheckRecipient), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : CloseFolderList
     *
     * @param CloseFolderList $clWsdlTypeCloseFolderList
     * @param array           $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function closeFolderList(CloseFolderList $clWsdlTypeCloseFolderList, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('CloseFolderList', array($clWsdlTypeCloseFolderList), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : CloseMessageList
     *
     * @param CloseMessageList $clWsdlTypeCloseMessageList
     * @param array            $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function closeMessageList(CloseMessageList $clWsdlTypeCloseMessageList, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('CloseMessageList', array($clWsdlTypeCloseMessageList), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : ConfirmResponse
     *
     * @param ConfirmResponse $clWsdlTypeConfirmResponse
     * @param array           $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function ConfirmResponse(ConfirmResponse $clWsdlTypeConfirmResponse, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('ConfirmResponse', array($clWsdlTypeConfirmResponse), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : Create
     *
     * @param Create $clWsdlTypeCreate
     * @param array  $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function create(Create $clWsdlTypeCreate, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('Create', array($clWsdlTypeCreate), null, null, $aHeaders);
    }
    

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : CreateFolder
     *
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function createFolder(array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('CreateFolder', array(), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : CreateFrom
     *
     * @param CreateFrom $clWsdlTypeCreateFrom
     * @param array      $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function createFrom(CreateFrom $clWsdlTypeCreateFrom, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('CreateFrom', array($clWsdlTypeCreateFrom), null, null, $aHeaders);
    }

    

    /**
     * @param Merge $merge
     * @param array $aHeaders
     *
     * @return XMLResponseWS
     * @throws \Exception
     */
    public function merge(Merge $merge, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('Merge', array($merge), null, null, $aHeaders);
    }

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : CreateMessage
     *
     * @param CreateMessage $clWsdlTypeCreateMessage
     * @param array         $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function createMessage(CreateMessage $clWsdlTypeCreateMessage, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('CreateMessage', array($clWsdlTypeCreateMessage), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : Delete
     *
     * @param Delete $clWsdlTypeDelete
     * @param array  $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function delete(Delete $clWsdlTypeDelete, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('Delete', array($clWsdlTypeDelete), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : DeleteFolder
     *
     * @param DeleteFolder $clWsdlTypeDeleteFolder
     * @param array        $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function deleteFolder(DeleteFolder $clWsdlTypeDeleteFolder, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('DeleteFolder', array($clWsdlTypeDeleteFolder), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : DeletePJ
     *
     * @param DeletePJ $clWsdlTypeDeletePJ
     * @param array    $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function deletePj(DeletePJ $clWsdlTypeDeletePJ, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('DeletePJ', array($clWsdlTypeDeletePJ), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : Disconnect
     *
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function disconnect(array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('Disconnect', array(), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : Display
     *
     * @param Display $clWsdlTypeDisplay
     * @param array   $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function display(Display $clWsdlTypeDisplay, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('Display', array($clWsdlTypeDisplay), null, null, $aHeaders);
    }
    

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : DrillThrough
     *
     * @param DrillThrough $clWsdlTypeDrillThrough
     * @param array        $aHeaders tableau d'headers a ajouter a la requete
     *
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function drillThrough(DrillThrough $clWsdlTypeDrillThrough, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('DrillThrough', array($clWsdlTypeDrillThrough), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : EnterReorderListMode
     *
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function enterReorderListMode(array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('EnterReorderListMode', array(), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : Execute
     *
     * @param Execute $clWsdlTypeExecute
     * @param array   $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function execute(Execute $clWsdlTypeExecute, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('Execute', array($clWsdlTypeExecute), null, null, $aHeaders);
    }


    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : Execute
     *
     * @param ExecuteWithoutIHM $clWsdlTypeExecute
     * @param array   $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function executeWithoutIHM(ExecuteWithoutIHM $clWsdlTypeExecute, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('ExecuteWithoutIHM', array($clWsdlTypeExecute), null, null, $aHeaders);
    }

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetCalculation
     *
     * @param GetCalculation $clWsdlTypeGetCalculation
     * @param array          $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */

    public function getCalculation(GetCalculation $clWsdlTypeGetCalculation, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('GetCalculation', array((array)$clWsdlTypeGetCalculation), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetChart
     *
     * @param GetChart $clWsdlTypeGetChart
     * @param array    $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function getChart(GetChart $clWsdlTypeGetChart, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('GetChart', array($clWsdlTypeGetChart), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetColInRecord
     *
     * @param GetColInRecord $clWsdlTypeGetColInRecord
     * @param array          $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function getColInRecord(GetColInRecord $clWsdlTypeGetColInRecord, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('GetColInRecord', array($clWsdlTypeGetColInRecord), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetColInRecord
     *
     * @param GetSubListContent $clWsdlTypeGetSubListContent
     * @param array             $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function getSubListContent(GetSubListContent $clWsdlTypeGetSubListContent, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('GetSubListContent', array($clWsdlTypeGetSubListContent), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetContentFolder
     *
     * @param GetContentFolder $clWsdlTypeGetContentFolder
     * @param array            $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function getContentFolder(GetContentFolder $clWsdlTypeGetContentFolder, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('GetContentFolder', array($clWsdlTypeGetContentFolder), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetContentFolder
     *
     * @param GetListIDMessFromFolder $clWsdType
     * @param array                   $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function getListIDMessFromFolder(GetListIDMessFromFolder $clWsdType, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('GetListIDMessFromFolder', array($clWsdType), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetContentFolder
     *
     * @param GetListIDMessFromRequest $clWsdType
     * @param array                    $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function getListIDMessFromRequest(GetListIDMessFromRequest $clWsdType, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('GetListIDMessFromRequest', array($clWsdType), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetContentFolder
     *
     * @param GetMessagesFromListID $clWsdType
     * @param array                 $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function getMessagesFromListID(GetMessagesFromListID $clWsdType, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('GetMessagesFromListID', array($clWsdType), null, null, $aHeaders);
    }
    

    /**
     * @param RequestMessage $requestMessage
     * @param array          $aHeaders
     * @return XMLResponseWS
     * @throws \Exception
     */
    public function getRequestMessage(RequestMessage $requestMessage, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('RequestMessage', array($requestMessage), null, null, $aHeaders);
    }

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetEndAutomatism
     *
     * @param GetEndAutomatism $clWsdlTypeGetEndAutomatism
     * @param array            $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function getEndAutomatism(GetEndAutomatism $clWsdlTypeGetEndAutomatism, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('GetEndAutomatism', array($clWsdlTypeGetEndAutomatism), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetFolderList
     *
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function getFolderList(array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('GetFolderList', array(), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetLanguages
     *
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function getLanguages(array $aHeaders) : XMLResponseWS
    {
        return $this->call('GetLanguages', array(), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetListMessage
     *
     * @param GetListMessage $clWsdlTypeGetListMessage
     * @param array          $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function getListMessage(GetListMessage $clWsdlTypeGetListMessage, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('GetListMessage', array($clWsdlTypeGetListMessage), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline :GetMailServiceStatus
     *
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function getMailServiceStatus(array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('GetMailServiceStatus', array(), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetPJ
     *
     * @param GetPJ $clWsdlTypeGetPJ
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function getPJ(GetPJ $clWsdlTypeGetPJ, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('GetPJ', array($clWsdlTypeGetPJ), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetPlanningInfo
     *
     * @param GetPlanningInfo $clWsdlTypeGetPlanningInfo
     * @param array           $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function getPlanningInfo(GetPlanningInfo $clWsdlTypeGetPlanningInfo, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('GetPlanningInfo', array($clWsdlTypeGetPlanningInfo), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetStartAutomatism
     *
     * @param GetStartAutomatism $clWsdlTypeGetStartAutomatism
     * @param array              $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function getStartAutomatism(GetStartAutomatism $clWsdlTypeGetStartAutomatism, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('GetStartAutomatism', array($clWsdlTypeGetStartAutomatism), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetTableChild
     *
     * @param GetTableChild $clWsdlTypeGetTableChild
     * @param array         $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function getTableChild(GetTableChild $clWsdlTypeGetTableChild, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('GetTableChild', array($clWsdlTypeGetTableChild), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetTemporalAutomatism
     *
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function getTemporalAutomatism(array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('GetTemporalAutomatism', array(), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetTokenSession
     *
     * @param GetTokenSession $clParam
     * @param array           $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @access public
     * @throws \Exception
     */
    public function getTokenSession(GetTokenSession $clParam, array $aHeaders = array()) : XMLResponseWS
    {
        if (isset($clParam->UsernameToken) && $clParam->UsernameToken instanceof UsernameToken) {
            $clParam->UsernameToken->Compute();
        }
        if ($clParam->ExtranetUser
            && isset($clParam->ExtranetUser->UsernameToken)
            && $clParam->ExtranetUser->UsernameToken instanceof UsernameToken) {
            $clParam->ExtranetUser->UsernameToken->Compute();
        }

        return $this->call('GetTokenSession', array($clParam), null, null, $aHeaders);
    }

    /**
     * @return GestionWSDL
     */
    public function getGestionWSDL() : GestionWSDL
    {
        return $this->clGestionWSDL;
    }

    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : HasChanged
     *
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function hasChanged(array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('HasChanged', array(), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : InitRecordFromAddress
     *
     * @param InitRecordFromAddress $clWsdlTypeInitRecordFromAddress
     * @param array                 $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function initRecordFromAddress(InitRecordFromAddress $clWsdlTypeInitRecordFromAddress, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('InitRecordFromAddress', array($clWsdlTypeInitRecordFromAddress), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : InitRecordFromMessage
     *
     * @param InitRecordFromMessage $clWsdlTypeInitRecordFromMessage
     * @param array                 $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function initRecordFromMessage(InitRecordFromMessage $clWsdlTypeInitRecordFromMessage, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('InitRecordFromMessage', array($clWsdlTypeInitRecordFromMessage), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : List
     *
     * @param ListParams $clWsdlTypeList
     * @param array      $aHeaders tableau d'headers a ajouter a la requete
     *
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function listAction(ListParams $clWsdlTypeList, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('List', array($clWsdlTypeList), null, null, $aHeaders);
    }

    /**
     * @param array $aHeaders
     *
     * @return XMLResponseWS
     * @throws \Exception
     */
    public function getEndListCalculation(array $aHeaders) : XMLResponseWS
    {
        return $this->call('GetEndListCalculation', null, null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : Modify
     *
     * @param Modify $clWsdlTypeModify
     * @param array  $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function modify(Modify $clWsdlTypeModify, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('Modify', array($clWsdlTypeModify), null, null, $aHeaders);
    }
    

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : ModifyFolder
     *
     * @param ModifyFolder $clWsdlTypeModifyFolder
     * @param array        $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function modifyFolder(ModifyFolder $clWsdlTypeModifyFolder, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('ModifyFolder', array($clWsdlTypeModifyFolder), null, null, $aHeaders);
    }
    


    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : ModifyMessage
     *
     * @param ModifyMessage $clWsdlTypeModifyMessage
     * @param array         $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function modifyMessage(ModifyMessage $clWsdlTypeModifyMessage, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('ModifyMessage', array($clWsdlTypeModifyMessage), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : Print
     *
     * @param PrintParams $clWsdlTypePrint
     * @param array       $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function printAction(PrintParams $clWsdlTypePrint, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('Print', array($clWsdlTypePrint), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : ReorderList
     *
     * @param ReorderList $clWsdlTypeReorderList
     * @param array       $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function reorderList(ReorderList $clWsdlTypeReorderList, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('ReorderList', array($clWsdlTypeReorderList), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : ReorderList
     *
     * @param ReorderSubList $clWsdlTypeReorderSubList
     * @param array          $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function reorderSubList(ReorderSubList $clWsdlTypeReorderSubList, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('ReorderSubList', array($clWsdlTypeReorderSubList), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : Request
     *
     * @param Request $clWsdlTypeRequest
     * @param array   $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function request(Request $clWsdlTypeRequest, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('Request', array($clWsdlTypeRequest), null, null, $aHeaders);
    }

    /**
     * @param       $requestParams
     * @param array $aHeaders
     *
     * @return XMLResponseWS
     * @throws \Exception
     */
    public function newRequest($requestParams, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('Request', '<Request>' . $requestParams . '</Request>', null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : RequestMessage
     *
     * @param RequestMessage $clWsdlTypeRequestMessage
     * @param array          $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function requestMessage(RequestMessage $clWsdlTypeRequestMessage, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('RequestMessage', array($clWsdlTypeRequestMessage), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : RequestParam
     *
     * @param RequestParam $clWsdlTypeRequestParam
     * @param array        $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function requestParam(RequestParam $clWsdlTypeRequestParam, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('RequestParam', array($clWsdlTypeRequestParam), null, null, $aHeaders);
    }
    

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : ResetPasswordFailed
     *
     * @param ResetPasswordFailed $clWsdlTypeResetPasswordFailed
     * @param array               $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function resetPasswordFailed(ResetPasswordFailed $clWsdlTypeResetPasswordFailed, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('ResetPasswordFailed', array($clWsdlTypeResetPasswordFailed), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : Search
     *
     * @param Search $clWsdlTypeSearch
     * @param array  $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function search(Search $clWsdlTypeSearch, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('Search', array($clWsdlTypeSearch), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : SelectForm
     *
     * @param SelectForm $clWsdlTypeSelectForm
     * @param array      $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function selectForm(SelectForm $clWsdlTypeSelectForm, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('SelectForm', array($clWsdlTypeSelectForm), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : SelectItems
     *
     * @param SelectItems $clWsdlTypeSelectItems
     * @param array       $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function selectItems(SelectItems $clWsdlTypeSelectItems, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('SelectItems', array($clWsdlTypeSelectItems), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : SelectPrintTemplate
     *
     * @param SelectPrintTemplate $clWsdlTypeSelectPrintTemplate
     * @param array               $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function selectPrintTemplate(SelectPrintTemplate $clWsdlTypeSelectPrintTemplate, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('SelectPrintTemplate', array($clWsdlTypeSelectPrintTemplate), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : SelectChoice
     *
     * @param SelectChoice $clWsdlTypeSelectChoice
     * @param array        $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function selectChoice(SelectChoice $clWsdlTypeSelectChoice, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('SelectChoice', array($clWsdlTypeSelectChoice), null, null, $aHeaders);
    }

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : SendMessage
     *
     * @param SendMessage $clWsdlTypeSendMessage
     * @param array       $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function sendMessage(SendMessage $clWsdlTypeSendMessage, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('SendMessage', array($clWsdlTypeSendMessage), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : SetOrderList
     *
     * @param SetOrderList $clWsdlTypeSetOrderList
     * @param array        $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function setOrderList(SetOrderList $clWsdlTypeSetOrderList, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('SetOrderList', array($clWsdlTypeSetOrderList), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : SetOrderSubList
     *
     * @param SetOrderSubList $clWsdlTypeSetOrderSubList
     * @param array           $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function setOrderSubList(SetOrderSubList $clWsdlTypeSetOrderSubList, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('SetOrderSubList', array($clWsdlTypeSetOrderSubList), null, null, $aHeaders);
    }
    

    /**
     *  fonction permettant l'appel de la fonction SOAP du service simaxOnline : TransformInto
     *
     * @param TransformInto $clWsdlTypeTransformInto
     * @param array         $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function transformInto(TransformInto $clWsdlTypeTransformInto, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('TransformInto', array($clWsdlTypeTransformInto), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : Update
     *
     * @param Update $clWsdlTypeUpdate
     * @param array  $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function update(Update $clWsdlTypeUpdate, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('Update', array($clWsdlTypeUpdate), null, null, $aHeaders);
    }


    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : Update
     *
     * @param UpdateFilter $clWsdlTypeUpdateFilter
     * @param array        $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function updateFilter(UpdateFilter $clWsdlTypeUpdateFilter, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('UpdateFilter', array($clWsdlTypeUpdateFilter), null, null, $aHeaders);
    }

    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : ButtonAction
     *
     * @param ButtonAction $clWsdlTypeButtonAction
     * @param array        $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function buttonAction(ButtonAction $clWsdlTypeButtonAction, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('ButtonAction', array($clWsdlTypeButtonAction), null, null, $aHeaders);
    }

    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : UpdateFolder
     *
     * @param UpdateFolder $clWsdlTypeUpdateFolder
     * @param array        $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function updateFolder(UpdateFolder $clWsdlTypeUpdateFolder, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('UpdateFolder', array($clWsdlTypeUpdateFolder), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : UpdateMessage
     *
     * @param UpdateMessage $clWsdlTypeUpdateMessage
     * @param array         $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function updateMessage(UpdateMessage $clWsdlTypeUpdateMessage, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('UpdateMessage', [$clWsdlTypeUpdateMessage], null, null, $aHeaders);
    }

    

    /**
     * @param UpdateColumnMessageValueInBatch $updateColumnMessageValueInBatch
     * @param array $aHeaders
     *
     * @return XMLResponseWS
     * @throws \Exception
     */
    public function updateColumnMessageValueInBatch(UpdateColumnMessageValueInBatch $updateColumnMessageValueInBatch, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('UpdateColumnMessageValueInBatch', array($updateColumnMessageValueInBatch), null, null, $aHeaders);
    }

    

    /**
     * @param DeleteMessage $deleteMessage
     * @param array $aHeaders
     *
     * @return XMLResponseWS
     * @throws \Exception
     */
    public function deleteMessage(DeleteMessage $deleteMessage, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('DeleteMessage', array($deleteMessage), null, null, $aHeaders);
    }

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : Validate
     *
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function validate(array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('Validate', array(), null, null, $aHeaders);
    }
    


    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : ValidateFolder
     *
     * @param ValidateFolder $clWsdlTypeValidateFolder
     * @param array          $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function validateFolder(ValidateFolder $clWsdlTypeValidateFolder, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('ValidateFolder', array($clWsdlTypeValidateFolder), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : WithAutomaticResponse
     *
     * @param WithAutomaticResponse $clWsdlTypeWithAutomaticResponse
     * @param array                 $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function withAutomaticResponse(WithAutomaticResponse $clWsdlTypeWithAutomaticResponse, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('WithAutomaticResponse', array($clWsdlTypeWithAutomaticResponse), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : ZipPJ
     *
     * @param ZipPJ $clWsdlTypeZipPJ
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function zipPJ(ZipPJ $clWsdlTypeZipPJ, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('ZipPJ', array($clWsdlTypeZipPJ), null, null, $aHeaders);
    }

    

    /**
     * @param       $export
     * @param array $aHeaders
     *
     * @return XMLResponseWS
     * @throws \Exception
     */
    public function export($export, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('Export', array($export), null, null, $aHeaders);
    }

    /**
     * @param       $import
     * @param array $aHeaders
     *
     * @return XMLResponseWS
     * @throws \Exception
     */
    public function import($import, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('Import', array($import), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : getUndoList
     *
     * @param GetUndoList $clWsdlTypeGetUndoList
     * @param array       $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function getUndoList(GetUndoList $clWsdlTypeGetUndoList, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('GetUndoList', array($clWsdlTypeGetUndoList), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : getUndoList
     *
     * @param GetUndoListID $clWsdlType
     * @param array         $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function getUndoListID(GetUndoListID $clWsdlType, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('GetUndoListID', array($clWsdlType), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetRedoList
     *
     * @param GetRedoList $clWsdlTypeGetRedoList
     * @param array       $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function getRedoList(GetRedoList $clWsdlTypeGetRedoList, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('GetRedoList', array($clWsdlTypeGetRedoList), null, null, $aHeaders);
    }
    

    /**
     *  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : GetRedoList
     *
     * @param GetRedoListID $clWsdlType
     * @param array         $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function getRedoListID(GetRedoListID $clWsdlType, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('GetRedoListID', array($clWsdlType), null, null, $aHeaders);
    }
    

    /**  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : DisplayUndoMessage
     *
     * @param DisplayUndoMessage $clWsdlTypeDisplayUndoMessage
     * @param array              $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function displayUndoMessage(DisplayUndoMessage $clWsdlTypeDisplayUndoMessage, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('DisplayUndoMessage', array($clWsdlTypeDisplayUndoMessage), null, null, $aHeaders);
    }
    

    /**  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : DisplayRedoMessage
     *
     * @param DisplayRedoMessage $clWsdlTypeDisplayRedoMessage
     * @param array              $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function displayRedoMessage(DisplayRedoMessage $clWsdlTypeDisplayRedoMessage, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('DisplayRedoMessage', array($clWsdlTypeDisplayRedoMessage), null, null, $aHeaders);
    }
    

    /**  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : Undo
     *
     * @param Undo  $clWsdlTypeUndo
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function undo(Undo $clWsdlTypeUndo, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('Undo', array($clWsdlTypeUndo), null, null, $aHeaders);
    }
    

    /**  Fonction permettant l'appel de la fonction SOAP du service simaxOnline : Redo
     *
     * @param Redo  $clWsdlTypeRedo
     * @param array $aHeaders tableau d'headers a ajouter a la requete
     * @return XMLResponseWS
     * @throws \Exception
     * @access public
     */
    public function redo(Redo $clWsdlTypeRedo, array $aHeaders = array()) : XMLResponseWS
    {
        return $this->call('Redo', array($clWsdlTypeRedo), null, null, $aHeaders);
    }

    


    public static function s_isValidHeaderProp($sHeaderProp) : bool
    {
        return ($sHeaderProp == self::HEADER_APIUUID ||
            $sHeaderProp == self::HEADER_UsernameToken ||
            $sHeaderProp == self::HEADER_SessionToken ||
            $sHeaderProp == self::HEADER_ActionContext ||
            $sHeaderProp == self::HEADER_AutoValidate ||
            $sHeaderProp == self::HEADER_APIUser ||
            $sHeaderProp == self::HEADER_OptionDialogue
        );
    }


    public static function s_isValidDialogOption($sDialogOption) : bool
    {
        return (($sDialogOption == self::HEADER_OptionDialogue_Readable) ||
            ($sDialogOption == self::HEADER_OptionDialogue_DisplayValue) ||
            ($sDialogOption == self::HEADER_OptionDialogue_EncodingOutput) ||
            // ReturnValue
            // ReturnXSD
            // HTTPForceReturn
            ($sDialogOption == self::HEADER_Ghost) ||
            // DefaultPagination
            ($sDialogOption == self::HEADER_OptionDialogue_LanguageCode) ||
            // ListContentAsync
            ($sDialogOption == self::HEADER_OptionDialogue_ListContentAsync)
        );
    }

    // Propriétés du Header
    const HEADER_APIUUID = 'APIUUID';
    const HEADER_UsernameToken = 'UsernameToken';
    const HEADER_SessionToken = 'SessionToken';
    const HEADER_ActionContext = 'ActionContext';
    const HEADER_AutoValidate = 'AutoValidate';
    const HEADER_APIUser = 'APIUser';
    const HEADER_OptionDialogue = 'OptionDialogue';

    // Propriétés de OptionDialogue
    const HEADER_OptionDialogue_Readable = 'Readable';
    const HEADER_OptionDialogue_DisplayValue = 'DisplayValue';
    const HEADER_OptionDialogue_EncodingOutput = 'EncodingOutput';
    const HEADER_Ghost = 'Ghost';
    const HEADER_OptionDialogue_LanguageCode = 'LanguageCode';
    const HEADER_OptionDialogue_ListContentAsync = 'ListContentAsync';

    // Autres
    const HEADER_WithFieldStateControl = 'WithFieldStateControl'; // ?? Pas dans la doc

    const AUTOVALIDATE_Validate = 1;
    const AUTOVALIDATE_None = 0;
    const AUTOVALIDATE_Cancel = -1;

    const APIUSER_Active = 1;
    const APIUSER_Desabled = 0;

    //enum pour le type d'affichage
    // !!!! NE PAS OUBLIER DE MODIFIER LA FONCTION s_sVerifDisplayMode !!!!
    const DISPLAYMODE_Liste = 'List';
    const DISPLAYMODE_Graphe = 'Chart';
    const DISPLAYMODE_ListeImage = 'Thumbnail';
    const DISPLAYMODE_Planning = 'Planning';
    const DISPLAYMODE_Plan = 'Map';
    const DISPLAYMODE_Gantt = 'Gantt';
    const DISPLAYMODE_Organigramme = 'FlowChart';
    //const DISPLAYMODE_Data = 'Data';
    //const DISPLAYMODE_ChartPicture = 'ChartPicture';

    public static function s_sVerifDisplayMode($sValueToVerif, $sDefaultValue)
    {
        $aTabPossible = array(
            self::DISPLAYMODE_Liste,
            self::DISPLAYMODE_Graphe,
            self::DISPLAYMODE_ListeImage,
            self::DISPLAYMODE_Planning,
            self::DISPLAYMODE_Plan,
            self::DISPLAYMODE_Gantt,
            self::DISPLAYMODE_Organigramme,
        );

        return (in_array($sValueToVerif, $aTabPossible)) ? $sValueToVerif : $sDefaultValue;
    }

    const SOCKET_TIMEOUT = 300;
}
//***


//-------------------------------------------------------------------------------------------------------------------
// Ensemble de classes utilisé par la classe SimaxOnlineServiceProxy
// Note : les conventions de code peuvent semblé non respecté sur les nom de variables, mais elle corresponde en realite
// au fichier WSDL
//-------------------------------------------------------------------------------------------------------------------


/*
 * definition des classe de constante utile a la communication soap
 */
//

/*
- class CalculationTypeEnum :                           voir la classe NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\CalculationListType
- class COutOfWsdlType_CalculEnumForGetCalculation :    voir la classe NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\CalculationListType
- class ReturnType :                                    voir la classe NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS les constantes RETURNTYPE_xxxx
- class DisplayModeParamEnum :                          voir la classe OnlineServiceProxy les constantes DISPLAYMODE_xxx
- class DisplayModeEnum :                               voir la classe OnlineServiceProxy les constantes DISPLAYMODE_xxx

/*
 * definition des classe de constante utile a la communication soap pour la partie messagerie
 */
/*
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

*/
