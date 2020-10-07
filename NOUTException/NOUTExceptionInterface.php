<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 02/03/2017
 * Time: 09:40
 */

namespace NOUT\Bundle\NOUTOnlineBundle\NOUTException;

interface NOUTExceptionInterface
{

    /**
     * @return int
     */
    public function getLevel();

    /**
     * @return int
     */
    public function getStatus();
}