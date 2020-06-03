<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 04/08/2015
 * Time: 16:02
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Service;


use NOUT\Bundle\NOUTOnlineBundle\Cache\NOUTCacheFactory;
use NOUT\Bundle\NOUTOnlineBundle\Cache\NOUTCacheProvider;
use Symfony\Component\EventDispatcher\GenericEvent;

class SessionDisconnectListener
{
    /**
     * @var NOUTCacheFactory
     */
    private $m_clCacheFactory;

    public function __construct(NOUTCacheFactory $cacheFactory)
    {
        $this->m_clCacheFactory = $cacheFactory;
    }

    public function disconnect(GenericEvent $event)
    {
        $clCache = $this->m_clCacheFactory->getCache($event->getSubject(), NOUTClientCache::SOUSREPCACHE_SESSION, NOUTClientCache::REPCACHE);
        $clCache->deletePrefix();
    }
}