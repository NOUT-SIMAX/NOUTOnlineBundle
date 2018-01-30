<?php

namespace NOUT\Bundle\SessionManagerBundle\Listener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class AjaxAuthenticationListener {
    public function onCoreException(GetResponseForExceptionEvent $event) {
        $exception = $event->getException();
        $request = $event->getRequest();

        if($request->isXmlHttpRequest()) {
            if($exception instanceof AuthenticationException || $exception instanceof AccessDeniedException) {
                $event->setResponse(new Response('', 403));
            }
        }
    }
}