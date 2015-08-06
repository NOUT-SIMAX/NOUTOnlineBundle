<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 03/08/2015
 * Time: 17:06
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Record;


class StructureSection extends StructureColonne
{
	/**
	 * @var array tableau des colonnes filles
	 */
	protected $m_TabStructureColonne;

	/**
	 * @var array map idcolonne => colonnes filles
	 */
	protected $m_MapIDColonne2Colonne;


	/**
	 * @param                   $sID
	 * @param \SimpleXMLElement $clAttribNOUT
	 * @param \SimpleXMLElement $clAttribXS
	 */
	public function __construct($sID, \SimpleXMLElement $clAttribNOUT, \SimpleXMLElement $clAttribXS)
	{
		parent::__construct($sID, $clAttribNOUT, $clAttribXS);

		$this->m_TabStructureColonne = array();
		$this->m_MapIDColonne2Colonne = array();

		$this->m_eTypeElement = self::TM_Separateur;
	}

	/**
	 * @return array
	 */
	public function getTabStructureColonne()
	{
		return $this->m_TabStructureColonne;
	}

	/**
	 * @param StructureColonne $clColonne
	 * @return $this
	 */
	public function addColonne(StructureColonne $clColonne)
	{
		$this->m_TabStructureColonne[]=$clColonne;
		$this->m_MapIDColonne2Colonne[$clColonne->getIDColonne()]=$clColonne;
		return $this;
	}

//	public function addColonne($sIDColonne)
//	{
//
//	}
//
//
//	/**
//	 * @param                  $sIDColonne
//	 * @param                  $sIDColPere
//	 * @param StructureColonne $clStruct
//	 * @return this
//	 */
//	public function addColonne2($sIDColonne, $sIDColPere, StructureColonne $clStruct = null)
//	{
//		if (!isset($sIDColPere))
//		{
//			$this->m_TabStructureColonne[] = isset($clStruct) ? $clStruct : $this->m_MapIDColonne2StructColonne[$sIDColonne];
//		}
//		else
//		{
//			if (!isset($this->m_MapIDColonne2StructColonne[$sIDColPere]) || !($this->m_MapIDColonne2StructColonne[$sIDColPere] instanceof StructureSection))
//			{
//				throw new \Exception('La colonne père n\'est pas une section');
//			}
//
//			$this->m_MapIDColonne2StructColonne[$sIDColPere]->addColonne2TabStruct($sIDColonne, null, $this->m_MapIDColonne2StructColonne[$sIDColonne]);
//		}
//		return $this;
//	}
//
//
//
//	/**
//	 * @param StructureColonne $clStructColonne
//	 * @return mixed|void
//	 */
//	public function setStructureColonne(StructureColonne $clStructColonne)
//	{
//		if ($clStructColonne instanceof StructureBouton)
//		{
//			if (empty($clStructColonne->getIDColonne()))
//			{
//				//c'est pas un bouton par programmation, il faut le sortir des structures colonnes
//				$this->m_TabBouton[]=$clStructColonne;
//				return;
//			}
//		}
//
//		$this->m_MapIDColonne2StructColonne[$clStructColonne->getIDColonne()] = $clStructColonne;
//		return $clStructColonne->getIDColonne();
//	}
//
//	/**
//	 * @param $sIDColonne string
//	 * @param ColonneRestriction $clRestriction
//	 */
//	public function setRestriction($sIDColonne, ColonneRestriction $clRestriction)
//	{
//		if (isset($this->m_MapIDColonne2StructColonne[$sIDColonne]))
//		{
//			$this->m_MapIDColonne2StructColonne[$sIDColonne]->setRestriction($clRestriction);
//		}
//	}
//
//	/**
//	 * @param $sIDColonne string
//	 * @param $eTypeElement string
//	 */
//	public function setTypeElement($sIDColonne, $eTypeElement)
//	{
//		if (isset($this->m_MapIDColonne2StructColonne[$sIDColonne]))
//		{
//			$this->m_MapIDColonne2StructColonne[$sIDColonne]->setTypeElement($eTypeElement);
//		}
//	}
//
//	/**
//	 * @param string $sIDColonne identifiant de la colonne
//	 * @return StructureColonne|null
//	 */
//	public function getStructureColonne($sIDColonne)
//	{
//		if (!isset($this->m_MapIDColonne2StructColonne[$sIDColonne]))
//		{
//			return;
//		}
//
//		return $this->m_MapIDColonne2StructColonne[$sIDColonne];
//	}
//
//	/**
//	 * @param string $sIDColonne identifiant de la colonne
//	 */
//	public function getTypeElement($sIDColonne)
//	{
//		if (!isset($this->m_MapIDColonne2StructColonne[$sIDColonne]))
//		{
//			return;
//		}
//
//		return $this->m_MapIDColonne2StructColonne[$sIDColonne]->getTypeElement();
//	}

}