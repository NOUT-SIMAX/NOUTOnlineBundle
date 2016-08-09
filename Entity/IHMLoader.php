<?php
/**
 * Classe outils pour charger les menus
 *
 * Created by PhpStorm.
 * User: Ninon
 * Date: 28/11/14
 * Time: 09:18
 */

namespace NOUT\Bundle\ContextsBundle\Entity;


use NOUT\Bundle\ContextsBundle\Entity\Menu\Menu;
use NOUT\Bundle\ContextsBundle\Entity\Menu\OptionMenu;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Langage;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\ParserList;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\ReponseWSParser;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS;

class IHMLoader
{
    /**
     * @var ParserList
     */
    protected $m_clParserOption;

    /**
     * @var ParserList
     */
    protected $m_clParserMenu;

    /**
     * @var array
     */
    protected $m_aTabIDEnregOptionMenu;

    /**
     * @var array
     */
    protected $m_aTabIDEnregMenu;

    /**
     * @var array
     */
    protected $m_aTabIDEnregBigIcon;

    /**
     * @var array
     */
    protected $m_aTabIDEnregSmallIcon;

    /**
     * @param XMLResponseWS $clReponseOptionMenu
     * @param XMLResponseWS $clReponseMenu
     * @param XMLResponseWS $clReponseSmallIcon
     * @param XMLResponseWS $clReponseBigIcon
     */
    public function __construct(XMLResponseWS $clReponseOptionMenu, XMLResponseWS $clReponseMenu, XMLResponseWS $clReponseSmallIcon, XMLResponseWS $clReponseBigIcon)
    {
        $clResponseParserOption = new ReponseWSParser();
        $this->m_clParserOption = $clResponseParserOption->InitFromXmlXsd($clReponseOptionMenu);

        $clResponseParserMenu = new ReponseWSParser();
        $this->m_clParserMenu = $clResponseParserMenu->InitFromXmlXsd($clReponseMenu);

        //on récupère tous les id des options de menu et des menus depuis les différentes réponses
        $this->m_aTabIDEnregOptionMenu = $this->m_clParserOption->GetTabEnregTableau()->GetTabIDEnreg(Langage::TABL_OptionMenuPourTous);
        $this->m_aTabIDEnregMenu = $this->m_clParserMenu->GetTabEnregTableau()->GetTabIDEnreg(Langage::TABL_MenuPourTous);


        $clResponseParserBigIcon = new ReponseWSParser();
        $clParserBigIcon = $clResponseParserBigIcon->InitFromXmlXsd($clReponseBigIcon);
        $this->m_aTabIDEnregBigIcon = $clParserBigIcon->GetTabEnregTableau()->GetTabIDEnreg(Langage::TABL_ImageCatalogue);


        $clResponseParserSmallIcon = new ReponseWSParser();
        $clParserSmallIcon = $clResponseParserSmallIcon->InitFromXmlXsd($clReponseSmallIcon);
        $this->m_aTabIDEnregSmallIcon = $clParserSmallIcon->GetTabEnregTableau()->GetTabIDEnreg(Langage::TABL_ImageCatalogue);
    }

    /**
     * @return InfoIHM
     */
    public function oGetInfoIHM()
    {
        $oInfoIHM = new InfoIHM();

        foreach($this->m_aTabIDEnregMenu as $sIDMenu)
        {
            $clMenu = $this->_aGetMenu($oInfoIHM, $sIDMenu, true);
            if (is_null($clMenu))
                continue;

            $oInfoIHM->aMenu[]=$clMenu;
        }

        return $oInfoIHM;
    }

    /**
     * @param InfoIHM $oInfoIHM
     * @param         $sIDMenu
     * @param         $bUniquementRacine
     * @return $this|null
     */
    protected function _aGetMenu(InfoIHM $oInfoIHM, $sIDMenu, $bUniquementRacine)
    {
        $clRecordMenu = $this->m_clParserMenu->getRecordFromID(Langage::TABL_MenuPourTous, $sIDMenu);

        $sIDMenuPere = $clRecordMenu->getValCol(Langage::COL_MENUPOURTOUS_IDMenuParent);
        if ($bUniquementRacine && !empty($sIDMenuPere))
            return null; //on prend que les menus qui n'ont pas de père

        //on construit un menu
        $clMenu = new Menu($sIDMenu, $clRecordMenu->getValCol(Langage::COL_MENUPOURTOUS_Libelle), $sIDMenuPere);

        $ValOptionMenu = $clRecordMenu->getValCol(Langage::COL_MENUPOURTOUS_OptionsMenu);
        foreach($ValOptionMenu as $sIDOptionMenu)
        {
            if (in_array($sIDOptionMenu, $this->m_aTabIDEnregMenu))
            {
                //c'est un sous-menu
                $clSousMenu = $this->_aGetMenu($oInfoIHM, $sIDOptionMenu, false);
                if (!is_null($clSousMenu))
                    $clMenu->AddOptionMenu($clSousMenu);

                continue;
            }

            if (!in_array($sIDOptionMenu, $this->m_aTabIDEnregOptionMenu))
                continue;

            $clRecordOption = $this->m_clParserOption->getRecordFromID(Langage::TABL_OptionMenuPourTous, $sIDOptionMenu);

            $clOptionMenu = new OptionMenu($sIDOptionMenu, $clRecordOption->getValCol(Langage::COL_OPTIONMENUPOURTOUS_Libelle), $clRecordOption->getValCol(Langage::COL_OPTIONMENUPOURTOUS_IDMenuParent));
            $clOptionMenu->setIDAction($clRecordOption->getValCol(Langage::COL_OPTIONMENUPOURTOUS_IDAction));
            $clOptionMenu->setCommande($clRecordOption->getValCol(Langage::COL_OPTIONMENUPOURTOUS_Commande));

            $sIDIcon = $clRecordOption->getValCol(Langage::COL_OPTIONMENUPOURTOUS_IDIcone);
            if (!empty($sIDIcon))
            {
                $clOptionMenu->setIDIcone($sIDIcon);
                $clOptionMenu->setBigIcon(in_array($sIDIcon, $this->m_aTabIDEnregBigIcon));
                $clOptionMenu->setSmallIcon(in_array($sIDIcon, $this->m_aTabIDEnregSmallIcon));
            }

            if (!$clMenu->bLastOptionIsSeparateur() || !$clOptionMenu->bEstSeparateur())
            {
                $clMenu->AddOptionMenu($clOptionMenu);

                //c'est une grosse icone
                if ($clOptionMenu->isBigIcon()){
                    $oInfoIHM->aBigIcon[]=$clOptionMenu;
                }

                if ($clOptionMenu->isSmallIcon()){
                    $oInfoIHM->aToolbar[]=$clOptionMenu;
                }
            }
        }

        //on vérifie que le menu n'est pas vide
        if ($clMenu->bIsEmpty())
            return null;

        return $clMenu->TrimSeparateur();
    }




} 