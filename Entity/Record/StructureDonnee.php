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
	 * @var bool colonne obligatoire
	 */
	protected $m_bRequired;

	/**
	 * @param                   $sID
	 * @param \SimpleXMLElement $clAttribNOUT
	 * @param \SimpleXMLElement $clAttribXS
	 */
	public function __construct($sID, \SimpleXMLElement $clAttribNOUT, \SimpleXMLElement $clAttribXS)
	{
		parent::__construct($sID, $clAttribNOUT, $clAttribXS);

		$this->m_bRequired = (isset($clAttribXS['use']) && ((string) $clAttribXS['use'] === 'required')); //xs:use="required"
	}

    /**
     * @param $sOption
     * @return bool
     */
	public function isOption($sOption): bool
	{
		//les options qui viennent de membres
		switch ($sOption)
		{
			case self::OPTION_Required:
				return $this->m_bRequired;
		}

		return parent::isOption($sOption);
	}

}