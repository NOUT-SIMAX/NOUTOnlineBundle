<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 05/08/2015
 * Time: 13:39
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Controller;


use NOUT\Bundle\NOUTOnlineBundle\Entity\Header\OptionDialogue;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\CalculationListType;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\ColListType;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\ConditionColonne;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\ConditionFileNPI;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\ReorderList;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\ReorderSubList;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\SetOrderList;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\SetOrderSubList;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\EnregTableauArray;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\Record;

use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\MessageBox;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\ReponseWSParser;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS;
use NOUT\Bundle\NOUTOnlineBundle\Service\ConnectionManager;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\OnlineServiceProxy as SOAPProxy;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Cancel;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ConfirmResponse;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Create;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\CreateFrom;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Delete;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Display;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\DrillThrough;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Execute;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetCalculation;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetChart;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetColInRecord;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetEndAutomatism;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetPlanningInfo;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetStartAutomatism;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetTableChild;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ListParams;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Modify;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\PrintParams;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Request;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\RequestParam;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Search;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SelectForm;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SelectItems;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SelectPrintTemplate;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\TransformInto;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Update;



use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route; // this imports the annotations
use Symfony\Component\HttpFoundation\Response;


/**
 * Class DefaultController
 * @package NOUT\Bundle\NOUTOnlineBundle\Controller
 * @Route("/soap")
 */
class SOAPController extends ProxyController
{
	/**
	 * @param string $host
	 * @return SOAPProxy
	 */
	protected function _clGetSOAPProxy($host)
	{
		return $this->get('nout_online.service_factory')->clGetSOAPProxy($this->_clGetConfiguration($host));
	}

	/**
	 * @param SOAPProxy $OnlineProxy
	 * @return string
	 */
	protected function _sConnexion(SOAPProxy $OnlineProxy)
	{
		/** @var ConnectionManager $connectionManager */
        $connectionManager = $this->get('nout_online.connection_manager');
		$clGetTokenSession = $connectionManager->getGetTokenSession();
		$clReponseXML      = $OnlineProxy->getTokenSession($clGetTokenSession);
		$this->_VarDumpRes('GetTokenSession', $clReponseXML);
		$this->_VarDumpRes('GetTokenSession', $clReponseXML->sGetTokenSession());

		return $clReponseXML->sGetTokenSession();
	}

	protected function _clGetOptionDialogue()
	{
		$clOptionDialogue = new OptionDialogue();
		$clOptionDialogue->InitDefault($this->getParameter('nout_online.version_dialogue_pref'));
		$clOptionDialogue->DisplayValue          = OptionDialogue::DISPLAY_No_ID;
		$clOptionDialogue->EncodingOutput        = 0;
		$clOptionDialogue->LanguageCode          = 0;
		$clOptionDialogue->WithFieldStateControl = 1;
		$clOptionDialogue->ReturnXSD             = 1;

		return $clOptionDialogue;
	}


	protected function _aGetTabHeader($sTokenSession, $nIDContexteAction = null)
	{
		$clUsernameToken = $this->get('nout_online.connection_manager')->getUsernameToken();
		$TabHeader       = array('UsernameToken' => $clUsernameToken, 'SessionToken' => $sTokenSession, 'OptionDialogue' => $this->_clGetOptionDialogue());

		if (!empty($nIDContexteAction))
		{
			$TabHeader['ActionContext'] = $nIDContexteAction;
		}

		return $TabHeader;
	}

	protected function _bDeconnexion(SOAPProxy $OnlineProxy, $sTokenSession)
	{
		$TabHeader = $this->_aGetTabHeader($sTokenSession);
        $clXMLResponse = $OnlineProxy->disconnect($TabHeader);
		$this->_VarDumpRes('Disconnect', $clXMLResponse);

		return $clXMLResponse;
	}

	/**
	 * testing connection/disconnection
     * @param string $host
	 * @Route("/cnx/ok/{host}", name="online_soap_connexion", defaults={"host"=""})
     * @return Response
	 */
	public function connexionAction($host)
	{
		ob_start();

		$OnlineProxy = $this->_clGetSOAPProxy($host);

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);

		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();

		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt' => $containt));
	}


	/**
	 * pour tester la connexion/déconnexion
     * @param string $host
     * @param int $error
	 * @Route("/cnx/error_excpt/{error}/{host}", name="online_soap_cnx_error", defaults={"host"=""})
     * @return Response
     * TODO: error parameter is called but unused, consider removing
	 */
	public function cnxErrorAction($host, $error)
	{
		ob_start();

		$OnlineProxy = $this->_clGetSOAPProxy($host);

        /** @var ConnectionManager $connectionManager */
        $connectionManager = $this->get('nout_online.connection_manager');
        $clGetTokenSession = $connectionManager->getGetTokenSession();
        $clReponseXML      = $OnlineProxy->getTokenSession($clGetTokenSession);

		$this->_VarDumpRes('GetTokenSession', $clReponseXML);
		$this->_VarDumpRes('GetTokenSession', $clReponseXML->sGetTokenSession());

		$containt = ob_get_contents();
		ob_get_clean();

		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt' => $containt));
	}


	/**
	 * pour tester la connexion/déconnexion
     * @param string $host
     * @param int $error
	 * @Route("/cnx/error_try/{error}/{host}", name="online_soap_cnx_try_error", defaults={"host"=""})
     * @return Response
     * TODO: error parameter is called but unused, consider removing
	 */
	public function cnxTryErrorAction($host, $error)
	{
		ob_start();

        /** @var ConnectionManager $connectionManager */
        $connectionManager = $this->get('nout_online.connection_manager');
        $clGetTokenSession = $connectionManager->getGetTokenSession();

        $OnlineProxy = $this->_clGetSOAPProxy($host);
		try
		{
			$OnlineProxy->getTokenSession($clGetTokenSession);
		}
		catch (\Exception $e)
		{
			//on ne veut pas l'objet retourné par NUSOAP qui est un tableau associatif mais un objet qui permet de manipuler la réponse
			$clReponseXML = $OnlineProxy->getXMLResponseWS();

			//on attrape l'exception
			$this->_VarDumpRes('GetTokenSession', $clReponseXML);
			$this->_VarDumpRes('GetTokenSession', $clReponseXML->bIsFault());
			$this->_VarDumpRes('GetTokenSession', $clReponseXML->getTabError());

			$this->_VarDumpRes('GetTokenSession', $clReponseXML->getNumError());
			$this->_VarDumpRes('GetTokenSession', $clReponseXML->getCatError());
		}


		$containt = ob_get_contents();
		ob_get_clean();

		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt' => $containt));
	}

	protected function _sNettoieForm($form)
	{
		// prefixe pour les balises où on utilise l'identifiant au lieu du libellé
		$_pszPrefixeBaliseID = "id_";

		if ($this->_bEstNumerique($form))
		{
			return $_pszPrefixeBaliseID.$form;
		}


		// caractères interdits dans une balise
		$_pszCaractereInterdit = " ()[]<>':/!;\"%$&@*°";
		// caractères interdits en début de balise
		//$_pszCaractereInterditDebut = "0123456789.-";
		// prefixe interdit en début de balise
		$_pszChaineInterditDebut = "xml";

		//---------------------------------------------------------------------
		/*
		XML elements must follow these naming rules:
		- Names can contain letters, numbers, and other characters..............OK
		- Names must not start with a number or punctuation character...........OK
		- Names must not start with the letters xml (or XML, or Xml, etc).......OK
		- Names cannot contain spaces...........................................OK
		*/

		// supprimer les espaces en début et fin de chaine
		$form = trim($form, ' ');

		// supprimer la casse (mettre en minuscule) et les accents
		$form = strtolower($this->_SupprAccents($form));

		// remplacer les espaces et les caractères spéciaux par des '_'
		$nLength = strlen($form)-1;
		while (($nLength >= 0) && (!is_null(strchr($_pszCaractereInterdit, $form[$nLength]))))
		{
			$pszLibelle[$nLength] = 0;
			$nLength--;
		}
		while ($nLength >= 0)
		{
			if (!is_null(strchr($_pszCaractereInterdit, $form[$nLength])))
			{
				$form[$nLength] = '_';
			}
			$nLength--;
		}

		// supprimer tout caractere different d'un alpha en debut de chaine
		$nLength = strlen($form);
		$nIndex  = 0;
		while (($nIndex < $nLength) && !((($form[0] >= 'a') && ($form[0] <= 'z')) || (($form[0] >= 'A') && ($form[0] <= 'Z'))))
		{
			$form = substr($form, 1);
			$nIndex++;
		}

		// un nom XML ne peut pas commencer par la chaine de caractere "xml"
		while (strncmp($form, $_pszChaineInterditDebut, strlen($_pszChaineInterditDebut)) == 0)
		{
			$form = substr($form, strlen($_pszChaineInterditDebut));
		}

		// si le libelle est une chaine vide, renvoyer faux
		// ce sera l'ID de la colonne qui sera utilisé pour construire le nom de la balise
		if (empty(trim($form)))
		{
			return false;
		}


		// créer un PXSTR qu'il faudra copier dans pszLibelle puis libérer
		return $form;
	}

	/**
	 * @param SOAPProxy $OnlineProxy
	 * @param $sTokenSession
	 * @param $form
     * @param string $displayMode
     * @param string $sActionContexte
	 * @return XMLResponseWS
	 */
	protected function _sList(SOAPProxy $OnlineProxy, $sTokenSession, $form, $sActionContexte = '', $displayMode = SOAPProxy::DISPLAYMODE_Liste)
	{
		$clParamList              = new ListParams();
		$clParamList->Table       = $form;
		$clParamList->DisplayMode = $displayMode;
		$clReponseXML             = $OnlineProxy->listAction($clParamList, $this->_aGetTabHeader($sTokenSession, $sActionContexte));
		$this->_VarDumpRes('List', $clReponseXML);

		return $clReponseXML;
	}

	/**
	 * @param SOAPProxy $OnlineProxy
	 * @param $sTokenSession
	 * @param $sIDActionContexte
	 * @param $TabIDColonne
	 * @return XMLResponseWS
	 */
	protected function _sGetCalculation(SOAPProxy $OnlineProxy, $sTokenSession, $sIDActionContexte, $TabIDColonne)
	{
		$clParamGetCalculation                  = new GetCalculation();
		$clParamGetCalculation->ColList         = new ColListType($TabIDColonne);
		$clParamGetCalculation->CalculationList = new CalculationListType(
			array(
				CalculationListType::SUM,
				CalculationListType::AVERAGE,
				CalculationListType::MIN,
				CalculationListType::MAX,
				CalculationListType::COUNT,
			)
		);

		$clReponseXML = $OnlineProxy->getCalculation($clParamGetCalculation, $this->_aGetTabHeader($sTokenSession, $sIDActionContexte));
		$this->_VarDumpRes('GetCalculation', $clReponseXML);

		return $clReponseXML;
	}

	/**
     * @param string $form
     * @param string $host
	 * @Route("/list/{form}/{host}", name="online_soap_list", defaults={"host"=""})
     * @return Response
	 */
	public function listAction($form, $host)
	{
		ob_start();
		$OnlineProxy = $this->_clGetSOAPProxy($host);

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);

		//la liste
		$clReponseWSList = $this->_sList($OnlineProxy, $sTokenSession, $form);
		$sActionContexte = $clReponseWSList->sGetActionContext();

		//on parse le XML pour avoir les enregistrement
		$clReponseWSParser = new ReponseWSParser();
		$clReponseWSParser->InitFromXmlXsd($clReponseWSList);

		//TODO: clGetStructureElement() is not a method of ReponseWSParser
		$StructForm   = $clReponseWSParser->clGetStructureElement($clReponseWSList->clGetForm()->getID());
		$TabIDColonne = $StructForm->getTabIDColonne();

		$clReponseWSCalcul = $this->_sGetCalculation($OnlineProxy, $sTokenSession, $sActionContexte, $TabIDColonne);
		$clReponseWSParser->InitFromXmlXsd($clReponseWSCalcul);

		$this->_VarDumpRes('Calculation', $clReponseWSParser->m_MapColonne2Calcul);

		//annulation de la liste
		$this->_sCancel($OnlineProxy, $sTokenSession, $sActionContexte);

		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();

		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt' => $containt));
	}


	/**
	 * @param SOAPProxy $OnlineProxy
	 * @param $sTokenSession
	 * @param $sIDActionContexte
	 * @param $form
	 * @param $index
	 * @return XMLResponseWS
	 */
	protected function _sGetChart(SOAPProxy $OnlineProxy, $sTokenSession, $sIDActionContexte, $form, $index)
	{
		$clParamChart         = new GetChart();
		$clParamChart->Height = 500;
		$clParamChart->Width  = 700;
		$clParamChart->DPI    = 72;
		$clParamChart->Index  = $index;
		$clParamChart->Table  = $form;

		$clReponseXML = $OnlineProxy->getChart($clParamChart, $this->_aGetTabHeader($sTokenSession, $sIDActionContexte));
		$this->_VarDumpRes('GetChart', $clReponseXML);

		return $clReponseXML;
	}

	/**
	 * @param SOAPProxy $OnlineProxy
	 * @param $sTokenSession
	 * @param $sIDActionContexte
	 * @param array $TabSelection
	 * @return XMLResponseWS
	 */
	protected function _sSelectItems(SOAPProxy $OnlineProxy, $sTokenSession, $sIDActionContexte, $TabSelection)
	{
		$clParamSelectItems        = new SelectItems();
		$clParamSelectItems->items = implode('|', $TabSelection);

		$clReponseXML = $OnlineProxy->selectItems($clParamSelectItems, $this->_aGetTabHeader($sTokenSession, $sIDActionContexte));
		$this->_VarDumpRes('SelectItems', $clReponseXML);

		return $clReponseXML;
	}


	/**
     * @param string $form
     * @param string $host
	 * @Route("/chart/{form}/{host}", name="online_soap_chart", defaults={"host"=""})
     * @return Response
	 */
	public function chartAction($form, $host)
	{
		ob_start();
		$OnlineProxy = $this->_clGetSOAPProxy($host);

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);

		//la liste
		$clReponseWSList = $this->_sList($OnlineProxy, $sTokenSession, $form);
		$sActionContexte = $clReponseWSList->sGetActionContext();

		$TabPossibleDM = $clReponseWSList->GetTabPossibleDisplayMode();
		if (in_array(SOAPProxy::DISPLAYMODE_Graphe, $TabPossibleDM))
		{
			$clParserList = new ReponseWSParser();
			$clParserList->InitFromXmlXsd($clReponseWSList);

			//TODO: GetTabEnregTableau() is not a method of ReponseWSParser
			$TabIDEnreg = array_slice($clParserList->GetTabEnregTableau()->GetTabIDEnreg($clReponseWSList->clGetForm()->getID()), 0, 5);
			$this->_sSelectItems($OnlineProxy, $sTokenSession, $sActionContexte, $TabIDEnreg);

			$clReponseWSGraphe = $this->_sList($OnlineProxy, $sTokenSession, $form, $sActionContexte, SOAPProxy::DISPLAYMODE_Graphe);
			$nNbChart          = $clReponseWSGraphe->nGetNumberOfChart();

			for ($i = 0; $i<$nNbChart; $i++)
			{
				$clReponseWSChart = $this->_sGetChart($OnlineProxy, $sTokenSession, $sActionContexte, $form, $i+1);
				$clParser         = new ReponseWSParser();
				$clParser->InitFromXmlXsd($clReponseWSChart);
				//TODO: $m_clChart is not a member of ReponseWSParser
				$this->_VarDumpRes('Chart', $clParser->m_clChart);
			}
		}
		else
		{
			echo '<pre>Affichage des graphes non possible</pre>';
		}

		//annulation de la liste
		$this->_sCancel($OnlineProxy, $sTokenSession, $sActionContexte);

		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();

		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt' => $containt));
	}


	/**
	 * @param SOAPProxy $OnlineProxy
	 * @param $sTokenSession
	 * @param $action
	 * @return XMLResponseWS
	 */
	protected function _sExecute(SOAPProxy $OnlineProxy, $sTokenSession, $action)
	{
		$clParamExecute = new Execute();

		if ($this->_bEstNumerique($action))
		{
			$clParamExecute->ID = $action;
		}
		else
		{
			$clParamExecute->Sentence = $action;
		}

		$clReponseXML = $OnlineProxy->execute($clParamExecute, $this->_aGetTabHeader($sTokenSession));
		$this->_VarDumpRes('Execute', $clReponseXML);


		return $clReponseXML;
	}

	/**
     * @param mixed $action
     * @param string $host
	 * @Route("/execute/{action}/{host}", name="online_soap_execute", defaults={"host"=""})
     * @return Response
	 */
	public function executeAction($action, $host)
	{
		ob_start();
		$OnlineProxy = $this->_clGetSOAPProxy($host);

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);

		//la liste
		$clReponseWS     = $this->_sExecute($OnlineProxy, $sTokenSession, $action);
		$sActionContexte = $clReponseWS->sGetActionContext();

		//annulation de la liste
		$this->_sCancel($OnlineProxy, $sTokenSession, $sActionContexte);

		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();

		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt' => $containt));
	}

	/**
	 * @param SOAPProxy $OnlineProxy
	 * @param $sTokenSession
	 * @param $sActionContexte
	 * @param $colonne
	 * @param $enreg
	 *
	 * @return XMLResponseWS
	 */
	protected function _sDrillthrought(SOAPProxy $OnlineProxy, $sTokenSession, $sActionContexte, $colonne, $enreg)
	{
		$clParamDrillThrough         = new DrillThrough();
		$clParamDrillThrough->Record = $enreg;
		$clParamDrillThrough->Column = $colonne;

		$clReponseXML = $OnlineProxy->drillThrough($clParamDrillThrough, $this->_aGetTabHeader($sTokenSession, $sActionContexte));
		$this->_VarDumpRes('DrillThrough', $clReponseXML);


		return $clReponseXML;
	}

	/**
     * @param string $host
	 * @Route("/drillthrought/{host}", name="online_soap_drillthrought", defaults={"host"=""})
     * @return Response
	 */
	public function drillthroughtAction($host)
	{
		ob_start();
		$OnlineProxy = $this->_clGetSOAPProxy($host);

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);


		//execute
		$clReponseWSList = $this->_sExecute($OnlineProxy, $sTokenSession, 'Afficher Nb Jour d\'absence par contact');
		$sActionContexte = $clReponseWSList->sGetActionContext();

		//on parse le résultat
		$clReponseWSParser = new ReponseWSParser();
		$clReponseWSParser->InitFromXmlXsd($clReponseWSList);

		//TODO: clGetStructureElement() is not a method of ReponseWSParser
		$StructForm   = $clReponseWSParser->clGetStructureElement($clReponseWSList->clGetForm()->getID());
		$TabIDColonne = $StructForm->getTabIDColonne();
		$TabIDEnreg   = $clReponseWSParser->GetTabIDEnregFromForm($clReponseWSList->clGetForm()->getID());

		$clReponseWSDrill     = $this->_sDrillthrought($OnlineProxy, $sTokenSession, $sActionContexte, $TabIDColonne[0], $TabIDEnreg[0]);
		$sActionContexteDrill = $clReponseWSDrill->sGetActionContext();

		//annulation de la liste
		$this->_sCancel($OnlineProxy, $sTokenSession, $sActionContexte);

		if ($sActionContexte != $sActionContexteDrill)
		{
			$this->_sCancel($OnlineProxy, $sTokenSession, $sActionContexteDrill);
		}

		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();

		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt' => $containt));
	}

	/**
	 * @param SOAPProxy $OnlineProxy
	 * @param $sTokenSession
	 * @param $form
	 * @param $colonne
	 * @param $value
	 * @return XMLResponseWS
	 */
	protected function _sRequest(SOAPProxy $OnlineProxy, $sTokenSession, $form, $colonne, $value)
	{
		$clFileNPI = new ConditionFileNPI();
		$clFileNPI->EmpileCondition($colonne, ConditionColonne::COND_EQUAL, $value);

		$clParamRequest           = new Request();
		$clParamRequest->Table    = $form;
		$clParamRequest->CondList = $clFileNPI->sToSoap();


		$clReponseXML = $OnlineProxy->request($clParamRequest, $this->_aGetTabHeader($sTokenSession));
		$this->_VarDumpRes('Request', $clReponseXML);


		return $clReponseXML;
	}

	/**
	 * @Route("/request/form/{form}/{colonne}/{valeur}/{host}", name="online_soap_request", defaults={"host"=""})
     * @param string $form
     * @param string $colonne
     * @param string $valeur
     * @param string $host
     * @return Response
	 */
	public function requestAction($form, $colonne, $valeur, $host)
	{
		ob_start();
		$OnlineProxy = $this->_clGetSOAPProxy($host);

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);

		//la liste
		$clReponseWS     = $this->_sRequest($OnlineProxy, $sTokenSession, $form, $colonne, $valeur);
		$sActionContexte = $clReponseWS->sGetActionContext();

		//annulation de la liste
		$this->_sCancel($OnlineProxy, $sTokenSession, $sActionContexte);

		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();

		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt' => $containt));
	}

	/**
	 * @Route("/request/param/{host}", name="online_soap_request_param", defaults={"host"=""})
	 * @param string $host
     * @return Response
     *
	 * <RequestParam><Table>8267</Table>
	 * <CondList>
	 * <Condition> <CondCol>8521</CondCol><CondType>Equal</CondType><CondValue>8267</CondValue></Condition>  </Operator>
	 * </RequestParam>
	 */
	public function requestParamAction($host)
	{
		ob_start();
		$OnlineProxy = $this->_clGetSOAPProxy($host);

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);

		$clFileNPI = new ConditionFileNPI();
		$clFileNPI->EmpileCondition('8521', ConditionColonne::COND_EQUAL, 8267);

		$clParamRequest           = new RequestParam();
		$clParamRequest->Table    = 8267;
		$clParamRequest->CondList = $clFileNPI->sToSoap();


		$clReponseXML = $OnlineProxy->requestParam($clParamRequest, $this->_aGetTabHeader($sTokenSession));
		$this->_VarDumpRes('RequestParam', $clReponseXML);

		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();

		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt' => $containt));
	}




	/**
	 * @param SOAPProxy $OnlineProxy
	 * @param $sTokenSession
	 * @param $form
	 * @return XMLResponseWS
	 */
	protected function _sSearch(SOAPProxy $OnlineProxy, $sTokenSession, $form)
	{
		$clParamSearch        = new Search();
		$clParamSearch->Table = $form;
		$clReponseXML         = $OnlineProxy->search($clParamSearch, $this->_aGetTabHeader($sTokenSession));
		$this->_VarDumpRes('Search', $clReponseXML);


		return $clReponseXML;
	}

	/**
	 * @Route("/search/{form}/{host}", name="online_soap_search", defaults={"host"=""})
     * @param string $form
     * @param string $host
     * @return Response
	 */
	public function searchAction($form, $host)
	{
		ob_start();
		$OnlineProxy = $this->_clGetSOAPProxy($host);

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);

		//la recherche
		$clReponseWS     = $this->_sSearch($OnlineProxy, $sTokenSession, $form);
		$sActionContexte = $clReponseWS->sGetActionContext();

		//annulation de la liste
		$this->_sCancel($OnlineProxy, $sTokenSession, $sActionContexte);

		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();

		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt' => $containt));
	}


	/**
	 * @param SOAPProxy $OnlineProxy
	 * @param $sTokenSession
	 * @param $form
	 * @param $id
	 * @return XMLResponseWS
	 */
	protected function _sDisplay(SOAPProxy $OnlineProxy, $sTokenSession, $form, $id)
	{
		$clParamDisplay        = new Display();
		$clParamDisplay->Table = $form;

		$baliseXML                = $this->_sNettoieForm($form);
		$clParamDisplay->ParamXML = "<$baliseXML>".htmlentities($id)."</$baliseXML>";

		$clReponseXML = $OnlineProxy->display($clParamDisplay, $this->_aGetTabHeader($sTokenSession));
		$this->_VarDumpRes('Display', $clReponseXML);


		return $clReponseXML;
	}


	/**
	 * @Route("/display/{form}/{id}/{host}", name="online_soap_display", defaults={"host"=""})
     * @param string $form
     * @param string $id
     * @param string $host
     * @return Response
	 */
	public function displayAction($form, $id, $host)
	{
		ob_start();
		$OnlineProxy = $this->_clGetSOAPProxy($host);

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);

		//ici il faut faire le display
		$clReponseWS     = $this->_sDisplay($OnlineProxy, $sTokenSession, $form, $id);
		$sActionContexte = $clReponseWS->sGetActionContext();

		//annulation de la liste
		$this->_sCancel($OnlineProxy, $sTokenSession, $sActionContexte);

		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();

		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt' => $containt));
	}


	/**
	 * @param SOAPProxy $OnlineProxy
	 * @param $sTokenSession
	 * @param $form
	 * @param $id
	 * @return XMLResponseWS
	 */
	protected function _sPrint(SOAPProxy $OnlineProxy, $sTokenSession, $form, $id)
	{
		$clParamPrint        = new PrintParams();
		$clParamPrint->Table = $form;

		$baliseXML              = $this->_sNettoieForm($form);
		$clParamPrint->ParamXML = "<$baliseXML>".htmlentities($id)."</$baliseXML>";

		$clReponseXML = $OnlineProxy->printAction($clParamPrint, $this->_aGetTabHeader($sTokenSession));
		$this->_VarDumpRes('Print', $clReponseXML);


		return $clReponseXML;
	}

	/**
	 * @Route("/print/{form}/{id}/{host}", name="online_soap_print", defaults={"host"=""})
     * @param string $form
     * @param string $id
     * @param string $host
     * @return Response
	 */
	public function printAction($form, $id, $host)
	{
		ob_start();
		$OnlineProxy = $this->_clGetSOAPProxy($host);

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);

		//ici il faut faire le display
		$clReponseWS       = $this->_sPrint($OnlineProxy, $sTokenSession, $form, $id);
		$clReponseWSParser = new ReponseWSParser();
		$clReponseWSParser->InitFromXmlXsd($clReponseWS);

		//TODO: clGetData() is not a method of ReponseWSParser
		$clData   = $clReponseWSParser->clGetData(0);
		$html_raw = $clData->sGetRaw();

		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();

		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt' => $containt, 'html_raw' => utf8_encode($html_raw)));
	}

	/**
	 * @param SOAPProxy $OnlineProxy
	 * @param $sTokenSession
	 * @param string $sActionContexte
	 * @param string $modele
	 * @return XMLResponseWS
	 */
	protected function _sSelectPrintTemplate(SOAPProxy $OnlineProxy, $sTokenSession, $sActionContexte, $modele)
	{
		$clParamSelectPrintTemplate           = new SelectPrintTemplate();
		$clParamSelectPrintTemplate->Template = $modele;

		$clReponseXML = $OnlineProxy->selectPrintTemplate($clParamSelectPrintTemplate, $this->_aGetTabHeader($sTokenSession, $sActionContexte));
		$this->_VarDumpRes('SelectPrintTemplate', $clReponseXML);


		return $clReponseXML;
	}


	/**
	 * @Route("/select_print_template/{form}/{id}/{modele}/{host}",
     *     name="online_soap_select_print_template", defaults={"host"=""})
     * @param string $form
     * @param string $id
     * @param string $host
     * @param string $modele
     * @return Response
	 */
	public function selectPrintTemplateAction($form, $id, $host, $modele)
	{
		ob_start();
		$OnlineProxy = $this->_clGetSOAPProxy($host);

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);

		//ici il faut faire le display
		$clReponseWS = $this->_sPrint($OnlineProxy, $sTokenSession, $form, $id);

		//sélection du modèle
		$clReponseWS       = $this->_sSelectPrintTemplate($OnlineProxy, $sTokenSession, $clReponseWS->sGetActionContext(), $modele);
		$clReponseWSParser = new ReponseWSParser();
		$clReponseWSParser->InitFromXmlXsd($clReponseWS);

		//TODO: clGetData() is not a method of ReponseWSParser
		$clData   = $clReponseWSParser->clGetData(0);
		$html_raw = $clData->sGetRaw();


		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();

		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt' => $containt, 'html_raw' => utf8_encode($html_raw)));
	}


	protected function _sGetColInRecord(SOAPProxy $OnlineProxy, $sTokenSession, $colonne, $id, $content)
	{
		$clParamGCR              = new GetColInRecord();
		$clParamGCR->Column      = $colonne;
		$clParamGCR->Record      = $id;
		$clParamGCR->WantContent = $content;


		$clReponseXML = $OnlineProxy->getColInRecord($clParamGCR, $this->_aGetTabHeader($sTokenSession));
		$this->_VarDumpRes('GetColInRecord', $clReponseXML);


		return $clReponseXML;
	}


	/**
	 * @Route("/getcolinrecord/{colonne}/{id}/{content}/{host}", name="online_soap_getcolinrecord", defaults={"host"=""})
     * @param string $colonne
     * @param string $id
     * @param string $host
     * @param string $content
     * @return Response
	 */
	public function getColInRecordAction($colonne, $id, $host, $content)
	{
		ob_start();
		$OnlineProxy = $this->_clGetSOAPProxy($host);

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);

		//ici il faut faire le display
		$clReponseWS     = $this->_sGetColInRecord($OnlineProxy, $sTokenSession, $colonne, $id, $content);
		$sActionContexte = $clReponseWS->sGetActionContext();

		//annulation de la liste
		$this->_sCancel($OnlineProxy, $sTokenSession, $sActionContexte);

		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();

		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt' => $containt));
	}


	/**
	 * @Route("/get_planning_info/{res}/{host}", name="online_soap_get_planning_info", defaults={"host"=""})
     * @param string $res
     * @param string $host
     * @return Response
     *
	 * <Resource>36683203627649</Resource><StartTime>20140901000000</StartTime><EndTime>20140907000000</EndTime>
	 */
	public function getPlanningInfoAction($res, $host)
	{
		ob_start();
		$OnlineProxy = $this->_clGetSOAPProxy($host);

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);

		$clPlanningInfo            = new GetPlanningInfo();
		$clPlanningInfo->Resource  = $res;
		$clPlanningInfo->StartTime = '20140901000000';
		$clPlanningInfo->EndTime   = '20140907000000';

		$clReponseXML = $OnlineProxy->getPlanningInfo($clPlanningInfo, $this->_aGetTabHeader($sTokenSession));
		$this->_VarDumpRes('GetPlanningInfo', $clReponseXML);

		$clReponseWSParser = new ReponseWSParser();
		$clReponseWSParser->InitFromXmlXsd($clReponseXML);


		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();

		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt' => $containt));
	}

	/**
	 * Valide la dernière action du contexte
	 * @param $sTokenSession
	 * @param $nIDContexteAction
     * @param SOAPProxy $OnlineProxy
     * @return XMLResponseWS
	 */
	protected function _Validate(SOAPProxy $OnlineProxy, $sTokenSession, $nIDContexteAction)
	{
		$clReponseWS = $OnlineProxy->validate($this->_aGetTabHeader($sTokenSession, $nIDContexteAction));

		$this->_VarDumpRes('Validate', $clReponseWS);

		return $clReponseWS;
	}


	/**
	 * Valide la dernière action du contexte
	 * @param $sTokenSession
	 * @param $nIDContexteAction
     * @param SOAPProxy $OnlineProxy
	 * @return XMLResponseWS
	 */
	protected function _sCancel(SOAPProxy $OnlineProxy, $sTokenSession, $nIDContexteAction)
	{
		$clParamCancel          = new Cancel();
		$clParamCancel->ByUser  = 1;
		$clParamCancel->Context = 0;

		$clReponseWS = $OnlineProxy->cancel($clParamCancel, $this->_aGetTabHeader($sTokenSession, $nIDContexteAction));

		$this->_VarDumpRes('Cancel', $clReponseWS);

		return $clReponseWS;
	}

	/**
	 * @param SOAPProxy $OnlineProxy
	 * @param $sTokenSession
	 * @param $form
	 * @param $id
	 *
	 * @return XMLResponseWS
	 */
	protected function _sModify(SOAPProxy $OnlineProxy, $sTokenSession, $form, $id)
	{
		$clParamModify        = new Modify();
		$clParamModify->Table = $form;

		$baliseXML               = $this->_sNettoieForm($form);
		$clParamModify->ParamXML = "<$baliseXML>".htmlentities($id)."</$baliseXML>";

		$clReponseXML = $OnlineProxy->modify($clParamModify, $this->_aGetTabHeader($sTokenSession));
		$this->_VarDumpRes('Modify', $clReponseXML);


		return $clReponseXML;
	}


	protected function _sUpdate(SOAPProxy $OnlineProxy, $sTokenSession, $nIDContexteAction, $form, $id, $colonne, $value)
	{
		$clParamUpdate        = new Update();
		$clParamUpdate->Table = $form;

		$baliseXML               = $this->_sNettoieForm($form);
		$clParamUpdate->ParamXML = "<$baliseXML>".htmlentities($id)."</$baliseXML>";

		$baliseColonne             = $this->_sNettoieForm($colonne);
		$clParamUpdate->UpdateData = "<xml><$baliseXML id=\"$id\"><$baliseColonne>".htmlentities($value)."</$baliseColonne></$baliseXML></xml>";

		$clReponseXML = $OnlineProxy->update($clParamUpdate, $this->_aGetTabHeader($sTokenSession, $nIDContexteAction));
		$this->_VarDumpRes('Update', $clReponseXML);

		return $clReponseXML;
	}

	/**
	 * @Route("/modify/{form}/{id}/{colonne}/{valeur}/{host}", name="online_soap_modify", defaults={"host"=""})
	 * @param string $form
     * @param string $id
     * @param string $colonne
     * @param string $valeur
     * @param string $host
     * @return Response
	 * exemple GUID : /modify/41296233836619/219237638150324/45208949043557/deux
	 */
	public function modifyAction($form, $id, $colonne, $valeur, $host)
	{
		ob_start();
		$OnlineProxy = $this->_clGetSOAPProxy($host);

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);

		//ici il faut faire le modify
		$clReponseWS     = $this->_sModify($OnlineProxy, $sTokenSession, $form, $id);
		$sActionContexte = $clReponseWS->sGetActionContext();

		//on parse le XML pour avoir les enregistrement
		$clReponseWSParser = new ReponseWSParser();
		$clReponseWSParser->InitFromXmlXsd($clReponseWS);

        //TODO: clGetRecord() is not a method of ReponseWSParser
		$clRecord = $clReponseWSParser->clGetRecord($clReponseWS->clGetForm(), $clReponseWS->clGetElement());
		if ($clRecord instanceof Record)
		{
			//on met à jour la valeur de la colonne
			$this->_sUpdate($OnlineProxy, $sTokenSession, $sActionContexte, $form, $id, $colonne, $valeur);

			//on valide
			$this->_Validate($OnlineProxy, $sTokenSession, $sActionContexte);
		}
		else
		{
			echo '<p>On n\'a pas l\'enregistrement</p>';
		}

		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();

		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt' => $containt));
	}

	/**
	 * @param SOAPProxy $OnlineProxy
	 * @param $sTokenSession
	 * @param $form
	 *
	 * @return XMLResponseWS
	 */
	protected function __sCreate(SOAPProxy $OnlineProxy, $sTokenSession, $form)
	{
		$clParamCreate        = new Create();
		$clParamCreate->Table = $form;

		$clReponseXML = $OnlineProxy->create($clParamCreate, $this->_aGetTabHeader($sTokenSession));
		$this->_VarDumpRes('Create', $clReponseXML);

		return $clReponseXML;
	}

	protected function _sHasChanged(SOAPProxy $OnlineProxy, $sTokenSession, $sContexteAction)
	{
		$clReponseXML = $OnlineProxy->hasChanged($this->_aGetTabHeader($sTokenSession, $sContexteAction));
		$this->_VarDumpRes('HasChanged', $clReponseXML);

		return $clReponseXML;
	}

	/**
	 * @param SOAPProxy $OnlineProxy
	 * @param $sTokenSession
	 * @param $form
	 * @param $colonne
	 * @param $valeur
	 * @return null|string
	 */
	protected function _sCreate(SOAPProxy $OnlineProxy, $sTokenSession, $form, $colonne, $valeur)
	{
		//ici il faut faire le modify
		$clReponseWS     = $this->__sCreate($OnlineProxy, $sTokenSession, $form);
		$sActionContexte = $clReponseWS->sGetActionContext();

		//on parse le XML pour avoir les enregistrement
		$clReponseWSParser = new ReponseWSParser();
		$clReponseWSParser->InitFromXmlXsd($clReponseWS);

		//TODO: clGetRecord is not a method of ReponseWSParser
		$clRecord = $clReponseWSParser->clGetRecord($clReponseWS->clGetForm(), $clReponseWS->clGetElement());
		if ($clRecord instanceof Record)
		{
			//on met à jour la valeur de la colonne
			$this->_sUpdate($OnlineProxy, $sTokenSession, $sActionContexte, $form, $clReponseWS->clGetElement()->getID(), $colonne, $valeur);

			//on valide
			$this->_Validate($OnlineProxy, $sTokenSession, $sActionContexte);

			//TODO: Accessing protected member. __get('m_nIDEnreg') will throw
			return $clRecord->m_nIDEnreg;
		}

		echo '<p>On n\'a pas l\'enregistrement</p>';

		return null;
	}

	/**
	 * @Route("/create/{form}/{colonne}/{valeur}/{host}", name="online_soap_create", defaults={"host"=""})
	 * @param string $form
     * @param string $colonne
     * @param string $valeur
     * @param string $host
     * @return Response
	 *
     * exemple GUID : /create/41296233836619/45208949043557/trois
	 */
	public function createAction($form, $colonne, $valeur, $host)
	{
		ob_start();
		$OnlineProxy = $this->_clGetSOAPProxy($host);

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);

		$this->_sCreate($OnlineProxy, $sTokenSession, $form, $colonne, $valeur);

		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();

		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt' => $containt));
	}

	/**
	 * @param SOAPProxy $OnlineProxy
	 * @param string $sTokenSession
	 * @param string $formDest
     * @param string $formSrc
     * @param string $elemSrc
	 *
	 * @return XMLResponseWS
	 */
	protected function _sTransformInto(SOAPProxy $OnlineProxy, $sTokenSession, $formDest, $formSrc, $elemSrc)
	{
		$clParamTransformInto           = new TransformInto();
		$clParamTransformInto->Table    = $formDest;
		$clParamTransformInto->TableSrc = $formSrc;
		$clParamTransformInto->ElemSrc  = $elemSrc;

		$clReponseXML = $OnlineProxy->transformInto($clParamTransformInto, $this->_aGetTabHeader($sTokenSession));
		$this->_VarDumpRes('TransformInto', $clReponseXML);

		return $clReponseXML;
	}



	/**
	 * @Route("/transform_into/{formSrc}/{formDest}/{colonne}/{valeur}/{host}",
     *     name="online_soap_transform_into", defaults={"host"=""})
	 * @param string $formSrc
     * @param string $formDest
     * @param string $colonne
     * @param string $valeur
     * @param string $host
     * @return Response
     *
	 * exemple GUID : /transform_into/51346223489588/40810668714136/40896568059607/trois
	 */
	public function transformIntoAction($formSrc, $formDest, $colonne, $valeur, $host)
	{
		ob_start();
		$OnlineProxy = $this->_clGetSOAPProxy($host);

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);

		//on commance par creer un enregistrement de type formulaire 1
		$sIDEnreg = $this->_sCreate($OnlineProxy, $sTokenSession, $formSrc, $colonne, $valeur);

		//et on le transforme en formulaire 2
		$clReponseWS = $this->_sTransformInto($OnlineProxy, $sTokenSession, $formDest, $formSrc, $sIDEnreg);
		$this->_Validate($OnlineProxy, $sTokenSession, $clReponseWS->sGetActionContext());

		$this->_sDisplay($OnlineProxy, $sTokenSession, $formDest, $sIDEnreg);

		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();

		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt' => $containt));
	}


	/**
	 * @Route("/has_changed/{form}/{colonne}/{valeur}/{host}", name="online_soap_has_changed", defaults={"host"=""})
	 * @param string $form
     * @param string $colonne
     * @param string $valeur
     * @param string $host
     * @return Response
     *
	 * exemple GUID : /create/41296233836619/45208949043557/trois
	 */
	public function hasChangedAction($form, $colonne, $valeur, $host)
	{
		ob_start();
		$OnlineProxy = $this->_clGetSOAPProxy($host);

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);


		//un create
		$clReponseWS     = $this->__sCreate($OnlineProxy, $sTokenSession, $form);
		$sActionContexte = $clReponseWS->sGetActionContext();

		//on parse le XML pour avoir les enregistrement
		$clReponseWSParser = new ReponseWSParser();
		$clReponseWSParser->InitFromXmlXsd($clReponseWS);

		//TODO: clGetRecord() is not a method of ReponseWSParser
		$clRecord = $clReponseWSParser->clGetRecord($clReponseWS->clGetForm(), $clReponseWS->clGetElement());
		if ($clRecord instanceof Record)
		{
			//on met à jour la valeur de la colonne
			$this->_sUpdate($OnlineProxy, $sTokenSession, $sActionContexte, $form, $clReponseWS->clGetElement()->getID(), $colonne, $valeur);

			//on fait un has changed pour pouvoir imprimer après
			$clReponseWS = $this->_sHasChanged($OnlineProxy, $sTokenSession, $sActionContexte);
			if ($clReponseWS->getValue() == 1)
			{
				//on valide
				$this->_Validate($OnlineProxy, $sTokenSession, $sActionContexte);

				//et on imprime
                //TODO: Accessing protected variable m_nIDEnreg. __get('m_nIDEnreg') will throw
				$clReponseWS       = $this->_sPrint($OnlineProxy, $sTokenSession, $form, $clRecord->m_nIDEnreg);
				$clReponseWSParser = new ReponseWSParser();
				$clReponseWSParser->InitFromXmlXsd($clReponseWS);

				//TODO: clGetData() is not a method of ReponseWSParser
				$clData   = $clReponseWSParser->clGetData(0);
				$html_raw = $clData->sGetRaw();
			}
		}


		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();

		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt' => $containt, 'html_raw' => utf8_encode($html_raw)));
	}


	protected function _sSelectForm(SOAPProxy $OnlineProxy, $sTokenSession, $sActionContexte, $form)
	{
		$clParamSelectForm       = new SelectForm();
		$clParamSelectForm->Form = $form;

		$clReponseXML = $OnlineProxy->selectForm($clParamSelectForm, $this->_aGetTabHeader($sTokenSession, $sActionContexte));
		$this->_VarDumpRes('SelectForm', $clReponseXML);

		return $clReponseXML;
	}

	/**
	 * @Route("/select_form/{form}/{host}", name="online_soap_select_form", defaults={"host"=""})
	 * @param string $form
     * @param string $host
     * @return Response
	 * exemple GUID : /selectForm/48918773563102
	 */
	public function selectFormAction($form, $host)
	{
		ob_start();
		$OnlineProxy = $this->_clGetSOAPProxy($host);

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);

		$clReponseXML    = $this->__sCreate($OnlineProxy, $sTokenSession, $form);
		$sActionContexte = $clReponseXML->sGetActionContext();

		//on parse le XML pour avoir les enregistrement
		$clReponseWSParser = new ReponseWSParser();
		$clReponseWSParser->InitFromXmlXsd($clReponseXML);

		//TODO: GetTabIDEnregFromForm() is not a method of ReponseWSParser
		$TabIDEnreg = $clReponseWSParser->GetTabIDEnregFromForm($clReponseXML->clGetForm()->getID());

		//le selectForm en réponse du retour d'action ambigue
		$this->_sSelectForm($OnlineProxy, $sTokenSession, $sActionContexte, $TabIDEnreg[0]);

		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();

		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt' => $containt));
	}


	/**
	 * @param SOAPProxy $OnlineProxy
	 * @param $sTokenSession
	 * @param $form
	 * @param $origin
	 * @return XMLResponseWS
	 */
	protected function _sCreateFrom(SOAPProxy $OnlineProxy, $sTokenSession, $form, $origin)
	{
		$clParamCreateFrom           = new CreateFrom();
		$clParamCreateFrom->Table    = $form;
		$clParamCreateFrom->TableSrc = $form;
		$clParamCreateFrom->ElemSrc  = $origin;

		$clReponseXML = $OnlineProxy->createFrom($clParamCreateFrom, $this->_aGetTabHeader($sTokenSession));
		$this->_VarDumpRes('CreateFrom', $clReponseXML);

		return $clReponseXML;
	}

	/**
	 * @Route("/create_from/{form}/{origine}/{host}", name="online_soap_create_from", defaults={"host"=""})
	 * @param string $form
     * @param string $origine
     * @param string $host
     * @return Response
     *
	 * exemple GUID : /create/41296233836619/45354977933184
	 */
	public function createFromAction($form, $origine, $host)
	{
		ob_start();
		$OnlineProxy = $this->_clGetSOAPProxy($host);

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);

		//ici il faut faire le modify
		$clReponseWS     = $this->_sCreateFrom($OnlineProxy, $sTokenSession, $form, $origine);
		$sActionContexte = $clReponseWS->sGetActionContext();

		//on valide
		$this->_Validate($OnlineProxy, $sTokenSession, $sActionContexte);

		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();

		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt' => $containt));
	}



	protected function _sDelete(SOAPProxy $OnlineProxy, $sTokenSession, $form, $id)
	{
		$clParamDelete        = new Delete();
		$clParamDelete->Table = $form;

		$baliseXML               = $this->_sNettoieForm($form);
		$clParamDelete->ParamXML = "<$baliseXML>".htmlentities($id)."</$baliseXML>";

		$clReponseXML = $OnlineProxy->delete($clParamDelete, $this->_aGetTabHeader($sTokenSession));
		$this->_VarDumpRes('Delete', $clReponseXML);


		return $clReponseXML;
	}

	/**
	 * @param SOAPProxy $OnlineProxy
	 * @param $sTokenSession
	 * @param $sActionContexte
	 * @param MessageBox $clMessageBox
	 *
	 * @return XMLResponseWS
	 */
	protected function _sConfirmResponse(SOAPProxy $OnlineProxy, $sTokenSession, $sActionContexte, MessageBox $clMessageBox)
	{
		$clConfirm                   = new ConfirmResponse();
		$clConfirm->TypeConfirmation = array_key_exists(MessageBox::IDYES, $clMessageBox->getTabButton()) ? MessageBox::IDYES : MessageBox::IDOK;

		$clReponseWS = $OnlineProxy->ConfirmResponse($clConfirm, $this->_aGetTabHeader($sTokenSession, $sActionContexte));
		$this->_VarDumpRes('ConfirmResponse', $clReponseWS);

		return $clReponseWS;
	}

	/**
	 * @Route("/delete/{form}/{colonne}/{valeur}/{host}", name="online_soap_delete", defaults={"host"=""})
	 * @param string $form
     * @param string $colonne
     * @param string $valeur
     * @param string $host
     * @return Response
     *
	 * exemple GUID : /delete
	 */
	public function deleteAction($form, $colonne, $valeur, $host)
	{
		ob_start();
		$OnlineProxy = $this->_clGetSOAPProxy($host);

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);

		$sIDEnreg = $this->_sCreate($OnlineProxy, $sTokenSession, $form, $colonne, $valeur);

		//ici il faut faire le delete
		$clReponseWS     = $this->_sDelete($OnlineProxy, $sTokenSession, $form, $sIDEnreg);
		$sActionContexte = $clReponseWS->sGetActionContext();

		//on valide
		if ($clReponseWS->sGetReturnType() == XMLResponseWS::RETURNTYPE_MESSAGEBOX)
		{
			//il faut confirmer la réponse
			$this->_sConfirmResponse($OnlineProxy, $sTokenSession, $sActionContexte, $clReponseWS->clGetMessageBox());
		}

		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();

		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt' => $containt));
	}

	protected function _sEnterReorderListMode(SOAPProxy $OnlineProxy, $sTokenSession, $sActionContexte)
	{
		$clReponseXML = $OnlineProxy->enterReorderListMode($this->_aGetTabHeader($sTokenSession, $sActionContexte));
		$this->_VarDumpRes('EnterReorderListMode', $clReponseXML);

		return $clReponseXML;
	}

	protected function _sSetOrderList(SOAPProxy $OnlineProxy, $sTokenSession, $sActionContexte, $tabIDEnreg, $nOffset)
	{
		$clSetOrderList = new SetOrderList($tabIDEnreg, $nOffset);

		$clReponseXML = $OnlineProxy->setOrderList($clSetOrderList, $this->_aGetTabHeader($sTokenSession, $sActionContexte));
		$this->_VarDumpRes('SetOrderList', $clReponseXML);

		return $clReponseXML;
	}


	protected function _sReOrderList(SOAPProxy $OnlineProxy, $sTokenSession, $sActionContexte, $tabIDEnreg, $nScale, $nMove)
	{
		$clReorderList = new ReorderList($tabIDEnreg, $nScale, $nMove);
		$clReponseXML  = $OnlineProxy->reorderList($clReorderList, $this->_aGetTabHeader($sTokenSession, $sActionContexte));
		$this->_VarDumpRes('ReOrderList', $clReponseXML);

		return $clReponseXML;
	}

	/**
	 * @Route("/reorder_list/{host}", name="online_soap_reorder_list", defaults={"host"=""})
     * @param string $host
     * @return Response
	 */
	public function ReorderListAction($host)
	{
		ob_start();
		$OnlineProxy = $this->_clGetSOAPProxy($host);

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);

		//il faut commencer par réordonner la liste
		$this->_sExecute($OnlineProxy, $sTokenSession, '221569603630667');

		//on affiche la liste
		$clReponseList = $this->_sList($OnlineProxy, $sTokenSession, '228261139523058');
		//on parse le XML pour avoir les enregistrement
		$clReponseWSParser = new ReponseWSParser();
		$clReponseWSParser->InitFromXmlXsd($clReponseList);

		//TODO: GetTabEnregTableau() is not a method of ReponseWSParser
		$tabEnregTableauOrigine = $clReponseWSParser->GetTabEnregTableau();

		$this->_sEnterReorderListMode($OnlineProxy, $sTokenSession, $clReponseList->sGetActionContext());

		$tempOrig = implode('|', array_slice($tabEnregTableauOrigine->GetTabIDEnreg(), 0, 5));
		var_dump($tempOrig);

		$tabSetOrder = new EnregTableauArray();
		$tabSetOrder->Add($tabEnregTableauOrigine->nGetIDTableau(3), $tabEnregTableauOrigine->nGetIDEnreg(3));
		$tabSetOrder->Add($tabEnregTableauOrigine->nGetIDTableau(2), $tabEnregTableauOrigine->nGetIDEnreg(2));
		$tabSetOrder->Add($tabEnregTableauOrigine->nGetIDTableau(1), $tabEnregTableauOrigine->nGetIDEnreg(1));

		$clReponseSetOrder = $this->_sSetOrderList($OnlineProxy, $sTokenSession, $clReponseList->sGetActionContext(), $tabSetOrder, 1);

		$tabReorder = new EnregTableauArray();
		$tabReorder->Add($tabEnregTableauOrigine->nGetIDTableau(0), $tabEnregTableauOrigine->nGetIDEnreg(0));

		$clReponseSetOrder = $this->_sReOrderList($OnlineProxy, $sTokenSession, $clReponseList->sGetActionContext(), $tabReorder, 4, ReorderList::MOVE_DOWN);

		//<items>218610348012442|225220302686986|220504428595852|227775829887743</items><offset>0</offset>

		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();

		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt' => $containt));
	}


	protected function _sSetOrderSubList(SOAPProxy $OnlineProxy, $sTokenSession, $sActionContexte, $nIDColonne, $tabIDEnreg, $nOffset)
	{
		$clSetOrderList = new SetOrderSubList($nIDColonne, $tabIDEnreg, $nOffset);

		$clReponseXML = $OnlineProxy->setOrderSubList($clSetOrderList, $this->_aGetTabHeader($sTokenSession, $sActionContexte));
		$this->_VarDumpRes('SetOrderSubList', $clReponseXML);

		return $clReponseXML;
	}


	protected function _sReOrderSubList(SOAPProxy $OnlineProxy, $sTokenSession, $sActionContexte, $nIDColonne, $tabIDEnreg, $nScale, $nMove)
	{
		$clReorderList = new ReorderSubList($nIDColonne, $tabIDEnreg, $nScale, $nMove);
		$clReponseXML  = $OnlineProxy->reorderSubList($clReorderList, $this->_aGetTabHeader($sTokenSession, $sActionContexte));
		$this->_VarDumpRes('ReOrderSubList', $clReponseXML);

		return $clReponseXML;
	}

	/**
	 * @Route("/reorder_sublist/{host}", name="online_soap_reorder_sublist", defaults={"host"=""})
     * @param string $host
     * @return Response
	 */
	public function ReorderSubListAction($host)
	{
		ob_start();
		$OnlineProxy = $this->_clGetSOAPProxy($host);

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);

		//modification d'enregistrement
		$clReponseModify = $this->_sModify($OnlineProxy, $sTokenSession, '228261139523058', '218610348012442');
		$sActionContexte = $clReponseModify->sGetActionContext();

		//on parse le XML pour avoir les enregistrement
		$clParserModify = new ReponseWSParser();
		$clParserModify->InitFromXmlXsd($clReponseModify);
		//TODO: clGetRecord() is not a method of ReponseWSParser
		$clRecord = $clParserModify->clGetRecord($clReponseModify->clGetForm(), $clReponseModify->clGetElement());
		if ($clRecord instanceof Record)
		{
			$TabValColOrig = $clRecord->getValCol('221655479824831');
			$this->_VarDumpRes('valcol', implode('|', $TabValColOrig));

			$TabSetOrder    = $TabValColOrig;
			$sTemp          = $TabSetOrder[0];
			$TabSetOrder[0] = $TabSetOrder[3];
			$TabSetOrder[3] = $sTemp;

			$clReponseSetOrder = $this->_sSetOrderSubList($OnlineProxy, $sTokenSession, $sActionContexte, '221655479824831', $TabSetOrder, 0);
			$tabSetOrderRes    = new EnregTableauArray();
			$tabSetOrderRes->AddFromListeStr('', $clReponseSetOrder->getValue());

			$tabIDEnreg = array($tabSetOrderRes->nGetIDEnreg(1), $tabSetOrderRes->nGetIDEnreg(3));

			$clReponseReOrder = $this->_sReOrderSubList($OnlineProxy, $sTokenSession, $sActionContexte, '221655479824831', $tabIDEnreg, 1, ReorderList::MOVE_DOWN);

			//on valide
			$this->_Validate($OnlineProxy, $sTokenSession, $sActionContexte);

			//on vérifie
			$clReponseGCIR = $this->_sGetColInRecord($OnlineProxy, $sTokenSession, '221655479824831', '218610348012442', 1);
			$this->_sCancel($OnlineProxy, $sTokenSession, $clReponseGCIR->sGetActionContext());


			$clParserGCIR = new ReponseWSParser();
			$clParserGCIR->InitFromXmlXsd($clReponseGCIR);
			//TODO: clGetData() is not a method of ReponseWSParser
			$clData = $clParserGCIR->clGetData(0);

			var_dump($clData->m_sContent, $clReponseReOrder->getValue());
		}
		else
		{
			echo '<p>On n\'a pas l\'enregistrement</p>';
		}

		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();

		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt' => $containt));
	}

	/**
	 * @param SOAPProxy $OnlineProxy
	 * @param $sTokenSession
	 * @return XMLResponseWS
	 */
	protected function _sGetStartAutomatism(SOAPProxy $OnlineProxy, $sTokenSession)
	{
		$clParamStartAutomatism = new GetStartAutomatism();

		$clReponseXML = $OnlineProxy->getStartAutomatism($clParamStartAutomatism, $this->_aGetTabHeader($sTokenSession));
		$this->_VarDumpRes('GetStartAutomatism', $clReponseXML);


		return $clReponseXML;
	}

	/**
	 * @Route("/getstartautomatism/{host}", name="online_soap_getstartautomatism", defaults={"host"=""})
     * @param string $host
     * @return Response
	 */
	public function getStartAutomatismAction($host)
	{
		ob_start();
		$OnlineProxy = $this->_clGetSOAPProxy($host);

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);

		//la liste
		$clReponseWS     = $this->_sGetStartAutomatism($OnlineProxy, $sTokenSession);
		$sActionContexte = $clReponseWS->sGetActionContext();

		//annulation de la liste
		$this->_sCancel($OnlineProxy, $sTokenSession, $sActionContexte);

		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();

		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt' => $containt));
	}

	/**
	 * @param SOAPProxy $OnlineProxy
	 * @param $sTokenSession
	 * @return XMLResponseWS
	 */
	protected function _sGetTemporalAutomatism(SOAPProxy $OnlineProxy, $sTokenSession)
	{
		$clReponseXML = $OnlineProxy->getTemporalAutomatism($this->_aGetTabHeader($sTokenSession));
		$this->_VarDumpRes('GetTemporalAutomatism', $clReponseXML);

		return $clReponseXML;
	}
	/**
	 * @Route("/gettemporalautomatism/{host}", name="online_soap_gettemporalautomatism", defaults={"host"=""})
     * @param string $host
     * @return Response
	 */
	public function getTemporalAutomatismAction($host)
	{
		ob_start();
		$OnlineProxy = $this->_clGetSOAPProxy($host);

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);

		//la liste
		$this->_sGetTemporalAutomatism($OnlineProxy, $sTokenSession);

		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();

		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt' => $containt));
	}


	protected function _sGetEndAutomatism(SOAPProxy $OnlineProxy, $sTokenSession)
	{
		$clParamEndAutomatism = new GetEndAutomatism();

		$clReponseXML = $OnlineProxy->getEndAutomatism($clParamEndAutomatism, $this->_aGetTabHeader($sTokenSession));
		$this->_VarDumpRes('GetEndAutomatism', $clReponseXML);


		return $clReponseXML;
	}

	/**
	 * @Route("/getendautomatism/{host}", name="online_soap_getendautomatism", defaults={"host"=""})
     * @param string $host
     * @return Response
	 */
	public function getEndAutomatismAction($host)
	{
		ob_start();
		$OnlineProxy = $this->_clGetSOAPProxy($host);

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);

		//la liste
		$clReponseWS     = $this->_sGetEndAutomatism($OnlineProxy, $sTokenSession);
		$sActionContexte = $clReponseWS->sGetActionContext();

		//annulation de la liste
		$this->_sCancel($OnlineProxy, $sTokenSession, $sActionContexte);

		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();

		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt' => $containt));
	}

	/**
	 * @Route("/getlanguages/{host}", name="online_soap_getlanguages", defaults={"host"=""})
     * @param string $host
     * @return Response
	 */
	public function getLanguagesAction($host)
	{
		ob_start();
		$OnlineProxy = $this->_clGetSOAPProxy($host);

		//récupération des langues
		$clReponseXML = $OnlineProxy->getLanguages($this->_aGetTabHeader(''));
		$this->_VarDumpRes('GetLanguages', $clReponseXML);

		$containt = ob_get_contents();
		ob_get_clean();

		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt' => $containt));
	}

	/**
	 * @Route("/gettablechild/{form}/{host}", name="online_soap_gettablechild", defaults={"host"=""})
     * @param string $form
     * @param string $host
     * @return Response
	 */
	public function getTableChildAction($form, $host)
	{
		ob_start();
		$OnlineProxy = $this->_clGetSOAPProxy($host);

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);

		$clTableChildParam            = new GetTableChild();
		$clTableChildParam->Table     = $form;
		$clTableChildParam->Recursive = 1;
		$clTableChildParam->ReadOnly  = 1;

		//récupération des langues
		$clReponseXML = $OnlineProxy->getTableChild($clTableChildParam, $this->_aGetTabHeader($sTokenSession));
		$this->_VarDumpRes('GetTableChild', $clReponseXML);

		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();

		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt' => $containt));
	}
}