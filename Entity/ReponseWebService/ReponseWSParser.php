<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 04/08/14
 * Time: 10:54
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService;

use NOUT\Bundle\NOUTOnlineBundle\Entity\Header\OptionDialogue;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\ColonneRestriction;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\EnregTableau;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\EnregTableauArray;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\InfoColonne;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\Record;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureColonne;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureElement;

/**
 * Class RecordManager
 * @package NOUT\Bundle\NOUTOnlineBundle\Entity\Record
 */
class ReponseWSParser
{
	/**
	 * @var array
	 * map qui contient la structure des formulaires
	 */
	public $m_MapIDTableau2Niv2StructureElement;
	/**
	 * @var array
	 * map qui contient l'association IDTableau=>IDEnreg=>Objet Record
	 */
	public $m_MapIDTableau2IDEnreg2Record;
	/**
	 * @var array;
	 * tableau qui contient l'ordre des enregistrements avec conservation de l'ordre de la réponse
	 */
	public $m_TabEnregTableau;
	/**
	 * @var array
	 * map qui associe une colonne à un calcul de fin de liste
	 */
	public $m_MapColonne2Calcul;
	/**
	 * @var array
	 * map qui associe une référence au data qui correspond
	 */
	public $m_MapRef2Data;

	/**
	 * @var array
	 * tableau qui contient les événements du planning
	 */
	public $m_TabEventPlanning;

	/**
	 * @var Chart
	 * membre qui contient le graphe
	 */
	public $m_clChart;

	public function __construct()
	{
		$this->m_MapIDTableau2Niv2StructureElement = array();
		$this->m_MapIDTableau2IDEnreg2Record = array();
		$this->m_TabEnregTableau = new EnregTableauArray();
		$this->m_MapColonne2Calcul = array();
		$this->m_MapRef2Data = array();
		$this->m_TabEventPlanning=array();
		$this->m_clChart = null;
	}

	/**
	 * @param $sIDTableau
	 * @return StructureElement
	 */
	public function clGetStructureElement($sIDTableau)
	{
		if (!isset($this->m_MapIDTableau2Niv2StructureElement) ||
			!isset($this->m_MapIDTableau2Niv2StructureElement[$sIDTableau]))
			return null;

		if (isset($this->m_MapIDTableau2Niv2StructureElement[$sIDTableau][StructureElement::NV_XSD_Enreg]))
			return $this->m_MapIDTableau2Niv2StructureElement[$sIDTableau][StructureElement::NV_XSD_Enreg];

		if (isset($this->m_MapIDTableau2Niv2StructureElement[$sIDTableau][StructureElement::NV_XSD_List]))
			return $this->m_MapIDTableau2Niv2StructureElement[$sIDTableau][StructureElement::NV_XSD_List];

		if (isset($this->m_MapIDTableau2Niv2StructureElement[$sIDTableau][StructureElement::NV_XSD_LienElement]))
			return $this->m_MapIDTableau2Niv2StructureElement[$sIDTableau][StructureElement::NV_XSD_LienElement];

		return null;
	}

	/**
	 * @param Form $clForm
	 * @param Element $clElement
	 * @return null|Record
	 */
	public function clGetRecord(Form $clForm, Element $clElement)
	{
		return $this->clGetRecordFromId($clForm->getID(), $clElement->getID());
	}

	/**
	 * @param $sIDForm
	 * @param $sIDEreng
	 * @return null|Record
	 */
	public function clGetRecordFromId($sIDForm, $sIDEreng)
	{
		if (    !isset($this->m_MapIDTableau2IDEnreg2Record)
			||  !isset($this->m_MapIDTableau2IDEnreg2Record[$sIDForm])
			||  !isset($this->m_MapIDTableau2IDEnreg2Record[$sIDForm][$sIDEreng]))
			return null;

		return $this->m_MapIDTableau2IDEnreg2Record[$sIDForm][$sIDEreng];
	}

	/**
	 * @param $form
	 * @return array : tableau des identifiants des enregistrements
	 */
	public function GetTabIDEnregFromForm($form)
	{
		if ($form instanceof Form)
			$form = $form->getID();

		if (    !isset($this->m_MapIDTableau2IDEnreg2Record)
			||  !isset($this->m_MapIDTableau2IDEnreg2Record[$form]))
			return array();

		return array_keys($this->m_MapIDTableau2IDEnreg2Record[$form]);
	}

	/**
	 * @param $form
	 * @return array|EnregTableauArray : tableau des identifiants des enregistrements
	 */
	public function GetTabEnregTableau()
	{
		return $this->m_TabEnregTableau;
	}

	/**
	 * @param $nRef
	 * @return Data
	 */
	public function clGetData($nRef)
	{
		return $this->m_MapRef2Data[$nRef];
	}

	/**
	 * @return array
	 */
	public function GetTabData()
	{
		return $this->m_MapRef2Data;
	}


	/**
	 * @param $nNiveau
	 * @param \SimpleXMLElement $clSchema
	 * @param array $MapStructureElement
	 */
	protected function _ParseXSD($nNiveau, \SimpleXMLElement $clSchema)
	{
		//récupération du noeud element fils de schema
		$ndElement = $clSchema->children('http://www.w3.org/2001/XMLSchema')->element;

		$TabAttribXS = $ndElement->attributes('http://www.w3.org/2001/XMLSchema');
		$TabAttribSIMAX = $ndElement->attributes('http://www.nout.fr/XMLSchema');

		//on récupère l'id du formulaire, son libelle et le type
		$sIDTableau = str_replace('id_', '', $TabAttribXS['name']);

		if (isset($this->m_MapIDTableau2Niv2StructureElement[$sIDTableau]) && isset($this->m_MapIDTableau2Niv2StructureElement[$sIDTableau][$nNiveau]))
			return ; //on a déjà parsé cette partie de l'XSD

		if (count($ndElement->children('http://www.w3.org/2001/XMLSchema'))>0)
		{
			$clStructureElement = new StructureElement();
			$clStructureElement->m_nNiveau = $nNiveau;
			$clStructureElement->m_nID = $sIDTableau;
			$clStructureElement->m_sLibelle = (string)$TabAttribSIMAX['name'];

			//$sType = $TabAttribSIMAX['tableType'];

			$ndSequence = $ndElement->children('http://www.w3.org/2001/XMLSchema')->complexType
				->children('http://www.w3.org/2001/XMLSchema')->sequence;

			$this->__ParseXSDSequence($nNiveau, $clStructureElement, $ndSequence, null);
		}

	}

	/**
	 * @param $clStructCurrent (StructureElement ou StructureColonne)
	 * @param $MapStructureElement
	 * @param \SimpleXMLElement $clSequence
	 */
	protected function __ParseXSDSequence($nNivCourant, StructureElement $clStructElement, \SimpleXMLElement $clSequence, $sIDColonnePere)
	{
		//'http://www.nout.fr/XMLSchema'

		foreach($clSequence->children('http://www.w3.org/2001/XMLSchema') as $ndNoeud)
		{
			if ($ndNoeud->getName() != 'element')
				continue; //je cherche quel les xs:element

			$clAttribXS = $ndNoeud->attributes('http://www.w3.org/2001/XMLSchema');
			$clAttribNOUT = $ndNoeud->attributes('http://www.nout.fr/XMLSchema');

			$sIDColonne = str_replace('id_', '', (string)$clAttribXS->name);
			$clStructElement->m_MapIDColonne2StructColonne[$sIDColonne]=new StructureColonne($sIDColonne, $clAttribNOUT);

			switch($clStructElement->m_MapIDColonne2StructColonne[$sIDColonne]->m_eTypeElement)
			{
				case StructureColonne::TM_ListeElem:
					if (count($ndNoeud->children('http://www.w3.org/2001/XMLSchema'))>0)
					{
						$ndSequence = $ndNoeud  ->children('http://www.w3.org/2001/XMLSchema')->complexType
												->children('http://www.w3.org/2001/XMLSchema')->sequence;

						$nNiveau = ($nNivCourant == StructureElement::NV_XSD_Enreg) ? StructureElement::NV_XSD_List : StructureElement::NV_XSD_LienElement;
						$this->_ParseXSD($nNiveau, $ndSequence);
					}
					break;
				case StructureColonne::TM_Separateur:
					if (count($ndNoeud->children('http://www.w3.org/2001/XMLSchema'))>0)
					{
						$ndSequence = $ndNoeud  ->children('http://www.w3.org/2001/XMLSchema')->complexType
							->children('http://www.w3.org/2001/XMLSchema')->sequence;

						$this->__ParseXSDSequence($nNivCourant, $clStructElement, $ndSequence, $sIDColonne);
					}
					break;
				case '':
					//il faut vérifier si pas extension d'un type de base (texte avec une limite de 100)
					if (count($ndNoeud->children('http://www.w3.org/2001/XMLSchema'))>0)
					{
						$ndSimpleType = $ndNoeud  ->children('http://www.w3.org/2001/XMLSchema')->simpleType
							->children('http://www.w3.org/2001/XMLSchema')->restriction;

						$clStructElement->m_MapIDColonne2StructColonne[$sIDColonne]->m_eTypeElement = (string)$ndSimpleType->attributes('http://www.w3.org/2001/XMLSchema')['base'];

						$clStructElement->m_MapIDColonne2StructColonne[$sIDColonne]->m_clRestriction = new ColonneRestriction();

						foreach($ndSimpleType->children('http://www.w3.org/2001/XMLSchema') as $ndFils)
						{
							$clStructElement->m_MapIDColonne2StructColonne[$sIDColonne]->m_clRestriction->m_sTypeRestriction = $ndFils->getName();
							switch($ndFils->getName())
							{
								case ColonneRestriction::R_MAXLENGTH:
									$clStructElement->m_MapIDColonne2StructColonne[$sIDColonne]->m_clRestriction->m_ValeurRestriction = (int)$ndFils->attributes('http://www.w3.org/2001/XMLSchema')['value'];
									break;
							}
						}

					}
					break;

				case StructureColonne::TM_Combo:
					//il faut vérifier si pas extension d'un type de base (texte avec une limite de 100)
					if (count($ndNoeud->children('http://www.w3.org/2001/XMLSchema'))>0)
					{
						$ndSimpleType = $ndNoeud  ->children('http://www.w3.org/2001/XMLSchema')->simpleType
							->children('http://www.w3.org/2001/XMLSchema')->restriction;

						$clStructElement->m_MapIDColonne2StructColonne[$sIDColonne]->m_clRestriction = new ColonneRestriction();
						foreach($ndSimpleType->children('http://www.w3.org/2001/XMLSchema') as $ndFils)
						{
							$clStructElement->m_MapIDColonne2StructColonne[$sIDColonne]->m_clRestriction->m_sTypeRestriction = $ndFils->getName();
							switch($ndFils->getName())
							{
								case ColonneRestriction::R_ENUMERATION:
									$clStructElement->m_MapIDColonne2StructColonne[$sIDColonne]->m_clRestriction->m_ValeurRestriction[(string)$ndFils->attributes('http://www.w3.org/2001/XMLSchema')['id']] = (string)$ndFils->attributes('http://www.w3.org/2001/XMLSchema')['value'];
									break;
							}
						}
					}
					break;

			}

			if (!isset($sIDColonnePere))
				$clStructElement->m_TabStructureColonne[] = $clStructElement->m_MapIDColonne2StructColonne[$sIDColonne];
			else
				$clStructElement->m_MapIDColonne2StructColonne[$sIDColonnePere]->m_TabStructureColonne[]=$clStructElement->m_MapIDColonne2StructColonne[$sIDColonne];
		}

		if (!isset($sIDColonnePere))
			$this->m_MapIDTableau2Niv2StructureElement[$clStructElement->m_nID][$clStructElement->m_nNiveau]=$clStructElement;
	}


	/**
	 * @param \SimpleXMLElement $clXML
	 */
	protected function _ParseXML(\SimpleXMLElement $clXML, $sIDForm)
	{
		/*
		 * xmlns:simax="http://www.nout.fr/XML/"
         * xmlns:simax-layout="http://www.nout.fr/XML/layout"
		 */

		//on commence par faire un premier tour, pour récuperer les formulaires présent au premier niveau
		$TabFormPresent=array();
		foreach($clXML->children() as $clNoeud)
		{
			$sTagName = $clNoeud->getName();
			if (strncmp($sTagName, 'id_', strlen('id_'))==0)
				$TabFormPresent[] = str_replace('id_', '', $sTagName);
		}


		foreach($clXML->children() as $clNoeud)
		{
			$sTagName = $clNoeud->getName();
			if (strncmp($sTagName, 'id_', strlen('id_'))==0)
			{
				$this->_ParseRecord($clNoeud, $TabFormPresent, $sIDForm);
				continue;
			}

			if (strcmp($sTagName, 'event')==0)
			{
				$this->_ParseEvent($clNoeud);
				continue;
			}

			if (strcmp($sTagName, 'Data')==0)
			{
				$this->_ParseData($clNoeud);
				continue;
			}
		}
	}

	/**
	 * Parse la balise Data
	 * @param \SimpleXMLElement $clXML
	 */
	protected function _ParseData(\SimpleXMLElement $ndData)
	{
		/*
		 * <Data simax:size="12770"
		 *  simax:filename="C:\Users\NINON~1.NOU\AppData\Local\Temp\Utilisateur - superviseur.html"
		 * simax:typemime="text/html"
		 * simax:encoding="base64"
		 * simax:ref="0"> ... </Data>
		 */

		$TabAttrib = $ndData->attributes('http://www.nout.fr/soap');

		$clData = new Data();
		$clData->m_nRef = (int)$TabAttrib['ref'];
		$clData->m_nSize = (int)$TabAttrib['size'];
		$clData->m_sEncoding = (string)$TabAttrib['encoding'];
		$clData->m_sFileName = (string)$TabAttrib['filename'];
		$clData->m_sMimeType = (string)$TabAttrib['typemime'];
		$clData->m_sContent = (string)$ndData;

		$this->m_MapRef2Data[$clData->m_nRef]=$clData;
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

		$TabAttrib = $ndEvent->attributes('http://www.nout.fr/XML/');

		$clEvent = new Event();
		$clEvent->m_sUID = (string)$TabAttrib['uid'];
		$clEvent->m_sStartTime = (string)$TabAttrib['startTime'];
		$clEvent->m_sEndTime = (string)$TabAttrib['endTime'];
		$clEvent->m_sSummary = (string)$TabAttrib['summary'];
		$clEvent->m_sDescription = (string)$TabAttrib['description'];
		$clEvent->m_nIDResource = (string)$TabAttrib['resource'];
		$clEvent->m_nTypeOfEvent = (string)$TabAttrib['typeOfEvent'];
		$clEvent->m_sRrules = (string)$TabAttrib['rrules'];

		$this->m_TabEventPlanning[$clEvent->m_sUID]=$clEvent;
	}

	/**
	 * Parse un élément XML
	 * @param \SimpleXMLElement $clXML
	 * @param $TabFormPresent
	 */
	protected function _ParseRecord(\SimpleXMLElement $clXML, $TabFormPresent, $sIDForm)
	{
		//<id_47909919412330 simax:id="33475861129246" simax:title="Janvier">

		$TabAttrib = $clXML->attributes('http://www.nout.fr/XML/');

		$sIDTableau = str_replace('id_', '', $clXML->getName());
		$sIDEnreg = (string)$TabAttrib['id'];

		if (isset($sIDForm) && ($sIDForm==$sIDTableau))
			$this->m_TabEnregTableau->AddNouveau($sIDTableau, $sIDEnreg);

		if (!isset($this->m_MapIDTableau2IDEnreg2Record[$sIDTableau]))
			$this->m_MapIDTableau2IDEnreg2Record[$sIDTableau] = array();

		if (!isset($this->m_MapIDTableau2IDEnreg2Record[$sIDTableau][$sIDEnreg]))
		{
			$clRecord = new Record();
			$clRecord->m_nIDTableau = $sIDTableau;
			$clRecord->m_nIDEnreg = $sIDEnreg;
			$clRecord->m_sTitle = (string)$TabAttrib['title'];
			$clRecord->m_clStructElem = $this->clGetStructureElement($sIDTableau);

			$this->m_MapIDTableau2IDEnreg2Record[$sIDTableau][$sIDEnreg] = $clRecord;
		}


		$this->__ParseColumnRecord($clXML, $TabFormPresent, $sIDTableau, $sIDEnreg);
	}

	/**
	 * Parse la colonne d'un enregistrement
	 * @param \SimpleXMLElement $clXML
	 * @param $TabFormPresent
	 * @param $sIDTableau
	 * @param $sIDEnreg
	 */
	protected function __ParseColumnRecord(\SimpleXMLElement $clXML, $TabFormPresent, $sIDTableau, $sIDEnreg)
	{
		/*
		 * xmlns:simax="http://www.nout.fr/XML/"
         * xmlns:simax-layout="http://www.nout.fr/XML/layout"
		 */

		foreach($clXML->children() as $ndColonne)
		{
			$sNom = $ndColonne->getName();
			$TabAttribNOUT = $clXML->attributes('http://www.nout.fr/XML/');
			$TabAttribLayout = $clXML->attributes('http://www.nout.fr/XML/layout');

			$clInfoColonne = new InfoColonne($TabAttribNOUT, $TabAttribLayout);
			$clInfoColonne->m_nIDColonne=str_replace('id_', '', $sNom);

			if ($ndColonne->count()>0)
			{
				//on a des fils
				if (isset($this->m_MapIDTableau2IDEnreg2Record[$sIDTableau][$sIDEnreg]->m_clStructElem))
				{
					$sTypeElement = $this->m_MapIDTableau2IDEnreg2Record[$sIDTableau][$sIDEnreg]->m_clStructElem->sGetColonneTypeElement($clInfoColonne->m_nIDColonne);
					//on a la structure de l'enregistrement
					if ($sTypeElement==StructureColonne::TM_Separateur)
					{
						//c'est un séparateur, il faut faire les colonnes filles
						$this->__ParseColumnRecord($ndColonne, $TabFormPresent, $sIDTableau, $sIDEnreg);
					}
					else
					{
						//c'est pas un séparateur, cela devrait normalement être un TM_ListeElem
						if ($sTypeElement==StructureColonne::TM_ListeElem)
						{
							//il faut prendre la valeur des colonnes filles et mettre dans un tableau
							$clInfoColonne->m_Valeur = array();
							foreach($ndColonne->children() as $ndValeur)
							{
								$clInfoColonne->m_Valeur[]=(string)$ndValeur;
							}
						}
					}
				}
				else
				{
					//on a pas la structure
					//on regarde si le fils est dans le tableau d'element premier niveau $TabFormPresent
					$bFormulaire = false;
					foreach($ndColonne->children() as $ndFils)
					{
						$sID = str_replace('id_', '', $ndFils->getName());
						if (array_search($sID, $TabFormPresent)==false)
							$bFormulaire=true;

						break;
					}

					if ($bFormulaire)
					{
						//c'est pas un separateur mais une colonne liste
						$clInfoColonne->m_Valeur = array();
						foreach($ndColonne->children() as $ndValeur)
						{
							$clInfoColonne->m_Valeur[]=(string)$ndValeur;
						}
					}
					else
					{
						//c'est un séparateur, il faut faire les colonnes filles
						$this->__ParseColumnRecord($ndColonne, $TabFormPresent, $sIDTableau, $sIDEnreg);
					}
				}
			}
			else
			{
				$clInfoColonne->m_Valeur = (string)$ndColonne;
			}

			$this->m_MapIDTableau2IDEnreg2Record[$sIDTableau][$sIDEnreg]->m_TabColumns[$clInfoColonne->m_nIDColonne]=$clInfoColonne;
		}
	}

	/**
	 * Parse les calculs de fin de liste
	 * @param \SimpleXMLElement $clXML
	 */
	protected function _ParseListCaculation(\SimpleXMLElement $clXML)
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
		foreach($clXML->children() as $ndCol)
		{
			$clCalculation = new Calculation((string)$ndCol->attributes('http://www.nout.fr/XML/')['id']);
			foreach($ndCol->children() as $ndCalcul)
				$clCalculation->AddCacul((string)$ndCalcul->getName(), (string)$ndCalcul);

			$this->m_MapColonne2Calcul[$clCalculation->m_nIDColonne]=$clCalculation;
		}
	}


	/**
	 * @param \SimpleXMLElement $clSchema
	 *
	 * le schema a la forme suivante :
	<xs:schema .... xmlns:simax="http://www.nout.fr/XMLSchema" xmlns:xs="http://www.w3.org/2001/XMLSchema" >
	<xs:element xs:name="event">...
	<simax:layout>
	<xs:element simax:typeOfEvent="17151" simax:colorRGB="555500"/>
	...
	</simax:layout>
	</xs:element>
	</xs:schema>
	</XSDSchema>
	 * @return array
	 */
	protected function _GetTabParseXSD_TypeEvent2Color(\SimpleXMLElement $clSchema)
	{

		$ndLayout = $clSchema->element
			->children('http://www.nout.fr/XMLSchema')->layout;


		$MapTypeEvent2ColorRGB=array();

		foreach($ndLayout->children('http://www.w3.org/2001/XMLSchema') as $ndFils)
		{
			if (strcmp($ndFils->getName(), 'element')==0)
			{
				$TabAttributes = $ndFils->attributes('http://www.nout.fr/XMLSchema');
				$MapTypeEvent2ColorRGB[(string)$TabAttributes['typeOfEvent']]=(string)$TabAttributes['colorRGB'];
			}
		}
		return $MapTypeEvent2ColorRGB;
	}


	protected function _ParseChart($ndXML)
	{
		$ndChart = $ndXML->chart;

		$this->m_clChart->m_sTitre = (string)$ndChart->title;
		$this->m_clChart->m_sType = (string)$ndChart->chartType;

		foreach($ndChart->axes->axis as $ndAxis)
		{
			$TabAttributes = $ndAxis->attributes('http://www.nout.fr/XML/');
			$sID = (string)$TabAttributes['id'];
			$bCalculation = isset($TabAttributes['isCalculation']) ? ((int)$TabAttributes['isCalculation'] != 0) : false;
			$this->m_clChart->m_TabAxes[$sID]=new ChartAxis($sID, (string)$TabAttributes['label'], $bCalculation);
		}

		foreach($ndChart->serie->tuple as $ndTuple)
		{
			$clTuple = new ChartTuple();
			foreach($ndTuple->children() as $ndFils)
			{
				$sTagName = $ndFils->getName();
				if (strncmp($sTagName, 'id_', strlen('id_'))==0)
				{
					$sID = str_replace('id_', '', $sTagName);
					$clTuple->Add($sID, (string)$ndFils->data, (string)$ndFils->displayValue);
				}
			}
			$this->m_clChart->m_TabSeries[]=$clTuple;
		}
	}

	/**
	 * @param $sReturnType
	 * @param \SimpleXMLElement $clXML
	 * @param \SimpleXMLElement $clSchema
	 */
	public function InitFromXmlXsd(XMLResponseWS $clXMLReponseWS)
	{
		$sReturnType = $clXMLReponseWS->sGetReturnType();
		$ndXML = $clXMLReponseWS->getNodeXML();
		$ndSchema = $clXMLReponseWS->getNodeSchema();

		//on commence par les schemas
		if (    ($sReturnType == XMLResponseWS::RETURNTYPE_RECORD)
			||  ($sReturnType == XMLResponseWS::RETURNTYPE_LIST)
			||  ($sReturnType == XMLResponseWS::RETURNTYPE_AMBIGUOUSACTION)
			||  ($sReturnType == XMLResponseWS::RETURNTYPE_PRINTTEMPLATE))
		{
			if (isset($ndSchema))
			{
				$this->m_MapIDTableau2Niv2StructureElement = array();
				$nNiveau = ($sReturnType == XMLResponseWS::RETURNTYPE_RECORD) ? StructureElement::NV_XSD_Enreg : StructureElement::NV_XSD_List;
				$this->_ParseXSD($nNiveau, $ndSchema);
			}

			//après, on fait le XML
			$this->m_MapIDTableau2IDEnreg2Record = array();
			$this->m_TabEnregTableau->RemoveAll();
			$this->_ParseXML($ndXML, $clXMLReponseWS->clGetForm()->getID());

			return ;
		}

		if ($sReturnType == XMLResponseWS::RETURNTYPE_LISTCALCULATION)
		{
			//on a un retour de GetCalculation
			$this->_ParseListCaculation($ndXML);
			return ;
		}

		if ($sReturnType == XMLResponseWS::RETURNTYPE_REPORT)
		{
			//après, on fait le XML
			$this->m_MapIDTableau2IDEnreg2Record = array();
			$this->m_TabEnregTableau->RemoveAll();
			$this->_ParseXML($ndXML, $clXMLReponseWS->clGetForm()->getID());
			return ;
		}

		if ($sReturnType == XMLResponseWS::RETURNTYPE_PLANNING)
		{
			$this->m_TabEventPlanning=array();
			$MapTypeElement2Color = $this->_GetTabParseXSD_TypeEvent2Color($ndSchema);

			$this->_ParseXML($ndXML, null);

			//on met à jour les couleurs
			foreach($this->m_TabEventPlanning as $clEvent)
				$clEvent->m_sColorRGB=$MapTypeElement2Color[$clEvent->m_nTypeOfEvent];

			return ;
		}

		if ($sReturnType == XMLResponseWS::RETURNTYPE_CHART)
		{
			$this->m_clChart = new Chart();
			$this->_ParseChart($ndXML);
			return ;
		}
	}



}