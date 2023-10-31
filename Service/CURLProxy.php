<?php
/**
 * Created by PhpStorm
 * User: ninon
 * Date: 29/03/2023 09:10
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Service;

use NOUT\Bundle\NOUTOnlineBundle\DataCollector\NOUTOnlineLogger;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ConfigurationDialogue;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\OnlineError;
use NOUT\Bundle\NOUTOnlineBundle\Entity\REST\Identification;
use NOUT\Bundle\NOUTOnlineBundle\REST\HTTPResponse;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\SOAPException;
use Symfony\Component\Stopwatch\Stopwatch;

class CURLProxy
{
    protected const HTTP_CONTENT_TYPE = 'Content-Type';
    protected const HTTP_ACCEPT = 'Accept';

    /** @var ConfigurationDialogue */
    protected ConfigurationDialogue $clConfigurationDialogue;

    /** @var NOUTOnlineLogger */
    protected NOUTOnlineLogger $clLogger;

    /** @var ClientInformation */
    protected ClientInformation $clInfoClient;

    /** @var Stopwatch */
    protected Stopwatch $clStopWatch;

    /** @var false|resource */
    protected $clCurl;

    /** @var string[] */
    protected array $aHttpFixedHeaders;

    /** @var string[] */
    protected array $aHttpHeaders;

    /** @var string  */
    protected ?string $payload = '';

    /** @var bool  */
    protected bool $bIsSoap = false;

    /** @var string  */
    protected string $sSOAPAction = '';

    /** @var bool  */
    protected bool $bEncodeHeaderUTF8 = true;

    /**
     * @param ClientInformation     $clientInfo
     * @param NOUTOnlineLogger      $clLogger
     * @param ConfigurationDialogue $clConfig
     * @param Stopwatch|null        $stopwatch
     */
    public function __construct(ClientInformation $clientInfo, NOUTOnlineLogger $clLogger, ConfigurationDialogue $clConfig, Stopwatch $stopwatch = null)
    {
        $this->clConfigurationDialogue = $clConfig;
        $this->clLogger = $clLogger;
        $this->clInfoClient = $clientInfo;
        $this->clStopWatch = $stopwatch;

        $this->clCurl = curl_init();
    }

    /**
     *
     */
    public function __destruct()
    {
        curl_close($this->clCurl);
    }

    /**
     * @param bool   $bIsSoap
     * @param string $sSOAPAction
     */
    public function setIsSoap(bool $bIsSoap, string $sSOAPAction='')
    {
        $this->bIsSoap=$bIsSoap;
        $this->sSOAPAction = $bIsSoap ? $sSOAPAction : '';
    }

    protected function _reset()
    {
        curl_reset($this->clCurl); //reinit

        //autres options
        curl_setopt($this->clCurl, CURLOPT_RETURNTRANSFER, 1); //Demande du contenu du fichier
        curl_setopt($this->clCurl, CURLOPT_HEADER, 1); // Demande des headers
        curl_setopt($this->clCurl, CURLINFO_HEADER_OUT, true);

        $this->aHttpFixedHeaders = [
            ConfigurationDialogue::HTTP_SIMAX_CLIENT_IP => $this->clInfoClient->getIP(),
            ConfigurationDialogue::HTTP_SIMAX_CLIENT => $this->clConfigurationDialogue->getSociete(),
            ConfigurationDialogue::HTTP_SIMAX_CLIENT_Version => $this->clConfigurationDialogue->getVersion(),
        ];
        $this->aHttpHeaders = [];
        $this->payload = '';
    }

    /**
     * @param $timeout
     */
    protected function _setTimeout($timeout = null)
    {
        if (!is_null($timeout)) {
            curl_setopt($this->clCurl, CURLOPT_CONNECTTIMEOUT, (floatval($timeout) < 1) ? 1 : intval($timeout));
        } else {
            curl_setopt($this->clCurl, CURLOPT_CONNECTTIMEOUT, 0);
        }
    }

    /**
     * @param false $bForceJson
     */
    protected function _setAccept(bool $bForceJson = false)
    {
        $this->aHttpHeaders[self::HTTP_ACCEPT] = ($bForceJson ? 'application/json' : '*/*');
    }

    /**
     * @param             $payload
     * @param string|null $mimetype
     * @return string
     */
    protected function _setPayload($payload, ?string $mimetype=null) : string
    {
        if (!is_array($payload) && !is_object($payload) && empty($payload)) {
            $this->payload = '';
            return '';
        }

        if (is_array($payload) || is_object($payload)) {
            $payload = json_encode($payload);
            $this->aHttpHeaders[self::HTTP_CONTENT_TYPE] = 'application/json';
        } else {
            if (!empty($mimetype)) {
                $this->aHttpHeaders[self::HTTP_CONTENT_TYPE] = $mimetype;
            } else {
                $this->aHttpHeaders[self::HTTP_CONTENT_TYPE] = 'text/plain';
            }
        }

        curl_setopt($this->clCurl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($this->clCurl, CURLOPT_POSTFIELDS, $payload);

        $this->payload = $payload;
        return $payload;
    }

    /**
     * @param string $uri
     */
    protected function _setUri(string $uri)
    {
        curl_setopt($this->clCurl, CURLOPT_URL, $uri);
    }

    /**
     * @param string $sUri
     * @param string $sAction
     * @return string
     */
    protected function _sGetAction(string $sUri, string $sAction) : string
    {
        if (empty($sAction)) {
            $sAction = basename(parse_url($sUri, PHP_URL_PATH));
        }
        return $sAction;
    }

    /**
     * @param array $aTabHeaders
     */
    protected function _setHeaders(array $aTabHeaders)
    {
        array_walk($aTabHeaders, function (&$value, $header) {
            $value = $header.': '.$value;
        });

        curl_setopt($this->clCurl, CURLOPT_HTTPHEADER, array_values($aTabHeaders));
    }

    /**
     * @param string              $sUri
     * @param string              $sAction
     * @param string              $function
     * @param Identification|null $clIdentification
     * @param null                $timeout
     * @param bool                $bForceJson
     * @return HTTPResponse
     * @throws \Exception
     */
    public function oExecuteGET(string $sUri, string $sAction='', string $function='', Identification $clIdentification = null, $timeout = null, bool $bForceJson = false) : HTTPResponse
    {
        $this->_reset();

        $this->_setUri($sUri);
        $this->_setAccept($bForceJson);
        $this->_setTimeout($timeout);

        $sAction = $this->_sGetAction($sUri, $sAction);
        return $this->_oExecute($sUri, $sAction, $function, $clIdentification);
    }

    /**
     * @param string              $sUri
     * @param                     $content
     * @param                     $contentMimeType
     * @param string              $sAction
     * @param string              $function
     * @param Identification|null $clIdentification
     * @param null                $timeout
     * @param bool                $bForceJson
     * @return HTTPResponse
     * @throws \Exception
     */
    public function oExecutePOST(string $sUri, $content, $contentMimeType, string $sAction='', string $function='', Identification $clIdentification = null, $timeout = null, bool $bForceJson = false) : HTTPResponse
    {
        $this->_reset();

        $this->_setUri($sUri);
        $this->_setAccept($bForceJson);
        $this->_setTimeout($timeout);
        $this->_setPayload($content, $contentMimeType);

        $sAction = $this->_sGetAction($sUri, $sAction);
        return $this->_oExecute($sUri, $sAction, $function, $clIdentification);
    }


    /**
     * @param string              $sAction
     * @param string              $function
     * @param Identification|null $clIdentification
     * @return HTTPResponse
     * @throws \Exception
     */
    protected function _oExecute(string $sUri, string $sAction, string $function='', Identification $clIdentification = null) : HTTPResponse
    {
        $this->_setHeaders($this->aHttpHeaders + $this->aHttpFixedHeaders);

        //demarre le log si necessaire
        $this->__startLogQuery($function);

        //---------------------------
        //execution
        $output = curl_exec($this->clCurl);
        $curlErrNum = curl_errno($this->clCurl);
        $requestHeaders = curl_getinfo($this->clCurl, CURLINFO_HEADER_OUT);


        if ($curlErrNum) {
            if ($curlErrNum==CURLE_OPERATION_TIMEDOUT) {
                $curlErrMess = 'Failed to connect to ' . $this->clConfigurationDialogue->getHost() . ' port ' . $this->clConfigurationDialogue->getPort() . ': Connection timed out';
            } else {
                $curlErrMess = curl_error($this->clCurl);
            }

            try {
                $this->__stopLogQuery($sUri, $requestHeaders, $this->payload, '', $curlErrMess, $sAction, $function, $clIdentification, true);
            } catch (\Exception $e) {

            }

            throw new \Exception($curlErrMess);
        }

        $httpCode = curl_getinfo($this->clCurl, CURLINFO_HTTP_CODE);
        $responseHeaderSize = curl_getinfo($this->clCurl, CURLINFO_HEADER_SIZE);
        $responseHeaders = substr($output, 0, $responseHeaderSize);
        $output = substr($output, $responseHeaderSize);

        try {
            $ret = $this->_oMakeResponse($httpCode, $output, $responseHeaders);
        } catch (\Exception $e) {
            //on stop le log pour avoir la requête
            $this->__stopLogQuery($sUri, $requestHeaders, $this->payload, $responseHeaders, $output, $sAction, $function, $clIdentification, true);
            throw $e;
        }

        $this->__stopLogQuery($sUri, $requestHeaders, $this->payload, $responseHeaders, $ret->content, $sAction, $function, $clIdentification);
        return $ret;
    }

    /**
     * @param $function
     */
    private function __startLogQuery($function)
    {
        if (isset($this->clLogger)) {
            //log des requetes
            $this->clLogger->startQuery();
        }

        if (isset($this->stopwatch) && !empty($function)) {
            $this->stopwatch->start($function);
        }
    }

    /**
     * @param string              $sUri
     * @param                     $requestHeaders
     * @param                     $payload
     * @param                     $responseHeaders
     * @param                     $reponse
     * @param                     $action
     * @param                     $function
     * @param Identification|null $clIdentification
     * @param false               $bError
     */
    private function __stopLogQuery(string $sUri, $requestHeaders, $payload, $responseHeaders, $reponse, $action, $function, Identification $clIdentification = null, $bError = false)
    {
        if (isset($this->stopwatch)&& !empty($function)) {
            $this->stopwatch->stop($function);
        }

        if (isset($this->clLogger)) {
            $extra = [];
            if (!is_null($clIdentification) && !empty($clIdentification->m_sIDContexteAction)) {
                $extra[NOUTOnlineLogger::EXTRA_ActionContext] = $clIdentification->m_sIDContexteAction;
            }

            if ($this->bIsSoap) {
                if ($bError) {
                    $this->clLogger->stopSOAPQueryError($payload, $reponse, !empty($this->sSOAPAction) ? $this->sSOAPAction : $action, $extra);
                } else {
                    $this->clLogger->stopSOAPQuery($payload, $reponse, !empty($this->sSOAPAction) ? $this->sSOAPAction :$action, $extra);
                }
            } else {
                if ($bError) {
                    $this->clLogger->stopRESTQueryError($requestHeaders, $payload, $responseHeaders, $reponse, $action, $extra);
                } else {
                    $this->clLogger->stopRESTQuery($requestHeaders, $payload, $responseHeaders, $reponse, $action, $extra);
                }
            }
        }
    }



    /**
     * @param $output
     * @param $headers
     * @return HTTPResponse
     * @throws SOAPException
     */
    protected function _oMakeResponse($httpCode, $output, $headers): HTTPResponse
    {
        $ret = new HTTPResponse($httpCode, $output, $headers, $this->bEncodeHeaderUTF8);
        if ($ret->getStatus() != 200) {
            $clNOError = new OnlineError(0, 0, 0, '');
            $clNOError->parseFromREST($output);
            //il y a une erreur, il faut parser l'erreur
            throw new SOAPException($clNOError->getMessage(), $clNOError->getCode(), $clNOError->getCategorie());
        }
        return $ret;
    }

}
