<?php
/**
 * Classe outils pour charger les menus
 *
 * Created by PhpStorm.
 * User: Ninon
 * Date: 28/11/14
 * Time: 09:18
 */

namespace NOUT\Bundle\ContextsBundle\Entity\Menu;


use NOUT\Bundle\NOUTOnlineBundle\Entity\Langage;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\ParserList;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\ReponseWSParser;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS;

class MenuLoader
{
	/**
	 * @param XMLResponseWS $clReponseOptionMenu
	 * @param XMLResponseWS $clReponseMenu
     * @param XMLResponseWS $clReponseBigIcon
	 * @return InfoMenu
	 */
	static public function s_aGetTabMenu(XMLResponseWS $clReponseOptionMenu, XMLResponseWS $clReponseMenu, XMLResponseWS $clReponseBigIcon)
	{
		$clResponseParserOption = new ReponseWSParser();
		$clParserOption = $clResponseParserOption->InitFromXmlXsd($clReponseOptionMenu);
        /** @var ParserList $clParserOption */

		$clResponseParserMenu = new ReponseWSParser();
		$clParserMenu = $clResponseParserMenu->InitFromXmlXsd($clReponseMenu);
        /** @var ParserList $clParserMenu */

        $clResponseParserBigIcon = new ReponseWSParser();
        $clParserBigIcon = $clResponseParserBigIcon->InitFromXmlXsd($clReponseBigIcon);
        /** @var ParserList $clParserBigIcon */

		//on récupère tous les id des options de menu et des menus depuis les différentes réponses
		$aTabIDEnregOptionMenu = $clParserOption->GetTabEnregTableau()->GetTabIDEnreg(Langage::TABL_OptionMenuPourTous);
		$aTabIDEnregMenu = $clParserMenu->GetTabEnregTableau()->GetTabIDEnreg(Langage::TABL_MenuPourTous);
        $aTabIDEnregBigIcon = $clParserBigIcon->GetTabEnregTableau()->GetTabIDEnreg(Langage::TABL_ImageCatalogue);

        $oInfoMenu = new InfoMenu();

		foreach($aTabIDEnregMenu as $sIDMenu)
		{
			$clMenu = self::_s_aGetMenu($clParserOption, $clParserMenu, $sIDMenu, $aTabIDEnregMenu, $aTabIDEnregOptionMenu, $aTabIDEnregBigIcon, true, $oInfoMenu);
			if (is_null($clMenu))
				continue;

            $oInfoMenu->aMenu[]=$clMenu;
		}

		return $oInfoMenu;
	}

	/**
	 * @param ParserList $clParserOption
	 * @param ParserList $clParserMenu
	 * @param $sIDMenu
     * @param array $aTabIDEnregMenu
	 * @param array $aTabIDEnregOptionMenu
     * @param bool $bUniquementRacine
	 * @return Menu
	 */
	static protected function _s_aGetMenu(ParserList $clParserOption, ParserList $clParserMenu, $sIDMenu, array $aTabIDEnregMenu, array $aTabIDEnregOptionMenu, array $aTabIDEnregBigIcon, $bUniquementRacine, InfoMenu $oInfoMenu)
	{
		$clRecordMenu = $clParserMenu->getRecordFromID(Langage::TABL_MenuPourTous, $sIDMenu);

		$sIDMenuPere = $clRecordMenu->getValCol(Langage::COL_MENUPOURTOUS_IDMenuParent);
		if ($bUniquementRacine && !empty($sIDMenuPere))
			return null; //on prend que les menus qui n'ont pas de père

		//on construit un menu
		$clMenu = new Menu($sIDMenu, $clRecordMenu->getValCol(Langage::COL_MENUPOURTOUS_Libelle), $sIDMenuPere);

		$ValOptionMenu = $clRecordMenu->getValCol(Langage::COL_MENUPOURTOUS_OptionsMenu);
		foreach($ValOptionMenu as $sIDOptionMenu)
		{
			if (in_array($sIDOptionMenu, $aTabIDEnregMenu))
			{
				//c'est un sous-menu
				$clSousMenu = self::_s_aGetMenu($clParserOption, $clParserMenu, $sIDOptionMenu, $aTabIDEnregMenu, $aTabIDEnregOptionMenu, $aTabIDEnregBigIcon, false, $oInfoMenu);
				if (!is_null($clSousMenu))
					$clMenu->AddOptionMenu($clSousMenu);

				continue;
			}

			if (!in_array($sIDOptionMenu, $aTabIDEnregOptionMenu))
				continue;

			$clRecordOption = $clParserOption->getRecordFromID(Langage::TABL_OptionMenuPourTous, $sIDOptionMenu);

			$clOptionMenu = new OptionMenu($sIDOptionMenu, $clRecordOption->getValCol(Langage::COL_OPTIONMENUPOURTOUS_Libelle), $clRecordOption->getValCol(Langage::COL_OPTIONMENUPOURTOUS_IDMenuParent));
			$clOptionMenu->setIDAction($clRecordOption->getValCol(Langage::COL_OPTIONMENUPOURTOUS_IDAction));
			$clOptionMenu->setCommande($clRecordOption->getValCol(Langage::COL_OPTIONMENUPOURTOUS_Commande));

            $sIDIcon = $clRecordOption->getValCol(Langage::COL_OPTIONMENUPOURTOUS_IDIcone);
			$clOptionMenu->setIDIcone($sIDIcon);

			if (!$clMenu->bLastOptionIsSeparateur() || !$clOptionMenu->bEstSeparateur())
            {
                $clMenu->AddOptionMenu($clOptionMenu);

                //c'est une grosse icone
                if (in_array($sIDIcon, $aTabIDEnregBigIcon)){
                    $oInfoMenu->aBigIcon[]=$clOptionMenu;
                }
            }
		}

		//on vérifie que le menu n'est pas vide
		if ($clMenu->bIsEmpty())
			return null;

		return $clMenu->TrimSeparateur();
	}



} 