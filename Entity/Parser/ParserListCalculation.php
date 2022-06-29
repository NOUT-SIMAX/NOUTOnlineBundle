<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 05/08/2015
 * Time: 16:34
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Parser;


use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\Calculation;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS;

class ParserListCalculation extends Parser
{
	/**
	 * @var array
	 * map qui associe une colonne à un calcul de fin de liste
	 */
	public $m_MapColonne2Calcul;

    /**
     * @param XMLResponseWS $clXMLReponseWS
     */
	public function Parse(XMLResponseWS $clXMLReponseWS, $idForm)
	{
        $ndXML = $clXMLReponseWS->getNodeXML();

		/*
			<col simax:id="1171">
				<sum/>
				<average/>
				<min/>
				<max/>
				<count>24</count>
			</col>
		*/
		$this->m_MapColonne2Calcul = array();

		//ne pas mettre empty car ce n'est pas un array mais un \SimpleXMLElement et empty ne marche pas dessus
		if (count($ndXML->children())>0)
		{
			foreach ($ndXML->children() as $ndCol)
			{
				$clCalculation = new Calculation((string) $ndCol->attributes(self::NAMESPACE_NOUT_XML)['id']);
				foreach ($ndCol->children() as $ndCalcul)
				{
					$clCalculation->AddCacul((string) $ndCalcul->getName(), (string) $ndCalcul);
				}

				$this->m_MapColonne2Calcul[$clCalculation->getIDColonne()] = $clCalculation;
			}
		}
	}
}