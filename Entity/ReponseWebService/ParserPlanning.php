<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 05/08/2015
 * Time: 16:22
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService;


/*
 * Parser pour le VIEUX planning (ancien site)
 */

class ParserPlanning extends Parser
{

	/**
	 * @var array
	 * tableau qui contient les événements du planning
	 */
	public $m_MapTypeEvent2Color;

	/**
	 * @var array
	 * tableau qui contient les événements du planning
	 */
	public $m_TabEventPlanning;

	public function TypeEvent2Color(\SimpleXMLElement $clSchema)
	{
		$ndLayout = $clSchema->element->children(self::NAMESPACE_NOUT_XSD)->layout;

		$this->m_MapTypeEvent2Color = array();

		//ne pas mettre empty car ce n'est pas un array mais un \SimpleXMLElement et empty ne marche pas dessus
		if (count($ndLayout->children(self::NAMESPACE_XSD))>0)
		{
			foreach ($ndLayout->children(self::NAMESPACE_XSD) as $ndFils)
			{
				if (strcmp($ndFils->getName(), 'element') == 0)
				{
					$TabAttributes = $ndFils->attributes(self::NAMESPACE_NOUT_XSD);
					$this->m_MapTypeEvent2Color[(string) $TabAttributes['typeOfEvent']] = (string) $TabAttributes['colorRGB'];
				}
			}
		}
	}


	public function Parse(\SimpleXMLElement $clXML)
	{
		//ne pas mettre empty car ce n'est pas un array mais un \SimpleXMLElement et empty ne marche pas dessus
		if (count($clXML->children())>0)
		{
			foreach ($clXML->children() as $clNoeud)
			{
				$sTagName = $clNoeud->getName();
				if (strcmp($sTagName, 'event') == 0)
				{
					$this->_ParseEvent($clNoeud);
					continue;
				}
			}
		}
	}

	protected function _ParseEvent(\SimpleXMLElement $ndEvent)
	{
		/*
		<xs:attribute xs:name="simax:uid" xs:use="required" simax:typeElement="xs:string"/>
		<xs:attribute xs:name="simax:startTime" xs:use="required" simax:typeElement="xs:datetime"/>
		<xs:attribute xs:name="simax:endTime" simax:typeElement="xs:datetime"/>
		<xs:attribute xs:name="simax:summary" xs:use="required" simax:typeElement="xs:string"/>
		<xs:attribute xs:name="simax:description" simax:typeElement="xs:string"/>
		<xs:attribute xs:name="simax:resource" xs:use="required" simax:typeElement="xs:string"/>
		<xs:attribute xs:name="simax:typeOfEvent" xs:use="required" simax:typeElement="xs:string"/>
		<xs:attribute xs:name="simax:rrules" simax:typeElement="xs:string"/>
		*/

		$TabAttrib = $ndEvent->attributes(self::NAMESPACE_NOUT_XML);

		$clEvent                 = new Event();
		$clEvent->m_sUID         = (string) $TabAttrib['uid'];
		$clEvent->m_sStartTime   = (string) $TabAttrib['startTime'];
		$clEvent->m_sEndTime     = (string) $TabAttrib['endTime'];
		$clEvent->m_sSummary     = (string) $TabAttrib['summary'];
		$clEvent->m_sDescription = (string) $TabAttrib['description'];
		$clEvent->m_nIDResource  = (string) $TabAttrib['resource'];
		$clEvent->m_nTypeOfEvent = (string) $TabAttrib['typeOfEvent'];
		$clEvent->m_sRrules      = (string) $TabAttrib['rrules'];
		$clEvent->m_sColorRGB    = $this->m_MapTypeEvent2Color[$clEvent->m_nTypeOfEvent];

		$this->m_TabEventPlanning[$clEvent->m_sUID] = $clEvent;
	}
}