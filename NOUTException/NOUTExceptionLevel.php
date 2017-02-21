<?php

/**
 * Created by PhpStorm.
 * User: simon
 * Date: 21/02/2017
 * Time: 10:42
 */

namespace NOUT\Bundle\WebSiteBundle\NOUTException;

use Symfony\Component\Translation\Translator;

abstract class NOUTExceptionLevel
{
    const NOTICE_LEVEL = 0;
    const WARNGING_LEVEL = 1;
    const ERROR_LEVEL = 2;

    /**
     * @var int level
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function toString($level)
    {
        $aValues = array(
            self::NOTICE_LEVEL      => 'Notice',
            self::WARNGING_LEVEL    => 'Warning',
            self::ERROR_LEVEL       => 'Error',
        );

        if (!array_key_exists($level, $aValues))
            throw new \InvalidArgumentException('Unknown error level');

        return $aValues[$level];
    }
}