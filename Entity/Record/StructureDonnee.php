<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 03/08/2015
 * Time: 17:06
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Record;


class StructureDonnee extends StructureColonne
{
	/**
	 * @var colonne obligatoire
	 */
	protected $m_bRequired;

	/**
	 * @var ColonneRestriction|null restriction sur le champ
	 */
	protected $m_clRestriction;


	/**
	 * @param                   $sID
	 * @param \SimpleXMLElement $clAttribNOUT
	 * @param \SimpleXMLElement $clAttribXS
	 */
	public function __construct($sID, \SimpleXMLElement $clAttribNOUT, \SimpleXMLElement $clAttribXS)
	{
		parent::__construct($sID, $clAttribNOUT, $clAttribXS);

		$this->m_bRequired = (isset($clAttribXS['use']) && ((string) $clAttribXS['use'] === 'required')); //xs:use="required"
		$this->m_clRestriction       = null;
	}

	/**
	 * @return null|ColonneRestriction
	 */
	public function getRestriction()
	{
		return $this->m_clRestriction;
	}



	public function isOption($sOption)
	{
		//les options qui viennent de membres
		switch ($sOption)
		{
			case self::OPTION_Required:
				return $this->m_bRequired;
		}

		parent::isOption($sOption);
	}

}