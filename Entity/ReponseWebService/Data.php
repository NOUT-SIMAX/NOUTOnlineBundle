<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 01/09/14
 * Time: 15:53
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService;

class Data
{
    public $m_sMimeType;
    public $m_nSize;
    public $m_sFileName;
    public $m_sEncoding;
    public $m_nRef;
    public $m_sContent;

    public function __construct()
    {
        $this->m_nRef      = 0;
        $this->m_nSize     = 0;
        $this->m_sFileName = '';
        $this->m_sEncoding = '';
        $this->m_sMimeType = '';
        $this->m_sContent  = '';
    }

    public function sGetRaw()
    {
        switch ($this->m_sEncoding)
        {
        case self::ENC_BASE64:
            return base64_decode($this->m_sContent);

        case self::ENC_QUOTEDPRINTABLE:
        {
            //windows-1252 ou latin-1 => quoted-printable => utf-8

            //str_replace : hack pour problème encodage euro
            $sToReturn = mb_convert_encoding(
                quoted_printable_decode($this->m_sContent)
                , 'UTF-8'
                , 'ISO-8859-1'
            );
            return $sToReturn;
        }

        case self::ENC_BINARY:
        default:
        case self::ENC_7BIT:
        case self::ENC_8BIT:
        case self::ENC_PLAIN:
            return $this->m_sContent;
        }
    }

    const ENC_BASE64            = 'base64';
    const ENC_7BIT              = '7bit';
    const ENC_8BIT              = '8bit';
    const ENC_BINARY            = 'binary';
    const ENC_QUOTEDPRINTABLE   = 'quoted-printable';
    const ENC_PLAIN             = 'plain';
}
