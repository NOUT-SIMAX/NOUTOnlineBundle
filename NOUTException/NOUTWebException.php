<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 02/03/2017
 * Time: 10:07
 */

namespace NOUT\Bundle\NOUTOnlineBundle\NOUTException;

use NOUT\Bundle\NOUTOnlineBundle\SOAP\SOAPException;
use phpDocumentor\Reflection\Types\Self_;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class NOUTException
 * @package NOUT\Bundle\WebSiteBundle\NOUTException
 * custom NOUT Exception parent class
 */
abstract class NOUTWebException extends SOAPException implements NOUTExceptionInterface
{
    const STATUS = Response::HTTP_INTERNAL_SERVER_ERROR;
    const LEVEL = NOUTExceptionLevel::ERROR;

    public static function getDefaultStatus(){
        return self::STATUS;
    }

    public static function getDefaultLevel(){
        return self::LEVEL;
    }

    public function getStatus(){
        return $this::STATUS;
    }

    public function getLevel(){
        return $this::LEVEL;
    }
}