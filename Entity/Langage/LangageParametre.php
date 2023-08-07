<?php
/**
 * Created by PhpStorm
 * User: ninon
 * Date: 02/08/2023 15:28
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Langage;

abstract class LangageParametre
{
    const RechercheGlobal = 16990; //id de la checkbox permettant de preciser que la recherche est global dans filtre de liste.

    const PLANNINGRES_IDRessource    = 8507; // Optionnel
    const PLANNINGMUTLIRES_IDTableau = 8521; // Optionnel

    const COPIECOLONNE_IDColSource = 6070;
    const COPIECOLONNE_IDColDest   = 6071;
    const COPIELIGNE_IDTabSource   = 6188;
    const COPIELIGNE_IDTabDest     = 6189;
    const COPIELIGNE_IDEnreg       = 6192;

    const REMPLACECOLONNE_IDCol     = 10648;
    const REMPLACECOLONNE_OldValeur = 10649;
    const REMPLACECOLONNE_NewValeur = 10650;

    const REMPLACEELEM_IDTableau  = 11968;
    const REMPLACEELEM_IDOldElem  = 11970;
    const REMPLACEELEM_IDNewElem  = 11972;

    const EXPORTER_Repertoire        = 11505;
    const EXPORTER_IDExport          = 15010;
    const IMPORTER_IDImport          = 15009;
//    const EXPORTER_FormatExport      = 11501;
//    const EXPORTER_IDTableau         = 12343;
//    const EXPORTER_Requete           = 11499;


    const PAIEMENTPAYPAL_Montant     = 17189;
    const PAIEMENTPAYPAL_Devise      = 17190;
    const PAIEMENTPAYPAL_Token       = 17191;
    const PAIEMENTPAYPAL_Payeur      = 17192;
    const PAIEMENTPAYPAL_Login       = 17194;
    const PAIEMENTPAYPAL_Password    = 17195;
    const PAIEMENTPAYPAL_Signature   = 17196;
    const PAIEMENTPAYPAL_SandBox     = 17197;
    const PAIEMENTPAYPAL_URL         = 17198;

    const MAJENREG_Tableau                = 11317;
    const MAJENREG_Colonne                = 17211;
    const MAJENREG_AvecAutomatisme        = 17210;
    const MAJENREG_AvecAnnulation         = 17212;
    const MAJENREG_AvecCalculLie          = 17213;
    const MAJENREG_UniquementCalculStocke = 17215;

    const AUDIT_TypeElement = 15837;
    const AUDIT_Element = 15838;
    const AUDIT_TypeAudit = 15833;


    const CONNEXIONEXTRANET_Extranet_Pseudo = 17217;
    const CONNEXIONEXTRANET_Extranet_Mdp    = 17218;
    const CONNEXIONEXTRANET_Intranet_Pseudo = 17299;
    const CONNEXIONEXTRANET_Intranet_Mdp = 17300;
    const CONNEXIONEXTRANET_Formulaire = 18020;
    const CONNEXIONEXTRANET_CodeLangue = 18021;
    const CONNEXIONEXTRANET_FromLogin  = 18022;
    const CONNEXIONEXTRANET_Hachage    = 18023;

    const LISTEREQUETETOUS_Formulaire = 6293;

    const LISTECHOIX_Modele          = 2554;
    const LISTEFORMULAIRE_SousModule = 2532;

    const MODIFIERPARAMETRE_Parametre  = 2226;
    const SUPPRIMERPARAMETRE_Parametre = 2232;

    const MODIFIERCOLONNE_Colonne  = 4107;
    const SUPPRIMERCOLONNE_Colonne = 4117;

    const MODIFIERSEPARATEUR_Separateur  = 6125;
    const MODIFIERCOLINFO_COlInfo = 2287;
    const MODIFIERBOUTON_Bouton = 5240;



}
