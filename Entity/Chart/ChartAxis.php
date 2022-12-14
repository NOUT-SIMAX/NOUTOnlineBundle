<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 02/10/14
 * Time: 14:13
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Chart;

class ChartAxis
{
	/**
	 * @var string
	 * identifiant de l'axe
	 */
	protected $m_sID;
	/**
	 * @var string
	 * libelle de l'axe
	 */
    protected $m_sLabel;
	/**
	 * @var bool
	 * axe de calcul
	 */
    protected $m_bIsCalculation;

    /**
     * ChartAxis constructor.
     * @param string $sID
     * @param string $sLabel
     * @param false  $bCalcultion
     */
	public function __construct(string $sID = '', string $sLabel = '', bool $bCalcultion = false)
	{
		$this->m_sID           = $sID;
		$this->m_sLabel        = $sLabel;
		$this->m_bIsCalculation = $bCalcultion;
	}

    /**
     * @return string
     */
	public function getID(): string
    {
        return $this->m_sID;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->m_sLabel;
    }

    /**
     * @return bool
     */
    public function isCalculation(): bool
    {
        return $this->m_bIsCalculation;
    }
}
