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
	 * @param                   $sID
	 * @param \SimpleXMLElement $clAttribNOUT
	 * @param \SimpleXMLElement $clAttribXS
	 */
	public function __construct($sID, \SimpleXMLElement $clAttribNOUT, \SimpleXMLElement $clAttribXS)
	{
		parent::__construct($sID, $clAttribNOUT, $clAttribXS);

		$this->m_TabStructureColonne = array();
	}


	/**
	 * @return array
	 */
	public function getTabStructureColonne()
	{
		return $this->m_TabStructureColonne;
	}
}