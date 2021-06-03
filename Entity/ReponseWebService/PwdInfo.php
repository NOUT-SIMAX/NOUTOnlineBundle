<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService;


class PwdInfo
{
    /**
     * @var string
     */
    protected $m_sPwdEncoded;
    /**
     * @var string
     */
    protected $m_sIV;
    /**
     * @var int
     */
    protected $m_nKS;

    public function __construct($pwd, $iv, $ks)
    {
        $this->m_sPwdEncoded = (string)$pwd;
        $this->m_sIV = (string)$iv;
        $this->m_nKS = (int)$ks;
    }

    /**
     * @return string|null
     */
    public function getPwd(): ?string
    {
        return $this->m_sPwdEncoded;
    }

    /**
     * @return string|null
     */
    public function getIV(): ?string
    {
        return $this->m_sIV;
    }

    /**
     * @return int|null
     */
    public function getKS(): ?int
    {
        return $this->m_nKS;
    }
}