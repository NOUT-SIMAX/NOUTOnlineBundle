<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 05/12/14
 * Time: 17:53
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\REST;

use NOUT\Bundle\NOUTOnlineBundle\Entity\OASIS\UsernameToken;

class Identification
{
	/**
	 * @var \NOUT\Bundle\NOUTOnlineBundle\Entity\OASIS\UsernameToken
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

	/**
	 * @param UsernameToken $clUsernameToken
	 */
	public function __construct()
	{
		$this->m_clUsernameToken   = null;
		$this->m_sTokenSession     = '';
		$this->m_sIDContexteAction = '';
		$this->m_bAPIUser          = false;
	}
}
