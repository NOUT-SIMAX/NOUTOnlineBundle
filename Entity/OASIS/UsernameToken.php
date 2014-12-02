<?php
namespace NOUT\Bundle\NOUTOnlineBundle\Entity\OASIS;

use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\UsernameToken as WSDLUsernameToken;

/**
 * Classe UserNameToken
 * permetant de générer les info de connection pour la norme OASIS (username, password, nonce, et created)
 */
class UsernameToken extends WSDLUsernameToken
{
	protected $m_sClearPassword;

	public function __construct($sUsername, $sPassword)
	{		
		$this->Username = $sUsername;
		$this->m_sClearPassword = $sPassword;
		$this->Password = $this->_getCryptedPassword($sPassword);
	}

	/**
	 * @return bool
	 */
	public function bIsValid()
	{
		return !empty($this->Username);
	}

	/**
	 * @return mixed
	 */
	public function getCreated()
	{
		return $this->Created;
	}

	/**
	 * @return mixed
	 */
	public function getNonce()
	{
		return $this->Nonce;
	}

	/**
	 * @return string
	 */
	public function getPassword()
	{
		return $this->Password;
	}

	/**
	 * @return mixed
	 */
	public function getUsername()
	{
		return $this->Username;
	}
	
	/**
	 * fonction permettant de générer le mot de passe encrypter compatible avec la norme oasis.
	 * elle initialise au passage, le nonce et le created
	 *
	 * @param string $strPassword le mot de passe en encrypter
	 */
	protected function _getCryptedPassword($sPassword=null)
	{		
		if (!empty($sPassword))
			//$this->securePassword = base64_encode(md5($strPassword, true));
			$sSecurePassword = base64_encode(md5($sPassword,true));
			//$this->securePassword = base64_encode(md5($strPassword));
		else
			$sSecurePassword = 'AAAAAAAAAAAAAAAAAAAAAA==';
	

		$this->Created = date('r');
		$this->Nonce = base64_encode( microtime() );
		
		return base64_encode(sha1($this->Nonce.$this->Created.$sSecurePassword, true));
	}
}
?>