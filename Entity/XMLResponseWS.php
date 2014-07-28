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
	protected $m_TabError;
	protected $m_sXML;
	protected $m_sNamespaceSOAP;

	public function __construct($sXML)
	{
		$this->m_sXML = $sXML;
		$clEnvelope = simplexml_load_string($sXML);

		//calcul du nom du namespace de l'enveloppe
		$sNomNSSoap='';
		$tabNamespace = $clEnvelope->getNamespaces();
		$tabNomNamespace = array_keys($tabNamespace, 'http://www.w3.org/2003/05/soap-envelope');
		if (count($tabNomNamespace)>0)
			$this->m_sNamespaceSOAP = $tabNomNamespace[0];
		else
		{
			$tabNomNamespace = array_keys($tabNamespace, 'http://schemas.xmlsoap.org/soap/envelope/');
			if (count($tabNomNamespace)>0)
				$this->m_sNamespaceSOAP = $tabNomNamespace[0];
		}

		//on trouve le noeud header et le noeud body
		$this->m_ndHeader = $clEnvelope->children($this->m_sNamespaceSOAP, true)->Header;
		$this->m_ndBody = $clEnvelope->children($this->m_sNamespaceSOAP, true)->Body;

		//on parse les erreurs si c'en est une
		$this->m_TabError = null;

		$ndFault = isset($this->m_ndBody) ? $this->m_ndBody->children($this->m_sNamespaceSOAP, true)->Fault : null;
		if (isset($ndFault))
		{
			//le noeud ListErr fils de  Detail
			$ndListErr = $ndFault->children($this->m_sNamespaceSOAP, true)->Detail->children()->ListErr;

			//on recherche le namespace pour les erreurs SIMAX http://www.nout.fr/soap/error
			foreach($ndListErr->children('http://www.nout.fr/soap/error') as $ndError)
			{
				$clError = new OnlineError($ndError->children()->Code['Name'],
					$ndError->children()->Code->Numero,
					$ndError->children()->Code->Category,
					$ndError->children()->Message
				);

				foreach($ndError->children()->Parameter as $ndParam)
				{
					$clParam = new OnlineErrorParameter($ndParam['IDParam'], $ndParam['TitleParam'], $ndParam['TitleElem']);
					$clError->AddParameter($clParam);
				}

				$this->m_TabError[]=$clError;
			}
		}
	}

	/**
	 * @return string
	 */
	public function sGetXML()
	{
		return $this->m_sXML;
	}

	/**
	 * retourne vrai si le retour est une erreur
	 */
	public function bIsFault()
	{
		return isset($this->m_TabError);
	}

	/**
	 * @return mixed, false si pas une erreur, un tableau d'erreur SIMAX si c'est une erreur
	 */
	public function getTabError()
	{
		if (!isset($this->m_TabError))
			return false;

		return $this->m_TabError;
	}

	public function getNumError()
	{
		if (!isset($this->m_TabError))
			return false;

		return $this->m_TabError[0]->getErreur();
	}
	public function getCatError()
	{
		if (!isset($this->m_TabError))
			return false;

		return $this->m_TabError[0]->getCategorie();
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