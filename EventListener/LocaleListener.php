<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 24/08/2015
 * Time: 10:36
 */

namespace NOUT\Bundle\SessionManagerBundle\EventListener;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class LocaleListener implements EventSubscriberInterface
{
    private $m_sDefaultLocale;

    public function __construct($defaultLocale)
    {
        $this->m_sDefaultLocale = $defaultLocale;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if (!$request->hasPreviousSession()) {
            return;
        }

        // on essaie de voir si la locale a été fixée dans le paramètre de routing _locale
        if ($locale = $request->attributes->get('_locale')) {
            $request->getSession()->set('_locale', $locale);
        } else {
            // si aucune locale n'a été fixée explicitement dans la requête, on utilise celle de la session
            $request->setLocale($request->getSession()->get('_locale', $this->m_sDefaultLocale));
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            // doit être enregistré avant le Locale listener par défaut
            KernelEvents::REQUEST => array(array('onKernelRequest', 17)),
        );
    }
}