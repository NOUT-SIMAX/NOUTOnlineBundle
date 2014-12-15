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
	protected $m_nIDColonne;
	protected $m_sLibelle;
	protected $m_eTypeElement;
	protected $m_clRestriction;

	protected $m_bPrinted;
	protected $m_bReadonly;
	protected $m_bRequired;
	protected $m_bComputed;
	protected $m_bTitled;
	protected $m_bSort;

	protected $m_bLink;
	protected $m_sLinkedTableXml;
	protected $m_sLinkedTableID;

	protected $m_TabStructureColonne;


	public function __construct($sID, \SimpleXMLElement $clAttribNOUT, \SimpleXMLElement $clAttribXS)
	{
		$this->m_TabStructureColonne = array();

		$this->m_nIDColonne = $sID;
		$this->m_sLibelle = '';
		$this->m_eTypeElement =  '';
		$this->m_clRestriction = null;
		$this->m_bPrinted=0;
		$this->m_bReadonly=0;
		$this->m_bTitled=0;
		$this->m_bRequired=false;
		$this->m_bComputed=0;
		$this->m_bSort=0;
		$this->m_bLink=0;
		$this->m_sLinkedTableXml='';
		$this->m_sLinkedTableID='';

		$this->_InitInfoColonne($clAttribNOUT, $clAttribXS);
	}

	protected function _InitInfoColonne(\SimpleXMLElement $clAttribNOUT, \SimpleXMLElement $clAttribXS)
	{
		foreach($clAttribNOUT as $sAttribName => $ndAttrib)
		{
			switch($sAttribName)
			{
				case 'titled':
					$this->m_bTitled = (int)$ndAttrib;
					break;
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

		foreach($clAttribXS as $sAttribName => $ndAttrib)
		{
			switch($sAttribName)
			{
				case 'use': //xs:use="required"
					if ((string)$ndAttrib==='required')
						$this->m_bRequired = true;
					break;
			}
		}
	}

	public function bEstTypeSimple()
	{
		$aTypeSimple = array(
			self::TM_Booleen,
			self::TM_Entier,
			self::TM_Texte,
			self::TM_DateHeure,
			self::TM_Date,
			self::TM_Heure,
			self::TM_Reel,
			self::TM_Monetaire,
		);

		return in_array($this->m_eTypeElement, $aTypeSimple);
	}

	/**
	 * @return array
	 */
	public function getTabStructureColonne()
	{
		return $this->m_TabStructureColonne;
	}

	/**
	 * @return int
	 */
	public function getComputed()
	{
		return $this->m_bComputed;
	}

	/**
	 * @return int
	 */
	public function getLink()
	{
		return $this->m_bLink;
	}

	/**
	 * @return int
	 */
	public function getPrinted()
	{
		return $this->m_bPrinted;
	}

	/**
	 * @return int
	 */
	public function getReadonly()
	{
		return $this->m_bReadonly;
	}

	/**
	 * @return boolean
	 */
	public function getRequired()
	{
		return $this->m_bRequired;
	}

	/**
	 * @return int
	 */
	public function getSort()
	{
		return $this->m_bSort;
	}

	/**
	 * @return null|ColonneRestriction
	 */
	public function getRestriction()
	{
		return $this->m_clRestriction;
	}

	/**
	 * @param ColonneRestriction $clRestriction
	 * @return $this
	 */
	public function setRestriction(ColonneRestriction $clRestriction)
	{
		$this->m_clRestriction = $clRestriction;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTypeElement()
	{
		return $this->m_eTypeElement;
	}

	/**
	 * @param $eTypeElement string
	 * @return $this
	 */
	public function setTypeElement($eTypeElement)
	{
		$this->m_eTypeElement = $eTypeElement;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getFormType()
	{
		return str_replace(':', '_', $this->m_eTypeElement);
	}

	/**
	 * @return mixed
	 */
	public function getIDColonne()
	{
		return $this->m_nIDColonne;
	}

	/**
	 * @return string
	 */
	public function getLibelle()
	{
		return $this->m_sLibelle;
	}

	/**
	 * @return string
	 */
	public function getLinkedTableID()
	{
		return $this->m_sLinkedTableID;
	}

	/**
	 * @return string
	 */
	public function getLinkedTableXml()
	{
		return $this->m_sLinkedTableXml;
	}





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
}