<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 11/12/14
 * Time: 14:48
 */

namespace NOUT\Bundle\ContextesBundle\Entity;


class ActionResult
{
	/**
	 * @var string
	 */
	public  $ReturnType;

	/**
	 * @var mixed
	 */
	private $m_Data;


	/**
	 * @param string $sReturnType
	 */
	public function __construct($sReturnType)
	{
		$this->ReturnType = $sReturnType;
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


} 