<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 31/07/14
 * Time: 16:08
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Header;

use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\OptionDialogue as WSDLOptionDialogue;

class OptionDialogue extends WSDLOptionDialogue
{

	public function __construct()
	{
		$this->Readable=0; // integer TOUJOURS laisser 0, on considère que c'est toujours non lisible dans le bundle
		$this->EncodingOutput=0; // integer
		$this->ReturnValue=1; // integer
		$this->ReturnXSD=1; // integer
		$this->HTTPForceReturn=0; // integer
		$this->Ghost=0; // integer
		$this->DefaultPagination=20; // integer
		$this->DisplayValue=0xffffffff; // integer
		$this->LanguageCode=12; // integer
		$this->WithFieldStateControl=1; // integer
		$this->ListContentAsync=0; // integer
	}
}



