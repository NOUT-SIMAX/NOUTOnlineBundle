<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 29/08/14
 * Time: 17:55
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre;

class ConditionFileNPI
{
	protected $m_FileNPI;
	public function __construct()
	{
		$this->m_FileNPI = array();
	}

	public function EmpileCondition($colonne, $condition, $valeur)
	{
		$this->m_FileNPI[] = new ConditionColonne($colonne, $condition, $valeur);

		return $this;
	}

	public function EmpileOperateur($Op)
	{
		$this->m_FileNPI[] = new ConditionOperateur($Op);

		return $this;
	}

	public function sToSoap()
	{
		$TabCopyFileNPI = $this->m_FileNPI;
		$sSoap          = '';


		$clDummy = new ConditionOperateur(ConditionOperateur::OP_AND);

		$clElem = array_pop($TabCopyFileNPI);

		$nLast            = 0;
		$TabNbFilsCourant = array(0);
		$TabNbFilsAttendu = array(0);

		while (!is_null($clElem))
		{
			$TabNbFilsCourant[$nLast]++;

			if ($clElem instanceof ConditionColonne)
			{
				$sSoap .= $clElem->sToSOAP();
			}
			elseif ($clElem instanceof ConditionOperateur)
			{
				$sSoap .= $clElem->sOuvrirSOAP();
				$TabNbFilsCourant[] = 0;
				$TabNbFilsAttendu[] = (($clElem->getOperateur() == ConditionOperateur::OP_NOT) ? 1 : 2);
				$nLast++;
			}
			else
			{
				throw new \Exception("ConditionFileNPI ne doit contenir que des ConditionColonne ou des ConditionOperateur");
			}

			while ($TabNbFilsCourant[$nLast] == $TabNbFilsAttendu[$nLast])
			{
				$sSoap .= $clDummy->sFermeSOAP();
				array_pop($TabNbFilsCourant);
				array_pop($TabNbFilsAttendu);
				$nLast--;
			}
			$clElem = array_pop($TabCopyFileNPI);
		}

		return $sSoap;
	}
}
