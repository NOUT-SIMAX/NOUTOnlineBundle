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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Security\Core\Security;

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
			switch($exception->getCode())
			{
				case OnlineError::ERR_WS_UTIL_DECONNECTE:
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
						$session->set(Security::AUTHENTICATION_ERROR, array('message'=>$exception->getMessage()));

						//c'est l'erreur utilisateur déconnecté, il faut redirigé sur la page de login
						$event->setResponse(new RedirectResponse($this->_router->generate('login', array())));
					}
					break;
				}
				case OnlineError::ERR_NOUTONLINE_OFF:
				{
					$request = $event->getRequest();
					$session = $request->getSession();
					if ($request->isXmlHttpRequest())
					{
                        //si la requête est une requête ajax, on retourne l'erreur en json
                        $ret = new \stdClass();

                        $ret->idIHM           = '';
                        $ret->idCtxtOrig      = '';
                        $ret->idCtxtCur       = '';
                        $ret->html            = '';
                        $ret->title           = '';
                        $ret->elementID       = '';
                        $ret->elementTitle    = '';
                        $ret->isReadOnly      = false;

                        $ret->debug            = false;
                        $ret->ReturnType       = 'Error'; // Le type intercepté dans context-model.js

                        $ret->data             = new \stdClass();
                        $ret->data->code       = $exception->getCode();
                        $ret->data->message    = $exception->getMessageOrigine();

						$event->setResponse(new JsonResponse($ret, 500));
					}
					else
					{
                        // Ajout du message propre à l'erreur
						$session->set(Security::AUTHENTICATION_ERROR, array('message'=>$exception->getMessageOrigine()));

						// On redirige sur la page de login
						$event->setResponse(new RedirectResponse($this->_router->generate('login', array())));
					}
					break;
				}
				default:
				{
					// Toutes les autres erreurs
				}
			}
		}
	}
} 