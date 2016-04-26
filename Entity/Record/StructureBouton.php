<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 03/08/2015
 * Time: 17:06
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Record;


use NOUT\Bundle\NOUTOnlineBundle\Entity\Langage;

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
		$this->m_clInfoBouton = new InfoButton($clAttribNOUT);
	}

	/**
	 * @return InfoButton
	 */
	public function getInfoBouton()
	{
		return $this->m_clInfoBouton;
	}

	public function isReadOnly()
	{
        // Renvoit un booléen qui indique si le bouton est dispo en readOnly
        return Langage::s_isActionReadOnly($this->m_clInfoBouton->getOption(self::OPTION_IDTypeAction));
	}

}