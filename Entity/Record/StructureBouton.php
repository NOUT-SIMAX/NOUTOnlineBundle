<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 03/08/2015
 * Time: 17:06
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Record;


use NOUT\Bundle\NOUTOnlineBundle\Entity\Langage\Langage;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parser\ParserXSDSchema;

class StructureBouton extends StructureColonne
{

    /**
     * @var InfoButton information sur le bouton
     */
    protected InfoButton $clInfoBouton;

    /** @var  string */
    protected string $ID;

    /** @var StructureBouton[] */
    protected array $subButtons;

    /**
     * @param \SimpleXMLElement $clAttribNOUT
     * @param \SimpleXMLElement $clAttribXS
     * @param \SimpleXMLElement|null $subButtons
     */
    public function __construct(\SimpleXMLElement $clAttribNOUT, \SimpleXMLElement $clAttribXS, \SimpleXMLElement $subButtons = null)
    {
        parent::__construct('', $clAttribNOUT, $clAttribXS);
        $this->ID           = spl_object_hash($clAttribNOUT);
        $this->m_nIDColonne = (string)$clAttribNOUT['idButton'];
        $this->clInfoBouton = new InfoButton($clAttribNOUT);
        $this->subButtons = array();
        if (!is_null($subButtons)) {
            foreach ($subButtons->children(ParserXSDSchema::NAMESPACE_XSD) as $subButton) {
                /** @var \SimpleXMLElement $subButton */
                $clAttribNOUT = $subButton->attributes(ParserXSDSchema::NAMESPACE_NOUT_XSD);
                $clAttribXS   = $subButton->attributes(ParserXSDSchema::NAMESPACE_XSD);

                $ndSeqSousButtons = null;
                try
                {
                    $shema = ParserXSDSchema::NAMESPACE_XSD;
                    if ($subButton->children($shema)->count() > 0)
                    {
                        $ndSeqSousButtons = $subButton->children($shema)->complexType->children($shema)->sequence;
                        if ($ndSeqSousButtons->children($shema)->count() == 0)
                        {
                            $ndSeqSousButtons = null;
                        }
                    }
                }
                catch (\Exception $e) {
                    $ndSeqSousButtons = null;
                }

                array_push($this->subButtons, new StructureBouton($clAttribNOUT, $clAttribXS, $ndSeqSousButtons));
            }
        }
    }

    /**
     * @return StructureBouton[]
     */
    public function getSubButtons(): array
    {
        return $this->subButtons;
    }

    /**
     * @return string
     */
    public function getID(): string
    {
        return $this->ID;
    }

    /**
     * @return InfoButton
     */
    public function getInfoBouton(): InfoButton
    {
        return $this->clInfoBouton;
    }

    /**
     * @return bool
     */
    public function isReadOnly() : bool
    {
        // Renvoit un booléen qui indique si le bouton est dispo en readOnly
        return Langage::s_isActionReadOnly($this->clInfoBouton->getOption(self::OPTION_IDTypeAction));
    }

    const WITHVALIDATION_Default = 0;
    const WITHVALIDATION_Avant = 1;
    const WITHVALIDATION_Apres = 2;
    const WITHVALIDATION_SansFermer = 4;

    const SUBSTITUTION_Annuler = 1;
    const SUBSTITUTION_Enregistrer = 2;
    const SUBSTITUTION_Imprimer = 2392;
}
