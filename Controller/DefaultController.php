<?php

namespace NOUT\Bundle\SessionManagerBundle\Controller;

use NOUT\Bundle\NOUTOnlineBundle\Entity\ConfigurationDialogue;
use NOUT\Bundle\NOUTOnlineBundle\Entity\OASIS\UsernameToken;
use NOUT\Bundle\NOUTOnlineBundle\REST\OnlineServiceProxy as RESTProxy;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\OnlineServiceProxy as SOAPProxy;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ResetPasswordFailed;
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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;


class DefaultController extends Controller
{

	/**
	 * @return RESTProxy;
	 */
	protected function _clGetRESTProxy()
	{
		$clServiceFactory = $this->get('nout_online.service_factory');
		$clConfiguration = $this->get('nout_online.configuration_dialogue');

		return $clServiceFactory->clGetRESTProxy($clConfiguration);
	}

    /**
     * @return SOAPProxy;
     */
    protected function _clGetSOAPProxy()
    {
        $clServiceFactory = $this->get('nout_online.service_factory');
        $clConfiguration = $this->get('nout_online.configuration_dialogue');

        return $clServiceFactory->clGetSoapProxy($clConfiguration);
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
     * @Route("/login/",
     *     name="login",
     *     options={"expose"=true}
     * )
     */
    public function loginAction(Request $request)
    {
        $map = array(
            '12'    => array('code' => 12,  'lang' => 'Français',  'short' => 'fr'),
            '10'    => array('code' => 10,  'lang' => 'Español',   'short' => 'es'),
            '9'     => array('code' => 9,   'lang' => 'English',   'short' => 'en'),
            '7'     => array('code' => 7,   'lang' => 'Deutsh',    'short' => 'de'),
        );
        /** @var array $languages */
        try{
            $languages = (array) $this->_clGetSOAPProxy()->getLanguages(array())->getNodeXML()->LanguageCode;
        }
        catch (\Exception $e)
        {
            $languages = array();
        }
        $languages = array_map(function($language) use($map) {return $map[$language];}, $languages);
        $bSelectLanguage=count($languages)>1;

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
            'version_min'               => $this->getParameter('nout_online.version_min'),
            'display_version'           => $this->getParameter('nout_web_site.display_version'),
            'available_languages'       => $languages,
            'select_language'           => $bSelectLanguage,
	    ));

    }

	/**
	 *  Route de génération du formulaire de connexion
	 *
	 * @Route("/",
     *     name="session_index"
     * )
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

    /**
     * Route de génération du formulaire de connexion
     * Comme c'est pour l'identification, on n'utilise pas de formBuilder, uniquement un template twig avec le formulaire dedans
     *
     * @Route("/reset_password/",
     *     name="reset_password",
     *     options={"expose"=true}
     * )
     */
    public function resetPasswordAction(Request $request)
    {
        $param = new ResetPasswordFailed();
        $param->Login = $request->get('login');

        /** @var array $languages */
        try{
            /** @var XMLResponseWS $ret */
            $ret = $this->_clGetSOAPProxy()->resetPasswordFailed($param);
        }
        catch (\Exception $e)
        {
            return new JsonResponse(array('message' => $e->getMessage()), 500);
        }

        if ($ret->bIsFault()){
            //erreur
            return new JsonResponse($ret->getTabError(), 500);
        }

        return new JsonResponse(array('message' => $ret->sGetReport()));

    }

}
