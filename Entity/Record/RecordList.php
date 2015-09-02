<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 02/09/2015
 * Time: 16:06
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Record;


/**
 * Class RecordList, Description d'une liste d'enregistrement
 *
 * @package NOUT\Bundle\NOUTOnlineBundle\Entity\Record
 */
class RecordList 
{
    /**
     * @var string $m_sTitle : contient la mini desc de l'action qui a retournée une liste
     */
    protected $m_sTitle;

    /**
     * @var Record $m_clRecordParam : contient l'enregistrement qui correspond à la fiche
     */
    protected $m_clRecordParam;
}