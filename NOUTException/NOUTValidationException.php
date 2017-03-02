<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 02/03/2017
 * Time: 09:34
 */

namespace NOUT\Bundle\WebSiteBundle\NOUTException;
use Symfony\Component\HttpFoundation\Response;

class NOUTValidationException extends NOUTException
{
    /**
     * @inheritdoc
     */
    public function getLevel()
    {
        return NOUTExceptionLevel::WARNING;
    }

    public function getStatus()
    {
        return Response::HTTP_UNPROCESSABLE_ENTITY;
    }
}