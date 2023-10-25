<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService;


class PwdInfo
{
    protected string $sPwdEncoded;

    protected string $sIV;

    protected int $nKS;

    protected string $cipher;

    public function __construct(\SimpleXMLElement $pwd, \SimpleXMLElement $iv, \SimpleXMLElement $ks, ?\SimpleXMLElement $cipher)
    {
        $this->sPwdEncoded = (string)$pwd;
        $this->sIV         = (string)$iv;
        $this->nKS       = (int)$ks;
        if (!is_null($cipher)){
            $this->cipher = (string)$cipher;
        }
        else {
            $this->cipher = 'bf';
        }
    }

    /**
     * @return string|null
     */
    public function getPwd(): ?string
    {
        return $this->sPwdEncoded;
    }

    /**
     * @return string|null
     */
    public function getIV(): ?string
    {
        return $this->sIV;
    }

    /**
     * @return int|null
     */
    public function getKS(): ?int
    {
        return $this->nKS;
    }

    public function getCipher() : string
    {
        return $this->cipher;
    }


}
