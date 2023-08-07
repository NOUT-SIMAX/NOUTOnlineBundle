<?php
/**
 * Created by PhpStorm
 * User: ninon
 * Date: 02/08/2023 18:09
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Langage;

abstract class LangageChoix
{
    const TYPEAFFICHAGEFICHE_Oui = 17619;
    const TYPEAFFICHAGEFICHE_Non = 17620;

    const TYPEAFFICHAGELISTE_Always   = 17615;
    const TYPEAFFICHAGELISTE_BtnLine  = 17923;
    const TYPEAFFICHAGELISTE_OnDemand = 17650;
    const TYPEAFFICHAGELISTE_Never    = 17617;

    const NIVEAUSEP_Principal  = 15062;
    const NIVEAUSEP_Secondaire = 15063;
    const NIVEAUSEP_Sequence   = 16311;

    const BTNMODEVALIDATION_Defaut      = 16580;
    const BTNMODEVALIDATION_Avant       = 16581;
    const BTNMODEVALIDATION_Apres       = 16582;
    const BTNMODEVALIDATION_Enregistrer = 17348;
    const BTNMODEVALIDATION_Annuler     = 17349;
    const BTNMODEVALIDATION_Imprimer    = 17350;
    const BTNMODEVALIDATION_NotClose    = 17719;

    const CHOIX_TYPEAUDIT_ObjetLangage = 15835;
    const CHOIX_TYPEAUDIT_Formule      = 15836;
    const CHOIX_TYPEAUDIT_Global       = 15843;
}
