<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 28/07/14
 * Time: 14:20
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity;


class OnlineError
{
	protected $m_nCode;
	protected $m_nErreur;
	protected $m_nCategorie;
	protected $m_sMessage;
	protected $m_TabParametres;

	public function __construct($nCode, $nErreur, $nCategorie, $sMessage)
	{
		$this->m_nCode = (string)$nCode;
		$this->m_nCategorie = (int)$nCategorie;
		$this->m_nErreur = (int)$nErreur;
		$this->m_sMessage = (string)$sMessage;
	}

	public function AddParameter(OnlineErrorParameter $clError)
	{
		$this->m_TabParametres[]=$clError;
	}

	/**
	 * @param mixed $m_TabParametres
	 */
	public function setTabParametres($TabParametres)
	{
		$this->m_TabParametres = $TabParametres;
	}

	/**
	 * @return mixed
	 */
	public function getTabParametres()
	{
		return $this->m_TabParametres;
	}

	/**
	 * @param int $m_nCategorie
	 */
	public function setCategorie($nCategorie)
	{
		$this->m_nCategorie = $nCategorie;
	}

	/**
	 * @return int
	 */
	public function getCategorie()
	{
		return $this->m_nCategorie;
	}

	/**
	 * @param string $m_nCode
	 */
	public function setCode($nCode)
	{
		$this->m_nCode = $nCode;
	}

	/**
	 * @return string
	 */
	public function getCode()
	{
		return $this->m_nCode;
	}

	/**
	 * @param int $m_nErreur
	 */
	public function setErreur($nErreur)
	{
		$this->m_nErreur = $nErreur;
	}

	/**
	 * @return int
	 */
	public function getErreur()
	{
		return $this->m_nErreur;
	}

	/**
	 * @param string $m_sMessage
	 */
	public function setMessage($sMessage)
	{
		$this->m_sMessage = $sMessage;
	}

	/**
	 * @return string
	 */
	public function getMessage()
	{
		return $this->m_sMessage;
	}
} 