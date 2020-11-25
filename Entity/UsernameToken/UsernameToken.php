<?php
namespace NOUT\Bundle\NOUTOnlineBundle\Entity\UsernameToken;

use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Encryption;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\UsernameToken as WSDLUsernameToken;

/**
 * Classe UserNameToken
 * permetant de générer les info de connection pour la norme OASIS (username, password, nonce, et created)

 */
abstract class UsernameToken extends WSDLUsernameToken implements UsernameTokenInterface
{
    public function __construct(string $sUsername='')
	{
        $this->Username = $sUsername;
	}

    /**
     * Crypte les différents éléments
     */
	abstract protected function _Compute() : void;

    /**
     * @param string $password
     */
	abstract protected function _setClearPassword(string $password) : void;

    /**
     * @param string $password
     * @return $this
     */
	final public function setClearPassword(string $password) {
        $this->_setClearPassword($password);
	    return $this;
    }

    /**
     * Init Created and Nonce puis crypte
     */
    final public function Compute() : void
    {
        $this->Created = date('r');
        $this->Nonce   = bin2hex(random_bytes(20));

        $this->_Compute();
    }

    final public function transformForSOAP()
    {
        if (!is_null($this->Encryption) && ($this->Encryption instanceof Encryption))
        {
            $encryption = $this->Encryption;
            $this->Encryption = [
                '!' => $encryption->_,
                'md5' => $encryption->md5,
                'ks' => $encryption->ks,
                'iv' => $encryption->iv
            ];
        }
    }

}
