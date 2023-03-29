<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 25/07/14
 * Time: 10:22
 */

namespace NOUT\Bundle\NOUTOnlineBundle\DataCollector;

use NOUT\Bundle\NOUTOnlineBundle\REST\HTTPResponse;
use NOUT\Bundle\NOUTOnlineBundle\Service\DynamicConfigurationLoader;
use Psr\Log\LoggerInterface;

/**
 * Class NOUTOnlineLogger
 * @package NOUT\Bundle\NOUTOnlineBundle\DataCollector
 */
class NOUTOnlineLogger
{
    /**
     * Executed NOUTOnline queries.
     *
     * @var array
     */
    public $m_TabQueries = array();

    /**
     * If NOUTOnline Logger is enabled (log queries) or not.
     *
     * @var boolean
     */
    public $m_bEnabled = true;

    /**
     * @var float|null
     */
    public $m_fStart = null;

    /**
     * tableau qui contient les temps intermédiaire
     * @var array|null
     */
    public $m_fSend = null;

    /**
     * @var LoggerInterface $m_clMonolog : monolog pour voir les traces
     */
    protected $m_clMonolog;


    /**
     * @param $logger : l'instance monolog de symfony
     * @param $debug  : si site en debug
     */
    public function __construct(LoggerInterface $logger, DynamicConfigurationLoader $loader, bool $debug)
    {
        $this->m_clMonolog = $logger;
        $this->m_bEnabled = $loader->getParameter('log', false) || $debug;
    }

    public function getEnabled()
    {
        return $this->m_bEnabled;
    }

    public function startQuery()
    {
        if ($this->m_bEnabled) {
            $this->m_fStart = microtime(true);
        }
    }

    public function startSend()
    {
        if ($this->m_bEnabled) {
            $this->m_fSend = microtime(true);
        }
    }

    public function stopSend()
    {
        if ($this->m_bEnabled) {
            $this->m_fSend = microtime(true) - $this->m_fSend;
        }
    }

    /**
     * @param \stdClass $oCtxt
     * @param           $extra
     */
    protected function __getContext(\stdClass $oCtxt, $extra)
    {
        $oCtxt->extra = $extra;

        //transforme en array
        $stringjson = json_encode($oCtxt);

        if ($stringjson == false) {
            switch (json_last_error()) {
                case JSON_ERROR_NONE:
                    $erreur = ' - Aucune erreur';
                    break;
                case JSON_ERROR_DEPTH:
                    $erreur = ' - Profondeur maximale atteinte';
                    break;
                case JSON_ERROR_STATE_MISMATCH:
                    $erreur = ' - Inadéquation des modes ou underflow';
                    break;
                case JSON_ERROR_CTRL_CHAR:
                    $erreur = ' - Erreur lors du contrôle des caractères';
                    break;
                case JSON_ERROR_SYNTAX:
                    $erreur = ' - Erreur de syntaxe ; JSON malformé';
                    break;
                case JSON_ERROR_UTF8:
                    $erreur = ' - Caractères UTF-8 malformés, probablement une erreur d\'encodage';
                    break;
                default:
                    $erreur = ' - Erreur inconnue';
                    break;
            }

        }
        return json_decode($stringjson, true);
    }

    /**
     * @param $way
     * @param $sOperation
     * @param $extra
     */
    protected function _getSOAPContext($way, $sOperation, $extra)
    {
        $oCtxt = new \stdClass();
        $oCtxt->way = $way;
        $oCtxt->sOperation = $sOperation;
        $oCtxt->soap = true;
        $oCtxt->rest = false;
        $oCtxt->content_type = 'application/xml+soap';

        return $this->__getContext($oCtxt, $extra);
    }


    /**
     * @param $way
     * @param $sOperation
     * @param $extra
     */
    protected function _getRESTContext($way, $sOperation, $mimetype, $extra)
    {
        $oCtxt = new \stdClass();
        $oCtxt->way = $way;
        $oCtxt->sOperation = $sOperation;
        $oCtxt->soap = false;
        $oCtxt->rest = true;
        $oCtxt->content_type = $mimetype;

        return $this->__getContext($oCtxt, $extra);
    }

    public function stopSOAPQueryError($sTo, $sFrom, $sOperation, $extra)
    {
        if ($this->m_bEnabled){
            return ;
        }

        $this->_stopSOAPQuery($sTo, $sFrom, $sOperation, $extra, 'error');
    }

    /**
     * @param string $sTo
     * @param string $sFrom
     * @param string $sOperation
     * @param $extra
     */
    public function stopSOAPQuery($sTo, $sFrom, $sOperation, $extra)
    {
        if ($this->m_bEnabled) {
            return ;
        }

        $this->_stopSOAPQuery($sTo, $sFrom, $sOperation, $extra, 'notice');
    }

    protected function _stopSOAPQuery($sTo, $sFrom, $sOperation, $extra, $logMethod)
    {
        // log de l'envoi
        $sTo = str_replace('><', ">\r\n<", $sTo);
        $this->m_clMonolog->$logMethod(
            $sTo,
            $this->_getSOAPContext(self::WAY_Send, $sOperation, $extra)
        );

        //log du retour
        $this->m_clMonolog->$logMethod(
            $sFrom,
            $this->_getSOAPContext(self::WAY_Receive, $sOperation, $extra)
        );

        // log du retour
        $sSeparateur = "\r\n\r\n";
        $nPosT = strpos($sTo, $sSeparateur);
        $nPosF = strpos($sFrom, $sSeparateur);

        if ($nPosT) {
            $sRequestHeader = substr($sTo, 0, $nPosT);
            $sRequest = substr($sTo, $nPosT + strlen($sSeparateur));
        } else {
            $sRequestHeader = '';
            $sRequest = $sTo;
        }

        if ($nPosF) {
            $sResponseHeader = substr($sFrom, 0, $nPosF);
            $sResponse = substr($sFrom, $nPosF + strlen($sSeparateur));
        } else {
            $sResponseHeader = '';
            $sResponse = $sFrom;
        }


        $this->m_TabQueries[] = array(
            'request'         => $sRequest,
            'response'        => $sResponse,
            'request_header'  => $sRequestHeader,
            'response_header' => $sResponseHeader,
            'executionMS'     => microtime(true) - $this->m_fStart,
            'sendMS'          => $this->m_fSend,
            'operation'       => $sOperation,
            'soap'            => true,
            'xml'             => true,
        );
    }


    protected function _getMimeTypeFromHeaders($aHeaders)
    {
        $finded = array_filter($aHeaders, function ($line) {
            return strncmp($line, 'Content-Type:', strlen('Content-Type:'))==0;
        });

        if (!count($finded)) {
            return null;
        }
        $line = array_pop($finded);
        $contenttype = substr($line, strlen('Content-Type:'));
        $pos = strpos($contenttype, ';');
        if ($pos) {
            $contenttype = substr($contenttype, 0, $pos);
        }
        return trim($contenttype);

    }

    /**
     * @param $sMimeType
     * @param $payload
     */
    protected function _getRESTPayloadForLog($sMimeType, $payload)
    {
        if (isset($sMimeType) &&
            (strncasecmp($sMimeType, 'text/', strlen('text/')) != 0) &&
            (strcasecmp($sMimeType, 'application/json') != 0)) {
            return base64_encode($payload);
        }
        return $payload;
    }


    /**
     * @param $sTo
     * @param $sFrom
     */
    public function stopRESTQueryError($requestHeaders, $payload, $responseHeaders, $reponse, $action, $extra)
    {
        if (!$this->m_bEnabled) {
            return;
        }

        $this->_stopRESTQuery($requestHeaders, $payload, $responseHeaders, $reponse, $action, $extra, 'error');
    }

    /**
     * @param $sTo
     * @param $sFrom
     */
    public function stopRESTQuery($requestHeaders, $payload, $responseHeaders, $reponse, $action, $extra)
    {
        if (!$this->m_bEnabled) {
            return ;
        }

        $this->_stopRESTQuery($requestHeaders, $payload, $responseHeaders, $reponse, $action, $extra, 'notice');
    }


    /**
     * @param $sTo
     * @param $sFrom
     */
    public function _stopRESTQuery($requestHeaders, $payload, $responseHeaders, $reponse, $action, $extra, $logMethod)
    {
        if (!$this->m_bEnabled) {
            return ;
        }

        //l'envoi
        $aRequestHeaders = HTTPResponse::s_aParseHTTPHeaders($requestHeaders);
        $sRequestMimeType = $this->_getMimeTypeFromHeaders($aRequestHeaders);

        $payload = $this->_getRESTPayloadForLog($sRequestMimeType, $payload);

        $this->m_clMonolog->$logMethod(
            $requestHeaders."\r\n".$payload,
            $this->_getRESTContext(self::WAY_Send, $action, $sRequestMimeType, $extra)
        );

        //la réception
        $aResponsetHeaders = HTTPResponse::s_aParseHTTPHeaders($responseHeaders);
        $sResponseMimeType = $this->_getMimeTypeFromHeaders($aResponsetHeaders);

        $reponse = $this->_getRESTPayloadForLog($sResponseMimeType, $reponse);

        $this->m_clMonolog->$logMethod(
            $responseHeaders."\r\n".$reponse,
            $this->_getRESTContext(self::WAY_Receive, $action, $sResponseMimeType, $extra)
        );

        if (isset($sRequestMimeType) &&
            (strncmp($sRequestMimeType, 'image/', strlen('image/')) == 0)
        ) {
            $payload = '<img src="data:' . $sRequestMimeType . ';base64,' . $payload . '"/>';
        }

        if (isset($sResponseMimeType) &&
            (strncmp($sResponseMimeType, 'image/', strlen('image/')) == 0)
        ) {
            $reponse = '<img src="data:' . $sResponseMimeType . ';base64,' . $reponse . '"/>';
        }

        $this->m_TabQueries[] = array(
            'request'         => $payload,
            'response'        => $reponse,
            'request_header'  => '',
            'response_header' => '',
            'executionMS'     => microtime(true) - $this->m_fStart,
            'sendMS'          => $this->m_fSend,
            'operation'       => $action,
            'soap'            => false,
            'xml'             => false,
        );

    }

    const EXTRA_TokenSession = 'tokensession';
    const EXTRA_ActionContext = 'actioncontext';
    const EXTRA_Http_Headers = 'http-headers';

    protected const WAY_Send = 'send';
    protected const WAY_Receive = 'receive';
}
