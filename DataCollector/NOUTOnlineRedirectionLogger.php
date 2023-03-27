<?php
/**
 * Created by PhpStorm
 * User: ninon
 * Date: 27/03/2023 10:33
 */

namespace NOUT\Bundle\NOUTOnlineBundle\DataCollector;

use NOUT\Bundle\NOUTOnlineBundle\Service\DynamicConfigurationLoader;
use Psr\Log\LoggerInterface;

class NOUTOnlineRedirectionLogger extends NOUTOnlineLogger
{
    /**
     * @param LoggerInterface            $logger
     * @param DynamicConfigurationLoader $loader
     * @param bool                       $debug
     * @param array                      $redirConfig
     */
    public function __construct(
        LoggerInterface $logger,
        DynamicConfigurationLoader $loader,
        bool $debug,
        array $redirConfig
    )
    {
        parent::__construct($logger, $loader, $debug);
        $this->m_bEnabled  = $redirConfig['log'] || $debug;
    }
}
