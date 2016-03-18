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
	 * identifiant du formulaire
	 * @var string
	 */
	protected $m_nID;

	/**
	 * @var string
	 */
	protected $m_sLibelle;

	/**
	 * @var StructureSection
	 */
	protected $m_clFiche;

	/**
	 * @var array
	 */
	protected $m_TabBouton;

	/**
	 * @var array
	 */
	protected $m_MapIDColonne2Structure;


	/**
	 * @param $sID
	 * @param $sLibelle
	 * @param $nNiv
	 */
	public function __construct($sID, $sLibelle)
	{
		$this->m_nID                        = $sID;
		$this->m_sLibelle                   = $sLibelle;
		$this->m_clFiche					= new StructureSection('1', new \SimpleXMLElement('<root/>'), new \SimpleXMLElement('<root/>'));

		$this->m_MapIDColonne2Structure = array();
	}

	public function addColonne(StructureColonne $clColonne)
	{
		$this->m_MapIDColonne2Structure[$clColonne->getIDColonne()]=$clColonne;
		return $this;
	}

	/**
	 * @param string $sIDColonne identifiant de la colonne
	 */
	public function getTypeElement($sIDColonne)
	{
		if (!isset($this->m_MapIDColonne2Structure[$sIDColonne]))
		{
			return;
		}

		return $this->m_MapIDColonne2Structure[$sIDColonne]->getTypeElement();
	}

	/**
	 * @param string $sIDColonne identifiant de la colonne
	 * @return StructureColonne|null
	 */
	public function getStructureColonne($sIDColonne)
	{
		if (!isset($this->m_MapIDColonne2Structure[$sIDColonne]))
		{
			return;
		}

		return $this->m_MapIDColonne2Structure[$sIDColonne];
	}

	/**
	 * ajoute un des boutons d'actions (non paramétré)
	 * @param StructureBouton $clStructBouton
	 * @return $this
	 */
	public function addButton(StructureBouton $clStructBouton)
	{
		$this->m_TabBouton[]=$clStructBouton;
		return $this;
	}

	/**
	 * @param $sIDColonne string
	 * @param $eTypeElement string
	 */
	public function setTypeElement($sIDColonne, $eTypeElement)
	{
		if (isset($this->m_MapIDColonne2Structure[$sIDColonne]))
		{
			$this->m_MapIDColonne2Structure[$sIDColonne]->setTypeElement($eTypeElement);
		}
	}

	/**
	 * @param $sIDColonne string
	 * @param ColonneRestriction $clRestriction
	 */
	public function setRestriction($sIDColonne, ColonneRestriction $clRestriction)
	{
		if (isset($this->m_MapIDColonne2Structure[$sIDColonne]))
		{
			$this->m_MapIDColonne2Structure[$sIDColonne]->setRestriction($clRestriction);
		}
	}

	/**
	 * @param StructureColonne $clStructColonne
	 * @return mixed|void
	 */
	public function setStructureColonne(StructureColonne $clStructColonne)
	{
		if ($clStructColonne instanceof StructureBouton)
		{
			if (empty($clStructColonne->getIDColonne()))
			{
				//c'est pas un bouton par programmation, il faut le sortir des structures colonnes
				$this->m_TabBouton[]=$clStructColonne;
				return;
			}
		}

		$this->m_MapIDColonne2Structure[$clStructColonne->getIDColonne()] = $clStructColonne;
		return $clStructColonne->getIDColonne();
	}

	/**
	 * @return string
	 */
	public function getID()
	{
		return $this->m_nID;
	}

	/**
	 * @return StructureSection
	 */
	public function getFiche()
	{
		return $this->m_clFiche;
	}

	/**
	 * @return array
	 */
	public function getTabBouton()
	{
		return$this->m_TabBouton;
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

    /**
     * @return array
     */
    public function getTabIDColonne()
    {
        return array_keys($this->m_MapIDColonne2Structure);
    }

    /**
     * @param $option
     * @return array
     */
    public function getTabColonneAvecOption($option)
    {
        $aRet = array();
        foreach($this->m_MapIDColonne2Structure as $clStructureColonne)
        {
            /** @var StructureColonne $clStructureColonne */
            if ($clStructureColonne->isOption($option))
                $aRet[]=$clStructureColonne->getIDColonne();
        }

        return $aRet;
    }

	/**
	 * @param $option
	 * @return array
	 */
	public function getTabColonneTmTab()
	{
        // OPTION_LinkedTableID // Ne marche pas
        // On cherche les éléments avec l'ID StructureColonne::TM_Tableau

        // Tableau de retour
		$aRet = array();

		foreach($this->m_MapIDColonne2Structure as $clStructureColonne)
		{
            /** @var StructureColonne $clStructureColonne */
			if ($clStructureColonne->getTypeElement() == StructureColonne::TM_Tableau)
				$aRet[$clStructureColonne->getIDColonne()] = $clStructureColonne->getOption(StructureColonne::OPTION_LinkedTableID);
		}

		return $aRet;
	}

	const NV_XSD_Enreg                  = 0;
	const NV_XSD_List                   = 1;
	const NV_XSD_LienElement            = 2;
}
