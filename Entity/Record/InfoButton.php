<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 03/08/2015
 * Time: 17:01
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Record;


class InfoButton 
{
    /**
     * @var array
     */
    private $m_TabOptions;


	public function __construct(\SimpleXMLElement $clAttribNOUT)
	{
        $this->m_TabOptions = array();

        foreach ($clAttribNOUT as $sAttribName => $ndAttrib)
        {
            if (($sAttribName == StructureColonne::OPTION_IDBouton)
                || ($sAttribName == StructureColonne::OPTION_TypeElement)
                || ($sAttribName == StructureColonne::OPTION_Name))
            {
                // On ignore ces options
                continue;
            }

            $this->m_TabOptions[$sAttribName] = (string) $ndAttrib;
        }

	}

    /**
     * @param string $option
     */
    public function getOption($option)
    {
        if (isset($this->m_TabOptions[$option]))
        {
            return $this->m_TabOptions[$option];
        }
        return null;
    }

    /**
     * @return array
     */
    public function getTabOption()
    {
        return $this->m_TabOptions;
    }


    const TYPE_Imprimer = 'Imprimer';
    const TYPE_Supprimer = 'Supprimer';
    const TYPE_Ajouter = 'Ajouter';
    const TYPE_Modifier = 'Modifier';
    const TYPE_Enlever = 'Enlever';
    const TYPE_Creer = 'Créer';
    const TYPE_Detail = 'Détail';
}