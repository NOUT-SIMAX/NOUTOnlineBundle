<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 18/07/14
 * Time: 14:14
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Record;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\Element;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\Form;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\OptionDialogue;

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
	public $m_sTitle;
	/**
	 * @var $m_nID : identitifant de l'enregistrement
	 */
	public $m_nIDEnreg;

	/**
	 * @var $m_nIDTableau : identifiant du formulaire
	 */
	public $m_nIDTableau;

	/**
	 * @var $m_TabColumns : tableau avec les informations variables des colonnes (valeur, mise en forme ...)
	 */
	public $m_TabColumns;

	/**
	 * @var $m_clStructElem : classe contenant la structure
	 */
	public $m_clStructElem;

	/**
	 * @param Form $clForm : information sur le formulaire
	 */
	public function __construct()
	{
		$this->m_nIDTableau = '';
		$this->m_nIDEnreg = '';
		$this->m_sTitle='';
		$this->m_TabColumns=null;
		$this->m_clStructElem=null;
	}


	public function sGetValCol($idColonne)
	{
		if (isset($this->m_TabColumns) && isset($this->m_TabColumns[$idColonne]))
			return $this->m_TabColumns[$idColonne]->m_sValeur;

		return null;
	}



} 