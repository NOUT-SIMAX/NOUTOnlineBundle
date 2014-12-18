<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 29/08/14
 * Time: 17:45
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre;

class ConditionOperateur
{

	protected $m_sOperateur;

	public function __construct($Op)
	{
		$this->m_sOperateur = $Op;
	}

	/**
	 * @return string
	 */
	public function getOperateur()
	{
		return $this->m_sOperateur;
	}

	public function sOuvrirSOAP()
	{
		return '<Operator type="'.$this->m_sOperateur.'">';
	}

	public function sFermeSOAP()
	{
		return '</Operator>';
	}

	const OP_AND = 'AND';
	const OP_OR  = 'OR';
	const OP_NOT = 'NOT';
}
