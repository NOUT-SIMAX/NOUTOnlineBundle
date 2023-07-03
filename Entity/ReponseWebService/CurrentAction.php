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
	protected $m_nIDTypeAction;

    /**
     * @var string
     */
    protected $m_nIDForm;

    /**
     * @var string
     */
    protected $m_userConfirmation;

    /** @var bool  */
    protected $m_isConfiguration = false;

    /**
     * CurrentAction constructor.
     * @param \SimpleXMLElement $clAction
     */
	public function __construct(\SimpleXMLElement $clAction)
	{
		$this->m_sID                = (string) $clAction;
		$this->m_sTitle             = (string) $clAction['title'];
		$this->m_nIDTypeAction      = (int)$clAction['typeAction'];
        $this->m_nIDForm            = (string) $clAction['actionForm'];
        $this->m_userConfirmation   = (string) $clAction['userConfirmation'];
        $this->m_isConfiguration   = ((int) ($clAction['isConfiguration'] ?? 0)) != 0;
	}

	/**
	 * @return int
	 */
	public function getIDTypeAction(): int
    {
		return $this->m_nIDTypeAction;
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
    public function getIDForm(): string
    {
        return $this->m_nIDForm;
    }

    /**
	 * @return string
	 */
	public function getTitle(): string
    {
		return $this->m_sTitle;
	}

    /**
     * @return string
     */
	public function getUserConfirmation(): string
    {
        return $this->m_userConfirmation;
    }

    /**
     * @return bool
     */
    public function isConfiguration(): bool
    {
        return $this->m_isConfiguration;
    }

}
