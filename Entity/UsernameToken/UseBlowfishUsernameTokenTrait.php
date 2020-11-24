<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Entity\UsernameToken;


use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Encryption;

trait UseBlowfishUsernameTokenTrait
{
    protected function _crypt(Encryption $encryption, $txt, $secret, $nonce, $created) : string
    {
        $ivlen = openssl_cipher_iv_length('bf-cbc');
        $iv = openssl_random_pseudo_bytes($ivlen);

        $encryption->md5 = base64_encode(md5($secret.$nonce.$created, true));

        /* Create key */
        $securePassword = base64_encode(md5($nonce.$created.$secret, true));
        $key = substr($securePassword, 0, 16);
        $encryption->ks = 16;

        #PKCS #7 padding scheme by default
        $ciphertext = openssl_encrypt($txt, 'bf-cbc', $key, $options=0, $iv);

        $encryption->iv = base64_encode($iv);

        file_put_contents('D:\\Temp\\blowfish.json', json_encode([
            'txt' => $txt,
            'md5' => $encryption->md5,
            'iv' => bin2hex($iv),
            'key'=>$key,
            'cipher'=>$ciphertext
        ], JSON_PRETTY_PRINT));


        return $ciphertext;
    }
}