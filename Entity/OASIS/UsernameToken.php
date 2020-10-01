<?php
namespace NOUT\Bundle\NOUTOnlineBundle\Entity\OASIS;

use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\UsernameToken as WSDLUsernameToken;

/**
 * Classe UserNameToken
 * permetant de générer les info de connection pour la norme OASIS (username, password, nonce, et created)
 * @property string cryptMd5
 * @property string cryptIV
 * @property string cryptKS
 * @property string $cryptMode
 */
class UsernameToken extends WSDLUsernameToken
{
    /**
     * @var string
     */
	protected $m_sClearPassword;

    /**
     * @var string
     */
    protected $m_sMode;

    /**
     * @var string
     */
    protected $m_sSecret;

    public function __construct($sUsername, $sPassword, $sMode, $sSecret)
	{
		$this->Username         = $sUsername;
		$this->m_sClearPassword = $sPassword;
        $this->m_sMode          = $sMode;

        $this->Created = date('r');
        $this->Nonce   = base64_encode(microtime());


        if (empty($this->m_sMode))
            $this->m_sMode='';

        $this->_setSecret($sSecret);
		$this->ComputeCryptedPassword();
	}

    protected function _setSecret($sSecret)
    {
        //utf8_decode => il nous faut du latin1 pour NOUTOnline
        $this->m_sSecret        = utf8_decode(trim(str_replace("\r", "", $sSecret)));
        $this->cryptMd5         = base64_encode(md5($this->m_sSecret.$this->Nonce.$this->Created, true));
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
     * @return bool
     */
	public function bCrypted()
    {
        return !empty($this->m_sMode);
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->m_sMode;
    }
	/**
	 * fonction permettant de générer le mot de passe encrypter compatible avec la norme oasis.
	 * elle initialise au passage, le nonce et le created
	 *
	 * @param string $strPassword le mot de passe en encrypter
	 */
	public function ComputeCryptedPassword()
	{
        switch($this->m_sMode)
        {
        default:
        case '':
            $this->_OASIS();
            break;
        case 'base64':
            $this->_Base64();
            break;
        case 'blowfish':
            $this->_Blowfish();
            break;
        }
	}

    /**
     * génère le mot de passe crypté pour OASIS (système d'identification par défaut)
     */
    protected function _OASIS()
    {
        if (!empty($this->m_sClearPassword))
        {
            $sSecurePassword = base64_encode(md5($this->m_sClearPassword, true));
        }
        else
        {
            $sSecurePassword = 'AAAAAAAAAAAAAAAAAAAAAA==';
        }

        $this->Password = base64_encode(sha1($this->Nonce.$this->Created.$sSecurePassword, true));
    }

    protected function _Base64()
    {
        $sSecret = base64_encode(md5($this->Nonce.$this->m_sSecret.$this->Created, true));

        $securePassword = base64_encode($sSecret.$this->m_sClearPassword);
        $this->Password = $securePassword;
    }

    protected function _Blowfish()
    {
        if (extension_loaded('openssl')){
            $this->__Blowfish_openssl();
        }
        else {
            $this->__Blowfish_mcrypt();
        }
    }

    protected function __Blowfish_openssl()
    {
        $securePassword = base64_encode($this->Nonce.$this->Created.$this->m_sSecret);

        $ivlen = openssl_cipher_iv_length('bf-cbc');
        $iv = openssl_random_pseudo_bytes($ivlen);

        /* Create key */
        $key = substr($securePassword, 0, 16);
        $this->cryptKS = 16;

        $ciphertext = openssl_encrypt($this->m_sClearPassword, 'bf-cbc', $key, $options=0, $this->cryptIV, $tag);

        $this->cryptIV = base64_encode($iv);
        $this->Password = base64_encode($ciphertext);

    }

    protected function __Blowfish_mcrypt()
    {
        $securePassword = base64_encode($this->Nonce.$this->Created.$this->m_sSecret);

        /* Open the cipher */
        $aMode = array('blowfish'=>MCRYPT_BLOWFISH);

        $td = mcrypt_module_open($aMode[$this->cryptMode], '', 'cbc', '');

        /* Create the IV and determine the keysize length, use MCRYPT_RAND
         * on Windows instead */
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        $this->cryptIV = base64_encode($iv);
        $this->cryptKS = mcrypt_enc_get_key_size($td);

        /* Create key */
        $key = substr($securePassword, 0, $this->cryptKS);

        /* Intialize encryption */
        mcrypt_generic_init($td, $key, $iv);

        /* Encrypt data */
        $this->Password = base64_encode(mcrypt_generic($td, $this->m_sClearPassword));

        /* Terminate encryption handler */
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
    }
}
