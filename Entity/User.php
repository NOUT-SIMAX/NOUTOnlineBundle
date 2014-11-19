<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 18/11/14
 * Time: 11:34
 */

namespace NOUT\Bundle\NOUTSessionManagerBundle\Entity;


class User
{
	public $username;

	/**
	 * @param mixed $username
	 */
	public function setUsername($username)
	{
		$this->username = $username;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getUsername()
	{
		return $this->username;
	}


} 