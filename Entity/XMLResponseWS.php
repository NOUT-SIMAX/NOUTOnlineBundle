<?php
/**
 * Created by PhpStorm.
 * User: Ninon <ninon@nout.fr>
 * Date: 18/07/14
 * Time: 16:46
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity;

use NOUT\Bundle\NOUTOnlineBundle\Entity\CurrentAction;

/**
 * Class XMLResponseWS
 * @package NOUT\Bundle\NOUTOnlineBundle\Entity
 *
 * class qui contient le SimpleXMLElement de la réponse du webservice
 */
class XMLResponseWS
{
	//réponse générique
	const RETURNTYPE_EMPTY = 'Empty';
	const RETURNTYPE_REPORT = 'Report';
	const RETURNTYPE_VALUE = 'Value';
	const RETURNTYPE_RECORD = 'Record';
	const RETURNTYPE_LIST = 'List';
	const RETURNTYPE_REQUESTFILTER = 'RequestFilter';
	const RETURNTYPE_CHART = 'Chart';
	const RETURNTYPE_NUMBEROFCHART = 'NumberOfChart';

	//réponse particulière
	const RETURNTYPE_XSD = 'XSD';
	const RETURNTYPE_IDENTIFICATION = 'Identification';
	const RETURNTYPE_PLANNING = 'Planning';
	const RETURNTYPE_GLOBALSEARCH = 'GlobalSearch';
	const RETURNTYPE_LISTCALCULATION = 'ListCalculation';

	//réponse intermédiaire
	const RETURNTYPE_AMBIGUOUSACTION = 'AmbiguousAction';
	const RETURNTYPE_MESSAGEBOX = 'MessageBox';
	const RETURNTYPE_VALIDATEACTION = 'ValidateAction';
	const RETURNTYPE_VALIDATERECORD = 'ValidateRecord';
	const RETURNTYPE_PRINTTEMPLATE = 'PrintTemplate';


	//réponse de messagerie
	const RETURNTYPE_MAILSERVICERECORD = 'MailServiceRecord';
	const RETURNTYPE_MAILSERVICELIST = 'MailServiceList';
	const RETURNTYPE_MAILSERVICESTATUS = 'MailServiceStatus';
	const RETURNTYPE_WITHAUTOMATICRESPONSE = 'WithAutomaticResponse';


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
	 * renvoi le type de retour
	 * @return string
	 */
	public function sGetReturnType()
	{
		return (string)$this->m_ndHeader->children()->ReturnType;
	}

	/**
	 * renvoi l'identifiant du contexte d'action
	 * @return string
	 */
	public function sGetActionContext()
	{
		return (string)$this->m_ndHeader->children()->ActionContext;
	}

	/**
	 * @return CurrentAction : action en cours
	 */
	public function clGetAction()
	{
		$clAction = $this->m_ndHeader->children()->Action;
		return new CurrentAction($clAction, $clAction['title'], $clAction['typeAction']);
	}

	/**
	 * @return ConnectedUser : utilisateur actuellement connecté
	 */
	public function clGetConnectedUser()
	{
		$clConnectedUser = $this->m_ndHeader->children()->ConnectedUser;
		return new ConnectedUser(
			$clConnectedUser->children()->Element,
			$clConnectedUser->children()->Element['title'],
			$clConnectedUser->children()->Form,
			$clConnectedUser->children()->Form['title']
		);
	}

	public function clGetForm()
	{
		$clForm = $this->m_ndHeader->children()->Form;
		return new Form($clForm, $clForm['title']);
	}

	public function clGetElement()
	{
		$clElem = $this->m_ndHeader->children()->Element;
		return new Element($clElem, $clElem['title']);
	}

	/**
	 * retourne le noeud reponse de l'operation
	 * @param $sOperation
	 * @return SimpleXMLElement
	 */
	protected function _clGetNodeResponse($sOperation)
	{
		return $this->m_ndBody->children()->{$sOperation.'Response'};
	}

	/**
	 * récupère le noeud xml dans la réponse
	 * @param string $sOperation : operation lancée
	 * @return SimpleXMLElement
	 */
	public function getNodeXML($sOperation)
	{
		$clNodeResponse = $this->_clGetNodeResponse($sOperation);
		if (isset($clNodeResponse))
			return $clNodeResponse->xml;

		return null;
	}

	public function getNodeSchema()
	{
		$clXSDSchema = $this->m_ndHeader->children()->XSDSchema;
		if (!isset($clXSDSchema))
			return null;

		return $clXSDSchema->children('http://www.w3.org/2001/XMLSchema', false)->schema;
	}

	/**
	 * récupère le token session dans la réponse XML
	 * @return string
	 */
	public function sGetTokenSession()
	{
		$clNodeResponse = $this->_clGetNodeResponse('GetTokenSession');
		if (isset($clNodeResponse))
			return (string)$clNodeResponse->SessionToken;

		return '';
	}

} 