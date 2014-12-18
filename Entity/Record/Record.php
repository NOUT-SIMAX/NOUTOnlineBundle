<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 18/07/14
 * Time: 14:14
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Record;





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
	protected $m_nIDEnreg;

	/**
	 * @var $m_nIDTableau : identifiant du formulaire
	 */
	protected $m_nIDTableau;

	/**
	 * @var $m_TabColumns : tableau avec les informations variables des colonnes (mise en forme ...)
	 */
	protected $m_TabColumnsInfo;

	/**
	 * @var $m_TabColumnsValues : tableau avec les valeurs des colonnes
	 */
	protected $m_TabColumnsValues;
	/**
	 * @var $m_TabColumnsModified : tableau de booleen pour indiquer que la valeur à changée
	 */
	protected $m_TabColumnsModified;

	/**
	 * @var StructureElement
	 */
	protected $m_clStructElem;

	/**
	 * @param Form $clForm : information sur le formulaire
	 */
	public function __construct($sIDTableau, $sIDEnreg, $sLibelle, StructureElement $clStruct = null)
	{
		$this->m_nIDTableau  = $sIDTableau;
		$this->m_nIDEnreg    = $sIDEnreg;
		$this->m_sTitle      = $sLibelle;
		$this->m_clStructElem = $clStruct;

		$this->m_TabColumnsInfo    = array();
		$this->m_TabColumnsModified = array();
		$this->m_TabColumnsValues  = array();
	}

	/**
	 * @return StructureElement
	 */
	public function clGetStructElem()
	{
		return $this->m_clStructElem;
	}

	/**
	 * @param InfoColonne $clInfoColonne
	 * @return $this
	 */
	public function setInfoColonne(InfoColonne $clInfoColonne)
	{
		$this->m_TabColumnsInfo[$clInfoColonne->getIDColonne()] = $clInfoColonne;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function bRef()
	{
		return !empty($this->m_TabColumnsInfo);
	}

	/**
	 * @param $idColonne
	 * @return mixed
	 */
	public function getValCol($idColonne)
	{
		if (!isset($this->m_TabColumnsValues[$idColonne]))
		{
			return;
		}

		return $this->m_TabColumnsValues[$idColonne];
	}

	/**
	 * @param $idcolonne
	 * @param $value
	 * @param bool $modified
	 * @return $this
	 */
	public function setValCol($idcolonne, $value, $modifiedByUser = true)
	{
		$this->m_TabColumnsValues[$idcolonne]  = $value;
		$this->m_TabColumnsModified[$idcolonne] = $modifiedByUser;

		return $this;
	}


	/**
	 * méthode magique pour les formulaires
	 * @param $idColonne
	 * @return null
	 */
	public function __get($idColonne)
	{
		if (in_array($idColonne, array('m_sTitle', 'm_nIDEnreg', 'm_nIDTableau', 'm_TabColumnsInfo', 'm_TabColumnsValues', 'm_TabColumnsModified', 'm_clStructElem')))
		{
			throw new \Exception("Accès au membre $idColonne de ".get_class($this).'via __get() n\'est pas autorisé');
		}

		return $this->getValCol($idColonne);
	}

	/**
	 * méthode magique pour les formulaires
	 * @param $name
	 * @param $args
	 * @return $this
	 */
	public function __set($idColonne, $value)
	{
		if (in_array($idColonne, array('m_sTitle', 'm_nIDEnreg', 'm_nIDTableau', 'm_TabColumnsInfo', 'm_TabColumnsValues', 'm_TabColumnsModified', 'm_clStructElem')))
		{
			throw new \Exception("Accès au membre $idColonne de ".get_class($this).'via __call() n\'est pas autorisé');
		}

		return $this->setValCol($idColonne, $value, true);
	}
}
