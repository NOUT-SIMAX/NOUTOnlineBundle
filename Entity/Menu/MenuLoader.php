<?php
/**
 * Classe outils pour charger les menus
 *
 * Created by PhpStorm.
 * User: Ninon
 * Date: 28/11/14
 * Time: 09:18
 */

namespace NOUT\Bundle\ContextesBundle\Entity\Menu;


use NOUT\Bundle\NOUTOnlineBundle\Entity\Langage;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\ReponseWSParser;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS;

class MenuLoader
{
	/**
	 * @param XMLResponseWS $clReponseOptionMenu
	 * @param XMLResponseWS $clReponseMenu
	 * @return array
	 */
	static public function s_aGetTabMenu(XMLResponseWS $clReponseOptionMenu, XMLResponseWS $clReponseMenu)
	{
		$clParserOption = new ReponseWSParser();
		$clParserOption->InitFromXmlXsd($clReponseOptionMenu);

		$clParserMenu = new ReponseWSParser();
		$clParserMenu->InitFromXmlXsd($clReponseMenu);

		//on récupère tous les id des options de menu et des menus depuis les différentes réponses
		$aTabIDEnregOptionMenu = $clParserOption->GetTabEnregTableau()->GetTabIDEnreg(Langage::TABL_OptionMenuPourTous);
		$aTabIDEnregMenu = $clParserMenu->GetTabEnregTableau()->GetTabIDEnreg(Langage::TABL_MenuPourTous);

		$aTabMenuRet = array(); //tableau qu'on va retourner

		foreach($aTabIDEnregMenu as $sIDMenu)
		{
			$clMenu = self::_s_aGetMenu($clParserOption, $clParserMenu, $sIDMenu, $aTabIDEnregMenu, $aTabIDEnregOptionMenu, true);
			if (is_null($clMenu))
				continue;

			$aTabMenuRet[]=$clMenu;
		}

		return $aTabMenuRet;
	}

	/**
	 * @param ReponseWSParser $clParserOption
	 * @param ReponseWSParser $clParserMenu
	 * @param $sIDMenu
	 * @param array $aTabIDEnregOptionMenu
	 * @return Menu
	 */
	static protected function _s_aGetMenu(ReponseWSParser $clParserOption, ReponseWSParser $clParserMenu, $sIDMenu, array $aTabIDEnregMenu, array $aTabIDEnregOptionMenu, $bUniquementRacine)
	{
		$clRecordMenu = $clParserMenu->clGetRecordFromId(Langage::TABL_MenuPourTous, $sIDMenu);

		$sIDMenuPere = $clRecordMenu->sGetValCol(Langage::COL_MENUPOURTOUS_IDMenuParent);
		if ($bUniquementRacine && !empty($sIDMenuPere))
			return null; //on prend que les menus qui n'ont pas de père

		//on construit un menu
		$clMenu = new Menu($sIDMenu, $clRecordMenu->sGetValCol(Langage::COL_MENUPOURTOUS_Libelle), $sIDMenuPere);

		$ValOptionMenu = $clRecordMenu->sGetValCol(Langage::COL_MENUPOURTOUS_OptionsMenu);
		foreach($ValOptionMenu as $sIDOptionMenu)
		{
			if (in_array($sIDOptionMenu, $aTabIDEnregMenu))
			{
				//c'est un sous-menu
				$clSousMenu = self::_s_aGetMenu($clParserOption, $clParserMenu, $sIDOptionMenu, $aTabIDEnregMenu, $aTabIDEnregOptionMenu, false);
				if (!is_null($clSousMenu))
					$clMenu->AddOptionMenu($clSousMenu);

				continue;
			}

			if (!in_array($sIDOptionMenu, $aTabIDEnregOptionMenu))
				continue;

			$clRecordOption = $clParserOption->clGetRecordFromId(Langage::TABL_OptionMenuPourTous, $sIDOptionMenu);

			$clOptionMenu = new OptionMenu($sIDOptionMenu, $clRecordOption->sGetValCol(Langage::COL_OPTIONMENUPOURTOUS_Libelle), $clRecordOption->sGetValCol(Langage::COL_OPTIONMENUPOURTOUS_IDMenuParent));
			$clOptionMenu->setIDAction($clRecordOption->sGetValCol(Langage::COL_OPTIONMENUPOURTOUS_IDAction));
			$clOptionMenu->setCommande($clRecordOption->sGetValCol(Langage::COL_OPTIONMENUPOURTOUS_Commande));
			$clOptionMenu->setIDIcone($clRecordOption->sGetValCol(Langage::COL_OPTIONMENUPOURTOUS_IDIcone));

			if (!$clMenu->bLastOptionIsSeparateur() || !$clOptionMenu->bEstSeparateur())
				$clMenu->AddOptionMenu($clOptionMenu);
		}

		//on vérifie que le menu n'est pas vide
		if ($clMenu->bIsEmpty())
			return null;

		if ($clMenu->bLastOptionIsSeparateur())
			$clMenu->RemoveLastOption();

		return $clMenu;
	}



} 