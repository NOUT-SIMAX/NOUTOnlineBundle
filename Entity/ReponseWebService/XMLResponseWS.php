<?php
/**
 * Created by PhpStorm.
 * User: Ninon <ninon@nout.fr>
 * Date: 18/07/14
 * Time: 16:46
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService;
use NOUT\Bundle\NOUTOnlineBundle\Entity\NOUTFileInfo;

/**
 * Class XMLResponseWS
 * @package NOUT\Bundle\NOUTOnlineBundle\Entity
 *
 * class qui contient le SimpleXMLElement de la réponse du webservice
 */
class XMLResponseWS
{
	/**
	 * @var \SimpleXMLElement noeud du body
	 */
	protected $m_ndBody;

	/**
	 * @var \SimpleXMLElement noeud du header
	 */
	protected $m_ndHeader;

	/**
	 * @var OnlineError[]  tableau des erreur
	 */
	protected $m_TabError;

	/**
	 * @var string contenu du XML
	 */
	protected $m_sXML;

	/**
	 * @var string namespace SOAP
	 */
	protected $m_sNamespaceSOAP;

	public function __construct($sXML)
	{
		$this->m_sXML = $sXML;
		$clEnvelope   = simplexml_load_string($sXML);

		//calcul du nom du namespace de l'enveloppe
		$tabNamespaceDuXML    = $clEnvelope->getNamespaces();
		// tableau associatif nom => url

		// Il n'y a que ces 4 là qui sont possibles pour l'enveloppe SOAP (standard w3c)
		// les deux mêmes avec / final ou nom pour compatibilité avec vieille version de NOUTOnline
        $aNamespacePossible = array(
            'http://www.w3.org/2003/05/soap-envelope/',
            'http://schemas.xmlsoap.org/soap/envelope/',
            'http://www.w3.org/2003/05/soap-envelope',
            'http://schemas.xmlsoap.org/soap/envelope',
        );

		//recherche les namespaces possible dans les namespances du XML
        foreach($aNamespacePossible as $sUrlNamespacePossible)
		{
            $this->m_sNamespaceSOAP = array_search($sUrlNamespacePossible, $tabNamespaceDuXML); // false si ne trouve pas la valeur dans le tableau
			//renvoi la clé si trouvé, donc le nom du namespace

			if ($this->m_sNamespaceSOAP !== false) // Si le namespace de l'enveloppe est un de ceux autorisés
			{
                break;
            }
        }

		//on trouve le noeud header et le noeud body
		$this->m_ndHeader = $clEnvelope->children($this->m_sNamespaceSOAP, true)->Header;
		$this->m_ndBody   = $clEnvelope->children($this->m_sNamespaceSOAP, true)->Body;

		//on parse les erreurs si c'en est une
		$this->m_TabError = null;

		/* @var $ndFault \SimpleXMLElement */
		$ndFault = isset($this->m_ndBody) ? $this->m_ndBody->children($this->m_sNamespaceSOAP, true)->Fault : null;

		if (!empty($ndFault))
		{
			//le noeud ListErr fils de  Detail
			/* @var $ndListErr \SimpleXMLElement */
			$ndListErr = $ndFault->children($this->m_sNamespaceSOAP, true)->Detail->children()->ListErr;

			//on recherche le namespace pour les erreurs SIMAX http://www.nout.fr/soap/error
			foreach ($ndListErr->children('http://www.nout.fr/soap/error') as $ndError)
			{
				/* @var $ndError \SimpleXMLElement */
				$clError = new OnlineError($ndError->children()->Code['Name'],
					$ndError->children()->Code->Numero,
					$ndError->children()->Code->Category,
					$ndError->children()->Message
				);

				foreach ($ndError->children()->Parameter as $ndParam)
				{
					$clParam = new OnlineErrorParameter($ndParam['IDParam'], $ndParam['TitleParam'], $ndParam['TitleElem']);
					$clError->AddParameter($clParam);
				}

				$this->m_TabError[] = $clError;
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
		{
			return false;
		}

		return $this->m_TabError;
	}

	/**
	 * @return int
	 */
	public function getNumError()
	{
		if (!isset($this->m_TabError))
		{
			return 0;
		}

		return $this->m_TabError[0]->getErreur();
	}

	/**
	 * @return int
	 */
	public function getCatError()
	{
		if (!isset($this->m_TabError))
		{
			return 0;
		}

		return $this->m_TabError[0]->getCategorie();
	}

	/**
	 * @return string
	 */
	public function getMessError()
	{
		if (!isset($this->m_TabError))
		{
			return '';
		}

		return $this->m_TabError[0]->getMessage();
	}

	/**
	 * renvoi le type de retour
	 * @return string
	 */
	public function sGetReturnType()
	{
		return (string) $this->m_ndHeader->children()->ReturnType;
	}

	/**
	 * renvoi l'identifiant du contexte d'action
	 * @return string
	 */
	public function sGetActionContext()
	{
		return (string) $this->m_ndHeader->children()->ActionContext;
	}

    /**
     * renvoi l'identifiant du contexte d'action
     * @return string
     */
    public function sGetContextToValidateOnClose()
    {
        return (string) $this->m_ndHeader->children()->ContextToValidateOnClose;
    }

    /**
     * renvoi les id des contextes qu'on doit fermer
     * @return array
     */
    public function aGetActionContextToClose()
    {
        $val = (string) $this->m_ndHeader->children()->ActionContextToClose;
        if (empty($val))
        {
            return array();
        }

        return explode('|', trim($val, '|'));
    }


	/**
	 * @return MessageBox
	 */
	public function clGetMessageBox()
	{
		return new MessageBox($this->getNodeXML());
	}

    public function getData()
    {
        $ndXML = $this->getNodeXML();
        return (string)$ndXML->Data;
    }

    /**
     * @return NOUTFileInfo
     */
	public function getFile()
	{
        $ndXML = $this->getNodeXML(); // Récupération du noeud xml

        if ($ndXML->count()>0)
        {
            /* @var $DataElement \SimpleXMLElement */
            $DataElement 	= $ndXML->Data; // Récupération du noeud data fils du noeud xml
            if ($DataElement->count()>0)
            {
                /** @var \SimpleXMLElement[] $aAttributesXml */
                $aAttributesXml = $DataElement->attributes('http://www.nout.fr/soap'); // Va récupérer le préfixe déclaré dans l'entête SOAP
                $oFilename = $aAttributesXml['filename'];
                if (isset($oFilename) && !empty((string)$oFilename))
                {
                    $oNOUTFileInfo = new NOUTFileInfo();
                    $oNOUTFileInfo->initFromDataTag($DataElement, $aAttributesXml);
                    return $oNOUTFileInfo;
                }
            }
        }

        return null;
	}

	/**
	 * @return CurrentAction : action en cours
	 */
	public function clGetAction()
	{
		$clAction = $this->m_ndHeader->children()->Action;

		return new CurrentAction($clAction);
	}

    /**
     * @return String title of the document
     */
    public function clGetTitle()
    {
        try{
            return (string) $this->m_ndHeader->children()->Title;
        }
        catch(\Exception $e){
            return '';
        }
    }

	/**
	 * @return ConnectedUser : utilisateur actuellement connecté
	 */
	public function clGetConnectedUser()
	{
		/* @var $clConnectedUser \SimpleXMLElement */
		$clConnectedUser = $this->m_ndHeader->children()->ConnectedUser;

		return new ConnectedUser(
			$clConnectedUser->children()->Element,
			$clConnectedUser->children()->Element['title'],
			$clConnectedUser->children()->Form,
			$clConnectedUser->children()->Form['title']
		);
	}

	/**
	 * @return Form
	 */
	public function clGetForm()
	{
		$ndForm = $this->m_ndHeader->children()->Form;
        $clForm = new Form($ndForm, $ndForm['title'], $ndForm['withBtnOrderPossible'], $ndForm['withBtnOrderActive']);

		for ($n = 1; $n <= 3; $n++)
		{
			if (isset($ndForm['sort'.$n]))
			{
				$clForm->m_TabSort[] = new ListSort($ndForm['sort'.$n], isset($ndForm['sort'.$n.'asc']) ? $ndForm['sort'.$n.'asc'] : 1);
			}
		}

		return $clForm;
	}

	/**
	 * @return Element
	 */
	public function clGetElement()
	{
		$clElem = $this->m_ndHeader->children()->Element;
		if (isset($clElem))
		{
			return new Element($clElem, $clElem['title']);
		}

		return;
	}

	/**
	 * @return Count
	 */
	public function clGetCount()
	{
		$ndCount = $this->m_ndHeader->children()->Count;
		if (!isset($ndCount) || empty($ndCount))
		{
            return new Count();
		}

		$clCount                    = new Count();
		$clCount->m_nNbCalculation  = (int) $ndCount->children()->NbCalculation;
		$clCount->m_nNbLine         = (int) $ndCount->children()->NbLine;
		$clCount->m_nNbFiltered     = (int) $ndCount->children()->NbFiltered;
		$clCount->m_nNbTotal        = (int) $ndCount->children()->NbTotal;
		$clCount->m_nNbDisplay      = (int) $ndCount->children()->NbDisplay;

		return $clCount;
	}

	public function clGetFolderCount()
    {
        $ndFolderCount = $this->m_ndHeader->children()->FolderCount;
        if (!isset($ndFolderCount) || empty($ndFolderCount)) {
            return new Foldercount();
        }

        $clFolderCount                    = new Foldercount();
        $clFolderCount->m_nNbReceived     = (int) $ndFolderCount->children()->NbReceive;
        $clFolderCount->m_nNbUnread       = (int) $ndFolderCount->children()->NbUnRead;

        return $clFolderCount;
    }

	/**
	 * @return null|array
	 */
	public function GetTabPossibleDisplayMode()
	{
		$ndPossibleDM = $this->m_ndHeader->children()->PossibleDisplayMode;
		if (!isset($ndPossibleDM))
		{
			return;
		}

		return explode('|', (string) $ndPossibleDM);
	}

	/**
	 * @return null|string
	 */
	public function sGetDefaultDisplayMode()
	{
		$ndDefaultDM = $this->m_ndHeader->children()->DefaultDisplayMode;
		if (!isset($ndDefaultDM))
		{
			return;
		}

		return (string) $ndDefaultDM;
	}


	/**
	 * retourne le noeud reponse de l'operation
	 * @param $sOperation
	 * @return \SimpleXMLElement
	 */
	protected function _clGetNodeResponse()
	{
        // Récupère les enfants de Body
        $children = $this->m_ndBody->children();

        // Renvoi le premier enfant
		return $children[0];
	}

    /**
     * récupère le noeud xml des filtres
     * @return \SimpleXMLElement
     */
    public function getNodeXMLParam()
    {
        $clNodeFilter = $this->m_ndHeader->children()->Filter;
        if (isset($clNodeFilter))
        {
            return $clNodeFilter->xml;
        }

        return;
    }


    /**
     * récupère le noeud xml des ressources du planning de tous
     * @return \SimpleXMLElement
     */
    public function getNodeXMLRessource()
    {
        $clNodeSchedulerResource = $this->m_ndHeader->children()->SchedulerResource;
        if (isset($clNodeSchedulerResource))
        {
            return $clNodeSchedulerResource->xml;
        }

        return;
    }


    /**
	 * récupère le noeud xml dans la réponse
	 * @param string $sOperation : operation lancée
	 * @return \SimpleXMLElement
	 */
	public function getNodeXML()
	{
		$clNodeResponse = $this->_clGetNodeResponse();

		if (isset($clNodeResponse))
		{
			return $clNodeResponse->xml;
		}

		return;
	}

	public function getValue()
	{
		$clNodeResponse = $this->_clGetNodeResponse();
		if (isset($clNodeResponse))
		{
			return (string) $clNodeResponse->Value;
		}

		return;
	}

	/**
	 * @return \SimpleXMLElement
	 */
	public function getNodeSchema()
	{
		$clXSDSchema = $this->m_ndHeader->children()->XSDSchema;
		if (empty($clXSDSchema))
		{
			return;
		}

		//le noeud XSDSchema n'a qu'un fils
		return $clXSDSchema->children('http://www.w3.org/2001/XMLSchema', false)->schema;
	}

    /**
     * récupère le noeud schema des filtres
     * @return \SimpleXMLElement|null
     */
    public function getNodeXSDParam()
    {
		// Provoque l'erreur "Node no longer exists" avec ajax_ville
        /** @var \SimpleXMLElement $clNodeSchedulerResource */
		$clNodeFilter = $this->m_ndHeader->children()->Filter;

        if (isset($clNodeFilter) && ($clNodeFilter->count()>0))
        {
			// Erreur : "Node no longer exists" alors que dans le isSet
			// Données mal parsées ?
            return $clNodeFilter->children('http://www.w3.org/2001/XMLSchema', false)->schema;
        }
    }


    /**
     * récupère le noeud schema des ressources du planning de tous
     * @return \SimpleXMLElement|null
     */
    public function getNodeXSDRessource()
    {
        /** @var \SimpleXMLElement $clNodeSchedulerResource */
        $clNodeSchedulerResource = $this->m_ndHeader->children()->SchedulerResource;

        if (count($clNodeSchedulerResource)>0)
        {
            // Erreur : "Node no longer exists" alors que dans le isSet
            // Données mal parsées ?
            return $clNodeSchedulerResource->children('http://www.w3.org/2001/XMLSchema', false)->schema;
        }
    }

	/**
	 * @return ValidateError|null
	 */
	public function getValidateError()
	{
		$clValidateError = $this->m_ndHeader->children()->ValidateError;
		if ($clValidateError->count()>0)
		{
            return new ValidateError($clValidateError);
		}

	}


	/**
	 * récupère le token session dans la réponse XML
	 * @return string
	 */
	public function sGetTokenSession()
	{
		$clNodeResponse = $this->_clGetNodeResponse('GetTokenSession');
		if (isset($clNodeResponse))
		{
			return (string) $clNodeResponse->SessionToken;
		}

		return '';
	}

	/**
	 * @return int
	 */
	public function nGetNumberOfChart()
	{
		$ndXML = $this->getNodeXML();
		if (!isset($ndXML))
		{
			return 0;
		}

		return (int) $ndXML->numberOfChart;
	}


	/**
	 * @return string
	 */
	public function sGetReport()
	{
		$ndXML = $this->getNodeXML();

		if (!isset($ndXML))
		{
			return '';
		}

		return (string) $ndXML->Report;
	}


	/**
	 * returne le tableau des codes langues disponibles
	 * @return array
	 */
	public function GetTabLanguages()
	{
		$ndXML = $this->getNodeXML();
		if (!isset($ndXML))
		{
			return array();
		}
/*
		<xml>
			<LanguageCode>12</LanguageCode>
			<LanguageCode>9</LanguageCode>
			<LanguageCode>10</LanguageCode>
		</xml>
*/
		$TabLanguages = array();
		foreach ($ndXML->LanguageCode as $ndLanguageCode)
		{
			$TabLanguages[] = (int) $ndLanguageCode;
		}

		return $TabLanguages;
	}

    /**
     * @return \SimpleXMLElement
     */
    public function getExportsNode()
    {
        $exports = $this->m_ndHeader->children()->Exports;
        if (empty($exports))
        {
            return null;
        }

        //le noeud XSDSchema n'a qu'un fils
        return $exports->Export;
    }

    /**
     * @return \SimpleXMLElement
     */
    public function getImportsNode()
    {
        $imports = $this->m_ndHeader->children()->Imports;
        if (empty($imports))
        {
            return null;
        }

        //le noeud XSDSchema n'a qu'un fils
        return $imports->children();
    }

	//réponse générique
	const RETURNTYPE_EMPTY          = 'Empty';
    const RETURNTYPE_DONOTHING      = 'DoNothing';
	const RETURNTYPE_REPORT         = 'Report';
	const RETURNTYPE_VALUE          = 'Value';
	const RETURNTYPE_REQUESTFILTER  = 'RequestFilter';
	const RETURNTYPE_CHART          = 'Chart';
	const RETURNTYPE_NUMBEROFCHART  = 'NumberOfChart';

	//retourne des enregistrements
	const RETURNTYPE_RECORD         = 'Record';
	const RETURNTYPE_LIST           = 'List';
    const RETURNTYPE_THUMBNAIL      = 'Thumbnail';
    const RETURNTYPE_DATATREE      = 'Datatree';

	//réponse particulière
	const RETURNTYPE_XSD                = 'XSD';
	const RETURNTYPE_IDENTIFICATION     = 'Identification';
	const RETURNTYPE_PLANNING           = 'Planning'; // Vieux planning
	const RETURNTYPE_SCHEDULER          = 'Scheduler'; // Nouveau planning
	const RETURNTYPE_GLOBALSEARCH   	= 'GlobalSearch';
	const RETURNTYPE_LISTCALCULATION    = 'ListCalculation';
	const RETURNTYPE_EXCEPTION          = 'Exception';

	//réponse intermédiaire
	const RETURNTYPE_AMBIGUOUSCREATION  = 'AmbiguousAction';
	const RETURNTYPE_MESSAGEBOX         = 'MessageBox';
	const RETURNTYPE_VALIDATEACTION     = 'ValidateAction';
	const RETURNTYPE_VALIDATERECORD     = 'ValidateEnreg';
	const RETURNTYPE_PRINTTEMPLATE      = 'PrintTemplate';
	const RETURNTYPE_CHOICE             = 'Choice';

	//réponse de messagerie
	const RETURNTYPE_MAILSERVICERECORD      = 'MailServiceRecord';
	const RETURNTYPE_MAILSERVICELIST        = 'MailServiceList';
	const RETURNTYPE_MAILSERVICESTATUS      = 'MailServiceStatus';
	const RETURNTYPE_WITHAUTOMATICRESPONSE  = 'WithAutomaticResponse';

	//types virtuels pour traitement spéciaux
    const VIRTUALRETURNTYPE_FILE = "File";
    const VIRTUALRETURNTYPE_FILE_PREVIEW = "FilePreview";
    const VIRTUALRETURNTYPE_CASCADE = 'Cascade';

    //les différent type d'affichage pour les listes
    const DISPLAYMODE_List = 'List';
    const DISPLAYMODE_Chart = 'Chart';
    const DISPLAYMODE_Planning = 'Planning';
    const DISPLAYMODE_Flowchart = 'Flowchart';
    const DISPLAYMODE_DataTree = 'DataTree';
    const DISPLAYMODE_Gantt = 'Gantt';
    const DISPLAYMODE_Map = 'Map';
    const DISPLAYMODE_Thumbnail = 'Thumbnail';
}
