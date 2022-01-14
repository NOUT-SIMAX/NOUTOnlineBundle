<?php
/**
 * Created by PhpStorm
 * User: ninon
 * Date: 12/01/2022 11:23
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Messaging;

class MailServiceStatus
{
    /** @var int  */
    public $nbMaxUnreadUrgent = 0;

    /** @var int  */
    public $nbMaxUnread = 0;

    /** @var int  */
    public $nbUnreadUrgent = 0;

    /** @var int  */
    public $nbUnread = 0;

    /** @var int  */
    public $nbReceive = 0;

    /** @var string  */
    public $LastUnread = '';
}