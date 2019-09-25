<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 27/11/14
 * Time: 15:30
 *
 * Règle pour l'execution de l'action
 * 1 - Commande sinon vide
 * 2 - IDAction si != 0
 * 3 - Libelle de l'option
 */

namespace NOUT\Bundle\ContextsBundle\Entity\Menu;

use NOUT\Bundle\NOUTOnlineBundle\Entity\Langage;

class ItemMenu
{
    /**
     * identifiant du menu conteneur
     * @var string
     */
    protected $idMenuParent;

    /**
     * identifiant de l'option de menu
     * @var string $idOptionMenu
     */
	protected $idOptionMenu;

    /**
     * vrai si menu principal
     * @var bool
     */
    public $rootMenu;

    /**
     * vrai si c'est un menu, faux si c'est une option de menu
     * @var bool
     */
    public $optionMenu;

    /**
     * vrai si c'est un separateur
     * @var bool
     */
    public $separator;

	/**
	 * libellé de l'option de menu
	 * @var string
	 */
	public $title;

	/**
	 * identifiant de l'action à lancer
	 * @var string
	 */
	public $idAction = null;

	/**
	 * commande de l'action
	 * @var string
	 */
    public $command = null;

    /**
     * id de l'icone
     * @var string
     */
    public $iconSmall = '';

    /**
     * id de l'icone
     * @var string
     */
    public $iconBig = '';


    /**
     * tableau des options de menu
     * @var array
     */
    public $tabOptions = array();

    /*******************************************************************/
    /* LA PAGE D'ACCUEIL */
    /**
     * @var int
     */
    public $homeTypeDisplay = null;

    /**
     * @var string
     */
    public $homeTitle;

    /**
     * @var string
     */
    public $homeDesc;

    /**
     * @var bool
     */
    public $homeWithImg=false;

    /**
     * @var int
     */
    public $homeWidth=0;

    /**
     * @var int
     */
    public $homeHeight=0;



	/**
	 * @param $idOptionMenu
	 * @param $sLibelle
	 * @param $bEstOptionMenu
	 */
	public function __construct($idOptionMenu, $sLibelle, $bEstOptionMenu)
	{
		$this->idOptionMenu = is_string($idOptionMenu) ? $idOptionMenu : sprintf ( "%.0f", $idOptionMenu );
		$this->title = str_replace('&&', '&', $sLibelle);
		$this->optionMenu = $bEstOptionMenu;
	}

    /**
     * @return string
     */
    public function getIdMenuParent()
    {
        return $this->idMenuParent;
    }

    /**
     * @param string $idMenuParent
     * @return $this
     */
    public function setIdMenuParent($idMenuParent)
    {
        $this->idMenuParent = is_string($idMenuParent) ? $idMenuParent : sprintf("%.0f", $idMenuParent);
        return $this;
    }

    /**
     * @return string
     */
    public function getIdOptionMenu()
    {
        return $this->idOptionMenu;
    }

    /**
     * @param string $idOptionMenu
     * @return $this
     */
    public function setIdOptionMenu($idOptionMenu)
    {
        $this->idOptionMenu = is_string($idOptionMenu) ? $idOptionMenu : sprintf("%.0f", $idOptionMenu);
        return $this;
    }

    /**
     * @return boolean
     */
    public function isRootMenu()
    {
        return $this->rootMenu;
    }

    /**
     * @param boolean $rootMenu
     * @return $this
     */
    public function setRootMenu($rootMenu)
    {
        $this->rootMenu = $rootMenu;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isOptionMenu()
    {
        return $this->optionMenu;
    }

    /**
     * @param boolean $optionMenu
     * @return $this
     */
    public function setOptionMenu($optionMenu)
    {
        $this->optionMenu = $optionMenu;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isSeparator()
    {
        return $this->separator;
    }

    /**
     * @param boolean $separator
     * @return $this
     */
    public function setSeparator($separator)
    {
        $this->separator = $separator;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getIdAction()
    {
        return $this->idAction;
    }

    /**
     * @param string $idAction
     * @return $this
     */
    public function setIdAction($idAction)
    {
        $this->idAction = is_string($idAction) ? $idAction : sprintf("%.0f", $idAction);
        return $this;
    }

    /**
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @param string $command
     * @return $this
     */
    public function setCommand($command)
    {
        $this->command = $command;
        return $this;
    }

    /**
     * @return string
     */
    public function getIconSmall()
    {
        return $this->iconSmall;
    }

    /**
     * @param string $iconSmall
     * @return $this
     */
    public function setIconSmall($iconSmall)
    {
        $this->iconSmall = $iconSmall;
        return $this;
    }

    /**
     * @return string
     */
    public function getIconBig()
    {
        return $this->iconBig;
    }

    /**
     * @param string $iconBig
     * @return $this
     */
    public function setIconBig($iconBig)
    {
        $this->iconBig = $iconBig;
        return $this;
    }

    /**
     * @return array
     */
    public function getTabOptions()
    {
        return $this->tabOptions;
    }

    /**
     * @param array $tabOptions
     * @return $this
     */
    public function setTabOptions($tabOptions)
    {
        $this->tabOptions = $tabOptions;
        return $this;
    }

    /**
     * @return int
     */
    public function getHomeTypeDisplay()
    {
        return $this->homeTypeDisplay;
    }

    /**
     * @param int $homeTypeDisplay
     * @return $this
     */
    public function setHomeTypeDisplay($homeTypeDisplay)
    {
        $this->homeTypeDisplay = $homeTypeDisplay;
        return $this;
    }

    /**
     * @return string
     */
    public function getHomeTitle()
    {
        return $this->homeTitle;
    }

    /**
     * @param string $homeTitle
     * @return $this
     */
    public function setHomeTitle($homeTitle)
    {
        $this->homeTitle = $homeTitle;
        return $this;
    }

    /**
     * @return string
     */
    public function getHomeDesc()
    {
        return $this->homeDesc;
    }

    /**
     * @param string $homeDesc
     * @return $this
     */
    public function setHomeDesc($homeDesc)
    {
        $this->homeDesc = $homeDesc;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isHomeWithImg()
    {
        return $this->homeWithImg;
    }

    /**
     * @param boolean $homeWithImg
     * @return $this
     */
    public function setHomeWithImg($homeWithImg)
    {
        $this->homeWithImg = $homeWithImg;
        return $this;
    }

    /**
     * @return int
     */
    public function getHomeWidth()
    {
        return $this->homeWidth;
    }

    /**
     * @param int $homeWidth
     * @return $this
     */
    public function setHomeWidth($homeWidth)
    {
        $this->homeWidth = $homeWidth;
        return $this;
    }

    /**
     * @return int
     */
    public function getHomeHeight()
    {
        return $this->homeHeight;
    }

    /**
     * @param int $homeHeight
     * @return $this
     */
    public function setHomeHeight($homeHeight)
    {
        $this->homeHeight = $homeHeight;
        return $this;
    }




	/***************************************
	 * Autres méthodes
	 ***************************************/

	/*
	 * Règle pour l'execution de l'action
	 * 1 - Commande sinon vide
	 * 2 - IDAction si != 0
	 * 3 - Libelle de l'option
	 * @return bool
	 */
	public function isExecByAction()
	{
		if (!empty($this->command))
			return false;

		return !empty($this->idAction);
	}

    public function FinInit()
    {
        $this->separator = ($this->optionMenu && (empty($this->idAction) && empty($this->command) && (($this->title=='-') || ($this->title==''))));

    }


	/**
	 * retourne la phrase a executer
	 * @return string
	 */
	public function getCommandToExec()
	{
		if (!empty($this->command))
			return $this->command;

		return $this->title;
	}

    /***************************************
     * Autres méthodes
     ***************************************/

    /**
     * @return bool
     */
    public function isWithSmallIcon()
    {
        if (empty($this->iconSmall)){
            return false;
        }

        if ($this->iconSmall == '0'){
            return false;
        }

        return true;

        return !empty($this->iconSmall) && ($this->iconSmall != '0');
    }

    public function isWithBigIcon()
    {
        return !empty($this->iconBig) && ($this->iconBig != '0');
    }
    /**
     * @param ItemMenu $oOption
     * @return $this
     */
    public function AddOptionMenu(ItemMenu $oOption)
    {
        $this->tabOptions[]=$oOption;
        return $this;
    }

    /**
     * @param ItemMenu $oOption
     * @param $index
     * @return $this
     */
    public function SetOptionMenuAt(ItemMenu $oOption, $index)
    {
        $this->tabOptions[$index]=$oOption;
        return $this;
    }

    /**
     * @param $index
     * @return $this
     */
    public function RemoveOptionMenuAt($index)
    {
        unset($this->tabOptions[$index]);
        return $this;
    }

    /**
     * @return $this
     */
    public function RemoveAll()
    {
        $this->tabOptions=array();
        return $this;
    }

    /**
     * true si contient au moins une option de menu qui n'est pas un séparateur
     * @return bool
     */
    public function bIsEmpty()
    {
        foreach($this->tabOptions as $clOption)
        {
            /** @var ItemMenu $clOption */
            if (!$clOption->separator){
                return false;
            }
        }
        return true;
    }

    /**
     * @return bool
     */
    public function bFirstOptionIsSeparateur()
    {
        if (empty($this->tabOptions))
        {
            return false;
        }

        return reset($this->tabOptions)->separator;
    }


    /**
     * @return bool
     */
    public function bLastOptionIsSeparateur()
    {
        if (empty($this->tabOptions))
        {
            return false;
        }

        return end($this->tabOptions)->separator;
    }

    /**
     * supprime la dernière option de menu
     * @return $this
     */
    public function RemoveLastOption()
    {
        array_pop($this->tabOptions);
        return $this;
    }

    /**
     * supprime la première option de menu
     * @return $this
     */
    public function RemoveFirstOption()
    {
        array_shift($this->tabOptions);
        return $this;
    }


    public function TrimSeparateur()
    {
        if ($this->bFirstOptionIsSeparateur())
            $this->RemoveFirstOption();
        if ($this->bLastOptionIsSeparateur())
            $this->RemoveLastOption();

        return $this;
    }


    public function isOverlay()
    {
        return  ($this->homeTypeDisplay==Langage::ICONCENTRAL_OverlayBottom)
            ||  ($this->homeTypeDisplay==Langage::ICONCENTRAL_OverlayTop)
            ||  ($this->homeTypeDisplay==Langage::ICONCENTRAL_OverlayMiddle);
    }

    public function isTop()
    {
        return  ($this->homeTypeDisplay==Langage::ICONCENTRAL_TitreImgHelp)
                ||  ($this->homeTypeDisplay==Langage::ICONCENTRAL_OverlayTop);
    }

    public function isMiddle()
    {
        return  ($this->homeTypeDisplay==Langage::ICONCENTRAL_OverlayMiddle);
    }

    public function isBottom()
    {
        return  ($this->homeTypeDisplay==Langage::ICONCENTRAL_OverlayBottom);
    }

} 