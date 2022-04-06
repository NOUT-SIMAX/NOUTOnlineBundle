<?php
/**
 * Created by PhpStorm
 * User: ninon
 * Date: 25/03/2022 11:02
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Security;

class EncryptionType
{
    private $m_sUuidLicence = '';
    private $m_id = '';

    private $m_bForExtranet=false;
    private $m_dwPassOptions;
    private $m_sTypeEncryption;

    public function __construct(string $sTypeEncryption, int $dwPassOptions, bool $bForExtranet)
    {
        $this->m_sTypeEncryption = $sTypeEncryption;
        $this->m_bForExtranet = $bForExtranet;
        $this->m_dwPassOptions = $dwPassOptions;
    }

    /**
     * pour la serialisation
     * @return array
     */
    public function forSerialization() : array
    {
        return [
            $this->m_sTypeEncryption,
            $this->m_dwPassOptions,
            $this->m_bForExtranet,
            $this->m_sUuidLicence,
            $this->m_id,
        ];
    }

    /**
     * pour l'init suivant à la deserialization
     * @param array $data
     */
    public function fromSerialization(array $data) : void
    {
        list($this->m_sTypeEncryption, $this->m_dwPassOptions, $this->m_bForExtranet, $this->m_sUuidLicence, $this->m_id) = $data;
    }

    /**
     * @param string|null $sEncodedBlowfish
     * @param string|null $sIV
     */
    public function initFromBlowfish(?string $sEncodedBlowfish, ?string $sIV)
    {
        if (!empty($sEncodedBlowfish))
        {
            if (!empty($sIV)){
                $sDecrypt = openssl_decrypt($sEncodedBlowfish, 'bf-cfb', hex2bin(self::BLOWFISHKEY), 0, base64_decode($sIV));
            }
            else {
                $sDecrypt = openssl_decrypt($sEncodedBlowfish, 'bf-cfb', hex2bin(self::BLOWFISHKEY));
            }

            if ($sDecrypt){
                $json = json_decode($sDecrypt);
                if ($json){
                    $this->m_sUuidLicence = $json->uuidlicence;
                    $this->m_id = $json->id;
                    switch ((int)$json->security)
                    {
                        case -1:
                            $this->m_sTypeEncryption = self::PLAINTEXT;
                            break;
                        case 0:
                            $this->m_sTypeEncryption = self::MD5;
                            break;
                        case 1:
                            $this->m_sTypeEncryption = self::SHA_1;
                            break;
                        case 2:
                            $this->m_sTypeEncryption = self::SHA_256;
                            break;
                    }
                }
            }
        }
    }

    /**
     * @param string|null $plaintext
     * @return string
     */
    protected function _sGetSaltedPassword(?string $plaintext) : string
    {
        $plaintextSalted='';
        if (!empty($plaintext)){
            if ($this->m_dwPassOptions & self::OPT_SALT_ID){
                $plaintextSalted.=$this->m_id;
            }
            if ($this->m_dwPassOptions & self::OPT_SALT_UUID){
                $plaintextSalted.=$this->m_sUuidLicence;
            }
            $plaintextSalted.=$plaintext;
        }
        return $plaintextSalted;
    }

    /**
     * retourne la valeur nohash en base64 ou hexa
     * @param bool $bBase64
     * @return string|null
     */
    protected static function _s_sGetNoHashVal(string $sTypeEncryption, bool $bBase64) : string
    {
        if ($bBase64)
        {
            switch ($sTypeEncryption)
            {
                case self::MD5:
                    return 'AAAAAAAAAAAAAAAAAAAAAA==';
                case self::SHA_1:
                    return 'AAAAAAAAAAAAAAAAAAAAAAAAAAA=';
                case self::SHA_256:
                    return 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=';
            }
        }

        switch ($sTypeEncryption)
        {
            case self::MD5:
                return '00000000000000000000000000000000';
            case self::SHA_1:
                return '0000000000000000000000000000000000000000';
            case self::SHA_256:
                return '0000000000000000000000000000000000000000000000000000000000000000';
        }

        return '';
    }


    /**
     * @param string $plaintextSalted
     * @return string
     */
    protected function _sGetPassword_GetTokenSession(string $plaintextSalted) : ?string
    {
        if (($this->m_sTypeEncryption == self::PLAINTEXT) && !$this->m_bForExtranet){
            return $plaintextSalted;
        }

        if (empty($plaintextSalted) && ($this->m_dwPassOptions & self::OPT_EmptyNoHash)){
            return self::_s_sGetNoHashVal($this->m_sTypeEncryption, true);
        }

        switch ($this->m_sTypeEncryption)
        {
            default:
            case self::PLAINTEXT:
            case self::MD5:
                return base64_encode(hash('md5', $plaintextSalted, true));
            case self::SHA_1:
                return base64_encode(hash('sha1', $plaintextSalted, true));
            case self::SHA_256:
                return base64_encode(hash('sha256', $plaintextSalted, true));
        }
    }

    protected function _sGetPassword_FctCnxExtranet(?string $plaintextSalted) : string
    {
        if (empty($plaintextSalted) && ($this->m_dwPassOptions & self::OPT_EmptyNoHash)){
            $ret = self::_s_sGetNoHashVal($this->m_sTypeEncryption==self::PLAINTEXT ? self::SHA_1 : $this->m_sTypeEncryption, false);
        }
        else {
            switch ($this->m_sTypeEncryption)
            {
                default:
                case self::PLAINTEXT:
                case self::SHA_1:
                    $ret = bin2hex(hash('sha1', $plaintextSalted, true));
                    break;
                case self::SHA_256:
                    $ret = bin2hex(hash('sha256', $plaintextSalted, true));
                    break;
                case self::MD5:
                    $ret = bin2hex(hash('md5', $plaintextSalted, true));
                    break;
            }
        }

        if (!$this->m_bForExtranet){
            //on a une couche supplémentaire sur l'encodage pour le mot de passe utilisateur SIMAXs
            $ret = bin2hex(sha1($ret, true));
        }

        return $ret;
    }

    /**
     * @param string|null $plaintext
     * @param bool        $bForFctCnxExtranet
     * @return string
     */
    public function sGetPassword(?string $plaintext, bool $bForFctCnxExtranet=false) : string
    {
        $salted = $this->_sGetSaltedPassword($plaintext);
        if ($bForFctCnxExtranet){
            return $this->_sGetPassword_FctCnxExtranet($salted);
        }
        return $this->_sGetPassword_GetTokenSession($salted);
    }

    const PLAINTEXT = 'plaintext';
    const MD5 = 'md5';
    const SHA_1 = 'sha-1';
    const SHA_256 = 'sha-2-256';

    protected const BLOWFISHKEY = 'c215ffb8f826dcc77f162350d89622b328653fab43fb4776c33a6be5171af3270f8420609fa2f4eb4d50d7b23c1232c28b59e244c7cc7357e50314254fd7cd3cd9d3329cefbd8cf7f820f9b1d8ddd746f4de6580104a34c9ccbf56ae76982821b4bf6a459ccedff0447f0f6a06a3f2d4bad3354d114b9531f7d20ac2b5d93e21';

    protected const OPT_SALT_ID = 0x01;
    protected const OPT_SALT_UUID = 0x02;
    public const OPT_EmptyNoHash = 0x04;
}