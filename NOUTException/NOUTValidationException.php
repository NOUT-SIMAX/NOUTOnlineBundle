<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 02/03/2017
 * Time: 09:34
 */

namespace NOUT\Bundle\WebSiteBundle\NOUTException;
use Symfony\Component\HttpFoundation\Response;

class NOUTValidationException extends NOUTWebException
{
    const STATUS    = Response::HTTP_UNPROCESSABLE_ENTITY;
    const LEVEL     = NOUTExceptionLevel::WARNING;
}