<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 11/12/14
 * Time: 14:48
 */

namespace NOUT\Bundle\ContextesBundle\Entity;

use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS;

class ActionResult
{
	/**
	 * @var string
	 */
	public $ReturnType;

	/**
	 * @var mixed
	 */
	private $m_Data;

	/**
	 * @var string
	 */
	private $m_sIDContexte;

	/**
	 * @var string
	 */
	private $m_sLibelle;

	/**
	 * @var \NOUT\Bundle\ContextesBundle\Entity\ActionResultCache
	 */
	private $m_clCache;


	/**
	 * @param string $sReturnType
	 */
	public function __construct(XMLResponseWS $clReponseXML = null)
	{
		if (isset($clReponseXML))
		{
			$this->ReturnType    = $clReponseXML->sGetReturnType();
			$this->m_sIDContexte = $clReponseXML->sGetActionContext();
			$this->m_sLibelle    = $clReponseXML->clGetAction()->getTitle();
		}
		else
		{
			$this->ReturnType    = null;
			$this->m_sIDContexte = '';
			$this->m_sIDContexte = '';
		}

		$this->m_Data     = null;
		$this->m_clCache  = new ActionResultCache();
	}

	/**
	 * @param string $sReturnType
	 * @return $this
	 */
	public function setReturnType($sReturnType)
	{
		$this->ReturnType = $sReturnType;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getReturnType()
	{
		return $this->ReturnType;
	}

	/**
	 * @param $data
	 * @return $this
	 */
	public function setData($data)
	{
		$this->m_Data = $data;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getData()
	{
		return $this->m_Data;
	}

	/**
	 * @return string
	 */
	public function getIDContexte()
	{
		return $this->m_sIDContexte;
	}

	/**
	 * @return string
	 */
	public function getLibelle()
	{
		return $this->m_sLibelle;
	}


	/**
	 * @param $eTypeCache
	 * @return $this
	 */
	public function setTypeCache($eTypeCache)
	{
		$this->m_clCache->setTypeCache($eTypeCache);

		return $this;
	}

	/**
	 * @param \DateTime $clExpires
	 * @return $this
	 */
	public function setExpires(\DateTime $clExpires)
	{
		$this->m_clCache->setExpires($clExpires);

		return $this;
	}

	/**
	 * @param int $nMaxAge
	 * @return $this
	 */
	public function setMaxAge($nMaxAge)
	{
		$this->m_clCache->setMaxAge($nMaxAge);

		return $this;
	}

	/**
	 * @param int $nSharedMaxAge
	 * @return $this
	 */
	public function setSharedMaxAge($nSharedMaxAge)
	{
		$this->m_clCache->setSharedMaxAge($nSharedMaxAge);

		return $this;
	}

	/**
	 * @param string $sETAG
	 * @return $this
	 */
	public function setETAG($sETAG)
	{
		$this->m_clCache->setETAG($sETAG);

		return $this;
	}

	/**
	 * @param \DateTime $lastModified
	 * @return $this
	 */
	public function setLastModified(\DateTime $lastModified)
	{
		$this->m_clCache->setLastModified($lastModified);

		return $this;
	}

	/**
	 * @return ActionResultCache
	 */
	public function getCache()
	{
		return $this->m_clCache;
	}
}
