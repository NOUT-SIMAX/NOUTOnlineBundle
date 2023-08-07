<?php
/**
 * Created by PhpStorm
 * User: ninon
 * Date: 02/08/2023 15:25
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Langage;

abstract class TypeAction
{
    //typeAction
    const Unknown              = 0;        //Action inconnue (ne manipule pas un objet de façon générique)
    const DescEnreg            = 1;        //Description d'un enreg
    const Creation             = 2386;
    const Modification         = 2387;
    const Liste                = 2388;
    const Recherche            = 2389;
    const Consultation         = 2390;
    const Suppression          = 2391;
    const Impression           = 2392;
    const OBS_Info             = 2393;        //Action qui va afficher une information a partir d'une phrase (genre "Aujourd'hui")
    const Particuliere         = 2394;
    const EnleverDe            = 3182;
    const AjouterA             = 3183;
    const CreerAPartirDe       = 5303;
    const LanceExe             = 8684;        // Action lancement d'exe n'est pas un choix du modele type action, n'existe pas dans le langage
    const TransformerEn        = 8720;
    const AfficheTableauCroise = 9338;
    const AfficheVue           = 15135;
    const Planning             = 10790;
    const Exporter             = 15477;
    const Importer             = 15478;
    const DeclencherAuto       = 15702;
}
