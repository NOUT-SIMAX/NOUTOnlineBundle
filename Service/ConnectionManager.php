<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 23/07/14
 * Time: 11:14
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Service;


use NOUT\Bundle\NOUTOnlineBundle\OASIS\UsernameToken;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetTokenSession;

class ConnectionManager {

	public function __construct()
	{

	}

	/**
	 * Génère les paramètres pour la méthode GetTokenSession
	 * @return GetTokenSession
	 */
	public function getGetTokenSession($nErreur=0)
	{
		/*
		if(isset($_SESSION) && isset($_SESSION[SS_URL . '_' . SESSION_NAME_COMPLEMENT]['UTILLOGIN']) && isset($_SESSION[SS_URL . '_' . SESSION_NAME_COMPLEMENT]['SECUREPASSWD']))
			return array("UsernameToken"=>getTokenHeader($_SESSION[SS_URL . '_' . SESSION_NAME_COMPLEMENT]['UTILLOGIN']));
		return false;
		*/

		//il faut retourner les paramètres pour la connexion
		$clGetTokenSession = new GetTokenSession();
		$clGetTokenSession->DefaultClientLanguageCode=12;
		switch($nErreur)
		{
			default:
			case 0:
				$clGetTokenSession->UsernameToken = new UsernameToken('superviseur', '');
				break;
			case 1:
				$clGetTokenSession->UsernameToken = new UsernameToken('superviseure', '');
				break;
			case 2:
				$clGetTokenSession->UsernameToken = new UsernameToken('superviseur', 'aze');
				break;
		}
		$clGetTokenSession->ExtranetUser = null;

		return $clGetTokenSession;
	}

	public function getUsernameToken()
	{
		return new UsernameToken('superviseur', '');
	}


} 