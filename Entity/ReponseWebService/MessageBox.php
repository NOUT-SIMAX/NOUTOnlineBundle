<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 06/08/14
 * Time: 14:38
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService;

class MessageBox
{

	public $m_sMessage;
	public $m_TabButton;

	public function __construct(\SimpleXMLElement $clXML)
	{
		$this->m_sMessage = $clXML->children()->MessageBox->Children()->Message;

		foreach ($clXML->children()->MessageBox->Children()->ButtonList as $ndTypeConfirmation)
		{
			$this->m_TabButton[(int) $ndTypeConfirmation] = (string) $ndTypeConfirmation->attributes()['title'];
		}

		/*
		 <xml>
<MessageBox>
<Message> - Formulaire avec liste images : test delete

Confirmez-vous cette action ?</Message>
<ButtonList>
<TypeConfirmation title ="OK" >1</TypeConfirmation >
<TypeConfirmation title ="Annuler" >2</TypeConfirmation >
</ButtonList>
</MessageBox>
</xml>
		 */
	}


	const IDOK          = 1;    //The OK button was selected.
	const IDCANCEL      = 2;    //The Cancel button was selected.
	const IDABORT       = 3;    //The Abort button was selected.
	const IDRETRY       = 4;    //The Retry button was selected.
	const IDIGNORE      = 5;    //The Ignore button was selected.
	const IDYES         = 6;    //The Yes button was selected.
	const IDNO          = 7;    //The No button was selected.
	const IDCONTINUE    = 11;   //The Continue button was selected.
	const IDTRYAGAIN    = 10;   //The Try Again button was selected.
}
