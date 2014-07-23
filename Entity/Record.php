<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 18/07/14
 * Time: 14:14
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\OptionDialogue;

/**
 * Class Record
 * @package NOUT\Bundle\NOUTOnlineBundle\Entity
 *
 *
 * classe qui gère un enregistrement NOUT
 * les membres ne commence pas par m_ et ne sont pas préfixé pour le json_encode et json_decode
 *
 * format du json :
 * {
 *      "title": "mini desc du datasource",
 *      "id": 12345,
 *      "columns": [
 *      {
 *          "<id de la colonne>":
 *          {
 *              "value": "valeur de la colonne",
 *              "formatting": { "bold":false, "italic":true, "color": "#ffeeff", "bgcolor": "#ffeeff"},
 *              "fcs": "(hidden|readonly|disabled)",
 *              "file": { "encoding": "base64", "size":1254, "mimtype":"text/javascript", "filename": "test.js"}
 *          },...
 *      }
 *      ]
 * }
 */
class Record
{

	/**
	 * @var $m_sTitle : contient la mini desc de l'enregistrement
	 */
	protected $m_sTitle;
	/**
	 * @var $m_nID : identitifant de l'enregistrement
	 */
	protected $m_nID;
	/**
	 * @var $m_TabColumns : tableau avec les informations variables des colonnes (valeur, mise en forme ...)
	 */
	protected $m_TabColumns;

	/**
	 * @var $m_clForm : classe qui contient les informations sur le formulaire
	 */
	protected $m_clForm;

	/**
	 * @var $m_clElement : classe qui contient les informations sur l'enregistrement
	 */
	protected $m_clElement;


	const LEVEL_RECORD = 1;
	const LEVEL_LIST = 2;
	const LEVEL_TITLE = 3;
	/**
	 * @var $m_nLevel : niveau de la description de l'enregistrement
	 */
	protected $m_nLevel;

	/**
	 * @param Form $clForm : information sur le formulaire
	 */
	public function __construct($nLevel, Form $clForm, Element $clElement)
	{
		$this->m_nLevel = $nLevel;
		$this->m_clForm = $clForm;
		$this->m_clElement = $clElement;
		$this->m_nID = '';
		$this->m_TabColumns=null;
		$this->$m_sTitle='';
	}

	/**
	 * @param OptionDialogue $clOptionDialogue : option de dialogue qui a donner la réponse
	 * @param \SimpleXMLElement $clXML
	 * @param \SimpleXMLElement $clSchema : schema, peut être null si la structure n'est pas utile
	 */
	public function initFromReponseWS(OptionDialogue $clOptionDialogue, \SimpleXMLElement $clXML, \SimpleXMLElement $clSchema=null)
	{
		//il faut trouver le noeud de l'enregistrement dans le XML
		$sXPath='';
		if (!$clOptionDialogue->Readable)
		{
			//pas lisible
			$sXPath='.//id_'.$this->m_clForm->getID();
		}
		$sXPath.='[simax:id='.$this->m_clElement->getID().']';


		$clEnreg=$clXML->xpath($sXPath);
		var_dump($clEnreg);
	}

	/**
	 * initialise les colonnes à plat
	 * @param \SimpleXMLElement $clXML
	 * format du json pour les colonnes:
	 * {
	 *          "<id de la colonne>":
	 *          {
	 *              "value": "valeur de la colonne",
	 *              "formatting": { "bold":false, "italic":true, "color": "#ffeeff", "bgcolor": "#ffeeff"},
	 *              "fcs": "(hidden|readonly|disabled)",
	 *              "file": { "encoding": "base64", "size":1254, "mimtype":"text/javascript", "filename": "test.js"}
	 *          },...
	 *      }
	 *      ]
	 * }
	 */
	protected function _initFromEnregXML(OptionDialogue $clOptionDialogue, \SimpleXMLElement $clXML)
	{


	}




} 