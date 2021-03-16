<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 29/08/14
 * Time: 17:55
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre;

use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\Condition\CondColumn;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\Condition\Condition;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\Condition\CondType;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\Condition\CondValue;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\Operator\Operator;

class ConditionFileNPI
{
	protected $m_FileNPI;
	public function __construct()
	{
		$this->m_FileNPI = array();
	}

	public function EmpileCondition($colonne, $condition, $valeur): ConditionFileNPI
    {

		$this->m_FileNPI[] = new Condition(
		    new CondColumn($colonne),
            new CondType($condition),
            new CondValue($valeur)
        );
		return $this;
	}

	public function EmpileOperateur($Op): ConditionFileNPI
    {
		$this->m_FileNPI[] = new Operator($Op);

		return $this;
	}

    /**
     * @return string
     * @throws \Exception
     */
	public function sToSoap(): string
    {
		$TabCopyFileNPI = $this->m_FileNPI;
		$sSoap          = '';


		$clDummy = new Operator(Operator::OP_AND);

		$clElem = array_pop($TabCopyFileNPI);

		$nLast            = 0;
		$TabNbFilsCourant = array(0);
		$TabNbFilsAttendu = array(0);

		while (!is_null($clElem))
		{
			$TabNbFilsCourant[$nLast]++;

			if ($clElem instanceof Condition)
			{
				$sSoap .= $clElem->sToSOAP();
			}
			elseif ($clElem instanceof Operator)
			{
				$sSoap .= $clElem->getOpeningTag();
				$TabNbFilsCourant[] = 0;
				$TabNbFilsAttendu[] = (($clElem->type == Operator::OP_NOT) ? 1 : 2);
				$nLast++;
			}
			else
			{
				throw new \Exception("ConditionFileNPI ne doit contenir que des Condition ou des Operator");
			}

			while ($TabNbFilsCourant[$nLast] == $TabNbFilsAttendu[$nLast])
			{
				$sSoap .= $clDummy->getClosingTag();
				array_pop($TabNbFilsCourant);
				array_pop($TabNbFilsAttendu);
				$nLast--;
			}
			$clElem = array_pop($TabCopyFileNPI);
		}

		return $sSoap;
	}
}
