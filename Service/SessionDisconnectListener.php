<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 04/08/2015
 * Time: 16:02
 */

namespace NOUT\Bundle\ContextsBundle\Service;


use Symfony\Component\EventDispatcher\GenericEvent;

class SessionDisconnectListener
{
    /**
     * @var NOUTClient
     */
    private $m_clNOUTClient;

    public function __construct(NOUTClient $client)
    {
        $this->m_clNOUTClient = $client;
    }

    public function disconnect(GenericEvent $event)
    {
        $clCache = $this->m_clNOUTClient->getCacheSession();
        if (isset($clCache)){
            $clCache->flushAll();
        }
    }
}