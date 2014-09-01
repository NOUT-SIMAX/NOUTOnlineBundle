<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 01/09/14
 * Time: 09:24
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Tests\SOAP;


use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\ConditionColonne;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\ConditionFileNPI;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\ConditionOperateur;

class ConditionFileNPITest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @group entity
	 */
	public function testConditionFileNPI()
	{
		$clFileNPI = new ConditionFileNPI();

		/*                                          (                                   <Operator type="ET">
		 * (                                            ET,                                 <Operator type="ET">
		 *      (                                       (                                       <Condition> .. M .. </Condition>
		 *           (                                      ET,                                     <Operator type="ET">
		 *               (A, B, ET),                        M,                                          <Operator type="ET">
		 *               (C, NOT),                          (                                               <Condition> .. L .. </Condition>
		 *               OU                                     ET,                                         <Condition> .. K .. </Condition>
		 *           ),                                         (ET, K, L),                             </Operator>
		 *           NOT                                        (ET, J, I),                             <Operator type="ET">
		 *      ),                                          )                                               <Condition> .. J .. </Condition>
		 *      (                                       ),                                                  <Condition> .. I .. </Condition>
		 *           D,                                 (                                           </Operator>
		 *           (                     =>               OU,                                 </Operator>
		 *               (E, F, OU),                        (                               </Operator>
		 *               (G, H, ET),                            OU,                         <Operator type="OU">
		 *                ET                                    (                               <Operator type="OU">
		 *           ),                                             ET,                             <Operator type="ET">
		 *           OU                                             (ET, H, G),                         <Operator type="ET">
		 *      ),                                                  (OU, F, E),                             <Condition> .. H .. </Condition>
		 *      OU                                               ),                                         <Condition> .. G .. </Condition>
		 * ),                                                    D                                      </Operator>
		 * (                                                ),                                          <Operator type="OU">
		 *      (                                           (                                               <Condition> .. F .. </Condition>
		 *          (I, J, ET),                                 NOT,                                        <Condition> .. E .. </Condition>
		 *          (K, L, ET),                                 (                                       </Operator>
		 *          ET                                              OU,                             </Operator>
		 *      ),                                                  (                               <Condition> .. D .. </Condition>
		 *      M,                                                      (NOT, C),               </Operator>
		 *      ET                                                      (ET, B, A)              <Operator type="NOT">
		 * ),                                                       )                               <Operator type="OU">
		 * ET                                                   )                                       <Operator type="NOT">
		 *                                                  )                                               <Condition> .. C .. </Condition>
		 *                                              )                                               </Operator>
		 *                                          )                                               <Operator type="ET">
		 *                                                                                              <Condition> .. B .. </Condition>
		 *                                                                                              <Condition> .. B .. </Condition>
		 *                                                                                          </Operator>
		 *                                                                                      </Operator>
		 *                                                                                  </Operator>
		 *                                                                              </Operator>
		 *
		 * => A, B, ET, C, NOT, OU, NOT, D, E, F, OU, G, H, ET, ET, OU, OU, I, J, ET, K, L, ET, ET, M, ET, ET
		 */

		$sResultatAttendu = <<<RESULTAT
<Operator type="AND">
	<Operator type="AND">
		<Condition><CondCol>M</CondCol><CondType>DoNotContain</CondType><CondValue>a</CondValue></Condition>
			<Operator type="AND">
				<Operator type="AND">
					<Condition><CondCol>L</CondCol><CondType>DoNotEndWith</CondType><CondValue>a</CondValue></Condition>
					<Condition><CondCol>K</CondCol><CondType>DoNotBeginWith</CondType><CondValue>a</CondValue></Condition>
				</Operator>
				<Operator type="AND">
					<Condition><CondCol>J</CondCol><CondType>Contain</CondType><CondValue>a</CondValue></Condition>
					<Condition><CondCol>I</CondCol><CondType>BeginWith</CondType><CondValue>a</CondValue></Condition>
			</Operator>
		</Operator>
	</Operator>
	<Operator type="OR">
		<Operator type="OR">
			<Operator type="AND">
				<Operator type="AND">
					<Condition><CondCol>H</CondCol><CondType>BeginWithWordByWord</CondType><CondValue>a</CondValue></Condition>
					<Condition><CondCol>G</CondCol><CondType>WithRight</CondType><CondValue>1</CondValue></Condition>
				</Operator>
				<Operator type="OR">
					<Condition><CondCol>F</CondCol><CondType>BetterOrEqual</CondType><CondValue>1</CondValue></Condition>
					<Condition><CondCol>E</CondCol><CondType>Better</CondType><CondValue>1</CondValue></Condition>
				</Operator>
			</Operator>
			<Condition><CondCol>D</CondCol><CondType>LessOrEqual</CondType><CondValue>1</CondValue></Condition>
		</Operator>
		<Operator type="NOT">
			<Operator type="OR">
				<Operator type="NOT">
					<Condition><CondCol>C</CondCol><CondType>Less</CondType><CondValue>1</CondValue></Condition>
				</Operator>
				<Operator type="AND">
					<Condition><CondCol>B</CondCol><CondType>Different</CondType><CondValue>1</CondValue></Condition>
					<Condition><CondCol>A</CondCol><CondType>Equal</CondType><CondValue>1</CondValue></Condition>
				</Operator>
			</Operator>
		</Operator>
	</Operator>
</Operator>
RESULTAT;

		//=> A, B, ET, C, NOT, OU, NOT, D, E, F, OU, G, H, ET, ET, OU, OU, I, J, ET, K, L, ET, ET, M, ET, ET

		$clFileNPI->EmpileCondition('A', ConditionColonne::COND_EQUAL, '1');
		$clFileNPI->EmpileCondition('B', ConditionColonne::COND_DIFFERENT, '1');
		$clFileNPI->EmpileOperateur(ConditionOperateur::OP_AND);
		$clFileNPI->EmpileCondition('C', ConditionColonne::COND_LESS, '1');
		$clFileNPI->EmpileOperateur(ConditionOperateur::OP_NOT);
		$clFileNPI->EmpileOperateur(ConditionOperateur::OP_OR);
		$clFileNPI->EmpileOperateur(ConditionOperateur::OP_NOT);
		$clFileNPI->EmpileCondition('D', ConditionColonne::COND_LESSOREQUAL, '1');
		$clFileNPI->EmpileCondition('E', ConditionColonne::COND_BETTER, '1');
		$clFileNPI->EmpileCondition('F', ConditionColonne::COND_BETTEROREQUAL, '1');
		$clFileNPI->EmpileOperateur(ConditionOperateur::OP_OR);
		$clFileNPI->EmpileCondition('G', ConditionColonne::COND_WITHRIGHT, '1');
		$clFileNPI->EmpileCondition('H', ConditionColonne::COND_BEGINWITHWORDBYWORD, 'a');
		$clFileNPI->EmpileOperateur(ConditionOperateur::OP_AND);
		$clFileNPI->EmpileOperateur(ConditionOperateur::OP_AND);
		$clFileNPI->EmpileOperateur(ConditionOperateur::OP_OR);
		$clFileNPI->EmpileOperateur(ConditionOperateur::OP_OR);
		$clFileNPI->EmpileCondition('I', ConditionColonne::COND_BEGINWITH, 'a');
		$clFileNPI->EmpileCondition('J', ConditionColonne::COND_CONTAIN, 'a');
		$clFileNPI->EmpileOperateur(ConditionOperateur::OP_AND);
		$clFileNPI->EmpileCondition('K', ConditionColonne::COND_DONOTBEGINWITH, 'a');
		$clFileNPI->EmpileCondition('L', ConditionColonne::COND_DONOTENDWITH, 'a');
		$clFileNPI->EmpileOperateur(ConditionOperateur::OP_AND);
		$clFileNPI->EmpileOperateur(ConditionOperateur::OP_AND);
		$clFileNPI->EmpileCondition('M', ConditionColonne::COND_DONOTCONTAIN, 'a');
		$clFileNPI->EmpileOperateur(ConditionOperateur::OP_AND);
		$clFileNPI->EmpileOperateur(ConditionOperateur::OP_AND);

		$sSoap = $clFileNPI->sToSoap();

		$this->assertEquals(str_replace(array("\t", "\n", "\r"), array("","",""), $sResultatAttendu), $sSoap);
	}
} 