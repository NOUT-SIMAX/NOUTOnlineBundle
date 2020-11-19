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
use NOUT\Bundle\NOUTOnlineBundle\Entity\NOUTOnlineVersion;
use NOUT\Bundle\NOUTOnlineBundle\Entity\UsernameToken\UsernameToken;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\OnlineError;
use NOUT\Bundle\NOUTOnlineBundle\Entity\REST\Identification;
use NOUT\Bundle\NOUTOnlineBundle\Service\ClientInformation;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\SOAPException;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Stopwatch\Stopwatch;

class OnlineServiceProxy
{
	/**
	 * classe de configuration
	 * @var \NOUT\Bundle\NOUTOnlineBundle\Entity\ConfigurationDialogue
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
	 * @param ClientInformation $clientInfo
	 * @param ConfigurationDialogue $clConfig
	 * @param NOUTOnlineLogger $_clLogger
	 */
	public function __construct(ClientInformation $clientInfo, ConfigurationDialogue $clConfig, NOUTOnlineLogger $_clLogger, Stopwatch $stopwatch=null)
	{
		$this->__ConfigurationDialogue 	= $clConfig;
		$this->__clLogger              	= $_clLogger;
		$this->__clInfoClient 			= $clientInfo;
		$this->__stopwatch              = $stopwatch;
	}

	/**
	 * Test si le service est démarré
	 * @return bool
	 */
	public function bIsStarted()
	{
		try
		{
			$this->clGetVersion();
			return true;
		}
		catch(\Exception $e)
		{
			return false;
		}
	}

	/**
	 * Retourne la fin de la requette rest (partie identification)
	 * @param string $sIdContext le context de l'action (facultatif)
	 * @return string la fin de la requette rest
	 */
	private function _sCreateIdentification(Identification $clIdentification=null)
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
     */
    private function __sGetUsernameToken(UsernameToken $usernameToken)
    {
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
	 * @param string $sAction le nom de l'action
	 * @param array $aTabParam tableau des parametres
	 * @param array $aTabOption tableau des options
     * @param $clIdentification
	 * @return string la requette rest
	 */
	private function _sCreateRequest($sAction, array $aTabParam, array $aTabOption, Identification $clIdentification=null)
	{
		$sUrl = $this->__ConfigurationDialogue->getServiceAddress().$sAction.'?';

		//la liste des paramètres (entre ? et ;)
		if (is_array($aTabParam) && count($aTabParam)>0)
		{
			$sListeParam = '';

			foreach ($aTabParam as $sKey => $sValue)
			{
				$sListeParam .= '&'.urlencode(utf8_decode($sKey)).'='.urlencode(utf8_decode($sValue));
			}

			$sUrl .= trim($sListeParam,  '&');
		}

		//la liste des options (entre ; et !)
		if (is_array($aTabOption) && count($aTabOption)>0)
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

    protected function _oMakeResponse($output, $headers)
    {
        $ret = new HTTPResponse($output, $headers);
        if ($ret->getStatus()!=200)
        {
            $no_error = new OnlineError(0, 0, 0, '');
            $no_error->parseFromREST($output);
            //il y a une erreur, il faut parser l'erreur
            $e = new SOAPException($no_error->getMessage(), $no_error->getCode(), $no_error->getCategorie());
            throw $e;
        }
        return $ret;
    }

    /**
     * @param $sAction
     * @param $sURI
     * @return HTTPResponse
     * @throws \Exception
     */
	protected function _oExecute($sAction, $sURI, $function, Identification $clIdentification=null, $timeout=null)
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
            $this->__stopLogQuery($sURI, $curl_errmess, $sAction, null, $function, $clIdentification);

            throw new \Exception($curl_errmess);
        }

        $header_request = curl_getinfo($curl, CURLINFO_HEADER_OUT);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        curl_close($curl);
        $headers = substr($output, 0, $header_size);
        $output = substr($output, $header_size);

        $parsedHeaders = $this->_aGetHeadersFromCurlResponse($headers);
        // ------------------------------------------------

        $ret = $this->_oMakeResponse($output, $parsedHeaders);

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
    private function __stopLogQuery($uri, $request, $reponse, $action, $header, $function, Identification $clIdentification=null)
    {
        if (isset($this->__stopwatch)){
            $this->__stopwatch->stop(get_class($this).'::'.$function);
        }

        if (isset($this->__clLogger))
        {
            $extra = array();
            if (!empty($header)){
                $extra[NOUTOnlineLogger::EXTRA_Http_Headers]=$header;
            }
            if (!is_null($clIdentification) && !empty($clIdentification->m_sTokenSession)){
                $extra[NOUTOnlineLogger::EXTRA_TokenSession]=$clIdentification->m_sTokenSession;
            }
            if (!is_null($clIdentification) && !empty($clIdentification->m_sIDContexteAction)){
                $extra[NOUTOnlineLogger::EXTRA_ActionContext]=$clIdentification->m_sIDContexteAction;
            }

            $this->__clLogger->stopQuery($request, $reponse, (empty($action) ? substr($uri, 0, 50) : $action), false, $extra);
        }
    }


	/**
	 * recherche un utilisateur par son pseudo
	 * @param $login
	 * @return int :
	 * - TYPEUTIL_NONE : n'existe pas
	 * - TYPEUTIL_UTILISATEUR : utilisateur non superviseur
	 * - TYPEUTIL_SUPERVISEUR : utilisateur superviseur
	 */
	public function nGetUserExists($login)
	{
		$sURI = $this->_sCreateRequest('GetUserExists', array('login' => $login), array());

		return (int) $this->_oExecute('GetUserExists', $sURI, __FUNCTION__)->content;
	}

	public function nGetUserSSOExists($email, $id)
    {
        $sURI = $this->_sCreateRequest('GetUserSSOExists', array('login' => $email, 'id' => $id), array());
        return (int) $this->_oExecute('GetUserSSOExists', $sURI, __FUNCTION__)->content;
    }

	/**
	 * récupère la version de NOUTOnline
	 * @return NOUTOnlineVersion
	 */
	public function clGetVersion()
	{
		$sURI = $this->_sCreateRequest('GetVersion', array(), array());

		return $clVersion = new NOUTOnlineVersion($this->_oExecute('GetVersion', $sURI, __FUNCTION__, null, 1)->content);
	}

    /**
     * récupère le menu
     * @return string
     */
    public function sGetMenu(Identification $clIdentification)
    {
        $sURI = $this->_sCreateRequest('GetMenu', array(), array(), $clIdentification);

        return $this->_oExecute('GetMenu', $sURI, __FUNCTION__, $clIdentification)->content;
    }

    /**
     * @return string
     * @param Identification $clIdentification
     */
    public function sGetHelp(Identification $clIdentification)
    {
        $sURI = $this->_sCreateRequest('GetHelp', array(), array(), $clIdentification);

        return $this->_oExecute('GetHelp', $sURI, __FUNCTION__, $clIdentification)->content;
    }

    /**
     * récupère des évènements
     * @param array          $aTabParam
     * @param Identification $clIdentification
     * @return string
     * @throws \Exception
     */
    public function sGetSchedulerInfo(array $aTabParam, Identification $clIdentification)
    {
        $sURI = $this->_sCreateRequest('GetSchedulerInfo', $aTabParam, array(), $clIdentification);

        return $this->_oExecute('GetSchedulerInfo', $sURI, __FUNCTION__, $clIdentification);
    }

    /**
     * récupère des évènements
     * @param array          $aTabParam
     * @param Identification $clIdentification
     * @param $idForm
     * @param $idEnreg
     * @param $idColumn
     * @return string
     * @throws \Exception
     */
    public function sGetSchedulerCardInfo($idForm, $idEnreg, $idColumn, array $aTabParam, Identification $clIdentification)
    {
        $sURI = $this->_sCreateRequest($idForm.'/'.$idEnreg.'/'.$idColumn.'/GetSchedulerInfo', $aTabParam, array(), $clIdentification);

        return $this->_oExecute('GetSchedulerInfo', $sURI, __FUNCTION__, $clIdentification);
    }


    /**
     * récupère la barre de menu
     * @return string
     */
    public function sGetToolbar(Identification $clIdentification)
    {
        $sURI = $this->_sCreateRequest('GetToolbar', array(), array(), $clIdentification);

        return $this->_oExecute('GetToolbar', $sURI, __FUNCTION__, $clIdentification)->content;
    }

    /**
     * récupère les icones centraux
     * @return string
     */
    public function sGetCentralIcon(Identification $clIdentification)
    {
        $sURI = $this->_sCreateRequest('GetCentralIcon', array(), array(), $clIdentification);

        return $this->_oExecute('GetCentralIcon', $sURI, __FUNCTION__, $clIdentification)->content;
    }

	/**
	 * récupère la version du langage
	 * @param Identification $clIdentification
	 * @return string
	 */
	public function sGetChecksumLangage(Identification $clIdentification)
	{
		$sURI = $this->_sCreateRequest('GetLangageVersion', array(), array(), $clIdentification);

		return $this->_oExecute('GetLangageVersion', $sURI, __FUNCTION__, $clIdentification)->content;
	}

	/**
	 * récupère le checksum d'un formulaire
	 * @param string $idTableau identifiant du formulaire
	 * @param Identification $clIdentification
	 * @return string
	 */
	public function sGetChecksum($idTableau, Identification $clIdentification)
	{
		$sURI = $this->_sCreateRequest($idTableau.'/GetChecksum', array(), array(), $clIdentification);

		return $this->_oExecute('GetChecksum', $sURI, __FUNCTION__, $clIdentification)->content;
	}

	// IdTableau est IDForm

	/**
	 * @param $sIDTableau
	 * @param $sIDEnreg
	 * @param $sIDColonne
	 * @param $aTabParam
	 * @param $aTabOption
	 * @param Identification $clIdentification
	 * @return string
	 */
	public function sGetColInRecord($sIDTableau, $sIDEnreg, $sIDColonne, $aTabParam, $aTabOption, Identification $clIdentification)
	{
		$sURI = $this->_sCreateRequest($sIDTableau.'/'.$sIDEnreg.'/'.$sIDColonne.'/', $aTabParam, $aTabOption, $clIdentification);
        return $this->_oExecute('GetColInRecord', $sURI, __FUNCTION__, $clIdentification)->content;
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
    public function oGetFileInRecord($sIDTableau, $sIDEnreg, $sIDColonne, $aTabParam, $aTabOption, Identification $clIdentification)
    {
        $sURI = $this->_sCreateRequest($sIDTableau.'/'.$sIDEnreg.'/'.$sIDColonne.'/', $aTabParam, $aTabOption, $clIdentification);

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
	public function sGetSuggestFromQuery($sIDForm, $sQuery,  $aTabParam, $aTabOption, Identification $clIdentification)
	{
        $sEndPart = "autocomplete";

		$sURI = $this->_sCreateRequest($sIDForm.'/'.urlencode($sQuery).'/'.$sEndPart, $aTabParam, $aTabOption, $clIdentification);

		$result = $this->_oExecute('GetSuggestFromQuery', $sURI, __FUNCTION__, $clIdentification); // On veut la réponse complète ici

		return $result;
	}

	public function sPrintMessage($messageId, $clIdentification) {
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
     */
    protected function _aGetHeadersFromCurlResponse($response)
    {
        $headers = array();

        $header_text = substr($response, 0, strpos($response, "\r\n\r\n"));

        foreach (explode("\r\n", $header_text) as $i => $line)
        {
            array_push($headers, $line);
        }

        return $headers;
    }


	const TYPEUTIL_NONE        = 0;
	const TYPEUTIL_UTILISATEUR = 1;
	const TYPEUTIL_SUPERVISEUR = 2;

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
