<?php
/**
 * Created by PhpStorm
 * User: ninon
 * Date: 22/02/2023 14:58
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Service;

use NOUT\Bundle\NOUTOnlineBundle\Entity\ConfigurationDialogue;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NOUTOnlineRedirection
{

    /** @var CURLRedirectionProxy */
    private CURLRedirectionProxy $clCurl;

    /** @var ConfigurationDialogue  */
    private ConfigurationDialogue $clConfigurationDialogue;

    /**
     * @param CURLRedirectionProxy  $clCurl
     * @param ConfigurationDialogue $clConfig
     */
    public function __construct(CURLRedirectionProxy $clCurl, ConfigurationDialogue $clConfig)
    {
        $this->clCurl = $clCurl;
        $this->clConfigurationDialogue = $clConfig;
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

        $this->clCurl->setRequest($request);

        $requesturi = $request->getRequestUri();
        $decoupe = explode('?', $requesturi);
        $toNOUTOnline = $action;
        if (count($decoupe) > 1) {
            list(, $querystring) = $decoupe;
            $toNOUTOnline.='?'.$querystring;
        }
        $sURI= $this->clConfigurationDialogue->getServiceAddress().$toNOUTOnline;

        $nContentSize = (int)$request->headers->get('Content-Length', 0);

        try
        {
            if ($nContentSize) {
                $sContentType = $request->headers->get('Content-Type');

                //est-ce que c'est du SOAP
                $bSoap = (empty($sURI) || ($sURI=='/')) && (strncasecmp($sContentType,'application/xml', strlen('application/xml'))==0);
                $sSOAPAction = $request->headers->get('SOAPAction', '');
                $this->clCurl->setIsSoap($bSoap, $sSOAPAction);

                $ret = $this->clCurl->oExecutePOST($sURI, file_get_contents("php://input"), $sContentType);
            }
            else {
                $this->clCurl->setIsSoap(false);

                $ret = $this->clCurl->oExecuteGET($sURI);
            }

            return new Response($ret->content, $ret->httpCode, $ret->httpHeaders);
        }
        catch (\Exception $e)
        {
            return new Response($e->getMessage(), 500); //service unavailable
        }


    }


}
