<?php
namespace NOUT\Bundle\NOUTOnlineBundle\OASIS;

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
		if (strlen($sPassword)>0 || $sPassword!=null )
			//$this->securePassword = base64_encode(md5($strPassword, true));
			$sSecurePassword = base64_encode(md5($sPassword,true));
			//$this->securePassword = base64_encode(md5($strPassword));
		else
			$sSecurePassword = 'AAAAAAAAAAAAAAAAAAAAAA==';
	

		$this->Created = date('r');
		$this->Nonce = base64_encode( microtime() );
		
		return base64_encode(sha1($this->Nonce.$this->Created.$sSecurePassword, true));
	}
/*
	protected function _sBase64($strHex)
	{
		$strBase64ColAlphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
	
		$sEncoded='';
		$nLng = strlen($strHex);

		$nChar=0;
		$nCpt=0;
		$dwVal=0;
		while($nChar<$nLng)
		{
			$dwVal=$dwVal<<8;
			
			$dwTemp = hexdec($strHex[$nChar].$strHex[$nChar+1]);
			
			$dwVal|=$dwTemp;
			$nCpt++;
			if($nCpt==3)
			{
				$nCpt=0;
				$tabPortionCode = array(
					(($dwVal>>18)&0x3F),
					(($dwVal>>12)&0x3F),
					(($dwVal>>6)&0x3F),
					(($dwVal)&0x3F)
				);
				//BYTE byPortionCode[4];
				//byPortionCode[0]=(BYTE)((dwVal>>18)&0x3F);
				//byPortionCode[1]=(BYTE)((dwVal>>12)&0x3F);
				//byPortionCode[2]=(BYTE)((dwVal>>6)&0x3F);
				//byPortionCode[3]=(BYTE)((dwVal)&0x3F);

				$dwVal=0;
				$sEncoded.=$strBase64ColAlphabet[$tabPortionCode[0]];
				$sEncoded.=$strBase64ColAlphabet[$tabPortionCode[1]];
				$sEncoded.=$strBase64ColAlphabet[$tabPortionCode[2]];
				$sEncoded.=$strBase64ColAlphabet[$tabPortionCode[3]];
			}		
		
			$nChar+=2;
		}

		// ajoute des '=' pour le padding
		if ($nCpt>0)
		{
			$dwVal=$dwVal<<(8*(3-$nCpt));

			$tabPortionCode = array(
					(($dwVal>>18)&0x3F),
					(($dwVal>>12)&0x3F),
					(($dwVal>>6)&0x3F),
					(($dwVal)&0x3F)
				);

			$sEncoded.=$strBase64ColAlphabet[$tabPortionCode[0]];
			
			if ($tabPortionCode[1]!=0)
				$sEncoded.=$strBase64ColAlphabet[$tabPortionCode[1]];
			else
				$sEncoded.='=';
				
			if ($tabPortionCode[2]!=0)
				$sEncoded.=$strBase64ColAlphabet[$tabPortionCode[2]];
			else
				$sEncoded.='=';
			
			if ($tabPortionCode[3]!=0)
				$sEncoded.=$strBase64ColAlphabet[$tabPortionCode[3]];
			else
				$sEncoded.='=';
		}
		return $sEncoded;
	}
*/
}
?>