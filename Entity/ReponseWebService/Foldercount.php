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
class Foldercount
{
    public $m_nNbReceived;
    public $m_nNbUnread;

    public function __construct()
    {
        $this->m_nNbReceived    = 0;
        $this->m_nNbUnread      = 0;
    }
}
