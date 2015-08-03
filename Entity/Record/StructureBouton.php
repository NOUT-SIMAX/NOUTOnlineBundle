<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 03/08/2015
 * Time: 17:06
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Record;


class StructureBouton extends StructureColonne
{

	/**
	 * @var InfoButton information sur le bouton
	 */
	protected $m_clInfoBouton;


	/**
	 * @param \SimpleXMLElement $clAttribNOUT
	 * @param \SimpleXMLElement $clAttribXS
	 */
	public function __construct(\SimpleXMLElement $clAttribNOUT, \SimpleXMLElement $clAttribXS)
	{
		parent::__construct('', $clAttribNOUT, $clAttribXS);

		$this->m_nIDColonne = (string)$clAttribNOUT['idButton'];
		$this->m_clInfoBouton = new InfoButton(
			(string)$clAttribNOUT['typeSelection'],
			(string)$clAttribNOUT['idAction'],
			(string)$clAttribNOUT['icon'],
			(string)$clAttribNOUT['withValidation']);
	}

	/**
	 * @return InfoButton
	 */
	public function getInfoBouton()
	{
		return $this->m_clInfoBouton;
	}

}