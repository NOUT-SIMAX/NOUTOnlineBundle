<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 18/07/14
 * Time: 14:14
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Record;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Update;

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
	 * @var string $m_sTitle : contient la mini desc de l'enregistrement
	 */
	protected $m_sTitle;
	/**
	 * @var string $m_nID : identitifant de l'enregistrement
	 */
	protected $m_nIDEnreg;

	/**
	 * @var string $m_nIDTableau : identifiant du formulaire
	 */
	protected $m_nIDTableau;

	/**
	 * @var InfoColonne[] $m_TabColumns : tableau avec les informations variables des colonnes (mise en forme ...)
	 */
	protected $m_TabColumnsInfo;

	/**
	 * @var array $m_TabColumnsValues : tableau avec les valeurs des colonnes
	 */
	protected $m_TabColumnsValues;
	/**
	 * @var array $m_TabColumnsModified : tableau de booleen pour indiquer que la valeur à changée
	 */
	protected $m_TabColumnsModified;

	/**
	 * @var StructureElement
	 */
	protected $m_clStructElem;

	/**
	 * @var array
	 */
	protected $m_TabRecordLie;

	/**
	 * @var int
	 */
	protected $m_nXSDNiv;

	/**
	 * @param Form $clForm : information sur le formulaire
	 */
	public function __construct($sIDTableau, $sIDEnreg, $sLibelle, $nNiv, StructureElement $clStruct = null)
	{
		$this->m_nIDTableau   = $sIDTableau;
		$this->m_nIDEnreg     = $sIDEnreg;
		$this->m_sTitle       = $sLibelle;
		$this->m_clStructElem = $clStruct;
		$this->m_nXSDNiv      = (int)$nNiv;

		$this->m_TabColumnsInfo     = array();
		$this->m_TabColumnsModified = array();
		$this->m_TabColumnsValues   = array();

		//tableau des éléments liés
		$this->m_TabRecordLie = array();
	}

	/**
	 * @param Record $clRecord
	 * @return bool
	 */
	public function isBetterLevel(Record $clRecord)
	{
		return $this->m_nXSDNiv <= $clRecord->m_nXSDNiv;
	}


    /**
     * renvoi l'identifiant de l'enregistrement
     * @return string
     */
	public function getIDEnreg()
	{
		return $this->m_nIDEnreg;
	}

	/**
	 * @return string
	 */
	public function getIDTableau()
	{
		return $this->m_nIDTableau;
	}

    /**
     * renvoi la minidesc de l'enregistrement
     * @return string
     */
    public function getTitle()
    {
        return $this->m_sTitle;
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
     * @param $idColonne
     * @return InfoColonne|null
     */
    public function getInfoColonne($idColonne)
    {
        if (!isset($this->m_TabColumnsInfo[$idColonne]))
        {
            return null;
        }

        return $this->m_TabColumnsInfo[$idColonne];
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
     * @param $idColonne
     * @return bool
     */
    public function isModified($idColonne)
    {
        return $this->m_TabColumnsModified[$idColonne];
    }

	/**
	 * @param $idcolonne
	 * @param $value
	 * @param bool $modified
	 * @return $this
	 */
	public function setValCol($idcolonne, $value, $modifiedByUser = true)
	{
		$this->m_TabColumnsValues[$idcolonne]   = $value;
		$this->m_TabColumnsModified[$idcolonne] = $modifiedByUser;

		return $this;
	}

	/**
	 * @param Record $clRecordLie
	 * @return $this
	 */
	public function addRecordLie(Record $clRecordLie)
	{
		$sCle = $clRecordLie->getIDTableau().'/'.$clRecordLie->getIDEnreg();
		if (!isset($this->m_TabRecordLie[$sCle]))
		{
			$this->m_TabRecordLie[$sCle]=$clRecordLie;
		}
		return $this;
	}

	/**
	 * @param array $clRecordLie
	 * @return $this
	 */
	public function addTabRecordLie($aRecordsLies)
	{
		foreach($aRecordsLies as $clRecord)
		{
			$this->addRecordLie($clRecord);
		}
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


	/**
	 * on construit la structure qui est passée en paramètre de la méthode update du Proxy
	 * @return Update
	 */
	public function getStructForUpdateSOAP()
	{
		$clParamUpdate        = new Update();

		$sIDForm = $this->m_clStructElem->getID();
		$clParamUpdate->Table = $sIDForm;
		$clParamUpdate->ParamXML = '<id_'.$sIDForm.'>'.$this->m_nIDEnreg.'</id_'.$sIDForm.'>';
		$clParamUpdate->UpdateData = '<xml><id_'.$sIDForm.'>';

		foreach($this->m_TabColumnsValues as $sIDColonne=>$sValue)
		{
			if ($this->m_TabColumnsModified[$sIDColonne])
				$clParamUpdate->UpdateData.='<id_'.$sIDColonne.'>'.$sValue.'</id_'.$sIDColonne.'>';
		}

		$clParamUpdate->UpdateData.= '</id_'.$sIDForm.'></xml>';
		return $clParamUpdate;
	}

    /**
     * retourne la liste des colonnes qui déclenchent un update partiel
     * @return array
     */
    public function getLinkedColumns()
    {
        return $this->m_clStructElem->getTabColonneAvecOption(StructureColonne::OPTION_Link);
    }

    /**
     * enlève toutes les colonnes modifiées
     * @return $this
     */
    public function resetLastModified()
    {
        array_walk($this->m_TabColumnsModified, function(&$item, $key){
            $item=false;
        });
        return $this;
    }

    /**
     * met à jour l'enregistrement depuis la réponse de NOUTOnline
     * @param Record $clRecordSrc
     * @return $this
     */
    public function updateFromRecord(Record $clRecordSrc)
    {
        $this->resetLastModified();

        //mise à jour du titre
        $this->m_sTitle = $clRecordSrc->getTitle();

        //mise à jour des valeurs
        foreach($clRecordSrc->m_TabColumnsValues as $idcolonne=>$value)
        {
            $this->setValCol($idcolonne, $value);
        }

        return $this;
    }

}
