<?php


namespace NOUT\Bundle\NOUTOnlineBundle\NOUTException;


use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\OnlineError;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\ValidateError;

class NOUTValidateErrorException extends NOUTValidationException
{
    /** @var ValidateError  */
    protected $validateError;

    public function __construct(ValidateError $validateError)
    {
        parent::__construct($validateError->getMessage(), OnlineError::ERR_VALIDATE_ERROR);
        $this->validateError = $validateError;
    }

    /**
     * @return ValidateError
     */
    public function getValidateError(): ValidateError
    {
        return $this->validateError;
    }
}