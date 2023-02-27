<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 31/07/14
 * Time: 14:11
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Record;

class InfoColonne
{
    /** @var string  */
	protected $m_nIDColonne;

    /** @var array */
    protected $m_TabOptions;

    /**
     * InfoColonne constructor.
     * @param string $sIDColonne
     * @param $TabAttrib
     * @param $tabAttribLayout
     */
	public function __construct(string $sIDColonne, $TabAttrib, $tabAttribLayout)
	{
		$this->m_nIDColonne = $sIDColonne;

        $this->m_TabOptions = array();

		$this->_InitInfoColonne($TabAttrib);
		$this->_InitInfoColonne($tabAttribLayout);
	}

    /**
     * @return string
     */
    public function getIDColonne(): string
    {
        return $this->m_nIDColonne;
    }

    /**
     * @param \Traversable $TabAttrib
     */
	protected function _InitInfoColonne(\Traversable $TabAttrib)
	{

        foreach ($TabAttrib as $sAttribName => $ndAttrib)
        {
            switch($sAttribName)
            {
                case Record::OPTION_Unit:
                case Record::OPTION_DisplayDefault:
                case Record::OPTION_DisplayMode:
                case Record::OPTION_BGColor:
                case Record::OPTION_Color:
                case StructureColonne::OPTION_TypeElement:
                case StructureColonne::OPTION_WithWatermark:
                    $this->m_TabOptions[$sAttribName] = (string) $ndAttrib;
                    break;
                case Record::OPTION_Filename:
                default:
                    $this->m_TabOptions[$sAttribName] = (int) $ndAttrib;
                    break;
            }
        }
	}

    /**
     * @param $dwOption
     * @return bool
     */
	public function hasOption($dwOption): bool
    {
        return isset($this->m_TabOptions[$dwOption]);
    }

    /**
     * @param $dwOption
     * @param bool $bDefault
     * @return bool
     */
    public function isOption($dwOption, bool $bDefault=false): bool
    {
        if (!isset($this->m_TabOptions[$dwOption]))
            return $bDefault;

        return !empty($this->m_TabOptions[$dwOption]);
    }

    /**
     * @return int
     */
    public function getState() : int
    {
        return StructureColonne::s_nGetState(
            $this->isOption(StructureColonne::OPTION_Hidden),
            $this->isOption(StructureColonne::OPTION_Disabled),
            $this->isOption(StructureColonne::OPTION_ReadOnly),
            $this->isOption(StructureColonne::OPTION_ReadWithoutModify)
        );
    }

    /**
     * @param $dwOption
     * @return mixed
     */
    public function getOption($dwOption)
    {
        if (!isset($this->m_TabOptions[$dwOption]))
            return null;

        return $this->m_TabOptions[$dwOption];
    }

    // Renvoyer tout le tableau des options
    /**
     * @return array
     */
    public function getTabOptions(): array
    {
        return $this->m_TabOptions;
    }

    /**
     * @return bool
     */
    public function isWritable(): bool
    {
        $forbidden_states = array(
            StructureColonne::OPTION_Hidden,
            StructureColonne::OPTION_ReadOnly,
            StructureColonne::OPTION_Disabled,
        );
        return !count(array_intersect($this->m_TabOptions, $forbidden_states));
    }

}
