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
            if (!in_array($sAttribName, self::aGetOptionList()))
            {
                //on ne prend pas les attributs suivant
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

    /**
     * @return array
     */
    public static function aGetOptionList(): array
    {
        return [
            StructureColonne::OPTION_Icone            ,
            StructureColonne::OPTION_IDAction         ,
            StructureColonne::OPTION_WithValidation   ,
            StructureColonne::OPTION_TypeSelection    ,
            StructureColonne::OPTION_Sentence         ,
            StructureColonne::OPTION_TypeAction       ,
            StructureColonne::OPTION_IDColToUpdate    ,
            StructureColonne::OPTION_IDColSelection   ,
            StructureColonne::OPTION_IDTypeAction     ,
            StructureColonne::OPTION_Substitution     ,
            StructureColonne::OPTION_ColumnAssignation,
            StructureColonne::OPTION_DisplayOnLine    ,
            StructureColonne::OPTION_ListMode         ,
            StructureColonne::OPTION_IDButtonAction   ,
            StructureColonne::OPTION_Hidden   ,
        ];
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
