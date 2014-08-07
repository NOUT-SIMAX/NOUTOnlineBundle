<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 04/08/14
 * Time: 11:42
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Record;


class StructureElement
{
	const NV_XSD_Enreg                  = 0;
	const NV_XSD_List                   = 1;
	const NV_XSD_LienElement            = 2;


	public $m_nID;
	public $m_sLibelle;
	public $m_nNiveau;
	public $m_TabStructureColonne;
	public $m_MapIDColonne2StructColonne;

	public function __construct()
	{
		$this->m_nID = '';
		$this->m_sLibelle = '';
		$this->m_nNiveau = -1;
		$this->m_TabStructureColonne = array();
		$this->m_MapIDColonne2StructColonne = array();
	}


	public function sGetColonneTypeElement($sIDColonne)
	{
		if (!isset($this->m_MapIDColonne2StructColonne[$sIDColonne]))
			return null;

		return $this->m_MapIDColonne2StructColonne[$sIDColonne]->m_eTypeElement;
	}

} 