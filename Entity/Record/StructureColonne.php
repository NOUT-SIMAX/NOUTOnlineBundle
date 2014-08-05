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


	public function __construct($sID, $TabAttribSIMAX)
	{
		$this->m_TabStructureColonne = array();

		$this->m_nIDColonne = $sID;
		$this->m_sLibelle = (string)$TabAttribSIMAX['name'];
		$this->m_eTypeElement =  (string)$TabAttribSIMAX['typeElement'];
		$this->m_clRestriction = null;

		if (isset($TabAttribSIMAX['printed']))
			$this->m_bPrinted = (int)$TabAttribSIMAX['printed'];
		else
			$this->m_bPrinted=false;

		if (isset($TabAttribSIMAX['readOnly']))
			$this->m_bReadonly = (int)$TabAttribSIMAX['readOnly'];
		else
			$this->m_bReadonly=false;

		if (isset($TabAttribSIMAX['computed']))
			$this->m_bComputed = (int)$TabAttribSIMAX['computed'];
		else
			$this->m_bComputed=false;

		if (isset($TabAttribSIMAX['sort']))
			$this->m_bSort = (int)$TabAttribSIMAX['sort'];
		else
			$this->m_bSort=false;

		if (isset($TabAttribSIMAX['link']))
			$this->m_bLink = (int)$TabAttribSIMAX['link'];
		else
			$this->m_bLink=false;

		if (isset($TabAttribSIMAX['linkedTableXml']))
			$this->m_sLinkedTableXml = (string)$TabAttribSIMAX['linkedTableXml'];
		else
			$this->m_sLinkedTableXml='';

		if (isset($TabAttribSIMAX['linkedTableID']))
			$this->m_sLinkedTableID = (string)$TabAttribSIMAX['linkedTableID'];
		else
			$this->m_sLinkedTableID='';
	}
}