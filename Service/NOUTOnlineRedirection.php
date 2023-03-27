<?php
/**
 * Created by PhpStorm
 * User: ninon
 * Date: 22/02/2023 14:58
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Service;

use NOUT\Bundle\NOUTOnlineBundle\DataCollector\NOUTOnlineRedirectionLogger;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ConfigurationDialogue;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NOUTOnlineRedirection
{
    /**
     * @var NOUTOnlineRedirectionLogger
     */
    protected $m_clLogger;

    /**
     * @var ClientInformation
     */
    protected $m_clClientInformation;

    /**
     * classe de configuration
     * @var ConfigurationDialogue
     */
    private $m_clConfigurationDialogue;

    /**
     * @param ClientInformation           $clientInfo
     * @param NOUTOnlineRedirectionLogger $logger
     * @param ConfigurationDialogue       $clConfig
     */
    public function __construct(ClientInformation           $clientInfo,
                                NOUTOnlineRedirectionLogger $logger,
                                ConfigurationDialogue       $clConfig)
    {
        $this->m_clClientInformation = $clientInfo;
        $this->m_clConfigurationDialogue = $clConfig;
        $this->m_clLogger = $logger;
    }

    /**
     * @param Request $request
     * @param string  $action
     * @return Response
     */
    public function TraiteRequest(Request $request, string $action) : Response
    {
        try {
            ini_set("memory_limit",'16M');
        } catch (\Exception $e) {
            //error memory_limit
        }

        $this->__startLogQuery();

        $requesturi = $request->getRequestUri();
        $decoupe = explode('?', $requesturi);
        $sURI= $this->m_clConfigurationDialogue->getServiceAddress().$action;
        if (count($decoupe) > 1) {
            list(, $querystring) = $decoupe;
            $sURI.='?'.$querystring;
        }

        $aHttpHeadersObl = [
            ConfigurationDialogue::HTTP_SIMAX_CLIENT_IP      => $this->m_clClientInformation->getIP() ,
            ConfigurationDialogue::HTTP_SIMAX_CLIENT         => $this->m_clConfigurationDialogue->getSociete()." Proxy",
            ConfigurationDialogue::HTTP_SIMAX_CLIENT_Version => $this->m_clConfigurationDialogue->getVersion(),
            'Accept' => '*/*',
        ];

        array_walk($aHttpHeadersObl, function (&$value, $header) use ($request) {
            $value = $header.': '.$request->headers->get($header, $value);
        });


        $aHttpHeadersOpt = array_filter([
            'SOAPAction',
            'Content-Length',
            'Content-Type',
        ], function ($value) use ($request) {
            return $request->headers->has($value);
        });
        array_walk($aHttpHeadersOpt, function (&$value) use ($request) {
            $value = $value.': '.$request->headers->get($value, '');
        });

        $aHttpHeaders = array_merge(array_values($aHttpHeadersObl), $aHttpHeadersOpt, ['Connection: close']);

        //initialisation de curl
        $curl = curl_init($sURI);
        //time out de connection
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT , 0);
        //autres options
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //Demande du contenu du fichier
        curl_setopt($curl, CURLOPT_HEADER, 1); // Demande des headers
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $request->getMethod());

        $content_length = (int)$request->headers->get('Content-Length', 0);
        if ($content_length)
        {
            $raw_data = file_get_contents("php://input");
        }

        if (isset($raw_data) && !empty($raw_data)){
            curl_setopt( $curl, CURLOPT_POSTFIELDS, $raw_data );
        }

        curl_setopt($curl, CURLOPT_HTTPHEADER, $aHttpHeaders);

        //---------------------------
        //execution
        $output = curl_exec($curl);

        // Vérifie si une erreur survient
        $curl_errno = curl_errno($curl);
        if (!$curl_errno)
        {

            //on a pas d'erreur
            $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $header_request = curl_getinfo($curl, CURLINFO_HEADER_OUT);
            $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
            curl_close($curl);
            $headers = substr($output, 0, $header_size);
            $parsedHeaders = $this->_aGetHeadersFromCurlResponse($headers);
            $output = substr($output, $header_size);

            $this->__stopLogQuery($sURI, $header_request, $output, 'noutonline_redirection', $parsedHeaders);

            return new Response($output, $http_code, $parsedHeaders);
        }

        $curl_errmess = curl_error($curl);
        curl_close($curl);

        $this->__stopLogQuery($sURI, $curl_errmess, 'noutonline_redirection', null, '');
        return new Response('', 503); //service unavailable

    }

    /**
     * Parse les entêtes pour fournir une sortie au format natif
     *
     * @param $response
     * @return array
     * @throws \Exception
     */
    protected function _aGetHeadersFromCurlResponse($response) : array
    {
        $headers = [];

        $header_text = substr($response, 0, strpos($response, "\r\n\r\n"));

        foreach (explode("\r\n", $header_text) as /*$i =>*/ $line)
        {
            array_push($headers, $line);
        }

        return $headers;
    }


    private function __startLogQuery()
    {
        if (isset($this->m_clLogger))
        {
            //log des requetes
            $this->m_clLogger->startQuery();
        }
    }
    private function __stopLogQuery($uri, $request, $reponse, $action, $header, $bError=false)
    {
        if (isset($this->m_clLogger))
        {
            $extra = [];
            if (!empty($header)){
                $extra[$this->m_clLogger::EXTRA_Http_Headers]=$header;
            }

            $this->m_clLogger->stopQuery($request, $reponse, (empty($action) ? substr($uri, 0, 50) : $action), false, $extra, $bError);
        }
    }

}
