<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 25/07/14
 * Time: 09:27
 */

namespace NOUT\Bundle\NOUTOnlineBundle\DataCollector;



use NOUT\Bundle\NOUTSessionManagerBundle\Security\Authentication\Provider\NOUTToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\Security\Core\SecurityContextInterface;

class NOUTOnlineDataCollector  extends DataCollector
{
	private $m_clLogger;
	private $m_clSecurityContext;

	public function getName()
	{
		return 'NOUTOnline';
	}

	public function __construct(NOUTOnlineLogger $clLogger, SecurityContextInterface $context = null)
	{
		$this->m_clLogger=$clLogger;
		$this->m_clSecurityContext=$context;
	}

	public function collect(Request $request, Response $response, \Exception $exception = null)
	{
		$queries = array();
		$queries = $this->m_clLogger->m_TabQueries;

		$this->data = array(
			'queries'     => $queries,
		);

		if (is_null($this->m_clSecurityContext) || is_null($token = $this->m_clSecurityContext->getToken()))
		{
			$this->data['authenticated'] = false;
			$this->data['session_token'] = '';
			$this->data['time_zone']='';
			$this->data['user'] = '';
			$this->data['superviseur']=false;
		}
		else
		{
			$this->data['authenticated'] = $token->isAuthenticated();
			$this->data['session_token'] = '';
			$this->data['time_zone']='';
			$this->data['user'] = $token->getUsername();

			$tabRole = array_map(function ($role) { return $role->getRole();}, $token->getRoles());
			$this->data['superviseur']=in_array('ROLE_SUPERVISEUR', $tabRole);

			if ($token instanceof NOUTToken)
			{
				$this->data['session_token']=$token->getSessionToken();
				$this->data['time_zone']=$token->getTimeZone();
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
		foreach ($this->data['queries'] as $query) {
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