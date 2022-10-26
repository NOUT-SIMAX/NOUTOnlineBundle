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
class CurrentRequest
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
     * @var string
     */
    protected $m_nIDForm;

    /**
     * CurrentAction constructor.
     * @param \SimpleXMLElement $clRequest
     */
	public function __construct(\SimpleXMLElement $clRequest)
	{
		$this->m_sID                = (string) $clRequest;
		$this->m_sTitle             = (string)$clRequest['title'];
        $this->m_nIDForm            = (string)$clRequest['form'];
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
}
