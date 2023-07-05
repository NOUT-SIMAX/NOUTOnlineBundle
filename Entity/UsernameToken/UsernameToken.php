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
    final public function setClearPassword(string $password): UsernameToken
    {
        $this->_setClearPassword($password);
        return $this;
    }

    /**
     * Init Created and Nonce puis crypte
     * @throws \Exception
     */
    final public function Compute() : void
    {
        $this->Created = date('r');
        $this->Nonce   = bin2hex(random_bytes(20));

        $this->_Compute();
    }

    final public function transformForSOAP()
    {
        if ($this->Encryption instanceof Encryption)
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

    /**
     * @return string
     * @throws \Exception
     */
    public function sToRest() : string
    {
        $this->Compute(); //on fait le compute

        $sBottom = 'Username='.urlencode(utf8_decode($this->Username));
        $sBottom .= '&Password='.urlencode($this->Password); //pas la peine de décoder à cause des caractère utilisé
        $sBottom .= '&nonce='.urlencode(utf8_decode($this->Nonce));
        $sBottom .= '&created='.urlencode(utf8_decode($this->Created));

        if (!empty($this->Encryption))
        {
            $sBottom .= '&encryption=' . urlencode($this->Encryption->_);
            $sBottom .= '&md5=' . urlencode($this->Encryption->md5);
            if (!empty($this->Encryption->iv)){
                $sBottom .= '&iv=' . urlencode($this->Encryption->iv);
            }
            if (!empty($this->Encryption->ks)){
                $sBottom .= '&ks=' . urlencode($this->Encryption->ks);
            }
        }
        return $sBottom;
    }

    public function __clone()
    {
        if (is_object($this->Encryption)){
            $this->Encryption = clone $this->Encryption;
        }
    }
}

