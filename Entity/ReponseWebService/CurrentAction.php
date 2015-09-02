<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 18/07/14
 * Time: 17:30
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService;

/**
 * Class ResponseHeaderAction
 * @package NOUT\Bundle\NOUTOnlineBundle\Entity
 * contient les informations de l'action en cours
 */
class CurrentAction
{
	/**
	 * @var string
	 */
	protected $m_sID;

	/**
	 * @var string
	 */

	protected $m_sTitle;
	/**
	 * @var int
	 */
	protected $m_nTypeAction;

    /**
     * @var string
     */
    protected $m_nIDForm;

	/**
	 * @param $sID : identifiant de l'action
	 * @param $sTitle ; libellé de l'action
	 * @param int $nTypeAction : type de l'action, voir constante ci-dessus
	 */
	public function __construct(\SimpleXMLElement $clAction)
	{
		$this->m_sID         = (string) $clAction;
		$this->m_sTitle      = (string) $clAction['title'];
		$this->m_nTypeAction = (int) $clAction['typeAction'];
        $this->m_nIDForm     = (string) $clAction['actionForm'];
	}

	/**
	 * @return int
	 */
	public function getTypeAction()
	{
		return $this->m_nTypeAction;
	}

	/**
	 * @return string
	 */
	public function getID()
	{
		return $this->m_sID;
	}


    /**
     * @return string
     */
    public function getIDForm()
    {
        return $this->m_nIDForm;
    }

    /**
	 * @return string
	 */
	public function getTitle()
	{
		return $this->m_sTitle;
	}


	const ETYPEACTION_CREATE  = 2386;
	const ETYPEACTION_LIST    = 2388;
	const ETYPEACTION_SEARCH  = 2389;
	const ETYPEACTION_DISPLAY = 2390;
	const ETYPEACTION_MODIFY  = 2387;

	const ETYPEACTION_AFFTABRECAP      = 9338;
	const ETYPEACTION_AFFVUE           = 15135;
	const ETYPEACTION_CREATEFROM       = 5303;
	const ETYPEACTION_TRANSFORMIN      = 8720;
	const ETYPEACTION_PRINT            = 2392;
	const ETYPEACTION_DELETE           = 2391;
	const ETYPEACTION_STARTEXE         = 8684;
	const ETYPEACTION_ADDTO            = 3182;
	const ETYPEACTION_PARTICULARACTION = 2394;
}
