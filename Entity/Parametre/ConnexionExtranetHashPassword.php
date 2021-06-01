<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre;


use NOUT\Bundle\NOUTOnlineBundle\Entity\Langage;

class ConnexionExtranetHashPassword
{
    /**
     * @param string $sPassword
     * @param string $sTypeEncodage
     * @return string
     */
    static public function s_sHashPassword(string $sPassword, string $sTypeEncodage) : string
    {
        switch ($sTypeEncodage)
        {
            default:
            case Langage::PASSWORD_ENCODAGE_plaintext:
            case Langage::PASSWORD_ENCODAGE_sha1:
                return bin2hex(hash('sha1', $sPassword, true));
            case Langage::PASSWORD_ENCODAGE_sha256:
                return bin2hex(hash('sha256', $sPassword, true));
            case Langage::PASSWORD_ENCODAGE_md5:
                return bin2hex(hash('md5', $sPassword, true));
        }
    }

    /**
     * @param string $sPassword
     * @return string
     */
    static public function s_sHashPasswordSIMAX(string $sPassword) : string
    {
        //il faut encoder le mot de passe simax
        $sSecretSIMAX = ($sPassword == '') ? '00000000000000000000000000000000' : bin2hex(md5(  $sPassword,true ));
        return bin2hex(sha1($sSecretSIMAX, true));
    }
}