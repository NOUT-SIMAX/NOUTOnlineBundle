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

class OnlineServiceProxy
{
	/**
	 * classe de configuration
	 * @var \NOUT\Bundle\NOUTOnlineBundle\Entity\ConfigurationDialogue
	 */
	private $__ConfigurationDialogue ;

	//logger symfony
	private $__clLogger;



	/**
	 * constructeur permettant d'instancier les classe de communication soap avec les bonne question
	 * @param $clConfig
	 * @param $_clLogger
	 * @return unknown_type
	 */
	public function __construct(ConfigurationDialogue $clConfig, NOUTOnlineLogger $_clLogger)
	{
		$this->__ConfigurationDialogue = $clConfig;
		$this->__clLogger = $_clLogger;
	}
	/**
	 * Retourne la fin de la requette rest (partie identification)
	 * @param string $sIdContext le context de l'action (facultatif)
	 * @return string la fin de la requette rest
	 */
	private function _sCreateIdentification(UsernameToken $clTokenSession, $sTokenSession, $sIdContext = '')
	{
		if (!$clTokenSession->bIsValid())
			return '';

		$sBottom = '!';

		$sBottom .= 'Username=' . urlencode($clTokenSession->Username);
		$sBottom .= '&Password=' . urlencode($clTokenSession->Password);
		$sBottom .= '&nonce=' . urlencode($clTokenSession->Nonce);
		$sBottom .= '&created=' . urlencode($clTokenSession->Created);
		$sBottom .= '&SessionToken=' . urlencode($sTokenSession);

		if (strlen($this->__ConfigurationDialogue->m_sAPIUUID) > 0)
			$sBottom .= '&APIUUID=' . urlencode($this->__ConfigurationDialogue->m_sAPIUUID );

		if(strlen(sIdContext) > 0)
			$sBottom .= '&ActionContext=' . urlencode($sIdContext);

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
	private function _sCreateRequest($sAction, array $aTabParam, array $aTabOption, UsernameToken $clUsernameToken, $sTokenSession, $sIDContexte='')
	{
		$sUrl = $this->__ConfigurationDialogue->m_sServiceAddress.$sAction.'?';
		//la liste des paramètres (entre ? et ;)
		if(is_array($aTabParam) && count($aTabParam)>0)
		{
			$sListeParam='';
			foreach ($aTabParam as $sKey => $sValue)
			{
				$sListeParam .= '&' . $sKey .'=' . $sValue;
			}
			$sUrl.=trim($sListeParam,  '&');
		}
		//la liste des options (entre ; et !)
		if(is_array($aTabOption) && count($aTabOption)>0)
		{
			$sListeOption='';
			foreach ($aTabOption as $sKey => $sValue)
			{
				$sListeOption .= '&' . $sKey .'=' . $sValue;
			}
			$sUrl.=';'.trim($sListeOption,  '&');
		}

		$sUrl .= $this->_sCreateIdentification($clUsernameToken, $sTokenSession, $sIDContexte);
		return $sUrl;
	}



	public function bGetUserExists($login)
	{
		$sURI = $this->_sCreateRequest('GetUserExists', array('login'=>$login), array(), new UsernameToken('',''), '', '');

		$sRet = $this->_sExecute('GetUserExists', $sURI, '');
	}

	protected function _sExecute($sAction, $sURI, $sDestination)
	{
		if (isset($this->__clLogger)) //log des requetes
			$this->__clLogger->startQuery();

		if (strlen($sDestination)>0)
		{
			if (@copy($sURI, $sDestination) === false)
			{
				$aError=error_get_last();
				$e = new \Exception($aError['message']);

				if (isset($this->__clLogger))
					$this->__clLogger->stopQuery($sURI, $aError['message'], $sAction);

				throw $e;
			}
			//si le fichier est vide on le supprime
			if(is_file($sDestination) && filesize($sDestination) == 0)
			{
				unlink($sDestination);
				if (isset($this->__clLogger))
					$this->__clLogger->stopQuery($sURI, '', $sAction);

				return '';
			}

			if (isset($this->__clLogger))
				$this->__clLogger->stopQuery($sURI, file_get_contents($sDestination, null, null, 0, 20), $sAction);

			return $sDestination;
		}

		if(($sResp = @file_get_contents($sURI)) === false)
		{
			$aError=error_get_last();
			$e = new \Exception($aError['message']);

			if (isset($this->__clLogger))
				$this->__clLogger->stopQuery($sURI, $aError['message'], $sAction);

			throw $e;
		}


		if (isset($this->__clLogger))
			$this->__clLogger->stopQuery($sURI, $sResp, $sAction);

		return $sResp;
	}


} 