<?php

/**
 * Created by PhpStorm.
 * User: simon
 * Date: 21/02/2017
 * Time: 10:31
 */

namespace NOUT\Bundle\WebSiteBundle\NOUTException;

use NOUT\Bundle\NOUTOnlineBundle\SOAP\SOAPException;
use Symfony\Component\Translation\Translator;

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
        $aException['details']  = $this->getNOUTOnlineMessage($this->exception);
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

    const LEVEL_NOTICE = 0;
    const LEVEL_WARNING = 1;
    const LEVEL_ERROR = 2;
}