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
use NOUT\Bundle\NOUTOnlineBundle\Entity\OASIS\UsernameToken;
use NOUT\Bundle\NOUTOnlineBundle\Entity\REST\Identification;
use NOUT\Bundle\NOUTOnlineBundle\Service\ClientInformation;
use Symfony\Component\Config\Definition\Exception\Exception;

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
	 * constructeur permettant d'instancier les classe de communication soap avec les bonne question
	 * @param ClientInformation $clientInfo
	 * @param ConfigurationDialogue $clConfig
	 * @param NOUTOnlineLogger $_clLogger
	 */
	public function __construct(ClientInformation $clientInfo, ConfigurationDialogue $clConfig, NOUTOnlineLogger $_clLogger)
	{
		$this->__ConfigurationDialogue 	= $clConfig;
		$this->__clLogger              	= $_clLogger;
		$this->__clInfoClient 			= $clientInfo;
	}

	/**
	 * Test si le service est démarré
	 * @return bool
	 */
	public function bIsStarted()
	{
		try
		{
			$this->sGetVersion();
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
	private function _sCreateIdentification(Identification $clIdentification)
	{
		if (empty($clIdentification->m_clUsernameToken) || !$clIdentification->m_clUsernameToken->bIsValid())
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

        if ($usernameToken->bCrypted())
        {
            $sBottom .= '&encryption=' . urlencode($usernameToken->getMode());
            $sBottom .= '&md5=' . urlencode($usernameToken->CryptMd5);
            if (isset($usernameToken->CryptIV))
            {
                $sBottom .= '&iv=' . urlencode($usernameToken->CryptIV);
            }
            if (isset($usernameToken->CryptKS))
            {
                $sBottom .= '&ks=' . urlencode($usernameToken->CryptKS);
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
	 * @param UsernameToken $clUsernameToken pour l'identification
	 * @param string $sTokenSession avec le token de la session
	 * @param string $sIDContexte identifiant du contexte
	 * @return string la requette rest
	 */
	private function _sCreateRequest($sAction, array $aTabParam, array $aTabOption, Identification $clIdentification)
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

	protected function _sExecute_cURL($sAction, $sURI, $sDestination)
	{
        $response               = new \stdClass();
        $response->content      = '';
        $response->headers      = array();

		//initialisation de curl
		$curl = curl_init($sURI);

		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			ConfigurationDialogue::HTTP_SIMAX_CLIENT_IP     . ': '  . $this->__clInfoClient->getIP(),
			ConfigurationDialogue::HTTP_SIMAX_CLIENT        . ': '  . $this->__ConfigurationDialogue->getSociete(),
			ConfigurationDialogue::HTTP_SIMAX_CLIENT_Version. ': '  . $this->__ConfigurationDialogue->getVersion(),
		));

		if (!empty($sDestination)) // On a un fichier de destination, il faut écrire le résultat de l'url dans le fichier de destination
		{
			$fp = fopen($sDestination, "w");
			curl_setopt($curl, CURLOPT_FILE, $fp);
			curl_setopt($curl, CURLOPT_HEADER, 0);

			curl_exec($curl);
			fclose($fp);

            $info = curl_getinfo($curl);


			// Vérifie si une erreur survient
			if(curl_errno($curl))
			{
				$e = new \Exception(curl_error($curl));
				curl_close($curl);
				throw $e;
			}

			curl_close($curl);

			//si le fichier est vide on le supprime
			if (is_file($sDestination) && ((filesize($sDestination) == 0) || ($info['http_code']!=200)))
			{
				unlink($sDestination);
				return $response;
			}

            $response->content = $sDestination;
            return $response;
		}

        // ------------------------------------------------
		// Contenu du fichier
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($curl);

		// Vérifie si une erreur survient
		if(curl_errno($curl))
		{
			$e = new \Exception(curl_error($curl));
			curl_close($curl);
			throw $e;
		}

        $response->content = $output;

        // ------------------------------------------------
        // Entêtes
        curl_setopt($curl, CURLOPT_HEADER, 1); // Demande des headers

        $output = curl_exec($curl);

        // Vérifie si une erreur survient
        if(curl_errno($curl))
        {
            $e = new \Exception(curl_error($curl));
            curl_close($curl);
            throw $e;
        }

        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $headers = substr($output, 0, $header_size);
        $parsedHeaders = $this->get_headers_from_curl_response($headers);
        $response->headers = $parsedHeaders;
        // ------------------------------------------------

		curl_close($curl);

		return $response;
	}

	protected function _sExecute_natif($sAction, $sURI, $sDestination)
	{
        $response               = new \stdClass();
        $response->content      = '';
        $response->headers      = array();

		//obligé de rajouter l'ip ici car j'ai pas accès au entête http
		$sIP = $this->__clInfoClient->getIP();
		if (!empty($sIP))
		{
			$sURI .= '&ip=' . urlencode($sIP);
		}

		if (!empty($sDestination))
		{
			//on a un fichier de destination
			if (@copy($sURI, $sDestination) === false)
			{
				$aError = error_get_last();
				if (empty($aError))
				{
					$aError['message'] = 'le serveur ' . $this->__ConfigurationDialogue->getServiceAddress() . ' ne répond pas';
				}
				$e = new \Exception($aError['message']);

				throw $e;
			}

			//si le fichier est vide on le supprime
			if (is_file($sDestination) && filesize($sDestination) == 0)
			{
				unlink($sDestination);
				return $response;
			}

            $response->content = $sDestination;

			return $response;
		}

		if (($sResp = @file_get_contents($sURI)) === false)
		{
			$aError = error_get_last();
			if (empty($aError))
            {
                $aError['message'] = 'Le serveur '.$this->__ConfigurationDialogue->getServiceAddress().' ne répond pas';
            }

			$e = new \Exception($aError['message']);
			throw $e;
		}

        $response->content      = $sResp;
        $response->headers      = $http_response_header;
		return $response;
	}

	protected function _sExecute($sAction, $sURI, $sDestination)
	{
		if (isset($this->__clLogger))
		{
			//log des requetes
			$this->__clLogger->startQuery();
		}

		try
		{
            // content = le fichier
            // headers = contenu de $http_response_header

			if (!function_exists('curl_version'))
			{
				$ret = $this->_sExecute_natif($sAction, $sURI, $sDestination);  // curl n'est pas disponible
			}
			else
			{
				$ret = $this->_sExecute_cURL($sAction, $sURI, $sDestination);   // on utilise l'extension curl
			}

		}
		catch(\Exception $e)
		{
			if (isset($this->__clLogger))
			{
				$this->__clLogger->stopQuery($sURI, $e->getMessage(), (empty($sAction) ? substr($sURI, 0, 50) : $sAction), false, false);
			}
			throw $e;
		}

        // Parsage des Headers Http
        $ret->headers = $this->parseHeaders($ret->headers);

		if (isset($this->__clLogger))
		{
			$this->__clLogger->stopQuery($sURI, $ret->content, (empty($sAction) ? substr($sURI, 0, 50) : $sAction), false, array('http-headers'=>$ret->headers));
		}


        // - connection
        // - content-Type
        // - content-Length

		return $ret;
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
		$sURI = $this->_sCreateRequest('GetUserExists', array('login' => $login), array(), new Identification());

		return (int) $this->_sExecute('GetUserExists', $sURI, '')->content;
	}


	/**
	 * récupère la version de NOUTOnline
	 * @return string
	 */
	public function sGetVersion()
	{
		$sURI = $this->_sCreateRequest('GetVersion', array(), array(), new Identification());

		return $this->_sExecute('GetVersion', $sURI, '')->content;
	}

    /**
     * récupère le menu
     * @return string
     */
    public function sGetMenu(Identification $clIdentification)
    {
        $sURI = $this->_sCreateRequest('GetMenu', array(), array(), $clIdentification);

        return $this->_sExecute('GetMenu', $sURI, '')->content;
    }

    /**
     * récupère la barre de menu
     * @return string
     */
    public function sGetToolbar(Identification $clIdentification)
    {
        $sURI = $this->_sCreateRequest('GetToolbar', array(), array(), $clIdentification);

        return $this->_sExecute('GetToolbar', $sURI, '')->content;
    }

    /**
     * récupère les icones centraux
     * @return string
     */
    public function sGetCentralIcon(Identification $clIdentification)
    {
        $sURI = $this->_sCreateRequest('GetCentralIcon', array(), array(), $clIdentification);

        return $this->_sExecute('GetCentralIcon', $sURI, '')->content;
    }

	/**
	 * récupère la version du langage
	 * @param Identification $clIdentification
	 * @return string
	 */
	public function sGetChecksumLangage(Identification $clIdentification)
	{
		$sURI = $this->_sCreateRequest('GetLangageVersion', array(), array(), $clIdentification);

		return $this->_sExecute('GetLangageVersion', $sURI, '')->content;
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

		return $this->_sExecute('GetChecksum', $sURI, '')->content;
	}

	// IdTableau est IDForm

	/**
	 * @param $sIDTableau
	 * @param $sIDEnreg
	 * @param $sIDColonne
	 * @param $aTabParam
	 * @param $aTabOption
	 * @param UsernameToken $clUsernameToken
	 * @param $sTokenSession
	 * @param string $sIDContexte
	 * @return string
	 */
	public function sGetColInRecord($sIDTableau, $sIDEnreg, $sIDColonne, $aTabParam, $aTabOption, Identification $clIdentification, $sDest = '')
	{
		$sURI = $this->_sCreateRequest($sIDTableau.'/'.$sIDEnreg.'/'.$sIDColonne.'/', $aTabParam, $aTabOption, $clIdentification);

		$result = $this->_sExecute('GetColInRecord', $sURI, $sDest); // On veut la réponse complète ici

        return $result;
	}

    /*
     * @param $sURL
     * @param $sDest
     */
	public function sGetFileFromUrl($sURL, $sDest = '')
	{
		$result = $this->_sExecute('GetColInRecord', $sURL, $sDest); // On veut la réponse complète ici

		return $result;
	}

    /**
     * @param $sIDForm
     * @param $sQuery
     * @param $aTabParam
     * @param $aTabOption
     * @param Identification $clIdentification
     * @param string $sDest
     * @return \stdClass
     * @throws \Exception
     */
	public function sGetSuggestFromQuery($sIDForm, $sQuery,  $aTabParam, $aTabOption, Identification $clIdentification, $sDest = '')
	{
        $sEndPart = "autocomplete";

		$sURI = $this->_sCreateRequest($sIDForm.'/'.$sQuery.'/'.$sEndPart, $aTabParam, $aTabOption, $clIdentification);

		$result = $this->_sExecute('GetSuggestFromQuery', $sURI, $sDest); // On veut la réponse complète ici

		return $result;
	}



    /**
     * Parse les entêtes pour fournir une sortie au format natif
     *
     * @param $response
     * @return array
     */
    function get_headers_from_curl_response($response)
    {
        $headers = array();

        $header_text = substr($response, 0, strpos($response, "\r\n\r\n"));

        foreach (explode("\r\n", $header_text) as $i => $line)
        {
            array_push($headers, $line);
        }

        return $headers;
    }


    /**
     * Parse a set of HTTP headers
     *
     * @param array     // The php headers to be parsed
     * @param [string]  // The name of the header to be retrieved
     * @return          // A header value if a header is passed
     *                  // An array with all the headers otherwise
     */
    public function parseHeaders(array $headers, $header = null)
    {
        if(empty($headers))
        {
            return array();
        }

        $output = array();

        if ('HTTP' === substr($headers[0], 0, 4))
        {
            list(, $output['status'], $output['status_text']) = explode(' ', $headers[0]);
            unset($headers[0]);
        }

        foreach ($headers as $v)
        {
            $h = preg_split('/:\s*/', $v);
            $output[$h[0]] = $h[1];
        }

        if (null !== $header)
        {
            if (isset($output[$header]))
            {
                return $output[$header];
            }
            return '';
        }

        // Parser les options
        foreach ($output as $headerKey => $headerValue)
        {
            $options = explode(';', $headerValue);

            // if(count($options) > 1)
            {
                $headerWithOptions          = new \stdClass();
                $headerWithOptions->value   = array_shift($options); // Retire le premier élément
                $headerWithOptions->options = array();

                foreach ($options as $optionKey => $optionValue)
                {
                    $split = explode('=', $optionValue);
                    $cleanOption = str_replace('"', "", $split[1]); // Retirer les quotes
                    $headerWithOptions->options[$split[0]] = $cleanOption;
                }

                $output[$headerKey] = $headerWithOptions;
            }
        }

        return $output;
    }




	const TYPEUTIL_NONE        = 0;
	const TYPEUTIL_UTILISATEUR = 1;
	const TYPEUTIL_SUPERVISEUR = 2;

	const PARAM_TestRestart = 'TestRestart';
	const PARAM_Login       = 'Login';
	const PARAM_Table       = 'Table';
	const PARAM_TypeGraph   = 'TypeGraph';
	const PARAM_DPI         = 'DPI';
	const PARAM_Index       = 'Index';
	const PARAM_Axes        = 'Axes';
	const PARAM_OnlyData    = 'OnlyData';
	const PARAM_Items       = 'Items';
	const PARAM_MoveType    = 'MoveType';
	const PARAM_Scale       = 'Scale';
	const PARAM_Offset      = 'Offset';
	const PARAM_StartTime   = 'StartTime';
	const PARAM_EndTime     = 'EndTime';
	const PARAM_Resource    = 'Resource';
	const PARAM_RealOnly    = 'RealOnly';
	const PARAM_Recursive   = 'Recursive';

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
