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

class ConnectionManager
{
    /**
     * @var string
     */
    protected $m_sModeAuth;

    /**
     * @var string
     */
    protected $m_sSecret;

	public function __construct(array $aAuth)
	{
        $this->m_sModeAuth = $aAuth['mode'];
        $this->m_sSecret = $aAuth['secret'];
	}

	/**
	 * Génère les paramètres pour la méthode GetTokenSession
	 * @return GetTokenSession
	 */
	public function getGetTokenSession($nErreur = 0)
	{
		//il faut retourner les paramètres pour la connexion
		$clGetTokenSession = new GetTokenSession();
		switch ($nErreur)
		{
		default:
		case 0:
			$clGetTokenSession->UsernameToken = new UsernameToken('superviseur', '', $this->m_sModeAuth, $this->m_sSecret);
			break;
		case 1:
			$clGetTokenSession->UsernameToken = new UsernameToken('superviseureeeeeeeee', '', $this->m_sModeAuth, $this->m_sSecret);
			break;
		case 2:
			$clGetTokenSession->UsernameToken = new UsernameToken('superviseur', 'aze', $this->m_sModeAuth, $this->m_sSecret);
			break;
		}

		return $clGetTokenSession;
	}

	public function getUsernameToken()
	{
		return new UsernameToken('superviseur', '', $this->m_sModeAuth, $this->m_sSecret);
	}
}
