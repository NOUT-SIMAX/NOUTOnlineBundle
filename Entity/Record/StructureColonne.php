<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 04/08/14
 * Time: 11:00
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Record;


class StructureColonne
{
	const TM_Invalide   = null;

	//type simple
	const TM_Booleen    = 'xs:boolean';
	const TM_Entier     = 'xs:integer';
	const TM_Texte      = 'xs:string';
	const TM_DateHeure  = 'xs:dateTime';
	const TM_Date       = 'xs:date';
	const TM_Heure      = 'xs:time';
	const TM_Reel       = 'xs:float';
	const TM_Monetaire  = 'xs:decimal';

	//type complexe
	const TM_Tableau    = 'simax-element';
	const TM_ListeElem  = 'simax-list';
	const TM_Separateur = 'simax-section';
	const TM_Bouton     = 'simax-button';
	const TM_Combo      = 'simax-choice';
	const TM_Fichier    = 'xs:base64Binary';


	public $m_nIDColonne;
	public $m_sLibelle;
	public $m_eTypeElement;
	public $m_clRestriction;

	public $m_bPrinted;
	public $m_bReadonly;
	public $m_bComputed;
	public $m_bSort;


	public $m_bLink;
	public $m_sLinkedTableXml;
	public $m_sLinkedTableID;


	public $m_TabStructureColonne;


	public function __construct($sID, \SimpleXMLElement $clAttribNOUT)
	{
		$this->m_TabStructureColonne = array();

		$this->m_nIDColonne = $sID;
		$this->m_sLibelle = '';
		$this->m_eTypeElement =  '';
		$this->m_clRestriction = null;
		$this->m_bPrinted=0;
		$this->m_bReadonly=0;
		$this->m_bComputed=0;
		$this->m_bSort=0;
		$this->m_bLink=0;
		$this->m_sLinkedTableXml='';
		$this->m_sLinkedTableID='';

		$this->InitInfoColonne($clAttribNOUT);
	}

	public function InitInfoColonne(\SimpleXMLElement $clAttribNOUT)
	{
		foreach($clAttribNOUT as $sAttribName => $ndAttrib)
		{
			switch($sAttribName)
			{
				case 'name':
					$this->m_sLibelle = (string)$ndAttrib;
					break;
				case 'typeElement':
					$this->m_eTypeElement = (string)$ndAttrib;
					break;
				case 'printed':
					$this->m_bPrinted = (int)$ndAttrib;
					break;
				case 'readOnly':
					$this->m_bReadonly = (int)$ndAttrib;
					break;
				case 'computed':
					$this->m_bComputed = (int)$ndAttrib;
					break;
				case 'sort':
					$this->m_bSort = (int)$ndAttrib;
					break;
				case 'link':
					$this->m_bLink = (int)$ndAttrib;
					break;
				case 'linkedTableXml':
					$this->m_sLinkedTableXml = (string)$ndAttrib;
					break;
				case 'linkedTableID':
					$this->m_sLinkedTableID = (string)$ndAttrib;
					break;
			}
		}
	}
}