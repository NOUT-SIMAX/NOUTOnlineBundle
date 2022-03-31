<?php
/**
 * Created by PhpStorm
 * User: ninon
 * Date: 25/03/2022 10:28
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\UserExists;

use NOUT\Bundle\NOUTOnlineBundle\Security\EncryptionType;

class UserExists
{
    public $nTypeUser = self::TYPEUTIL_NONE;
    public $clEncryptionType;

    /**
     * @param string      $sType le contenu de la requête pour l'existance de l'utilisateur
     * @param string|null $sEncodedBlowfish
     * @param string|null $sIV
     * @param string|null $sDefaultEncryption
     */
    public function __construct(string $sType, ?string $sEncodedBlowfish, ?string $sIV, ?string $sDefaultEncryption)
    {
        $this->nTypeUser=(int)$sType;
        //par défaut
        if (is_null($sDefaultEncryption)){
            $this->clEncryptionType=new EncryptionType(EncryptionType::MD5, EncryptionType::OPT_EmptyNoHash, false);
        }
        else {
            $this->clEncryptionType=new EncryptionType($sDefaultEncryption, 0, true);
        }
        //init
        $this->clEncryptionType->initFromBlowfish($sEncodedBlowfish, $sIV);
    }



    const TYPEUTIL_NONE        = 0;
    const TYPEUTIL_UTILISATEUR = 1;
    const TYPEUTIL_SUPERVISEUR = 2;
}