<?php
/**
 * Created by PhpStorm
 * User: ninon
 * Date: 02/08/2023 15:23
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Langage;

abstract class LangageTableau
{
    // organigramme
    const AxeOrganigramme = 13734;
    const Organigramme    = 13781;

    // Pabx historisation des appels entrant
    const AppelRecu   = 13429;

    // tableau pour messagerie
    const Destinataire   = 1390;
    const Contact        = 1003;
    const GR_Contact     = 1004;
    const Utilisateur    = 1169;
    const GR_Utilisateur = 1175;
    const CompteEmail         = 1224;
    const ReponseType    = 12344;
    const PieceJointe    = 13299;

    // Donnees Externe
    const ConnexionBDD        = 14076;
    const ImportExportSynchro = 15881;
    const Export       = 14884;
    const Import       = 14387;
    const Synchro = 14966;
    const SynchroUtilisateur  = 17073; //tableau de dernière synchro utilisateur
    const DescDonneeExterne   = 14391;
    const DescCsv             = 14666;
    const DescXls             = 14718;
    const DescPocket          = 14761;
    const DescOutlook         = 14802;
    const DescBdd             = 14615;
    const DescTexteDelimite   = 14572;
    const DescIcsVcf          = 14843;
    const DescExchange        = 15145;
    const DescXml             = 15269;
    const DescXmlServeur      = 16612;
    const DescXmlSepa         = 16659;
    const CorrespColonne      = 14397;
    const CorrespClassique    = 14501;
    const CorrespDelimite     = 14536;
    const OLD_CorrespColonne  = 7567;

    // pour import outlook / pocket
    const ContactPerso      = 1394;
    const ContactPro        = 1403;
    const Tache             = 3017;
    const RendezVous        = 7945;

    // planification
    const MaSociete              = 9619;
    const HoraireOuverture       = 9569;
    const JourFerie              = 9629;
    const Ressource                   = 8267;        //ressource pour la planification
    const ReservationRessource   = 8372;
    const PlanificationRessource = 17151;
    const Periodicite            = 9787;
    const PeriodiciteJSemaine    = 9789;


    // menu
    const MenuPerso       = 10246;
    const OptionMenuPerso = 10291;
    const Menu            = 11615;
    const OptionMenu      = 11540;
    const ImageCatalogue       = 11508;                                                                                                                                                                                                                                                                        //tableau des icones


    // impression
    const Document             = 10543;
    const ImpressionTicket     = 10703;
    const ImpressionEtiquette  = 10762;

    // formule / fonction
    const Fonction = 11727;

    // historique
    const Historique = 12691;

    //--- Tableau de vocabulaire
    const Pays         = 9495;
    const Ville        = 9494;
    const Departement  = 10654;
    const Prenom       = 9931;


    // Gestion Packet SMX
    const FichierSMX           = 15348;
    const FichierImportSMX     = 15425;
    const PaquetSMX            = 15385;

    // Requete personnelle
    const Requete                     = 16165;                                                                                                                                                                                                                                                                     //entonoir
    const RequetePersonnelle     = 16242;
    const CondRequetePersonnelle = 16208;


    //--- Tableau du langage
    const Mot                          = 2020;
    const Synonyme                     = 2021;
    const Phrase                       = 2022;
    const Module                       = 2023;
    const Parametre = 2025;
    const Modele         = 2026;
    const Option  = 2027;
    const ColInfo           = 2028;
    const Tableau           = 2029;
    const CreationAuto    = 2032;
    const Choix           = 2362;
    const ModeleClassique      = 2419;
    const ModeleElem           = 2420;
    const ModeleListeElem = 2421;
    const ModeleChoixMult = 2422;
    const Colonne          = 3068;
    const Calcul             = 3075;
    const CalculSomme    = 3087;
    const CalculMoyenne  = 3086;
    const CalculMin          = 3080;
    const CalculMax           = 3085;
    const CalculFormule     = 3088;
    const CalculCompteur = 3089;
    const ColReference   = 15067;
    const Operation = 4845;
    const Condition  = 4827;
    const ColBoutonAction  = 5128;
    const ColLibelle       = 6097;
    const LanceAction = 7020;
    const EnvMessage                   = 7011;
    const ActionAutomatique            = 7001;
    const Evenement                    = 4822;
    const EvenementAction              = 7626;
    const EvenementTemporel = 7633;
    const Action                 = 2024;
    const ActionClassique   = 8685;
    const ActionParticuliere           = 8696;
    const ActionLanceExe               = 8707;
    const ActionAfficheTableauCroise = 9326;
    const TableauCroise                   = 9153;
    const AxeTableauCroise = 9160;
    const TableauBase           = 9268;
    const TableauAvecCond  = 9976;
    const MiseEnForme                  = 10038;
    const ControleValidite             = 10090;
    const QuestionReponse              = 10138;
    const ActionOuQuestion             = 10139;
    const ControleUnicite              = 11039;
    const TableauPrev                  = 11172;
    const LigneTableauPrev             = 11230;
    const AxeTableauPrev               = 11276;
    const ControleAction   = 13482;
    const Vue                   = 13615;
    const ActionAfficheVue = 13649;
    const AffectParametre              = 15575;
    const BoucleAutomatisme            = 15609;
    const Rupture         = 15641;
    const ModeleFichier        = 16017;
    const FonctionExterne = 15938;
    const FonctionExtWS                = 15968;
    const ModeleEdition                = 10961;
    const RequetePourTous              = 6201;
    const CondRequetePourTous          = 6195;
    const Droit                        = 7655;
    const DroitColonne       = 8527;
    const MenuPourTous       = 11654;
    const OptionMenuPourTous = 11579;
    const ControleEtat  = 16520;
    const ColonneBase                  = 16713;
    const Indicateur                   = 16746;
    const TableauDeBord                = 16781;
    const ModeAffichage                = 16860;
    const ControleAnnulation           = 16825;
    const ModeAffichageListe           = 16895;
    const PersonnalisationTable        = 16949;
    const TableauAvecModeAffichage     = 17115;
    const ModeAffichageNumerique       = 17656;
    const CategorieModele = 17761;
    const ColTexteImage        = 17928;

    //-------------------------------------------------
    const Messagerie_Message = 16510;
    const Messagerie_Dossier = 16511;
}
