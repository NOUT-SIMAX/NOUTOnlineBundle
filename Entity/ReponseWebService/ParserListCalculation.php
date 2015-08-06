<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 05/08/2015
 * Time: 16:34
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService;


class ParserListCalculation extends Parser
{
	/**
	 * @var array
	 * map qui associe une colonne à un calcul de fin de liste
	 */
	public $m_MapColonne2Calcul;

	public function Parse(\SimpleXMLElement $ndXML)
	{
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