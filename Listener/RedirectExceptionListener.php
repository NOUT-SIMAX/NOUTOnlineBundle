<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 02/12/14
 * Time: 14:43
 */

namespace NOUT\Bundle\SessionManagerBundle\Listener;


use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\OnlineError;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\SOAPException;
use NOUT\Bundle\SessionManagerBundle\Security\Authentication\Provider\NOUTToken;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Security\Core\SecurityContext;

class RedirectExceptionListener
{

	/**
	 * @var Router _router
	 */
	protected $_router;

	public function __construct(Router $router)
	{
		$this->_router = $router;
	}

	/**
	 * méthode pour attraper les exceptions
	 * @param GetResponseForExceptionEvent $event
	 */
	public function onKernelException(GetResponseForExceptionEvent $event)
	{
		// You get the exception object from the received event
		$exception = $event->getException();
		if ($exception instanceof SOAPException)
		{
			if ($exception->getCode() == OnlineError::ERR_UTIL_DECONNECTE)
			{
				$request = $event->getRequest();
				$session = $request->getSession();
				if ($request->isXmlHttpRequest())
				{
					//si la requête est une requête ajax, on retourne un 403
					//on passe en paramètre toutes les informations nécessaire pour la redirection vers la page de login
					$aParam = array('exception'=>json_encode(array('message'=>$exception->getMessageOrigine(), 'code'=>$exception->getCode())));

					$event->setResponse(new Response($this->_router->generate('forbidden', $aParam), 403));
				}
				else
				{
					$session->set(SecurityContext::AUTHENTICATION_ERROR, array('message'=>$exception->getMessage()));

					//c'est l'erreur utilisateur déconnecté, il faut redirigé sur la page de login
					$event->setResponse(new RedirectResponse($this->_router->generate('login', array())));
				}
			}
			else // Toutes les autres erreurs
			{
				// Pas le droit pour respecter la hiérarchie des bundles
			}
		}
	}
} 