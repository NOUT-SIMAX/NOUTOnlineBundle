<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 29/09/2017
 * Time: 16:45
 */

namespace NOUT\Bundle\NOUTOnlineBundle\NOUTException;
use Symfony\Component\HttpFoundation\Response;

class NOUTUserErrorException extends NOUTWebException
{
    const STATUS    = Response::HTTP_UNPROCESSABLE_ENTITY;
    const LEVEL     = NOUTExceptionLevel::WARNING;
}