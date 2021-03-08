<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 15/12/14
 * Time: 10:29
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity;

use Symfony\Component\HttpFoundation\Response;

class ActionResultCache
{
	/**
	 * @var string
	 */
	private $m_eTypeCache;

	/**
	 * @var \DateTime|null
	 */
	private $m_clExpires;

	/**
	 * @var integer
	 */
	private $m_nMaxAge;

	/**
	 * @var integer
	 */
	private $m_nSharedMaxAge;

	/**
	 * @var string
	 */
	private $m_sETAG;

	/**
	 * @var \DateTime
	 */
	private $m_lastModified;

	public function __construct()
	{
		$this->m_eTypeCache = self::TYPECACHE_None;
		$this->m_clExpires = null;
		$this->m_nMaxAge = 0;
		$this->m_nSharedMaxAge = 0;
		$this->m_sETAG = '';
		$this->m_lastModified = null;
	}

	/**
	 * @param Response $response
	 * @return Response
	 */
	public function InitResponseCache(Response $response): Response
    {
		if ($this->m_eTypeCache == self::TYPECACHE_Public)
			$response->setPublic();
		else if ($this->m_eTypeCache == self::TYPECACHE_Private)
			$response->setPrivate();

		if (!empty($this->m_clExpires))
			$response->setExpires($this->m_clExpires);

		if (!empty($this->m_nMaxAge))
			$response->setMaxAge($this->m_nMaxAge);

		if (!empty($this->m_nSharedMaxAge))
			$response->setSharedMaxAge($this->m_nSharedMaxAge);

		if (!empty($this->m_lastModified))
			$response->setLastModified($this->m_lastModified);

		if (!empty($this->m_sETAG))
			$response->setEtag($this->m_sETAG);

		return $response;
	}

    /**
     * @param $eTypeCache string
     * @return $this;
     */
	public function setTypeCache(string $eTypeCache): ActionResultCache
    {
		if (in_array($eTypeCache, array(self::TYPECACHE_None, self::TYPECACHE_Private, self::TYPECACHE_Public)))
			$this->m_eTypeCache = $eTypeCache;

		return $this;
	}

	/**
	 * @param \DateTime $clExpires
	 * @return $this
	 */
	public function setExpires(\DateTime $clExpires): ActionResultCache
    {
		$this->m_clExpires = $clExpires;
		return $this;
	}

    /**
     * @param int $nMaxAge
     * @return $this
     */
	public function setMaxAge(int $nMaxAge): ActionResultCache
    {
		$this->m_nMaxAge = $nMaxAge;
		return $this;
	}

    /**
     * @param int $nSharedMaxAge
     * @return $this
     */
	public function setSharedMaxAge(int $nSharedMaxAge): ActionResultCache
    {
		$this->m_nSharedMaxAge = $nSharedMaxAge;
		return $this;
	}

    /**
     * @param string $sETAG
     * @return $this
     */
	public function setETAG(string $sETAG): ActionResultCache
    {
		$this->m_sETAG = $sETAG;
		return $this;
	}

	/**
	 * @param \DateTime $lastModified
	 * @return $this
	 */
	public function setLastModified(\DateTime $lastModified): ActionResultCache
    {
		$this->m_lastModified = $lastModified;
		return $this;
	}


	const TYPECACHE_None='none';
	const TYPECACHE_Private='private';
	const TYPECACHE_Public='public';
} 