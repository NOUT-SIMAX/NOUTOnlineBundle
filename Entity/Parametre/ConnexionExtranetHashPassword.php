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
}