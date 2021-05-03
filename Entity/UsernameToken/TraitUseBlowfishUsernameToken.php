<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Entity\UsernameToken;


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
        return openssl_encrypt($txt, 'bf-cbc', $key, $options=0, $iv);
    }

    /**
     * @param string $pwd
     * @param string $iv
     * @param int    $ks
     * @param string $sPassPhrase
     * @return string
     */
    protected function _decryptConnectedUser(string $pwd, string $iv, int $ks, string $sPassPhrase) : string
    {
        $iv = base64_decode($iv);

        /* Create key */
        $securePassPhrase = base64_encode(md5($sPassPhrase, true));
        $key = substr($securePassPhrase, 0, $ks);

        $password = openssl_decrypt($pwd, 'bf-cbc', $key, 0, $iv);
        if ($password == false)
        {
            throw new BadCredentialsException('Error while login');
        }

        return $password;
    }
}