<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 04/08/2015
 * Time: 16:02
 */

namespace NOUT\Bundle\ContextsBundle\Service;


use NOUT\Bundle\NOUTOnlineBundle\Cache\NOUTCacheProvider;
use Symfony\Component\EventDispatcher\GenericEvent;

class SessionDisconnectListener
{
    /**
     * @var string
     */
    private $m_sCacheDir;

    public function __construct($cachedir)
    {
        $this->m_sCacheDir = $cachedir;
    }

    public function disconnect(GenericEvent $event)
    {
        $clCache = NOUTCacheProvider::initCache($event->getSubject(), NOUTClientCache::SOUSREPCACHE_SESSION, $this->m_sCacheDir.'/'.NOUTClientCache::REPCACHE);
        $clCache->flushAll();
    }
}