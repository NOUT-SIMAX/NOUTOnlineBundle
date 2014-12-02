<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 23/07/14
 * Time: 11:14
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Service;


use NOUT\Bundle\NOUTOnlineBundle\Entity\OASIS\UsernameToken;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\GetTokenSession;

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
		//il faut retourner les paramètres pour la connexion
		$clGetTokenSession = new GetTokenSession();
		switch($nErreur)
		{
			default:
			case 0:
				$clGetTokenSession->UsernameToken = new UsernameToken('superviseur', '');
				break;
			case 1:
				$clGetTokenSession->UsernameToken = new UsernameToken('superviseureeeeeeeee', '');
				break;
			case 2:
				$clGetTokenSession->UsernameToken = new UsernameToken('superviseur', 'aze');
				break;
		}
		return $clGetTokenSession;
	}

	public function getUsernameToken()
	{
		return new UsernameToken('superviseur', '');
	}


} 