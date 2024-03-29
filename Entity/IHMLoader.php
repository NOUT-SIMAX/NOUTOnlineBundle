<?php
/**
 * Classe outils pour charger les menus
 *
 * Created by PhpStorm.
 * User: Ninon
 * Date: 28/11/14
 * Time: 09:18
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity;


use NOUT\Bundle\NOUTOnlineBundle\Entity\Langage\LangageColonne;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Langage\LangageTableau;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Menu\ItemMenu;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parser\ParserList;
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
     * @throws \Exception
     */
    public function __construct(XMLResponseWS $clReponseOptionMenu, XMLResponseWS $clReponseMenu, XMLResponseWS $clReponseSmallIcon, XMLResponseWS $clReponseBigIcon)
    {
        $clResponseParserOption = new ReponseWSParser();
        $this->m_clParserOption = $clResponseParserOption->InitFromXmlXsd($clReponseOptionMenu);

        $clResponseParserMenu = new ReponseWSParser();
        $this->m_clParserMenu = $clResponseParserMenu->InitFromXmlXsd($clReponseMenu);

        //on récupère tous les id des options de menu et des menus depuis les différentes réponses
        $this->m_aTabIDEnregOptionMenu = $this->m_clParserOption->GetTabEnregTableau()->GetTabIDEnreg(LangageTableau::OptionMenuPourTous);
        $this->m_aTabIDEnregMenu = $this->m_clParserMenu->GetTabEnregTableau()->GetTabIDEnreg(LangageTableau::MenuPourTous);


        $clResponseParserBigIcon = new ReponseWSParser();
        /** @var ParserList $clParserBigIcon */
        $clParserBigIcon = $clResponseParserBigIcon->InitFromXmlXsd($clReponseBigIcon);
        $this->m_aTabIDEnregBigIcon = $clParserBigIcon->GetTabEnregTableau()->GetTabIDEnreg(LangageTableau::ImageCatalogue);


        $clResponseParserSmallIcon = new ReponseWSParser();
        /** @var ParserList $clParserSmallIcon */
        $clParserSmallIcon = $clResponseParserSmallIcon->InitFromXmlXsd($clReponseSmallIcon);
        $this->m_aTabIDEnregSmallIcon = $clParserSmallIcon->GetTabEnregTableau()->GetTabIDEnreg(LangageTableau::ImageCatalogue);
    }

    /**
     * @return InfoIHM
     */
    public function oGetInfoIHM(): InfoIHM
    {
        $oInfoIHM = new InfoIHM();

        foreach($this->m_aTabIDEnregMenu as $sIDMenu)
        {
            $clMenu = $this->_aGetMenu($oInfoIHM, $sIDMenu, true);
            if (is_null($clMenu))
            {
                continue;
            }

            $oInfoIHM->aMenu[]=$clMenu;
        }

        if (count($oInfoIHM->aMenu)==1)
        {
            //il y a qu'1 seul menu, il faut le remonter
            /** @var ItemMenu $clMenu */
            $clMenu = $oInfoIHM->aMenu[0];
            $oInfoIHM->aMenu=array();
            foreach($clMenu->tabOptions as $clSousMenu){
                /** @var ItemMenu $clSousMenu */
                if (!$clSousMenu->isSeparator()){
                    $oInfoIHM->aMenu[]=$clSousMenu;
                }
            }
        }
        return $oInfoIHM;
    }

    /**
     * @param InfoIHM $oInfoIHM
     * @param         $sIDMenu
     * @param         $bUniquementRacine
     * @return ItemMenu
     */
    protected function _aGetMenu(InfoIHM $oInfoIHM, $sIDMenu, $bUniquementRacine): ?ItemMenu
    {
        $clRecordMenu = $this->m_clParserMenu->getRecordFromID(LangageTableau::MenuPourTous, $sIDMenu);

        $sIDMenuPere = $clRecordMenu->getValCol(LangageColonne::MENUPOURTOUS_IDMenuParent);
        if ($bUniquementRacine && !empty($sIDMenuPere))
        {
            //on prend que les menus qui n'ont pas de père
            return null;
        }

        //on construit un menu
        $libelle = $clRecordMenu->getValCol(LangageColonne::MENUPOURTOUS_Libelle);
        if (is_array($libelle)){
            $libelle = $libelle['display'];
        }
        $clMenu = new ItemMenu($sIDMenu, $libelle, false);
        $clMenu->setIdMenuParent($sIDMenuPere);
        $clMenu->setRootMenu(empty($sIDMenuPere));

        $ValOptionMenu = $clRecordMenu->getValCol(LangageColonne::MENUPOURTOUS_OptionsMenu);
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

            $clRecordOption = $this->m_clParserOption->getRecordFromID(LangageTableau::OptionMenuPourTous, $sIDOptionMenu);

            $libelle = $clRecordOption->getValCol(LangageColonne::OPTIONMENUPOURTOUS_Libelle);
            if (is_array($libelle)){
                $libelle = $libelle['display'];
            }

            $clOptionMenu = new ItemMenu($sIDOptionMenu, $libelle, true);
            $clOptionMenu
                ->setIdMenuParent($clRecordOption->getValCol(LangageColonne::OPTIONMENUPOURTOUS_IDMenuParent))
                ->setIdAction($clRecordOption->getValCol(LangageColonne::OPTIONMENUPOURTOUS_IDAction))
                ->setCommand($clRecordOption->getValCol(LangageColonne::OPTIONMENUPOURTOUS_Commande))
            ;


            $sIDIcon = $clRecordOption->getValCol(LangageColonne::OPTIONMENUPOURTOUS_IDIcone);
            if (!empty($sIDIcon))
            {
                $sBigIcon = in_array($sIDIcon, $this->m_aTabIDEnregBigIcon) ? $sIDIcon : '';
                $sSmallIcon = in_array($sIDIcon, $this->m_aTabIDEnregSmallIcon) ? $sIDIcon : '';

                $clOptionMenu->setIconBig($sBigIcon);
                $clOptionMenu->setIconSmall($sSmallIcon);
            }
            else
            {
                $sBigIcon='';
                $sSmallIcon='';
            }
            $clOptionMenu->FinInit();

            if (!$clMenu->bLastOptionIsSeparateur() || !$clOptionMenu->isSeparator())
            {
                $clMenu->AddOptionMenu($clOptionMenu);

                //c'est une grosse icone
                if (!empty($sBigIcon)){
                    $oInfoIHM->aBigIcon[]=$clOptionMenu;
                }

                if (!empty($sSmallIcon)){
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