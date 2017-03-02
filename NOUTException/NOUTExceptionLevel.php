<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 02/03/2017
 * Time: 09:59
 */

namespace NOUT\Bundle\WebSiteBundle\NOUTException;


abstract class NOUTExceptionLevel
{
    const NOTICE    = 0;
    const WARNING   = 1;
    const ERROR     = 2;

    /**
     * @var int level
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function toString($level)
    {
        $aValues = array(
            self::NOTICE    => 'Notice',
            self::WARNING   => 'Warning',
            self::ERROR     => 'Error',
        );

        if (!array_key_exists($level, $aValues))
            throw new \InvalidArgumentException('Unknown exception level');

        return $aValues[$level];
    }
}