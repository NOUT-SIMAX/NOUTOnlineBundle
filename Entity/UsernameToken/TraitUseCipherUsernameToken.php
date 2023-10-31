<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Entity\UsernameToken;


use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\PwdInfo;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Encryption;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

trait TraitUseCipherUsernameToken
{
    protected function _crypt(Encryption $encryption, $txt, $sPassPhrase, $nonce, $created) : string
    {
        /* Create key */
        $securePassPhrase = base64_encode(md5($nonce.$created.$sPassPhrase, true));

        switch ($encryption->_)
        {
            case 'sso':
            case 'sso_bf':
            case 'blowfish':
                $cipher = 'bf-cbc';
                $encryption->ks = 16;
                break;
            case 'sso_aes':
            case 'aes':
                if (strlen($securePassPhrase)>=32) {
                    $encryption->ks = 32;
                }
                elseif (strlen($securePassPhrase)>=24) {
                    $encryption->ks = 24;
                }
                else {
                    $encryption->ks = 16;
                }
                $cipher='aes-'.($encryption->ks*8).'-cbc';
                break;
        }

        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $encryption->iv = base64_encode($iv);
        $encryption->md5 = base64_encode(md5($sPassPhrase.$nonce.$created, true));

        $key = substr($securePassPhrase, 0, $encryption->ks);

        #PKCS #7 padding scheme by default
        return openssl_encrypt($txt, $cipher, $key, 0, $iv);
    }

    /**
     * @param string $pwd
     * @param string $iv
     * @param int    $ks
     * @param string $sPassPhrase
     * @return string
     */
    protected function _decryptConnectedUser(PwdInfo $pwdInfo, string $sPassPhrase) : string
    {
        $iv = base64_decode($pwdInfo->getIV());
        switch ($pwdInfo->getCipher())
        {
            default:
            case 'bf':
                $cipherAlgo = 'bf-cbc';
                $securePassPhrase = base64_encode(md5($sPassPhrase, true));
                break;
            case 'aes':
                $cipherAlgo = 'aes-'.($pwdInfo->getKS()*8).'-cbc';
                $securePassPhrase = base64_encode(hash('sha256', $sPassPhrase, true));
                break;

        }

        //on coupe la clé à la bonne taille
        $key = substr($securePassPhrase, 0, $pwdInfo->getKS());
        $password = openssl_decrypt($pwdInfo->getPwd(), $cipherAlgo, $key, 0, $iv);
        if (!$password)
        {
            throw new BadCredentialsException('Error while login : '.openssl_error_string());
        }

        return $password;
    }
}
