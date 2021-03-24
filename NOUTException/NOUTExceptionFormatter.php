<?php

/**
 * Created by PhpStorm.
 * User: simon
 * Date: 21/02/2017
 * Time: 10:31
 */

namespace NOUT\Bundle\NOUTOnlineBundle\NOUTException;

use NOUT\Bundle\NOUTOnlineBundle\SOAP\SOAPException;
use Symfony\Contracts\Translation\TranslatorInterface;

class NOUTExceptionFormatter
{
    /** @var string $level */
    protected $level;

    /** @var  string $message */
    protected $message;

    /** @var  int $code */
    protected $code;

    /**
     * NOUTExceptionFormatter constructor.
     * @param string $message
     * @param \Exception $e
     * @param int|null $level
     */
    public function __construct($message, \Exception $e, $level = null)
    {
        $level = (!is_null($level) ? $level : NOUTWebException::getDefaultLevel());
        try{
            $this->level = NOUTExceptionLevel::toString($level);
            $this->message = $message;
            $this->code = $e->getCode();
            $this->exception = $e;
        }
        catch (\InvalidArgumentException $e)
        {
            $this->level = NOUTExceptionLevel::ERROR;
            $this->message = "Unable to format exception";
            $this->exception = $e;
            $this->code = 0;
        }
    }

    /**
     * @param TranslatorInterface $translator
     */
    public function translate($translator)
    {
        if('messages.' . $this->message != $translator->trans('messages.' . $this->message, array(), 'exceptions')) {
            $this->message = $translator->trans('messages.' . $this->message, array(), 'exceptions');
        }
        if('levels.' . $this->level != $translator->trans('levels.' . $this->level, array(), 'exceptions')) {
            $this->level = $translator->trans('levels.' . $this->level, array(), 'exceptions');
        }
    }

    /**
     * @return string[]
     */
    public function toArray()
    {
        $aException = array();
        $aException['message']  = $this->message;
        $aException['stacktrace'] = $this->exception->__toString();//$this->exception->getTraceAsString();
        $aException['level']    = $this->level;
        $aException['file']     = $this->exception->getFile();
        $aException['line']     = $this->exception->getLine();
        $aException['code']     = $this->code;
        $aException['details']  = $this->getNOUTOnlineMessage($this->exception);

        if ($this->exception instanceof NOUTValidateErrorException){
            $aException['validate_error']  = $this->exception->getValidateError();
        }
        elseif($this->exception->getPrevious() instanceof NOUTValidateErrorException) {
            $aException['validate_error']  = $this->exception->getPrevious()->getValidateError();
        }


        return $aException;
    }

    /**
     * Look for a NOUTOnline message in stack trace, returns an empty string if not found
     * @param \Exception $e
     * @return string
     */
    protected function getNOUTOnlineMessage(\Exception $e){
        if($e instanceof SOAPException)
            return $e->getMessageOrigine();
        if(!is_null($e->getPrevious()))
            return $this->getNOUTOnlineMessage($e->getPrevious());
        return '';
    }
}