<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Entity\UsernameToken;


use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\PwdInfo;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Encryption;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

trait TraitUseBlowfishUsernameToken
{
    protected function _crypt(Encryption $encryption, $txt, $sPassPhrase, $nonce, $created) : string
    {
        $ivlen = openssl_cipher_iv_length('bf-cbc');
        $iv = openssl_random_pseudo_bytes($ivlen);
        $encryption->iv = base64_encode($iv);
        $encryption->ks = 16;
        $encryption->md5 = base64_encode(md5($sPassPhrase.$nonce.$created, true));

        /* Create key */
        $securePassPhrase = base64_encode(md5($nonce.$created.$sPassPhrase, true));
        $key = substr($securePassPhrase, 0, $encryption->ks);

        #PKCS #7 padding scheme by default
        return openssl_encrypt($txt, 'bf-cbc', $key, 0, $iv);
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
                $cipher_algo = 'bf-cbc';
                $securePassPhrase = base64_encode(md5($sPassPhrase, true));
                break;
            case 'aes':
                $cipher_algo = 'aes-'.($pwdInfo->getKS()*8).'-cbc';
                $securePassPhrase = base64_encode(hash('sha256', $sPassPhrase, true));
                break;

        }

        //on coupe la clé à la bonne taille
        $key = substr($securePassPhrase, 0, $pwdInfo->getKS());
        $password = openssl_decrypt($pwdInfo->getPwd(), $cipher_algo, $key, 0, $iv);
        if (!$password)
        {
            throw new BadCredentialsException('Error while login : '.openssl_error_string());
        }

        return $password;
    }
}
