<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 04/08/14
 * Time: 16:55
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Record;


class ColonneRestriction
{
	const R_MAXLENGTH = 'maxLength';
	const R_ENUMERATION = 'enumeration';

	public $m_sTypeRestriction;
	public $m_ValeurRestriction;
} 