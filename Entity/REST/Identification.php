<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 05/12/14
 * Time: 17:53
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\REST;

use NOUT\Bundle\NOUTOnlineBundle\Entity\UsernameToken\UsernameToken;

class Identification
{
	/**
	 * @var UsernameToken
	 */
	public $m_clUsernameToken;
	/**
	 * @var string
	 */
	public $m_sTokenSession;
	/**
	 * @var string
	 */
	public $m_sIDContexteAction;
	/**
	 * @var bool
	 */
	public $m_bAPIUser;

	public function __construct()
	{
		$this->m_clUsernameToken   = null;
		$this->m_sTokenSession     = '';
		$this->m_sIDContexteAction = '';
		$this->m_bAPIUser          = false;
	}
}
