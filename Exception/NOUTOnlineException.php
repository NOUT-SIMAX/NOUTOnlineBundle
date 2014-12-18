<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 23/07/14
 * Time: 14:40
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Exception;

use NOUT\Bundle\NOUTOnlineBundle\SOAP\SOAPException;

class NOUTOnlineException extends SOAPException
{
	public function __construct($fault, $message = "", $code = 0, \Exception $previous = null)
	{
		parent::__construct($code.' '.$message, $code, $previous);


		/*
		 *
		 * //on affiche courament que le dernier message avec un systeme
		//pour avoir plus de détail. sauf dans les cas de deco
		if($strError != '')
		{
			if($iErrorNumber != ERROR_DECO_FROM_SERVER  && $iErrorNumber != ERROR_NOT_LOGGUED)
			{
				if($sDetailError != '')
				{
					$sDetailError = $strError . '<br /><br />' . $sDetailError;
				}
				else
				{
					$sDetailError = $strError;
				}
			}
		}
		//on ne change pas le message en cas d'un message de deco.
		if($iErrorNumber != ERROR_DECO_FROM_SERVER  && $iErrorNumber != ERROR_NOT_LOGGUED)
			$strError=$str;


		if( $i != -1 )
			$iErrorNumber = $i;
		 *
		 *
		 */
	}
}
