<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 29/08/14
 * Time: 17:38
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre;

class ConditionColonne
{

	protected $m_sColonne;
	protected $m_sCondition;
	protected $m_Valeur;

	public function __construct($colonne, $cond, $valeur)
	{
		$this->m_sColonne   = $colonne;
		$this->m_sCondition = $cond;
		$this->m_Valeur     = $valeur;
	}

	public function sToSOAP()
	{
		return '<Condition><CondCol>'.htmlspecialchars($this->m_sColonne).'</CondCol><CondType>'.$this->m_sCondition.'</CondType><CondValue>'.htmlspecialchars($this->m_Valeur).'</CondValue></Condition>';
	}


	//type de condition
	const COND_EQUAL               = 'Equal';
	const COND_DIFFERENT           = 'Different';
	const COND_LESS                = 'Less';
	const COND_LESSOREQUAL         = 'LessOrEqual';
	const COND_BETTER              = 'Better';
	const COND_BETTEROREQUAL       = 'BetterOrEqual';
	const COND_CONTAIN             = 'Contain';
	const COND_DONOTCONTAIN        = 'DoNotContain';
	const COND_BEGINWITH           = 'BeginWith';
	const COND_DONOTBEGINWITH      = 'DoNotBeginWith';
	const COND_ENDWITH             = 'EndWith';
	const COND_DONOTENDWITH        = 'DoNotEndWith';
	const COND_ISWITHIN            = 'IsWithin';
	const COND_WITHRIGHT           = 'WithRight';
	const COND_BEGINWITHWORDBYWORD = 'BeginWithWordByWord';
    const COND_MENUVISIBLE         = 'MenuVisible';
}
