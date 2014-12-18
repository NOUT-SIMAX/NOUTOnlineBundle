<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 02/10/14
 * Time: 14:13
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService;

class ChartAxis
{
	/**
	 * @var string
	 * identifiant de l'axe
	 */
	public $m_sID;
	/**
	 * @var string
	 * libelle de l'axe
	 */
	public $m_sLabel;
	/**
	 * @var bool
	 * axe de calcul
	 */
	public $m_bIsCalculation;

	public function __construct($sID = '', $sLabel = '', $bCalcultion = false)
	{
		$this->m_sID           = $sID;
		$this->m_sLabel        = $sLabel;
		$this->m_bIsCalculation = $bCalcultion;
	}
}
