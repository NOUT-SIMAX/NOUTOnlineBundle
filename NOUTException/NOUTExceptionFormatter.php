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
    public function __construct($message, $e, $level = self::LEVEL_ERROR)
    {
        $level = $level ? $level : NOUTExceptionFormatter::LEVEL_ERROR;
        try{
            $this->level = self::levelToString($level);
            $this->message = $message;
            $this->code = $e->getCode();
            $this->exception = $e;
        }
        catch (\InvalidArgumentException $e)
        {
            $this->level = self::LEVEL_ERROR;
            $this->message = "Unable to format exception";
            $this->exception = $e;
            $this->code = 0;
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

    /**
     * @var int level
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function levelToString($level)
    {
        $aValues = array(
            self::LEVEL_NOTICE      => 'Notice',
            self::LEVEL_WARNING    => 'Warning',
            self::LEVEL_ERROR       => 'Error',
        );

        if (!array_key_exists($level, $aValues))
            throw new \InvalidArgumentException('Unknown error level');

        return $aValues[$level];
    }

    const LEVEL_NOTICE = 0;
    const LEVEL_WARNING = 1;
    const LEVEL_ERROR = 2;
}