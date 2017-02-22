<?php

/**
 * Created by PhpStorm.
 * User: simon
 * Date: 21/02/2017
 * Time: 10:31
 */

namespace NOUT\Bundle\WebSiteBundle\NOUTException;

use Symfony\Component\Translation\Translator;

class NOUTExceptionFormatter
{
    /** @var string $level */
    protected $level;

    /** @var  string $message */
    protected $message;

    /** @var  int $code */
    protected $code;

    /** @var \Exception $previous */
    protected $previous;

    /**
     * NOUTExceptionFormatter constructor.
     * @param string $message
     * @param \Exception $e
     * @param int $level
     */
    public function __construct($message, $e, $level = NOUTExceptionLevel::ERROR_LEVEL)
    {
        try{
            $this->level = NOUTExceptionLevel::toString($level);
            $this->message = $message;
            $this->code = $e->getCode();
            $this->exception = $e;
        }
        catch (\InvalidArgumentException $e)
        {
            $e = new NOUTExceptionFormatter("Unable to format exception", $e, NOUTExceptionLevel::ERROR_LEVEL);
            return $e->toArray();
        }
    }

    /**
     * @param Translator $translator
     */
    public function translate($translator)
    {
        $this->message = $translator->trans('messages.' . $this->message, array(), 'exceptions');
        $this->level = $translator->trans('levels.' . $this->level, array(), 'exceptions');
    }

    /**
     * @return string[]
     */
    public function toArray()
    {
        $aException = array();
        $aException['message']  = $this->message;
        $aException['stacktrace'] = $this->exception->__toString();
        $aException['level']    = $this->level;
        $aException['file']     = $this->exception->getFile();
        $aException['line']     = $this->exception->getLine();
        $aException['code']     = $this->code;
        return $aException;
    }
}