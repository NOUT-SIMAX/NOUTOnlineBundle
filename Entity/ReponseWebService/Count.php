<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 07/08/14
 * Time: 14:58
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService;

/*
<Count>
<NbCalculation>0</NbCalculation>
<NbLine>24</NbLine>
<NbFiltered>24</NbFiltered>
<NbTotal>24</NbTotal>
</Count>
*/
class Count
{
	public $m_nNbCalculation;
	public $m_nNbLine;
	public $m_nNbFiltered;
	public $m_nNbTotal;
	public $m_nNbDisplay;

	public function __construct()
	{
		$this->m_nNbCalculation = 0;
		$this->m_nNbLine       = 0;
		$this->m_nNbFiltered   = 0;
		$this->m_nNbTotal      = 0;
		$this->m_nNbDisplay    = 0;
	}

	public function getNbEnregEtRupture() : int
    {
        return $this->m_nNbLine-$this->m_nNbCalculation;
    }
}
