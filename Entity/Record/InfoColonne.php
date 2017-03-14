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
	protected $m_nIDColonne;

    /**
     * @var array
     */
    protected $m_TabOptions;

	public function __construct($sIDColonne, $TabAttrib, $tabAttribLayout)
	{
		$this->m_nIDColonne = $sIDColonne;

        $this->m_TabOptions          = array();

		$this->_InitInfoColonne($TabAttrib);
		$this->_InitInfoColonne($tabAttribLayout);
	}

    /**
     * @return int
     */
    public function getIDColonne()
    {
        return $this->m_nIDColonne;
    }

	protected function _InitInfoColonne($TabAttrib)
	{

        foreach ($TabAttrib as $sAttribName => $ndAttrib)
        {
            switch($sAttribName)
            {
                case Record::OPTION_DisplayDefault:
                case Record::OPTION_DisplayMode:
                case Record::OPTION_BGColor:
                case Record::OPTION_Color:
                    $this->m_TabOptions[$sAttribName] = (string) $ndAttrib;
                    break;

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
    public function isOption($dwOption)
    {
        if (!isset($this->m_TabOptions[$dwOption]))
            return false;

        return !empty($this->m_TabOptions[$dwOption]);
    }

    /**
     * @param $dwOption
     * @return bool
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
    public function getTabOptions()
    {
        return $this->m_TabOptions;
    }

    public function isWritable()
    {
        $forbidden_states = array(
            StructureColonne::OPTION_Hidden,
            StructureColonne::OPTION_ReadOnly,
            StructureColonne::OPTION_Disabled,
        );
        return !count(array_intersect($this->m_TabOptions, $forbidden_states));
    }

}
