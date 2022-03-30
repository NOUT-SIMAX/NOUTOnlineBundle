<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 17/11/14
 * Time: 14:56
 *
 * Proxy REST
 */

namespace NOUT\Bundle\NOUTOnlineBundle\REST;

use NOUT\Bundle\NOUTOnlineBundle\DataCollector\NOUTOnlineLogger;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ConfigurationDialogue;
use NOUT\Bundle\NOUTOnlineBundle\Entity\NOUTFileInfo;
use NOUT\Bundle\NOUTOnlineBundle\Entity\NOUTOnlineState;
use NOUT\Bundle\NOUTOnlineBundle\Entity\NOUTOnlineVersion;
use NOUT\Bundle\NOUTOnlineBundle\Entity\UserExists\UserExists;
use NOUT\Bundle\NOUTOnlineBundle\Entity\UsernameToken\UsernameToken;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\OnlineError;
use NOUT\Bundle\NOUTOnlineBundle\Entity\REST\Identification;
use NOUT\Bundle\NOUTOnlineBundle\Service\ClientInformation;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\SOAPException;
use Symfony\Component\Stopwatch\Stopwatch;

class OnlineServiceProxy
{
	/**
	 * classe de configuration
	 * @var ConfigurationDialogue
	 */
	private $__ConfigurationDialogue;

	/**
	 * logger symfony
	 * @var NOUTOnlineLogger
	 */
	private $__clLogger;

	/**
	 * @var ClientInformation
	 */
	private $__clInfoClient;

    /**
     * @var Stopwatch
     */
	private $__stopwatch;

	/**
	 * constructeur permettant d'instancier les classe de communication soap avec les bonne question
	 * @param ClientInformation     $clientInfo
	 * @param ConfigurationDialogue $clConfig
	 * @param NOUTOnlineLogger      $_clLogger
     * @param Stopwatch|null        $stopwatch
	 */
	public function __construct(ClientInformation $clientInfo, ConfigurationDialogue $clConfig, NOUTOnlineLogger $_clLogger, Stopwatch $stopwatch=null)
	{
        $this->__ConfigurationDialogue = $clConfig;
        $this->__clLogger = $_clLogger;
        $this->__clInfoClient = $clientInfo;
        $this->__stopwatch = $stopwatch;
	}

	/**
	 * Retourne la fin de la requette rest (partie identification)
     * @param Identification|null $clIdentification
	 * @return string la fin de la requette rest
     * @throws \Exception
	 */
	private function _sCreateIdentification(Identification $clIdentification=null) : string
	{
		if (is_null($clIdentification) || empty($clIdentification->m_clUsernameToken) || !$clIdentification->m_clUsernameToken->bIsValid())
		{
            if (!empty($this->__ConfigurationDialogue->getAPIUUID()))
            {
                return '!APIUUID='.urlencode($this->__ConfigurationDialogue->getAPIUUID());
            }

			return '';
		}

		$sBottom = '!'.$this->__sGetUsernameToken($clIdentification->m_clUsernameToken);

		if (!empty($clIdentification->m_sTokenSession))
		{
			$sBottom .= '&SessionToken='.urlencode($clIdentification->m_sTokenSession);
		}

		if (!empty($clIdentification->m_sIDContexteAction))
		{
			$sBottom .= '&ActionContext='.urlencode($clIdentification->m_sIDContexteAction);
		}

		if (!empty($this->__ConfigurationDialogue->getAPIUUID()))
		{
			$sBottom .= '&APIUUID='.urlencode($this->__ConfigurationDialogue->getAPIUUID());
		}


		if (!empty($clIdentification->m_bAPIUser))
		{
			$sBottom .= '&APIUser=1';
		}

		return $sBottom;
	}

    /**
     * @param UsernameToken $usernameToken
     * @return string la partie username token
     * @throws \Exception
     */
    private function __sGetUsernameToken(UsernameToken $usernameToken) : string
    {
        $usernameToken->Compute(); //on fait le compute
        $sBottom = 'Username='.urlencode(utf8_decode($usernameToken->Username));
        $sBottom .= '&Password='.urlencode($usernameToken->Password);
        $sBottom .= '&nonce='.urlencode(utf8_decode($usernameToken->Nonce));
        $sBottom .= '&created='.urlencode(utf8_decode($usernameToken->Created));

        if (!empty($usernameToken->Encryption))
        {
            $sBottom .= '&encryption=' . urlencode($usernameToken->Encryption->_);
            $sBottom .= '&md5=' . urlencode($usernameToken->Encryption->md5);
            if (!empty($usernameToken->Encryption->iv)){
                $sBottom .= '&iv=' . urlencode($usernameToken->Encryption->iv);
            }
            if (!empty($usernameToken->Encryption->ks)){
                $sBottom .= '&ks=' . urlencode($usernameToken->Encryption->ks);
            }
        }

        return $sBottom;
    }


	/**
	 * fonction creant la requette rest
	 *
	 * @param array $TabPath
	 * @param array $aTabParam tableau des parametres
	 * @param array $aTabOption tableau des options
     * @param Identification|null $clIdentification
	 * @return string la requette rest
     * @throws \Exception
	 */
	private function _sCreateRequest(array $TabPath, array $aTabParam, array $aTabOption, Identification $clIdentification=null) : string
	{
	    //on forme le début de l'url à partir des parties
	    array_walk($TabPath, function(&$part) {
            $part = urlencode($part);
        });
	    $sAction = implode("/", $TabPath);

		$sUrl = $this->__ConfigurationDialogue->getServiceAddress().$sAction.'?';

		//la liste des paramètres (entre ? et ;)
		if (count($aTabParam)>0)
		{
			$sListeParam = '';

			foreach ($aTabParam as $sKey => $sValue)
			{
				$sListeParam .= '&'.urlencode(utf8_decode($sKey)).'='.urlencode(utf8_decode($sValue));
			}

			$sUrl .= trim($sListeParam,  '&');
		}

		//la liste des options (entre ; et !)
		if (count($aTabOption)>0)
		{
			$sListeOption = '';

			foreach ($aTabOption as $sKey => $sValue)
			{
				$sListeOption .= '&'.urlencode(utf8_decode($sKey)).'='.urlencode(utf8_decode($sValue));
			}

			$sUrl .= ';'.trim($sListeOption,  '&');
		}

		$sUrl .= $this->_sCreateIdentification($clIdentification);

		return $sUrl;
	}

    /**
     * @param $output
     * @param $headers
     * @return HTTPResponse
     * @throws SOAPException
     */
    protected function _oMakeResponse($output, $headers) : HTTPResponse
    {
        $ret = new HTTPResponse($output, $headers);
        if ($ret->getStatus()!=200)
        {
            $no_error = new OnlineError(0, 0, 0, '');
            $no_error->parseFromREST($output);
            //il y a une erreur, il faut parser l'erreur
            throw new SOAPException($no_error->getMessage(), $no_error->getCode(), $no_error->getCategorie());
        }
        return $ret;
    }

    /**
     * @param $sAction
     * @param $sURI
     * @param Identification|null $clIdentification
     * @param $function
     * @param null $timeout
     * @return HTTPResponse
     * @throws \Exception
     */
	protected function _oExecute($sAction, $sURI, $function, Identification $clIdentification=null, $timeout=null) : HTTPResponse
	{
	    //demarre le log si necessaire
		$this->__startLogQuery($function);

        //initialisation de curl
        $curl = curl_init($sURI);

        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            ConfigurationDialogue::HTTP_SIMAX_CLIENT_IP     . ': '  . $this->__clInfoClient->getIP(),
            ConfigurationDialogue::HTTP_SIMAX_CLIENT        . ': '  . $this->__ConfigurationDialogue->getSociete(),
            ConfigurationDialogue::HTTP_SIMAX_CLIENT_Version. ': '  . $this->__ConfigurationDialogue->getVersion(),
        ));

        if (!is_null($timeout))
        {
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT , (floatval($timeout)<1) ? 1 : intval($timeout));
        }
        else
        {
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT , 0);
        }

        //autres options
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //Demande du contenu du fichier
        curl_setopt($curl, CURLOPT_HEADER, 1); // Demande des headers
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);

        //---------------------------
        //execution
        $output = curl_exec($curl);

        // Vérifie si une erreur survient

        $curl_errno = curl_errno($curl);
        if($curl_errno){
            switch ($curl_errno)
            {
                case CURLE_OPERATION_TIMEDOUT:
                {
                    $curl_errmess = 'Failed to connect to '.$this->__ConfigurationDialogue->getHost().' port '.$this->__ConfigurationDialogue->getPort().': Connection timed out';
                    break;
                }

                default:
                {
                    $curl_errmess = curl_error($curl);
                    break;
                }
            }
            curl_close($curl);
            try {
                $this->__stopLogQuery($sURI, $curl_errmess, $sAction, null, $function, $clIdentification);
            }
            catch (\Exception $e)
            {

            }

            throw new \Exception($curl_errmess);
        }

        $header_request = curl_getinfo($curl, CURLINFO_HEADER_OUT);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        curl_close($curl);
        $headers = substr($output, 0, $header_size);
        $output = substr($output, $header_size);

        $parsedHeaders = $this->_aGetHeadersFromCurlResponse($headers);
        // ------------------------------------------------

        try {
            $ret = $this->_oMakeResponse($output, $parsedHeaders);
        }
        catch (\Exception $e)
        {
            //on stop le log pour avoir la requête
            $this->__stopLogQuery($sURI, $header_request, $output, $sAction, $parsedHeaders, $function, $clIdentification, true);
            throw $e;
        }

        $this->__stopLogQuery($sURI, $header_request, $ret->content, $sAction, $ret->headers, $function, $clIdentification);
        return $ret;
	}

	private function __startLogQuery($function)
    {
        if (isset($this->__clLogger))
        {
            //log des requetes
            $this->__clLogger->startQuery();
        }

        if (isset($this->__stopwatch)){
            $this->__stopwatch->start(get_class($this).'::'.$function);
        }
    }
    private function __stopLogQuery($uri, $request, $reponse, $action, $header, $function, Identification $clIdentification=null, $bError=false)
    {
        if (isset($this->__stopwatch)){
            $this->__stopwatch->stop(get_class($this).'::'.$function);
        }

        if (isset($this->__clLogger))
        {
            $extra = [];
            if (!empty($header)){
                $extra[NOUTOnlineLogger::EXTRA_Http_Headers]=$header;
            }
            if (!is_null($clIdentification) && !empty($clIdentification->m_sTokenSession)){
                $extra[NOUTOnlineLogger::EXTRA_TokenSession]=$clIdentification->m_sTokenSession;
            }
            if (!is_null($clIdentification) && !empty($clIdentification->m_sIDContexteAction)){
                $extra[NOUTOnlineLogger::EXTRA_ActionContext]=$clIdentification->m_sIDContexteAction;
            }

            $this->__clLogger->stopQuery($request, $reponse, (empty($action) ? substr($uri, 0, 50) : $action), false, $extra, $bError);
        }
    }


	/**
	 * recherche un utilisateur par son pseudo
	 * @param $login
     * @param int $dwAuthOpts
     * @return UserExists
	 *@throws \Exception
	 */
	public function clGetUserExists($login, int $dwAuthOpts=0) : UserExists
	{
		$sURI = $this->_sCreateRequest(['GetUserExists'], ['login' => $login], []);

		$clHttpResponse = $this->_oExecute('GetUserExists', $sURI, __FUNCTION__);
		$sContent = $clHttpResponse->content;
		$sInfoEncryption = $clHttpResponse->getXNOUTOnlineInfoCnx();
        $sIV = $clHttpResponse->getIVForInfoCnx();

		return new UserExists($sContent, $sInfoEncryption, $sIV, null, $dwAuthOpts);
	}

    /**
     * @param $login
     * @param $form
     * @param $defaultEncryption
     * @param int $dwAuthOpts
     * @return UserExists
     * @throws \Exception
     */
	public function clGetExtranetUserExists($login, $form, $defaultEncryption, int $dwAuthOpts=0) : UserExists
    {
        $sURI = $this->_sCreateRequest([$form, 'GetExtranetUserExists'], ['login' => $login], []);

        $clHttpResponse = $this->_oExecute('GetExtranetUserExists', $sURI, __FUNCTION__);
        $sContent = $clHttpResponse->content;
        $sInfoEncryption = $clHttpResponse->getXNOUTOnlineInfoCnx();
        $sIV = $clHttpResponse->getIVForInfoCnx();

        return new UserExists($sContent, $sInfoEncryption, $sIV, $defaultEncryption, $dwAuthOpts);
    }

    /**
     * @param $email
     * @param $id
     * @return int
     * @throws \Exception
     */
	public function nGetUserSSOExists($email, $id) : int
    {
        $sURI = $this->_sCreateRequest(['GetUserSSOExists'], ['login' => $email, 'id' => $id], []);
        return (int) $this->_oExecute('GetUserSSOExists', $sURI, __FUNCTION__)->content;
    }

	/**
	 * récupère la version de NOUTOnline
	 * @return NOUTOnlineVersion
     * @throws \Exception
	 */
	public function clGetVersion() : NOUTOnlineVersion
	{
		$sURI = $this->_sCreateRequest(['GetVersion'], [], []);

		return new NOUTOnlineVersion($this->_oExecute('GetVersion', $sURI, __FUNCTION__, null, 1)->content);
	}

    /**
     * @param string $versionMin
     * @return NOUTOnlineState
     * @throws \Exception
     */
	public function clGetNOUTOnlineState(string $versionMin) :NOUTOnlineState
    {
        $sURI = $this->_sCreateRequest(['GetVersion'], [], []);

        $ret = new NOUTOnlineState();
        try {
            $clVersion = new NOUTOnlineVersion($this->_oExecute('GetVersion', $sURI, __FUNCTION__, null, 1)->content);
            $ret->setVersionNO($clVersion, $versionMin);
        }
        catch(\Exception $e)
        {
        }
        return $ret;
    }


    /**
     * @return HTTPResponse
     * @param Identification $clIdentification
     * @throws \Exception
     */
    public function oGetHelp(Identification $clIdentification) : HTTPResponse
    {
        $sURI = $this->_sCreateRequest(['GetHelp'], [], [], $clIdentification);

        return $this->_oExecute('GetHelp', $sURI, __FUNCTION__, $clIdentification);
    }

    /**
     * récupère des évènements
     * @param array          $aTabParam
     * @param Identification $clIdentification
     * @return HTTPResponse
     * @throws \Exception
     */
    public function oGetSchedulerInfo(array $aTabParam, Identification $clIdentification) : HTTPResponse
    {
        $sURI = $this->_sCreateRequest(['GetSchedulerInfo'], $aTabParam, [], $clIdentification);

        return $this->_oExecute('GetSchedulerInfo', $sURI, __FUNCTION__, $clIdentification);
    }

    /**
     * récupère des évènements
     * @param array          $aTabParam
     * @param Identification $clIdentification
     * @param $idForm
     * @param $idEnreg
     * @param $idColumn
     * @return HTTPResponse
     * @throws \Exception
     */
    public function oGetSchedulerCardInfo($idForm, $idEnreg, $idColumn, array $aTabParam, Identification $clIdentification): HTTPResponse
    {
        $sURI = $this->_sCreateRequest([$idForm, $idEnreg, $idColumn, 'GetSchedulerInfo'], $aTabParam, [], $clIdentification);

        return $this->_oExecute('GetSchedulerInfo', $sURI, __FUNCTION__, $clIdentification);
    }


    /**
     * ne pas supprimer est utilisé par NOUTClient::_oGetIhmMenuPart
     * récupère le menu
     * @param Identification $clIdentification
     * @return HTTPResponse
     * @throws \Exception
     */
    public function oGetMenu(Identification $clIdentification) : HTTPResponse
    {
        $sURI = $this->_sCreateRequest(['GetMenu'], [], [], $clIdentification);

        return $this->_oExecute('GetMenu', $sURI, __FUNCTION__, $clIdentification);
    }

    /**
     * ne pas supprimer est utilisé par NOUTClient::_oGetIhmMenuPart
     * récupère la barre de menu
     * @param Identification $clIdentification
     * @return HTTPResponse
     * @throws \Exception
     */
    public function oGetToolbar(Identification $clIdentification) : HTTPResponse
    {
        $sURI = $this->_sCreateRequest(['GetToolbar'], [], [], $clIdentification);

        return $this->_oExecute('GetToolbar', $sURI, __FUNCTION__, $clIdentification);
    }

    /**
     * ne pas supprimer est utilisé par NOUTClient::_oGetIhmMenuPart
     * récupère les icones centraux
     * @param Identification $clIdentification
     * @return HTTPResponse
     * @throws \Exception
     */
    public function oGetCentralIcon(Identification $clIdentification) : HTTPResponse
    {
        $sURI = $this->_sCreateRequest(['GetCentralIcon'], [], [], $clIdentification);

        return $this->_oExecute('GetCentralIcon', $sURI, __FUNCTION__, $clIdentification);
    }

	/**
	 * récupère la version du langage
	 * @param Identification $clIdentification
	 * @return HTTPResponse
     * @throws \Exception
	 */
	public function oGetChecksumLangage(Identification $clIdentification) : HTTPResponse
	{
		$sURI = $this->_sCreateRequest(['GetLangageVersion'], [], [], $clIdentification);

		return $this->_oExecute('GetLangageVersion', $sURI, __FUNCTION__, $clIdentification);
	}

	/**
	 * récupère le checksum d'un formulaire
	 * @param $idTableau , identifiant du formulaire
	 * @param Identification $clIdentification
	 * @return HTTPResponse
     * @throws \Exception
	 */
	public function oGetChecksum($idTableau, Identification $clIdentification) : HTTPResponse
	{
		$sURI = $this->_sCreateRequest([$idTableau, 'GetChecksum'], [], [], $clIdentification);

		return $this->_oExecute('GetChecksum', $sURI, __FUNCTION__, $clIdentification);
	}

	// IdTableau est IDForm

	/**
	 * @param $sIDTableau
	 * @param $sIDEnreg
	 * @param $sIDColonne
	 * @param $aTabParam
	 * @param $aTabOption
	 * @param Identification $clIdentification
	 * @return HTTPResponse
     * @throws \Exception
	 */
	public function oGetColInRecord($sIDTableau, $sIDEnreg, $sIDColonne, $aTabParam, $aTabOption, Identification $clIdentification) : HTTPResponse
	{
	    //on met la chaine vide à la fin du tableau pour avoir le trailing /
		$sURI = $this->_sCreateRequest([$sIDTableau, $sIDEnreg, $sIDColonne, ''], $aTabParam, $aTabOption, $clIdentification);
        return $this->_oExecute('GetColInRecord', $sURI, __FUNCTION__, $clIdentification);
	}

    /**
     * @param                $sIDTableau
     * @param                $sIDEnreg
     * @param                $sIDColonne
     * @param                $aTabParam
     * @param                $aTabOption
     * @param Identification $clIdentification
     * @return NOUTFileInfo
     * @throws \Exception
     */
    public function oGetFileInRecord($sIDTableau, $sIDEnreg, $sIDColonne, $aTabParam, $aTabOption, Identification $clIdentification) : NOUTFileInfo
    {
        $sURI = $this->_sCreateRequest([$sIDTableau, $sIDEnreg, $sIDColonne, ''], $aTabParam, $aTabOption, $clIdentification);

        $oHTTPResponse = $this->_oExecute('GetColInRecord', $sURI, __FUNCTION__, $clIdentification);
        $oHTTPResponse->setLastModifiedIfNotExists();

        $oNOUTFileInfo = new NOUTFileInfo();
        $oNOUTFileInfo->initFromHTTPResponse($oHTTPResponse);

        return $oNOUTFileInfo;
    }

    /**
     * @param                $sIDForm
     * @param                $sQuery
     * @param                $aTabParam
     * @param                $aTabOption
     * @param Identification $clIdentification
     * @return HTTPResponse
     * @throws \Exception
     */
	public function oGetSuggestFromQuery($sIDForm, $sQuery, $aTabParam, $aTabOption, Identification $clIdentification) : HTTPResponse
	{
        $sEndPart = "autocomplete";

		$sURI = $this->_sCreateRequest([$sIDForm, $sQuery, $sEndPart], $aTabParam, $aTabOption, $clIdentification);

		$result = $this->_oExecute('GetSuggestFromQuery', $sURI, __FUNCTION__, $clIdentification); // On veut la réponse complète ici

		return $result;
	}

    /**
     * @param $messageId
     * @param $clIdentification
     * @return HTTPResponse
     * @throws \Exception
     */
	public function oPrintMessage($messageId, $clIdentification) : HTTPResponse
    {
	    $identification = $this->_sCreateIdentification($clIdentification);

        $host = $this->__ConfigurationDialogue->getServiceAddress();

        $printMessage = new PrintMessage($messageId, $host);
        $printMessage->setIdentification($identification);

        $sURI = $printMessage->generateRoute();

        $result = $this->_oExecute('printMessage', $sURI, __FUNCTION__);

	    return $result;
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


	const PARAM_TestRestart     = 'TestRestart';
	const PARAM_Login           = 'Login';
	const PARAM_Table           = 'Table';
	const PARAM_TypeGraph       = 'TypeGraph';
	const PARAM_DPI             = 'DPI';
	const PARAM_Index           = 'Index';
	const PARAM_Axes            = 'Axes';
	const PARAM_OnlyData        = 'OnlyData';
	const PARAM_Items           = 'Items';
	const PARAM_MoveType        = 'MoveType';
	const PARAM_Scale           = 'Scale';
	const PARAM_Offset          = 'Offset';
	const PARAM_StartTime       = 'StartTime';
	const PARAM_EndTime         = 'EndTime';
	const PARAM_Resource        = 'Resource';
	const PARAM_RealOnly        = 'RealOnly';
	const PARAM_Recursive       = 'Recursive';
    const PARAM_CallingColumn   = 'CallingColumn';

	const OPTION_First              = 'First';
	const OPTION_Length             = 'Length';
	const OPTION_ChangePage         = 'ChangePage';
	const OPTION_Sort1              = 'Sort1';
	const OPTION_Sort2              = 'Sort2';
	const OPTION_Sort3              = 'Sort3';
	const OPTION_WithBreakRow       = 'WithBreakRow';
	const OPTION_WithEndCalculation = 'WithEndCalculation';
	const OPTION_DisplayMode        = 'DisplayMode';
	const OPTION_MaxResult          = 'MaxResult';
	const OPTION_ColList            = 'ColList';
	const OPTION_Encoding           = 'Encoding';
	const OPTION_MimeType           = 'MineType';
	const OPTION_TransColor         = 'TransColor';
	const OPTION_WantContent        = 'WantContent';
	const OPTION_Readable           = 'Readable';
	const OPTION_LanguageCode       = 'LanguageCode';
	const OPTION_DisplayValue       = 'DisplayValue';
	const OPTION_ColorFrom          = 'ColorFrom';
	const OPTION_ColorTo            = 'ColorTo';
	const OPTION_Width              = 'Width';
	const OPTION_Height             = 'Height';
	const OPTION_ListMode           = 'ListMode';
    const OPTION_IDCol              = 'IDCol';

	const OPTION_Record             = 'Record';
    const OPTION_Column             = 'Column';
}
