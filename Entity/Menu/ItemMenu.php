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

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Menu;

use NOUT\Bundle\NOUTOnlineBundle\Entity\Langage;

class ItemMenu
{
    /**
     * identifiant du menu conteneur
     * @var string
     */
    protected $idMenuParent='';

    /**
     * identifiant de l'option de menu
     * @var string $idOptionMenu
     */
	protected $idOptionMenu='';

    /**
     * vrai si menu principal
     * @var bool
     */
    public $rootMenu=false;

    /**
     * vrai si c'est un menu, faux si c'est une option de menu
     * @var bool
     */
    public $optionMenu=false;

    /**
     * vrai si c'est un separateur
     * @var bool
     */
    public $separator=false;

	/**
	 * libellé de l'option de menu
	 * @var string
	 */
	public $title='';

	/**
	 * identifiant de l'action à lancer
	 * @var string
	 */
	public $idAction = '';

	/**
	 * commande de l'action
	 * @var string
	 */
    public $command = '';

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
    public $homeTypeDisplay = 0;

    /**
     * @var string
     */
    public $homeTitle='';

    /**
     * @var string
     */
    public $homeDesc='';

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
	 * @param string $sLibelle
	 * @param bool $bEstOptionMenu
	 */
	public function __construct($idOptionMenu, string $sLibelle, bool $bEstOptionMenu)
	{
		$this->idOptionMenu = is_string($idOptionMenu) ? $idOptionMenu : sprintf ( "%u", $idOptionMenu );
		$this->title = str_replace('&&', '&', $sLibelle);
		$this->optionMenu = $bEstOptionMenu;
	}

    /**
     * @return string
     */
    public function getIdMenuParent(): string
    {
        return $this->idMenuParent;
    }

    /**
     * @param mixed $idMenuParent
     * @return $this
     */
    public function setIdMenuParent($idMenuParent): ItemMenu
    {
        $this->idMenuParent = is_string($idMenuParent) ? $idMenuParent : sprintf("%.0f", $idMenuParent);
        return $this;
    }

    /**
     * @return string
     */
    public function getIdOptionMenu(): string
    {
        return $this->idOptionMenu;
    }

    /**
     * @param mixed $idOptionMenu
     * @return $this
     */
    public function setIdOptionMenu($idOptionMenu): ItemMenu
    {
        $this->idOptionMenu = is_string($idOptionMenu) ? $idOptionMenu : sprintf("%.0f", $idOptionMenu);
        return $this;
    }

    /**
     * @return bool
     */
    public function isRootMenu(): bool
    {
        return $this->rootMenu;
    }

    /**
     * @param bool $rootMenu
     * @return $this
     */
    public function setRootMenu(bool $rootMenu): ItemMenu
    {
        $this->rootMenu = $rootMenu;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isOptionMenu(): bool
    {
        return $this->optionMenu;
    }

    /**
     * @param bool $optionMenu
     * @return $this
     */
    public function setOptionMenu(bool $optionMenu): ItemMenu
    {
        $this->optionMenu = $optionMenu;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isSeparator(): bool
    {
        return $this->separator;
    }

    /**
     * @param bool $separator
     * @return $this
     */
    public function setSeparator(bool $separator): ItemMenu
    {
        $this->separator = $separator;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): ItemMenu
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getIdAction(): string
    {
        return $this->idAction;
    }

    /**
     * @param string $idAction
     * @return $this
     */
    public function setIdAction(string $idAction): ItemMenu
    {
        $this->idAction = is_string($idAction) ? $idAction : sprintf("%u", $idAction);
        return $this;
    }

    /**
     * @return string
     */
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * @param string $command
     * @return $this
     */
    public function setCommand(string $command): ItemMenu
    {
        $this->command = $command;
        return $this;
    }

    /**
     * @return string
     */
    public function getIconSmall(): string
    {
        return $this->iconSmall;
    }

    /**
     * @param string $iconSmall
     * @return $this
     */
    public function setIconSmall(string $iconSmall): ItemMenu
    {
        $this->iconSmall = $iconSmall;
        return $this;
    }

    /**
     * @return string
     */
    public function getIconBig(): string
    {
        return $this->iconBig;
    }

    /**
     * @param string $iconBig
     * @return $this
     */
    public function setIconBig(string $iconBig): ItemMenu
    {
        $this->iconBig = $iconBig;
        return $this;
    }

    /**
     * @return array
     */
    public function getTabOptions(): array
    {
        return $this->tabOptions;
    }

    /**
     * @param array $tabOptions
     * @return $this
     */
    public function setTabOptions(array $tabOptions): ItemMenu
    {
        $this->tabOptions = $tabOptions;
        return $this;
    }

    /**
     * @return int
     */
    public function getHomeTypeDisplay(): int
    {
        return $this->homeTypeDisplay;
    }

    /**
     * @param int $homeTypeDisplay
     * @return $this
     */
    public function setHomeTypeDisplay(int $homeTypeDisplay): ItemMenu
    {
        $this->homeTypeDisplay = $homeTypeDisplay;
        return $this;
    }

    /**
     * @return string
     */
    public function getHomeTitle(): string
    {
        return $this->homeTitle;
    }

    /**
     * @param string $homeTitle
     * @return $this
     */
    public function setHomeTitle(string $homeTitle): ItemMenu
    {
        $this->homeTitle = $homeTitle;
        return $this;
    }

    /**
     * @return string
     */
    public function getHomeDesc(): string
    {
        return $this->homeDesc;
    }

    /**
     * @param string $homeDesc
     * @return $this
     */
    public function setHomeDesc(string $homeDesc): ItemMenu
    {
        $this->homeDesc = $homeDesc;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isHomeWithImg(): bool
    {
        return $this->homeWithImg;
    }

    /**
     * @param bool $homeWithImg
     * @return $this
     */
    public function setHomeWithImg(bool $homeWithImg): ItemMenu
    {
        $this->homeWithImg = $homeWithImg;
        return $this;
    }

    /**
     * @return int
     */
    public function getHomeWidth(): int
    {
        return $this->homeWidth;
    }

    /**
     * @param int $homeWidth
     * @return $this
     */
    public function setHomeWidth(int $homeWidth): ItemMenu
    {
        $this->homeWidth = $homeWidth;
        return $this;
    }

    /**
     * @return int
     */
    public function getHomeHeight(): int
    {
        return $this->homeHeight;
    }

    /**
     * @param int $homeHeight
     * @return $this
     */
    public function setHomeHeight(int $homeHeight): ItemMenu
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
	public function isExecByAction(): bool
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
	public function getCommandToExec(): string
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
    public function isWithSmallIcon(): bool
    {
        if (empty($this->iconSmall)){
            return false;
        }

        if ($this->iconSmall == '0'){
            return false;
        }

        return true;
    }

    public function isWithBigIcon(): bool
    {
        return !empty($this->iconBig) && ($this->iconBig != '0');
    }
    /**
     * @param ItemMenu $oOption
     * @return $this
     */
    public function AddOptionMenu(ItemMenu $oOption): ItemMenu
    {
        $this->tabOptions[]=$oOption;
        return $this;
    }

    /**
     * @param ItemMenu $oOption
     * @param $index
     * @return $this
     */
    public function SetOptionMenuAt(ItemMenu $oOption, $index): ItemMenu
    {
        $this->tabOptions[$index]=$oOption;
        return $this;
    }

    /**
     * @param $index
     * @return $this
     */
    public function RemoveOptionMenuAt($index): ItemMenu
    {
        unset($this->tabOptions[$index]);
        return $this;
    }

    /**
     * @return $this
     */
    public function RemoveAll(): ItemMenu
    {
        $this->tabOptions=array();
        return $this;
    }

    /**
     * true si contient au moins une option de menu qui n'est pas un séparateur
     * @return bool
     */
    public function bIsEmpty(): bool
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
    public function bFirstOptionIsSeparateur(): bool
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
    public function bLastOptionIsSeparateur(): bool
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
    public function RemoveLastOption(): ItemMenu
    {
        array_pop($this->tabOptions);
        return $this;
    }

    /**
     * supprime la première option de menu
     * @return $this
     */
    public function RemoveFirstOption(): ItemMenu
    {
        array_shift($this->tabOptions);
        return $this;
    }


    public function TrimSeparateur(): ItemMenu
    {
        if ($this->bFirstOptionIsSeparateur())
            $this->RemoveFirstOption();
        if ($this->bLastOptionIsSeparateur())
            $this->RemoveLastOption();

        return $this;
    }


    public function isOverlay(): bool
    {
        return  ($this->homeTypeDisplay==Langage::ICONCENTRAL_OverlayBottom)
            ||  ($this->homeTypeDisplay==Langage::ICONCENTRAL_OverlayTop)
            ||  ($this->homeTypeDisplay==Langage::ICONCENTRAL_OverlayMiddle);
    }

    public function isTop(): bool
    {
        return  ($this->homeTypeDisplay==Langage::ICONCENTRAL_TitreImgHelp)
                ||  ($this->homeTypeDisplay==Langage::ICONCENTRAL_OverlayTop);
    }

    public function isMiddle(): bool
    {
        return  ($this->homeTypeDisplay==Langage::ICONCENTRAL_OverlayMiddle);
    }

    public function isBottom(): bool
    {
        return  ($this->homeTypeDisplay==Langage::ICONCENTRAL_OverlayBottom);
    }

} 