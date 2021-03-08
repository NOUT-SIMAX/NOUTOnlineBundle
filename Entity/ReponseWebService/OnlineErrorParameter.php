<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 28/07/14
 * Time: 14:36
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService;

class OnlineErrorParameter implements \JsonSerializable
{
	protected $m_nID;
	protected $m_sTitle;
	protected $m_sValue;

	public function __construct($nID, $sTitle, $sValue)
	{
		$this->m_nID    = (string) $nID;
		$this->m_sTitle = (string) $sTitle;
		$this->m_sValue = (string) $sValue;
	}

    public function jsonSerialize(): array
    {
        return array(
            'id'    => $this->m_nID,
            'title' => $this->m_sTitle,
            'value' => $this->m_sValue,
        );
    }

	/**
	 * @param mixed $m_nID
	 */
	public function setID($m_nID)
	{
		$this->m_nID = $m_nID;
	}

	/**
	 * @return mixed
	 */
	public function getID(): string
    {
		return $this->m_nID;
	}

    /**
     * @param string $m_sTitle
     */
	public function setTitle(string $m_sTitle)
	{
		$this->m_sTitle = $m_sTitle;
	}

	/**
	 * @return string
	 */
	public function getTitle(): string
    {
		return $this->m_sTitle;
	}

    /**
     * @param string $m_sValue
     */
	public function setValue(string $m_sValue)
	{
		$this->m_sValue = $m_sValue;
	}

	/**
	 * @return string
	 */
	public function getValue(): string
    {
		return $this->m_sValue;
	}
}
