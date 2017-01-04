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
    /**
     * @var string
     */
    protected $m_sTitle='';
    /**
     * @var string
     */
    protected $m_sMessage;
    /**
     * @var array
     */
    protected $m_TabButton;

    /**
     * @var int
     */
	protected $m_nDefaultBtn=0;

	public function __construct(\SimpleXMLElement $clXML)
	{
	    /** @var \SimpleXMLElement $ndMessage */
	    $ndMessage = $clXML->children()->MessageBox->Children()->Message;
		$this->m_sMessage = (string)$ndMessage;

		$aMessageAttributes = $ndMessage->attributes();
		if (isset($aMessageAttributes['title']))
		{
		    $this->m_sTitle = (string)$aMessageAttributes['title'];
        }


        // Besoin d'aller chercher les boutons en profondeur
		foreach ($clXML->children()->MessageBox->Children()->ButtonList->Children()->TypeConfirmation as $noeudTypeConfirmation)
		{
			/** @var \SimpleXMLElement $noeudTypeConfirmation */
			$nTypeConfirmation = (int)$noeudTypeConfirmation;
			$aTabAttributes = $noeudTypeConfirmation->attributes();
			$sLibelle = (string)$aTabAttributes['title'];

			if (isset($aTabAttributes['default']))
            {
                $this->m_nDefaultBtn = $nTypeConfirmation;
            }

			$this->m_TabButton[$nTypeConfirmation] = $sLibelle;
		}

        // Code XML pour référence
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

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->m_sTitle;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->m_sMessage;
    }

    /**
     * @return array
     */
    public function getTabButton()
    {
        return $this->m_TabButton;
    }

    /**
     * @return int
     */
    public function getDefaultBtn()
    {
        return $this->m_nDefaultBtn;
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
