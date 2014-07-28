<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 23/07/14
 * Time: 14:56
 */

namespace NOUT\Bundle\NOUTOnlineBundle\SOAP;


class SOAPException extends \Exception {

	public  function __construct($message = "", $code = 0, Exception $previous = null)
	{
		parent::__construct($code.' '.$message, $code, $previous);
	}
} 