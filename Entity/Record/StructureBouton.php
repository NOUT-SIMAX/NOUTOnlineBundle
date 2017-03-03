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

	/** @var  string $m_ButtonID */
	protected $m_ID;


	/**
	 * @param \SimpleXMLElement $clAttribNOUT
	 * @param \SimpleXMLElement $clAttribXS
	 */
	public function __construct(\SimpleXMLElement $clAttribNOUT, \SimpleXMLElement $clAttribXS)
	{
		parent::__construct('', $clAttribNOUT, $clAttribXS);
		//TODO: Build a real object id
        $this->m_ID = spl_object_hash($clAttribNOUT);
        $this->m_nIDColonne = (string)$clAttribNOUT['idButton'];
		$this->m_clInfoBouton = new InfoButton($clAttribNOUT);
	}

    /**
     * @return string
     */
	public function getID(){
	    return $this->m_ID;
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