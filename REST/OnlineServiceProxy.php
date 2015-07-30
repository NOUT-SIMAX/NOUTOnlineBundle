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
use NOUT\Bundle\NOUTOnlineBundle\Entity\Langage;
use NOUT\Bundle\NOUTOnlineBundle\Entity\OASIS\UsernameToken;
use NOUT\Bundle\NOUTOnlineBundle\Entity\REST\Identification;
use Symfony\Component\Config\Definition\Exception\Exception;

class OnlineServiceProxy
{
	/**
	 * classe de configuration
	 * @var \NOUT\Bundle\NOUTOnlineBundle\Entity\ConfigurationDialogue
	 */
	private $__ConfigurationDialogue;

	//logger symfony
	private $__clLogger;

	/**
	 * @var string
	 */
	private $m_sIPClient;


	/**
	 * constructeur permettant d'instancier les classe de communication soap avec les bonne question
	 * @param $clConfig
	 * @param $_clLogger
	 * @return unknown_type
	 */
	public function __construct(ConfigurationDialogue $clConfig, NOUTOnlineLogger $_clLogger)
	{
		$this->__ConfigurationDialogue = $clConfig;
		$this->__clLogger              = $_clLogger;
	}

	/**
	 * @param string $sIP
	 * @return $this
	 */
	public function setIPClient($sIP)
	{
		$this->m_sIPClient=trim($sIP);
		return $this;
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
			return '';
		}

		$sBottom = '!';

		$sBottom .= 'Username='.urlencode($clIdentification->m_clUsernameToken->Username);
		$sBottom .= '&Password='.urlencode($clIdentification->m_clUsernameToken->Password);
		$sBottom .= '&nonce='.urlencode($clIdentification->m_clUsernameToken->Nonce);
		$sBottom .= '&created='.urlencode($clIdentification->m_clUsernameToken->Created);

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
				$sListeParam .= '&'.urlencode($sKey).'='.urlencode($sValue);
			}
			$sUrl .= trim($sListeParam,  '&');
		}
		//la liste des options (entre ; et !)
		if (is_array($aTabOption) && count($aTabOption)>0)
		{
			$sListeOption = '';
			foreach ($aTabOption as $sKey => $sValue)
			{
				$sListeOption .= '&'.urlencode($sKey).'='.urlencode($sValue);
			}
			$sUrl .= ';'.trim($sListeOption,  '&');
		}

		$sUrl .= $this->_sCreateIdentification($clIdentification);

		return $sUrl;
	}

	protected function _sExecute_cURL($sAction, $sURI, $sDestination)
	{
		if (empty($this->m_sIPClient))
		{
			throw new \Exception('Il faut obligatoirement spécifier l\'adresse IP du client final au niveau du proxy REST');
		}

		//initialisation de curl
		$curl = curl_init($sURI);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			ConfigurationDialogue::HTTP_SIMAX_CLIENT_IP.': '.$this->m_sIPClient,
			ConfigurationDialogue::HTTP_SIMAX_CLIENT.': '.$this->__ConfigurationDialogue->getSociete(),
			ConfigurationDialogue::HTTP_SIMAX_CLIENT_Version.': '.$this->__ConfigurationDialogue->getVersion(),
		));

		if (!empty($sDestination))
		{
			//on a un fichier de destination, il faut écrire le résultat de l'url dans le fichier de destination
			$fp = fopen($sDestination, "w");
			curl_setopt($curl, CURLOPT_FILE, $fp);
			curl_setopt($curl, CURLOPT_HEADER, 0);

			curl_exec($curl);
			fclose($fp);

			// Vérifie si une erreur survient
			if(curl_errno($curl))
			{
				$e = new \Exception(curl_error($curl));
				curl_close($curl);
				throw $e;
			}
			curl_close($curl);

			//si le fichier est vide on le supprime
			if (is_file($sDestination) && filesize($sDestination) == 0)
			{
				unlink($sDestination);
				return '';
			}
			return $sDestination;
		}

		//pas de fichier de destination, on récupère le contenu de l'url dans une chaine
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($curl);

		// Vérifie si une erreur survient
		if(curl_errno($curl))
		{
			$e = new \Exception(curl_error($curl));
			curl_close($curl);
			throw $e;
		}

		curl_close($curl);
		return $output;
	}

	protected function _sExecute_natif($sAction, $sURI, $sDestination)
	{
		//obligé de rajouter l'ip ici car j'ai pas accès au entête http
		$sURI.='&ip='.urlencode($this->m_sIPClient);

		if (!empty($sDestination))
		{
			//on a un fichier de destination
			if (@copy($sURI, $sDestination) === false)
			{
				$aError = error_get_last();
				if (empty($aError))
					$aError['message']='le serveur '.$this->__ConfigurationDialogue->getServiceAddress().' ne répond pas';
				$e = new \Exception($aError['message']);

				throw $e;
			}
			//si le fichier est vide on le supprime
			if (is_file($sDestination) && filesize($sDestination) == 0)
			{
				unlink($sDestination);
				return '';
			}

			return $sDestination;
		}

		if (($sResp = @file_get_contents($sURI)) === false)
		{
			$aError = error_get_last();
			if (empty($aError))
				$aError['message']='le serveur '.$this->__ConfigurationDialogue->getServiceAddress().' ne répond pas';

			$e = new \Exception($aError['message']);
			throw $e;
		}
		return $sResp;

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
			if (!function_exists('curl_version'))
			{
				//curl n'est pas disponible
				$ret = $this->_sExecute_natif($sAction, $sURI, $sDestination);
			}
			else
			{
				//on l'extension curl
				$ret = $this->_sExecute_cURL($sAction, $sURI, $sDestination);
			}
		}
		catch(Exception $e)
		{
			if (isset($this->__clLogger))
			{
				$this->__clLogger->stopQuery($sURI, $e.getMessage(), (empty($sAction) ? substr($sURI, 0, 50) : $sAction), false, true);
			}

			throw $e;
		}

		if (isset($this->__clLogger))
		{
			$this->__clLogger->stopQuery($sURI, $ret, (empty($sAction) ? substr($sURI, 0, 50) : $sAction), false, true);
		}
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

		return (int) $this->_sExecute('GetUserExists', $sURI, '');
	}


	/**
	 * récupère la version de NOUTOnline
	 * @return string
	 */
	public function sGetVersion()
	{
		$sURI = $this->_sCreateRequest('GetVersion', array(), array(), new Identification());

		return $this->_sExecute('GetVersion', $sURI, '');
	}

	/**
	 * récupère la version du langage
	 * @param Identification $clIdentification
	 * @return string
	 */
	public function sGetChecksumLangage(Identification $clIdentification)
	{
		$sURI = $this->_sCreateRequest('GetLangageVersion', array(), array(), $clIdentification);

		return $this->_sExecute('GetLangageVersion', $sURI, '');
	}

	/**
	 * récupère le checksum d'un formulaire
	 * @param $idTableau identifiant du formulaire
	 * @param Identification $clIdentification
	 * @return string
	 */
	public function sGetChecksum($idTableau, Identification $clIdentification)
	{
		$sURI = $this->_sCreateRequest($idTableau.'/GetChecksum', array(), array(), $clIdentification);

		return $this->_sExecute('GetChecksum', $sURI, '');
	}

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

		return $this->_sExecute('GetColInRecord', $sURI, $sDest);
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
}
