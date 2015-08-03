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

	/**
	 * @var array
	 */
	protected $m_TabBouton;


	/**
	 * @param $sID
	 * @param $sLibelle
	 * @param $nNiv
	 */
	public function __construct($sID, $sLibelle, $nNiv)
	{
		$this->m_nID                        = $sID;
		$this->m_sLibelle                   = $sLibelle;
		$this->m_nNiveau                    = $nNiv;
		$this->m_TabStructureColonne        = array();
		$this->m_MapIDColonne2StructColonne = array();
	}


	/**
	 * @param string $sIDColonne identifiant de la colonne
	 */
	public function getTypeElement($sIDColonne)
	{
		if (!isset($this->m_MapIDColonne2StructColonne[$sIDColonne]))
		{
			return;
		}

		return $this->m_MapIDColonne2StructColonne[$sIDColonne]->getTypeElement();
	}

	/**
	 * @param string $sIDColonne identifiant de la colonne
	 * @return StructureColonne|null
	 */
	public function getStructureColonne($sIDColonne)
	{
		if (!isset($this->m_MapIDColonne2StructColonne[$sIDColonne]))
		{
			return;
		}

		return $this->m_MapIDColonne2StructColonne[$sIDColonne];
	}

	/**
	 * @param $sIDColonne string
	 * @param $eTypeElement string
	 */
	public function setTypeElement($sIDColonne, $eTypeElement)
	{
		if (isset($this->m_MapIDColonne2StructColonne[$sIDColonne]))
		{
			$this->m_MapIDColonne2StructColonne[$sIDColonne]->setTypeElement($eTypeElement);
		}
	}

	/**
	 * @param $sIDColonne string
	 * @param ColonneRestriction $clRestriction
	 */
	public function setRestriction($sIDColonne, ColonneRestriction $clRestriction)
	{
		if (isset($this->m_MapIDColonne2StructColonne[$sIDColonne]))
		{
			$this->m_MapIDColonne2StructColonne[$sIDColonne]->setRestriction($clRestriction);
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

		$this->m_MapIDColonne2StructColonne[$clStructColonne->getIDColonne()] = $clStructColonne;
		return $clStructColonne->getIDColonne();
	}

	/**
	 * @param                  $sIDColonne
	 * @param                  $sIDColPere
	 * @param StructureColonne $clStruct
	 * @return this
	 */
	public function addColonne2TabStruct($sIDColonne, $sIDColPere, StructureColonne $clStruct = null)
	{
		if (!isset($sIDColPere))
		{
			$this->m_TabStructureColonne[] = isset($clStruct) ? $clStruct : $this->m_MapIDColonne2StructColonne[$sIDColonne];
		}
		else
		{
			$this->m_MapIDColonne2StructColonne[$sIDColPere]->addColonne2TabStruct($sIDColonne, null, $this->m_MapIDColonne2StructColonne[$sIDColonne]);
		}
		return $this;
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
        return array_keys($this->m_MapIDColonne2StructColonne);
    }

    /**
     * @param $option
     * @return array
     */
    public function getTabColonneAvecOption($option)
    {
        $aRet = array();
        foreach($this->m_MapIDColonne2StructColonne as $clStructureColonne)
        {
            if ($clStructureColonne->isOption($option))
                $aRet[]=$clStructureColonne->getIDColonne();
        }

        return $aRet;
    }





	const NV_XSD_Enreg                  = 0;
	const NV_XSD_List                   = 1;
	const NV_XSD_LienElement            = 2;
}
