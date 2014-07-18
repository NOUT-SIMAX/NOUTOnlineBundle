<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 18/07/14
 * Time: 14:14
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity;

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
	 * @var title : contient la mini desc de l'enregistrement
	 */
	protected $title;
	/**
	 * @var id : identitifant de l'enregistrement
	 */
	protected $id;
	/**
	 * @var columns : tableau avec les informations variables des colonnes (valeur, mise en forme ...)
	 */
	protected $columns;

	public function __construct()
	{


	}

	/**
	 * @param SimpleXMLElement $clEnvelope
	 * @return SimpleXMLElement le noeud racine XML qui contient la description de l'enregistrement
	 */
	protected function _ndGetXMLFromSimpleXML(SimpleXMLElement $clEnvelope)
	{


	}

	public function testXML()
	{
		$sXML = file_get_contents('./bundles/noutonline/test/xml/FormEtatChamp_fiche_listesync.xml');
		$clXML = new XMLResponseWS($sXML);




		$action = $clXML->clGetAction();
		var_dump($action);




	}




} 