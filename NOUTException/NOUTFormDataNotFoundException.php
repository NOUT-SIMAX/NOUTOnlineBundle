<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 10/10/2017
 * Time: 14:29
 */

namespace NOUT\Bundle\NOUTOnlineBundle\NOUTException;


use Symfony\Component\HttpFoundation\Response;

class NOUTFormDataNotFoundException extends NOUTWebException
{
    const STATUS    = Response::HTTP_INTERNAL_SERVER_ERROR;
    const LEVEL     = NOUTExceptionLevel::ERROR;
}