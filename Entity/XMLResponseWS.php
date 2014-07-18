<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 18/07/14
 * Time: 16:46
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity;

/**
 * Class XMLResponseWS
 * @package NOUT\Bundle\NOUTOnlineBundle\Entity
 *
 * class qui contient le SimpleXMLElement de la réponse du webservice
 */
class XMLResponseWS
{
	//noeud particulier
	protected $m_ndBody;
	protected $m_ndHeader;

	public function __construct($sXML)
	{
		$clEnvelope = simplexml_load_string($sXML);

		//calcul du nom du namespace de l'enveloppe
		$sNomNSSoap='';
		$tabNamespace = $clEnvelope->getNamespaces();
		$tabNomNamespace = array_keys($tabNamespace, 'http://www.w3.org/2003/05/soap-envelope');
		if (count($tabNomNamespace)>0)
			$sNomNSSoap = $tabNomNamespace[0];
		else
		{
			$tabNomNamespace = array_keys($tabNamespace, 'http://schemas.xmlsoap.org/soap/envelope/');
			if (count($tabNomNamespace)>0)
				$sNomNSSoap = $tabNomNamespace[0];
		}

		//on trouve le noeud header et le noeud body
		$this->m_ndHeader = $clEnvelope->children($sNomNSSoap, true)->Header;
		$this->m_ndBody = $clEnvelope->children($sNomNSSoap, true)->Body;

	}

	/**
	 * @return mixed
	 */
	public function sGetReturnType()
	{
		return $this->m_ndHeader->children()->ReturnType;
	}
	public function sGetActionContext()
	{
		return $this->m_ndHeader->children()->ActionContext;
	}

	public function sGetAction()
	{
		return $this->m_ndHeader->children()->ActionContext;
	}



	/**
	 * récupère le noeud xml dans la réponse
	 * @param string $sOperation : operation lancée
	 * @return SimpleXMLElement
	 */
	public function getNodeXML($sOperation)
	{
		return $this->m_ndBody->children()->{$sOperation.'Response'}->xml;
	}

} 