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
    /** @var int  */
    public $nTypeUser = self::TYPEUTIL_NONE;
    /** @var EncryptionType  */
    public $clEncryptionType;
    /** @var bool  */
    public $bWithConfiguration = false;

    /**
     * @param string      $sType le contenu de la requête pour l'existance de l'utilisateur
     * @param string|null $sEncodedBlowfish
     * @param string|null $sIV
     * @param string|null $sDefaultEncryption
     */
    public function __construct(string $sType, ?string $sEncodedBlowfish, ?string $sIV, ?string $sDefaultEncryption)
    {
        $nType = (int)$sType;

        $this->nTypeUser=$nType & self::_MASQUE_TYPEUTIL;
        if (($nType & self::_TYPEUTIL_WITHCONFIGURATION) && ($this->nTypeUser==self::TYPEUTIL_SUPERVISEUR)){
            $this->bWithConfiguration = true;
        }
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

    protected const _TYPEUTIL_WITHCONFIGURATION = 0x10;
    protected const _MASQUE_TYPEUTIL = 0xf;
}