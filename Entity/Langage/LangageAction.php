<?php
/**
 * Created by PhpStorm
 * User: ninon
 * Date: 02/08/2023 15:28
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Langage;

abstract class LangageAction
{

    /*************************************************************
     * IDENTIFIANT D'ACTION
     *************************************************************/

    const CreerPlannificationRessource                   = 8458;
    const CreerPlannificationSansReservationRessource    = 17159;

    const Preference                 = 1000;
    const Maintenance                = 4666;
    const MaintenancePlus            = -1;
    const MaintenancePlusLocal       = -2;
    const MaintenancePlusFicRel      = -3;
    const MaintenancePlusFicRelLocal = -4;
    const CopieColonne               = 6063;
    const CopieLigne                 = 6177;
    const Import  = 7558;
    const Export         = 11495;
    const Synchro = 14370;
    const MonPlanning                = 8508;
    const PlanningRes                = 8499;
    const PlanningMultiRes           = 8483;
    const SauvegardeDonnees          = 9747;
    const SauveFichierInit           = 9759;
    const GenereSMX                  = 9773;
    const ImportSMX                  = 9920;
    const ExtraitToutSMX             = 13572;
    const Traduction                 = 10564;
    const RemplaceColonne = 10630;
    const Annulation                  = 10850;
    const Refaire                     = 10853;
    const GenererModeleEdition = 11090;
    const FusionEnreg                = 11133;
    const MAJEnreg                   = 11315;
    const RejoueAutomatismes         = 11710;
    const RemplaceElement            = 11966;
    const GenerationDonneesTest      = 12744;
    const ParametrerAvecImport       = 13267;
    const PasserUnAppel              = 13349;
    const AppelEntrant               = 17267;
//    const Stock                      = 11776;
//    const Publipostage               = 10618;
    const ListeChoix              = 2549;
    const ListeFormulaire  = 2317;
    const ListeUtilisateur = 1496;

    const ExporterOrganigramme    = 13774;
    const Messagerie_ListeMessage = 0;
    const Messagerie_CheckMessage = 0;
    const SuppressionComplete     = 14373;

    const CreeTableauRecapitulatif       = 9164;
    const CreeAxeTableauRecapitulatif    = 9221;
    const CreeCalculCompteur             = 4312;

    const Aide             = 15018;
    const DemarrageSIMAX   = 15242;
    const ArretSIMAX       = 15244;

    const CreerPaquetSMX   = 15344;
    const InitSynchro      = 15482;

    const RechercheGlobale  = 15557;

    const AuditParametrage  = 15832;
    const ExporterVersBDD   = 15869;

    const DupliquerFormulaire              = 15921;
    const SupprimerCompletementFormulaire  = 15934;

    const ExporterGrapheDependancesCalcul                = 16929;
    const ExporterGrapheDependancesCalcul_Colonne        = 16930;
    const ExporterGrapheDependancesCalcul_DossierSortie  = 16933;
    const ExporterGrapheDependancesCalcul_FormatSortie   = 16934;

    const InitBaseUtilisateur = 16943;

    const ListeDestinataireMessagerie        = 1989;
    const RechercherDestinataireMessagerie = 1991;
    const RechercherReponseType            = 12359;

    const PayementPaypal                     = 17186;

    const AfficherFichier_NomFichier    = 17203;    //Affichage d'un fichier en "IFRAME" modele nom de fichier avec repertoire
    const AfficherFichier_ModeleFichier = 17207;    //affichage d'un fichier en "IFRAME" modele fichier

    const ConnexionExtranet = 17216;    //passage du mode anonyme au mode connecté extranet (demande de login au client.)

    const Messagerie_Nouveau         = 17040;
    const Messagerie_Liste           = 17041;
    const Messagerie_Consulter       = 17042;
    const Maintenance_AnnulerRefaire = 17282;

    const ViderTableBDD = 17238;

    const ActionClassiqueGenerique = 17272;

    const ListeRequetePourTous = 6274;

    const HorairesOuverture = 9600;

    const ExtranetResetPass = 18253;

    //sur le formulaire paramètre
    const MODIFIER_Parametre  = 2223;
    const SUPPRIMER_Parametre = 2230;
    const CREER_Parametre     = 2221;

    const MODIFIER_Colonne  = 4103;
    const SUPPRIMER_Colonne = 4113;
    const CREER_Colonne     = 4099;

    const MODIFIER_Separateur = 6120;
    const MODIFIER_ColInfo = 2285;
    const MODIFIER_Bouton = 5235;

    const EXTRANETRESETPASS_Pseudo = 18254;
    const EXTRANETRESETPASS_Email = 18255;
    const EXTRANETRESETPASS_Formulaire = 18256;
}
