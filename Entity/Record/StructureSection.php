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


    const LEVEL_FICHE   = 0;
    const LEVEL_PRINCIPAL = 1;
    const LEVEL_SECONDAIRE = 2;
    const LEVEL_SEQUENCE = 3;

    const MODE_1COlONNE = 1;
    const MODE_2COlONNE = 2;
    const MODE_3COlONNE = 3;
    const MODE_4COlONNE = 4;
    const MODE_5COlONNE = 5;

    const SENS_HORIZONTAL = 1;
    const SENS_VERTICAL = 2;
}