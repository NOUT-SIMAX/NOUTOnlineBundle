<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 03/08/2015
 * Time: 17:01
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Record;


use NOUT\Bundle\NOUTOnlineBundle\Entity\Langage;

class InfoButton
{
    /**
     * @var string[]
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
     * @return string|null
     */
    public function getOption(string $option): ?string
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
    public function getTabOption(): array
    {
        return $this->m_TabOptions;
    }


    const TYPE_Imprimer = Langage::eTYPEACTION_Impression;
    const TYPE_Imprimer_liste = Langage::eTYPEACTION_Impression;
    const TYPE_Supprimer = Langage::eTYPEACTION_Suppression;
    const TYPE_Ajouter = Langage::eTYPEACTION_AjouterA;
    const TYPE_Modifier = Langage::eTYPEACTION_Modification;
    const TYPE_Enlever = Langage::eTYPEACTION_EnleverDe;
    const TYPE_Creer = Langage::eTYPEACTION_Creation;
    const TYPE_Detail = Langage::eTYPEACTION_Consultation;
}