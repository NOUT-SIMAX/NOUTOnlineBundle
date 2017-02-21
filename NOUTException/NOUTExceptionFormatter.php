<?php

/**
 * Created by PhpStorm.
 * User: simon
 * Date: 21/02/2017
 * Time: 10:31
 */

namespace NOUT\Bundle\WebSiteBundle\NOUTException;

class NOUTExceptionFormatter
{
    /** @var int $level */
    protected $level;

    /** @var  string $message */
    protected $message;

    /** @var string $trace */
    protected $trace;

    /** @var  int $code */
    protected $code;

    /**
     * NOUTExceptionFormatter constructor.
     * @param string $message
     * @param \Exception $e
     * @param int $level
     */
    public function __construct($message, $e, $level = NOUTExceptionLevel::ERROR_LEVEL)
    {
        $this->level = $level;
        $this->message = $message;
        $this->trace = $e->getTraceAsString();
        $this->code = $e->getCode();
    }

    public function toArray()
    {
        try
        {
            $aException = array();
            $aException['message']  = $this->message;
            $aException['level']    = NOUTExceptionLevel::toString($this->level);
            $aException['trace']    = $this->trace;
            $aException['code']     = $this->code;
        }
        catch (InvalidArgumentException $e)
        {
            $e = new NOUTExceptionFormatter("Unable to format exception", $e, NOUTExceptionLevel::ERROR_LEVEL);
            return $e->toArray();
        }
    }
}