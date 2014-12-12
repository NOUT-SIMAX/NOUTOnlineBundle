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
	/**
	 * @var string
	 */

	protected $m_nID;
	/**
	 * @var string
	 */

	protected $m_sLibelle;
	/**
	 * @var integer
	 */

	protected $m_nNiveau;
	/**
	 * @var array
	 */
	protected $m_TabStructureColonne;

	/**
	 * @var array
	 */
	protected $m_MapIDColonne2StructColonne;

	public function __construct($sID, $sLibelle, $nNiv)
	{
		$this->m_nID = $sID;
		$this->m_sLibelle = $sLibelle;
		$this->m_nNiveau = $nNiv;
		$this->m_TabStructureColonne = array();
		$this->m_MapIDColonne2StructColonne = array();
	}


	public function getTypeElement($sIDColonne)
	{
		if (!isset($this->m_MapIDColonne2StructColonne[$sIDColonne]))
			return null;

		return $this->m_MapIDColonne2StructColonne[$sIDColonne]->getTypeElement();
	}

	/**
	 * @param $sIDColonne string
	 * @param $eTypeElement string
	 */
	public function setTypeElement($sIDColonne, $eTypeElement)
	{
		if (isset($this->m_MapIDColonne2StructColonne[$sIDColonne]))
			$this->m_MapIDColonne2StructColonne[$sIDColonne]->setTypeElement($eTypeElement);
	}

	/**
	 * @param $sIDColonne string
	 * @param ColonneRestriction $clRestriction
	 */
	public function setRestriction($sIDColonne, ColonneRestriction $clRestriction)
	{
		if (isset($this->m_MapIDColonne2StructColonne[$sIDColonne]))
			$this->m_MapIDColonne2StructColonne[$sIDColonne]->setRestriction($clRestriction);
	}

	public function setStructureColonne(StructureColonne $clStructColonne)
	{
		$this->m_MapIDColonne2StructColonne[$clStructColonne->getIDColonne()]=$clStructColonne;
		return $this;
	}

	public function addColonne2TabStruct($sIDColonne, $sIDColPere, StructureColonne $clStruct=null)
	{
		if (!isset($sIDColPere))
			$this->m_TabStructureColonne[] = isset($clStruct) ? $clStruct : $this->m_MapIDColonne2StructColonne[$sIDColonne];
		else
			$this->m_MapIDColonne2StructColonne[$sIDColPere]->addColonne2TabStruct($sIDColonne, null, $this->m_MapIDColonne2StructColonne[$sIDColonne]);
	}


	/**
	 * @return string
	 */
	public function getID()
	{
		return $this->m_nID;
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
	public function getNiveau()
	{
		return $this->m_nNiveau;
	}

	/**
	 * @return string
	 */
	public function getLibelle()
	{
		return $this->m_sLibelle;
	}





	const NV_XSD_Enreg                  = 0;
	const NV_XSD_List                   = 1;
	const NV_XSD_LienElement            = 2;
} 