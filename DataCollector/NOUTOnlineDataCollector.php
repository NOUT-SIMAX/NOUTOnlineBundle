<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 25/07/14
 * Time: 09:27
 */

namespace NOUT\Bundle\NOUTOnlineBundle\DataCollector;

use NOUT\Bundle\NOUTOnlineBundle\Entity\NOUTOnlineVersion;
use NOUT\Bundle\NOUTOnlineBundle\Security\Authentication\Token\NOUTToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class NOUTOnlineDataCollector  extends DataCollector
{
	private $m_clLogger;
	private $m_clTokenStorage;

	public function getName()
	{
		return 'NOUTOnline';
	}

	public function __construct(NOUTOnlineLogger $clLogger, TokenStorageInterface $tokenStorage = null)
	{
		$this->m_clLogger          = $clLogger;
		$this->m_clTokenStorage    = $tokenStorage;
	}

    public function reset()
    {

    }
	public function collect(Request $request, Response $response, \Throwable $exception = null)
	{
		$queries = $this->m_clLogger->m_TabQueries;

		$this->data = array(
			'queries'     => $queries,
		);


        $this->data['authenticated'] = false;
        $this->data['session_token'] = '';
        $this->data['time_zone']     = '';
        $this->data['user']          = '';
        $this->data['superviseur']   = false;
        $this->data['ip']			 = '';
        $this->data['version']		 = '';
        $this->data['extranet']      = false;

		if (!is_null($this->m_clTokenStorage) && !is_null($token = $this->m_clTokenStorage->getToken()))
		{
			$this->data['authenticated'] = $token->isAuthenticated();
            $this->data['user']          = $token->getUsername();

			$tabRole = $token->getRoleNames();
			$this->data['superviseur'] = in_array('ROLE_SUPERVISEUR', $tabRole);

			if ($token instanceof NOUTToken)
			{
			    $oExtranetUsernameToken = $token->getExtranetUsernameToken();

                $this->data['extranet']      = $oExtranetUsernameToken ? $oExtranetUsernameToken->Username : '';
				$this->data['session_token'] = $token->getSessionToken();
				$this->data['time_zone']     = $token->getTimeZone();
				$this->data['ip']            = $token->getIP();
                $this->data['version']       = $token->getVersionNO();
			}
		}
	}

	/**
	 * @return int
	 */
	public function getQueryCount()
	{
		return count($this->data['queries']);
	}

	/**
	 * @return array
	 */
	public function getQueries()
	{
		return $this->data['queries'];
	}

	/**
	 * @return int
	 */
	public function getTime()
	{
		$time = 0;
		foreach ($this->data['queries'] as $query)
		{
			$time += $query['executionMS'];
		}

		return $time;
	}

	/**
	 * @return bool
	 */
	public function getSuperviseur()
	{
		return $this->data['superviseur'];
	}

    /**
     * @return bool
     */
    public function getExtranet()
    {
        return $this->data['extranet'];
    }

    /**
     * @return bool
     */
    public function getVersion()
    {
        if ($this->data['version'] instanceof NOUTOnlineVersion){
            return $this->data['version']->get();
        }
        return $this->data['version'];
    }


	/**
	 * Checks if the user is authenticated or not.
	 *
	 * @return bool    true if the user is authenticated, false otherwise
	 */
	public function isAuthenticated()
	{
		return $this->data['authenticated'];
	}

	/**
	 * Gets the user.
	 *
	 * @return string The user
	 */
	public function getUser()
	{
		return $this->data['user'];
	}

	/**
	 * Gets the client IP.
	 *
	 * @return string the client IP
	 */
	public function getIP()
	{
		return $this->data['ip'];
	}
	/**
	 * get the token of the session
	 *
	 * @return string
	 */
	public function getSessionToken()
	{
		return $this->data['session_token'];
	}

	/**
	 * get the time zone
	 * @return string
	 */
	public function getTimeZone()
	{
		return $this->data['time_zone'];
	}
}
