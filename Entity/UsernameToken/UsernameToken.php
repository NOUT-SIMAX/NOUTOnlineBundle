<?php
namespace NOUT\Bundle\NOUTOnlineBundle\Entity\UsernameToken;

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
		$this->Compute();
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
	    $this->Compute();
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
}
