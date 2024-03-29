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
    private NOUTOnlineLogger $clLogger;
    private ?TokenStorageInterface $clTokenStorage;

    public function getName()
    {
        return 'NOUTOnline';
    }

    public function __construct(NOUTOnlineLogger $clLogger, TokenStorageInterface $tokenStorage = null)
    {
        $this->clLogger       = $clLogger;
        $this->clTokenStorage = $tokenStorage;
    }

    public function reset()
    {
        //c'est normal si c'est vide, pas de reset
    }
    public function collect(Request $request, Response $response, \Throwable $exception = null)
    {
        $queries = $this->clLogger->m_TabQueries;

        $this->data = array(
            'queries'     => $queries,
        );


        $this->data['authenticated'] = false;
        $this->data['session_token'] = '';
        $this->data['time_zone']     = '';
        $this->data['user']          = '';
        $this->data['superviseur']   = false;
        $this->data['ip']            = '';
        $this->data['version']       = '';
        $this->data['extranet']      = '';

        if ( !is_null($this->clTokenStorage) && !is_null($token = $this->clTokenStorage->getToken()))
        {
            $username = $token->getUsername();
            $this->data['user']          = $username;
            $this->data['authenticated'] = !is_null($username);

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
     * @noinspection PhpUnused
     */
    public function getQueryCount() : int
    {
        return count($this->data['queries']);
    }

    /**
     * @return array
     * @noinspection PhpUnused
     */
    public function getQueries() : array
    {
        return $this->data['queries'];
    }

    /**
     * @return int
     * @noinspection PhpUnused
     */
    public function getTime() : int
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
     * @noinspection PhpUnused
     */
    public function getSuperviseur() : bool
    {
        return $this->data['superviseur'];
    }

    /**
     * @return string
     * @noinspection PhpUnused
     */
    public function getExtranet() : string
    {
        return $this->data['extranet'];
    }

    /**
     * @return string
     */
    public function getVersion() : string
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
    public function isAuthenticated() : bool
    {
        return $this->data['authenticated'];
    }

    /**
     * Gets the user.
     *
     * @return string The user
     */
    public function getUser() : string
    {
        return $this->data['user'];
    }

    /**
     * Gets the client IP.
     *
     * @return string the client IP
     * @noinspection PhpUnused
     */
    public function getIP() : string
    {
        return $this->data['ip'];
    }
    /**
     * get the token of the session
     *
     * @return string
     * @noinspection PhpUnused
     */
    public function getSessionToken() : string
    {
        return $this->data['session_token'];
    }

    /**
     * get the time zone
     * @return string
     */
    public function getTimeZone() : string
    {
        return $this->data['time_zone'];
    }
}
