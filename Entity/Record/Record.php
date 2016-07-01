<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 18/07/14
 * Time: 14:14
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Record;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\RecordCache;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Update;

/**
 * Class Record, Description d'un enregistrement
 * @package NOUT\Bundle\NOUTOnlineBundle\Entity
 *
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
     * @var array
     */
    protected $m_TabOptionsRecord;

    /**
     * @var array
     */
    protected $m_TabOptionsLayout;

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
	 * @var RecordCache
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
        $this->m_TabOptionsRecord   = array();

		//tableau des éléments liés
		$this->m_TabRecordLie = new RecordCache();
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
     * @param $option
     * @param $valeur
     * @return $this
     */
    public function addOption($option, $valeur)
    {
        $this->m_TabOptionsRecord[$option]=$valeur;
        return $this;
    }
    /**
     * @param $option
     * @param $valeur
     * @return $this
     */
    public function addOptions(\SimpleXMLElement $tabAttribut)
    {
        foreach($tabAttribut as $name=>$attr)
        {
            $nCmp = strncasecmp($name, 'record', strlen('record'));
            if ($nCmp==0)
            {
                $this->addOption($name, (string)$attr);
            }
        }
        return $this;
    }

    /**
     * @param $option
     * @return mixed
     */
    public function getOption($option)
    {
        if (isset($this->m_TabOptionsRecord[$option]))
        {
            return $this->m_TabOptionsRecord[$option];
        }
    }


    /**
     * @param $option
     * @param $valeur
     * @return $this
     */
    public function addOptionLayout($option, $valeur)
    {
        $this->m_TabOptionsLayout[$option]=$valeur;
        return $this;
    }
    /**
     * @param $option
     * @param $valeur
     * @return $this
     */
    public function addOptionsLayout(\SimpleXMLElement $tabAttribut)
    {
        foreach($tabAttribut as $name=>$attr)
        {
            $this->addOptionLayout($name, (string)$attr);
        }
        return $this;
    }

    /**
     * @param $option
     * @return mixed
     */
    public function getOptionLayout($option)
    {
        if (isset($this->m_TabOptionsLayout[$option]))
        {
            return $this->m_TabOptionsLayout[$option];
        }
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
     * @return RecordCache
     */
    public function getRecordLie()
    {
        return $this->m_TabRecordLie;
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
     * @return StructureColonne
     */
    public function clGetStructColonne($idColonne)
    {
        return $this->m_clStructElem->getStructureColonne($idColonne);
    }


    /**
	 * @return StructureElement
	 */
	public function clGetStructElem()
	{
		return $this->m_clStructElem;
	}

    /**
     * @return array
     */
    public function aGetTabOptionsLayout()
    {
        return $this->m_TabOptionsLayout;
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
     * retourne la valeur stockée
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
     * retourne le titre d'un Enreg Lié
     * @param $idRecordLie
     * @param $idColonne
     * @return string
     */
    public function getTitleFromIDRecordLie($idRecordLie, $idColonne)
    {
        if (!isset($this->m_TabRecordLie) || !isset($this->m_clStructElem))
        {
            return "";
        }

        $clStructureColonne = $this->m_clStructElem->getStructureColonne($idColonne);
        $record = $this->m_TabRecordLie->getRecord($clStructureColonne->getOption(StructureColonne::OPTION_LinkedTableID), $idRecordLie);

        return $record->getTitle();
    }



    /**
     * retourne la valeur affichée
     * @param $idColonne
     * @return mixed
     */
    public function getDisplayValCol($idColonne)
    {
        if (!isset($this->m_TabColumnsValues[$idColonne]))
        {
            return;
        }

        $clStructureColonne = $this->m_clStructElem->getStructureColonne($idColonne);

        switch($clStructureColonne->getTypeElement())
        {
            case StructureColonne::TM_Tableau:
            {
                $valStockee = $this->m_TabColumnsValues[$idColonne];
                if (empty($valStockee) || ($valStockee=='0'))
                {
                    return '';
                }

                $clRecordLie = $this->m_TabRecordLie->getRecord($clStructureColonne->getOption(StructureColonne::OPTION_LinkedTableID), $valStockee);
                /** @var Record|null $clRecordLie */
                if (is_null($clRecordLie))
                {
                    return "#{$valStockee}#";
                }
                return $clRecordLie->getTitle();
            }

            default:
            {
                return $this->m_TabColumnsValues[$idColonne];
            }
        }

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
     * @param $nNiv
	 * @param Record $clRecordLie
	 * @return $this
	 */
	public function addRecordLie($nNiv, Record $clRecordLie)
	{
        $this->m_TabRecordLie->SetRecord($nNiv, $clRecordLie);
		return $this;
	}

	/**
     * @param $nNiv
	 * @param array $clRecordLie
	 * @return $this
	 */
	public function addTabRecordLie($nNiv, $aRecordsLies)
	{
		foreach($aRecordsLies as $clRecord)
		{
            $this->addRecordLie($nNiv, $clRecord);
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
		$clParamUpdate              = new Update();

		$sIDForm                    = $this->m_clStructElem->getID();
		$clParamUpdate->Table       = $sIDForm;
		$clParamUpdate->ParamXML    = '<id_'.$sIDForm.'>'.$this->m_nIDEnreg.'</id_'.$sIDForm.'>';
		$clParamUpdate->UpdateData  = '<xml><id_'.$sIDForm.'>';

		foreach($this->m_TabColumnsValues as $sIDColonne=>$sValue)
		{
			if ($this->m_TabColumnsModified[$sIDColonne])
            {
                if(is_array($sValue))
                {
                    $listValue = "";
                    foreach ($sValue as $key => $value)
                    {
                        $listValue .= $value;
                        $listValue .= '|';
                    }
                    $sValue = rtrim($listValue, "|");
                }
                $clParamUpdate->UpdateData.='<id_'.$sIDColonne.'>'.$sValue.'</id_'.$sIDColonne.'>';
            }
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
     * Tableau clé->valeur pour les colonne/liste élements
     * Appelé dans transformViewRecord2JSON
     * @return array
     */
    public function getLinkedElems()
    {
        // Récupère un tableau associatif [Id Colonne] -> [Id TmTab] pour tout le formulaire
        return $this->m_clStructElem->getTabColonneTmTab();
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

    /**
     * @param $option
     * @return mixed|null
     */
    public function transformOption2CSSProperty($option)
    {

        $SIMAXStyleToCSS = array(
            "color"     => "color",         // Couleur du texte
            "bgcolor"   => "background",    // Couleur de fond
            "bold"      => "font-weight",   // Epaisseur (blod..)
            "italic"    => "font-style"     // Normal, italique..
        );

        if (array_key_exists($option, $SIMAXStyleToCSS))
        {
            return $SIMAXStyleToCSS[$option];
        }
    }

    public function transformOptionValue2CSSValue($option, $value=null)
    {
        if (is_null($value))
        {
            $value = $this->getOptionLayout($option);
        }

        // Si c'est une couleur on doit rajouter le #
        // Si c'est un 0 ou un 1 on doit envoyer la bonne valeur..
        // donc ça dépend aussi de l'option

        switch($option)
        {
            case "bold":
            case "italic":
            {
                if($value == "1")
                {
                    return $option;
                }
                return "";
            }

            case "color":
            case "bgcolor":
                return '#'.$value;
        }

        return null;
    }


    public function setFilesData($filesToSend)
    {
        // Structure attendue des données XML d'un fichier
        /*
        <id_38566479741459 id="38738278443168">                     // Identifiant 1 = idFormulaire et 2 = idEnreg ???
            <id_47723350017105 simax:ref="14673000757953052016">    // Identifiant 3 = idColonne et 4 = id unique au choix, retrouvé dans ref
                lst_oper_L33-1 (1) (1) (1).csv
            </id_47723350017105>
            <simax:Data
            simax:ref = "14673000757953052016"
            simax:title = "lst_oper_L33-1 (1) (1) (1).csv"
            simax:encoding = "base64"
            simax:size = "215465"
            simax:filename = "lst_oper_L33-1 (1) (1) (1).csv"
            simax:typemime = "text/plain" >
                fileContentHere
            </simax:Data>
        </id_38566479741459>
        */

    }


    const OPTION_Icon       = 'recordIconID';
    const OPTION_RColor     = 'recordColor';

    const OPTION_Bold       = 'bold';
    const OPTION_Color      = 'color';
    const OPTION_BGColor    = 'bgcolor';
    const OPTION_Italic     = 'italic';
}
