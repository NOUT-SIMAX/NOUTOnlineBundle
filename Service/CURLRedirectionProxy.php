<?php
/**
 * Created by PhpStorm
 * User: ninon
 * Date: 29/03/2023 09:13
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Service;

use NOUT\Bundle\NOUTOnlineBundle\DataCollector\NOUTOnlineRedirectionLogger;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ConfigurationDialogue;
use NOUT\Bundle\NOUTOnlineBundle\REST\HTTPResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Stopwatch\Stopwatch;

class CURLRedirectionProxy extends CURLProxy
{
    /** @var Request  */
    private Request $request;

    public function __construct(ClientInformation $clientInfo, NOUTOnlineRedirectionLogger $clLogger, ConfigurationDialogue $clConfig, Stopwatch $stopwatch = null)
    {
        parent::__construct($clientInfo, $clLogger, $clConfig, $stopwatch);
        $this->bEncodeHeaderUTF8 = false;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param array $aTabHeaders
     */
    protected function _setHeaders(array $aTabHeaders)
    {
        array_walk($this->aHttpFixedHeaders, function (&$value, $header) {
            $value = $this->request->headers->get($header, $value);
        });

        $this->aHttpHeaders[self::HTTP_ACCEPT] = $this->request->headers->get(self::HTTP_ACCEPT, '*/*');
        $this->aHttpHeaders[self::HTTP_CONNECTION] = 'close';

        $aHttpHeadersOpt = array_filter([
            'SOAPAction',
            'Content-Length',
            'Content-Type',
        ], function ($value) {
            return $this->request->headers->has($value);
        });

        array_walk(
            $aHttpHeadersOpt,
            function (&$value) {
                $value = [$value => $this->request->headers->get($value, '')];
            }
        );

        $aHttpHeadersOpt = array_reduce(
            $aHttpHeadersOpt,
            function ($carry, $item) {
                return $carry+$item;
            },
            []
        );

        parent::_setHeaders($this->aHttpFixedHeaders+$aHttpHeadersOpt+$this->aHttpHeaders);
    }


    /**
     * @param $output
     * @param $headers
     * @return HTTPResponse
     */
    protected function _oMakeResponse($httpCode, $output, $headers): HTTPResponse
    {
        return new HTTPResponse($httpCode, $output, $headers, $this->bEncodeHeaderUTF8);
    }

    protected const HTTP_CONNECTION = 'Connection';
}


