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
	 * @var UsernameToken|null
	 */
	public $m_clUsernameToken = null;

    /**
     * @var string|null
     */
    public $m_sAuthToken = null;

	/**
	 * @var string
	 */
	public $m_sTokenSession='';
	/**
	 * @var string
	 */
	public $m_sIDContexteAction='';
	/**
	 * @var bool
	 */
	public $m_bAPIUser=false;

}
