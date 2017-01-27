<?php

namespace NOUT\Bundle\SessionManagerBundle\Controller;

use NOUT\Bundle\NOUTOnlineBundle\Entity\ConfigurationDialogue;
use NOUT\Bundle\NOUTOnlineBundle\Entity\OASIS\UsernameToken;
use NOUT\Bundle\NOUTOnlineBundle\REST\OnlineServiceProxy;
use NOUT\Bundle\SessionManagerBundle\Entity\TimeZone;
use NOUT\Bundle\SessionManagerBundle\Entity\User;
use NOUT\Bundle\SessionManagerBundle\Security\Authentication\Provider\NOUTToken;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\GetTokenSession;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\SOAPException;
use NOUT\Bundle\SessionManagerBundle\Entity\ConnectionInfos;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;


class DefaultController extends Controller
{

	/**
	 * @return OnlineServiceProxy;
	 */
	protected function _clGetRESTProxy()
	{
		$clServiceFactory = $this->get('nout_online.service_factory');
		$clConfiguration = $this->get('nout_online.configuration_dialogue');

		return $clServiceFactory->clGetRESTProxy($clConfiguration);
	}


	protected function _aGetTabTimezone()
	{
		$script_tz = date_default_timezone_get();

		$zones_array = array();
		$timestamp = time();
		foreach(timezone_identifiers_list() as $key => $zone)
		{
			date_default_timezone_set($zone);
			$zones_array[$key]['zone'] = $zone;
			$zones_array[$key]['diff_from_GMT'] = date('P', $timestamp); //'UTC/GMT ' .
		}

		date_default_timezone_set($script_tz);


		usort($zones_array, function($a, $b)
		{
			$diff_a = (int)$a['diff_from_GMT'];
			$diff_b = (int)$b['diff_from_GMT'];

			if ($diff_a < $diff_b)
				return -1;
			else if ($diff_a > $diff_b)
				return 1;

			$zone_a = $a['zone'];
			$zone_b = $b['zone'];

			return strcmp($zone_a, $zone_b);
		});


		return $zones_array;
	}

	/**
	 * @param $sNameSpace, le namespace
	 * @param $sFile, le chemin du fichier dans le namespace sans l'extension
	 * @return string
	 */
	protected function _sGetTemplate($sFile, $sNameSpace='')
	{
		if (empty($sNameSpace))
			$sNameSpace='NOUTSessionManagerBundle';

		return "$sNameSpace:Default:$sFile.html.twig";
	}

    /**
     * Route de génération du formulaire de connexion
     * Comme c'est pour l'identification, on n'utilise pas de formBuilder, uniquement un template twig avec le formulaire dedans
     *
     * @Route("/login/", name="login")
     */
    public function loginAction(Request $request)
    {
	    $session = $request->getSession();

	    // get the login error if there is one
	    if ($request->attributes->has(Security::AUTHENTICATION_ERROR))
	    {
		    $error = $request->attributes->get(Security::AUTHENTICATION_ERROR);
	    }
	    else
	    {
		    $error = $session->get(Security::AUTHENTICATION_ERROR);
		    $session->remove(Security::AUTHENTICATION_ERROR);
	    }

	    return $this->render($this->_sGetTemplate('Security/index'), array(
		    // last username entered by the user
		    'last_username'             => $session->get(Security::LAST_USERNAME),
		    'error'                     => $error,
		    'timezone_list'             => TimeZone::s_aGetTabTimezone(),
		    'last_timezone'             => $session->get(NOUTToken::SESSION_LastTimeZone),
            'customization'             => $this->getParameter('nout_session_manager.customization'),
            'extranet'                  => $this->getParameter('nout_online.extranet')['actif'],
	    ));

    }

	/**
	 *  Route de génération du formulaire de connexion
	 *
	 * @Route("/", name="session_index")
	 */
	public function indexAction()
	{
		$oToken = $this->get('security.token_storage')->getToken();
		$oUser = $oToken->getUser();

		//page d'index
		return $this->render(
			'NOUTSessionManagerBundle:Default:index.html.twig',
			array(
                'username'          => $oUser->getUsername(),
                'tokensession'      => $oToken->getSessionToken(),
            )
		);
	}
}
