<?php
/**
 * Created by PhpStorm
 * User: ninon
 * Date: 02/08/2023 15:26
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Langage;

abstract class LangageColonne
{

    const GENERIQUE_TYPEFORMULAIRE = 15134;
    const GENERIQUE_MINIDESC = 15311;
    const GENERIQUE_RUPTURE  = 15910;


    /*************************************************************
     * IDENTIFIANT DE COLONNE
     *************************************************************/

    const AXEORGANIGRAMME_IDAxe         = 13735;
    const AXEORGANIGRAMME_Orientation   = 13737;
    const AXEORGANIGRAMME_Depart        = 13744;
    const AXEORGANIGRAMME_Arrivee       = 13746;

    const ORGANIGRAMME_IDOrganigramme       = 13782;
    const ORGANIGRAMME_Libelle              = 13784;
    const ORGANIGRAMME_Repertoire           = 13786;
    const ORGANIGRAMME_Axes                 = 13788;
    const ORGANIGRAMME_IDModule             = 13790;
    const ORGANIGRAMME_OrganigrammesAnnexes = 13899;
    const ORGANIGRAMME_IDPaquetages         = 13963;

    const PAQUETAGE_IDPaquetage    = 13922;
    const PAQUETAGE_Domaine        = 13926;
    const PAQUETAGE_Libelle        = 13924;
    const PAQUETAGE_Formulaires    = 13928;


    const UTILISATEUR_IDUtilisateur     = 1170;
    const UTILISATEUR_Pseudo            = 1171;
    const UTILISATEUR_Nom               = 1172;
    const UTILISATEUR_Prenom            = 1173;
    const UTILISATEUR_EMail             = 1174;
    const UTILISATEUR_Identifiant1      = 9424;
    const UTILISATEUR_Identifiant2      = 9426;
    const UTILISATEUR_Invalide          = 10545;
    const UTILISATEUR_Photo             = 7623;
    const UTILISATEUR_EstSuperviseur    = 7624;
    const UTILISATEUR_TypeLicence       = 15913; // eTYPELICENCE
    const UTILISATEUR_Parametre         = 7622; //???

    const GRUTILISATEUR_Nom               = 1177;
    const GRUTILISATEUR_ListeUtilisateurs = 1972;

    const MASOCIETE_Logo                = 10535;
    const MASOCIETE_ImageEnteteEtat     = 10536;
    const MASOCIETE_PiedDePageEtat      = 10537;
    const MASOCIETE_PoliceTitreEtat     = 10532;
    const MASOCIETE_PoliceNormaleEtat   = 10533;
    const MASOCIETE_PoliceEnteteCol     = 10534;
    const MASOCIETE_PoliceLibSeparateur = 10538;
    const MASOCIETE_PoliceEtiAdresse    = 15189;
    const MASOCIETE_PoliceXxxxx         = 15190;
    const MASOCIETE_Nom                 = 10747;
    const MASOCIETE_Telephone           = 10748;
    const MASOCIETE_Adresse             = 10750;
    const MASOCIETE_CodePostal          = 10751;
    const MASOCIETE_Ville               = 10752;
    const MASOCIETE_NumeroTVA           = 10757;
    const MASOCIETE_FormeJuridique      = 10760;
    const MASOCIETE_CapitalSocial       = 10761;
    const MASOCIETE_PAYPAL_LOGIN        = 17182;
    const MASOCIETE_PAYPAL_PASSWORD     = 17183;
    const MASOCIETE_PAYPAL_SIGNATURE    = 17184;
    const MASOCIETE_PAYPAL_SANDBOX      = 17185;

    const HORAIREOUVERTURE_JourSemaine = 9685;
    const HORAIREOUVERTURE_HeureDeb    = 9573;
    const HORAIREOUVERTURE_HeureFin    = 9574;

    const PERJOURSEM_IDPeriodicite = 9791;
    const PERJOURSEM_ResRessource  = 9792;
    const PERJOURSEM_JourSemaine   = 9793;
    const PERJOURSEM_Frequence     = 9802;
    const PERJOURSEM_Mois          = 9817;
    const PERJOURSEM_DateDebut     = 10524;
    const PERJOURSEM_DateFin       = 10525;

    const JOURFERIE_Jour                = 9631;
    const JOURFERIE_Mois                = 9632;
    const JOURFERIE_Libelle             = 9633;
    const JOURFERIE_Annee               = 11036;

    //---menu------
    const MENU_IDMenu               = 11616;
    const MENU_Libelle              = 11617;
    const MENU_OptionsMenu          = 11618;
    const MENU_Ordre                = 11619;

    const OPTIONMENU_IDOptionMenu   = 11541;
    const OPTIONMENU_IDMenuParent   = 11544;
    const OPTIONMENU_Libelle        = 11543;
    const OPTIONMENU_Raccourci      = 11546;
    const OPTIONMENU_IDAction       = 11542;
    const OPTIONMENU_Icone          = 11551;
    const OPTIONMENU_Ordre          = 11547;
    const OPTIONMENU_Commande       = 11549;

    const MENUPERSO_IDMenu           = 10247;
    const MENUPERSO_Libelle          = 10248;
    const MENUPERSO_OptionsMenu      = 10344;
    const MENUPERSO_Ordre            = 10249;
    const MENUPERSO_IDMenuParent     = 16942;

    const OPTIONMENUPERSO_IDOptionMenu = 10293;
    const OPTIONMENUPERSO_IDMenuParent = 10296;
    const OPTIONMENUPERSO_Libelle      = 10294;
    const OPTIONMENUPERSO_Raccourci    = 10297;
    const OPTIONMENUPERSO_IDAction     = 10299;
    const OPTIONMENUPERSO_Icone        = 11696;
    const OPTIONMENUPERSO_Ordre        = 10298;
    const OPTIONMENUPERSO_Commande     = 11695;

    const IMAGECATALOGUE_Image       = 11513;
    const IMAGECATALOGUE_ImageGrande = 17295;

    //-------------//

    const PUBLIPOSTAGE_IDPubli       = 10345;
    const PUBLIPOSTAGE_DateCrea      = 10346;
    const PUBLIPOSTAGE_TypePubli     = 10347;
    const PUBLIPOSTAGE_ListeDest     = 10348;
    const PUBLIPOSTAGE_Message       = 10349;

    const PUBLIPOSTAGEWORD_IDPubliWord  = 10873;
    const PUBLIPOSTAGEWORD_FichierDoc   = 10879;
    const PUBLIPOSTAGEWORD_ListeDest    = 10876;
    const PUBLIPOSTAGEWORD_ListeBalise  = 10950;

    const BALISEWORD_IDBalise           = 10914;
    const BALISEWORD_IDPubliWord        = 10916;
    const BALISEWORD_Balise             = 10918;
    const BALISEWORD_Correspondance     = 10921;
    const BALISEWORD_ALister            = 10960;



    // colonnes des tableaux reponse type
    const REPONSETYPE_Sujet             = 12347;
    const REPONSETYPE_Message           = 12349;
    const REPONSETYPE_ListePieceJointe  = 13335;

    const PIECEJOINTE_Libelle       = 13304;
    const PIECEJOINTE_Fichier       = 13306;
    const PIECEJOINTE_Description   = 13308;

    const DESTINATAIRE_Nom          = 10627;
    const DESTINATAIRE_Prenom       = 10628;
    const DESTINATAIRE_Societe      = 10629;
    const DESTINATAIRE_AdresseEmail = 10626;

    const CONTACT_Email        = 3178;
    const CONTACT_Tel          = 6034;
    const CONTACT_Nom          = 3180;
    const CONTACT_Prenom       = 3181;
    const CONTACT_Civilite     = 5306;

    const CONTACTPERSO_IdUnique = 1395;
    const CONTACTPERSO_Pseudo   = 3051;
    const CONTACTPERSO_Prenom   = 3052;
    const CONTACTPERSO_Nom      = 3053;
    const CONTACTPERSO_Email    = 3054;
    const CONTACTPERSO_Tel      = 1397;

    const CONTACTPRO_IdUnique = 1404;
    const CONTACTPRO_Prenom   = 3055;
    const CONTACTPRO_Nom      = 3056;
    const CONTACTPRO_Email    = 3057;
    const CONTACTPRO_Tel      = 3058;

//    const TACHE_IdUnique      = 3018;
    const TACHE_Utilisateur   = 3021;
    const TACHE_Createur      = 3022;

    const RDV_IdUnique      = 7946;
    const RDV_Utilisateur   = 8194;
    const RDV_Createur      = 8195;


//    //Compte email (petit idauto non utilisé 17231<->17233 (données locales uniquement)
//    const COMPTEEMAIL_IDUnique     = 1226;
//    const COMPTEEMAIL_AdresseEMail = 1227;
//
//    const COMPTEEMAIL_FAI           = 1231;
    const COMPTEEMAIL_Signature             = 15259;
    const COMPTEEMAIL_SignatureNouveau  = 17803;
    const COMPTEEMAIL_SignatureRepondre = 17804;
//    const COMPTEEMAIL_Pseudo        = 15261;
//    const COMPTEEMAIL_AdresseDefaut = 15866;
//
//     //reception
//    const COMPTEEMAIL_Recep_Login           = 10995;
//    const COMPTEEMAIL_Recep_Password        = 1228;
//    const COMPTEEMAIL_Recep_Server          = 1229;
//    const COMPTEEMAIL_Recep_Port            = 15256;
//    const COMPTEEMAIL_Recep_NoDeletePOP     = 15258;
//    const COMPTEEMAIL_Recep_Protocole       = 17226;
//    const COMPTEEMAIL_Recep_Securite        = 17227;
//    const COMPTEEMAIL_Recep_DossierPerso    = 17228;
//    const COMPTEEMAIL_Recep_DossierIMAP     = 17230;
//    const COMPTEEMAIL_Recep_DureeTraite     = 17296;
//
//     //envoi
//    const COMPTEEMAIL_Send_Login        = 10996;
//    const COMPTEEMAIL_Send_Password     = 10994;
//    const COMPTEEMAIL_Send_Server       = 1230;
//    const COMPTEEMAIL_Send_Port         = 15257;
//    const COMPTEEMAIL_Send_Securite     = 17229;

    //--------------------------
    //colonnes du vocabulaire
    //--------------------------
    //const PAYS_Nom = 0;

    const VILLE_Nom        = 9489;    //gen
    const VILLE_CodePostal = 9490;    //gen
//    const VILLE_CodeINSEE  = 7617;


    //----
    const CONNEXIONBDD_IDAuto           = 14077;
    const CONNEXIONBDD_Serveur          = 14078;
    const CONNEXIONBDD_NomBase          = 14082;
    const CONNEXIONBDD_TypeConnexion    = 14079;
    const CONNEXIONBDD_Utilisateur      = 14083;
    const CONNEXIONBDD_MotDePasse       = 14084;
    const CONNEXIONBDD_InfoSuppl        = 14085;
    const CONNEXIONBDD_DecalageTemps    = 15260;

    //--------------  Nouveau modele Connectivite -------------

    const EXPORT_Libelle             = 14886;
    const EXPORT_IDDescDonneeExterne = 14887;
    const EXPORT_IDTableau           = 14888;
    const EXPORT_ListeCondition      = 15122;
    const EXPORT_FormuleRepSortie    = 15851;
    const EXPORT_AjouterEnFin        = 17072;

    const IMPORT_Libelle           = 14389;
    const IMPORT_DescDonneeExterne  = 14390;
    const IMPORT_Formulaire         = 14464;
    const IMPORT_ListeCondition = 15113;
    const IMPORT_DesactiverAutomatismes = 17142;

    const SYNCHRO_Libelle             = 14968;
    const SYNCHRO_DescDonneeExterne   = 14969;
    const SYNCHRO_Formulaire          = 14970;
    const SYNCHRO_DateDerniereSynchro = 15186;
    const SYNCHRO_ModeSynchro         = 15248;
    const SYNCHRO_Priorite            = 15036;
    const SYNCHRO_ListeCondition      = 15225;

    const SYNCHROUTIL_IDSynchro         = 17075;
    const SYNCHROUTIL_IDUtilisateur     = 17076;
    const SYNCHROUTIL_DateHeureDerniere = 17077;

    const DESCDONNEE_Localisation   = 14395;
    const DESCDONNEE_Libelle        = 14393;
//    const DESCDONNEE_Formulaire     = 14394;  // uniquement pour l'utilisateur
    const DESCDONNEE_ListeCorresp          = 14396;
    const DESCDONNEE_ListeColReference     = 15703;
    const DESCDONNEE_ImportExportSynchro   = 16318;

    const CSV_Libelle                 = 14668;
    const CSV_Formulaire              = 14669;
    const CSV_CnxBDD                  = 14670;
    const CSV_ListeCorresp            = 14672;
    const CSV_Source                  = 14673;
    const CSV_LigneEntete             = 14674;
    const CSV_Separateur              = 14675;
    const CSV_DelimiteurTexte         = 15224;
    const CSV_ListeColReference       = 15706;
    const CSV_LigneVideEnFin          = 17255;

    const XLS_Libelle                 = 14720;
    const XLS_Formulaire              = 14721;
    const XLS_CnxBDD                  = 14722;
    const XLS_ListeCorresp            = 14727;
    const XLS_Source                  = 14724;
    const XLS_LigneEntete             = 14726;
    const XLS_Feuille                 = 14725;
    const XLS_ListeColReference       = 15707;

    const POCKET_Libelle                = 14763;
    const POCKET_Formulaire             = 14764;
    const POCKET_ListeCorresp           = 14768;
    const POCKET_Dossier                = 14767;
    const POCKET_CnxBDD                 = 15014;
    const POCKET_TypeSynchro            = 15109;
    const POCKET_Prioritaire            = 15111;
    const POCKET_ListeColReference      = 15708;

    const OUTLOOK_Libelle                = 14804;
    const OUTLOOK_Formulaire             = 14805;
    const OUTLOOK_ListeCorresp           = 14809;
    const OUTLOOK_Dossier                = 14808; //15033;
    const OUTLOOK_CnxBDD                 = 15013;
    const OUTLOOK_TypeSynchro            = 15108;
    const OUTLOOK_Prioritaire            = 15110;
    const OUTLOOK_ListeColReference      = 15709;

    const EXCHANGE_Libelle            = 15147;
    const EXCHANGE_Formulaire         = 15148;
    const EXCHANGE_CnxBDD             = 15149;
    const EXCHANGE_ListeCorresp       = 15150;
    const EXCHANGE_Dossier            = 15151;
//    const EXCHANGE_ListeColReference  = ?????;

    const XML_Libelle                 = 15271;
    const XML_Formulaire              = 15272;
    const XML_ListeCorresp            = 15276;
    const XML_Source                  = 15310;
    const XML_BaliseEnreg             = 17280;
    const XML_ListeColReference       = 15711;

    const XMLSERVEUR_Libelle               = 16614;
    const XMLSERVEUR_Formulaire            = 16624;
    const XMLSERVEUR_CnxBDD                = 16616;
    const XMLSERVEUR_ListeCorresp          = 16619;
    const XMLSERVEUR_Source                = 16615;
    const XMLSERVEUR_LigneEntete           = 16621;
    const XMLSERVEUR_Separateur            = 16622;
    const XMLSERVEUR_DelimiteurTexte       = 16623;
    const XMLSERVEUR_ListeColReference     = 16620;

    const XMLSEPA_Libelle             = 16661;
    const XMLSEPA_FichierSource       = 16662;
    const XMLSEPA_FichierXsd          = 17034;
    const XMLSEPA_ListeCorresp        = 16666;
    const XMLSEPA_LigneEntete         = 16668;


    const BDD_Libelle             = 14617;
    const BDD_Formulaire          = 14618;
    const BDD_ListeCorresp        = 14709;
    const BDD_CnxBDD              = 14619;
    const BDD_NomTable            = 14622;
    const BDD_ColIdent            = 14621;
    const BDD_ModeCnx             = 14623;
    const BDD_ListeColReference   = 15705;

    const TEXTEDELIM_Libelle           = 14574;
    const TEXTEDELIM_Formulaire        = 14575;
    const TEXTEDELIM_CnxBDD            = 14576;
    const TEXTEDELIM_ListeCorresp      = 14578;
    const TEXTEDELIM_Source            = 14579;
    const TEXTEDELIM_LigneEntete       = 14580;
    const TEXTEDELIM_LigneParEnreg     = 14581;
    const TEXTEDELIM_ListeColReference = 15704;

    const ICSVCF_Libelle            = 14845;
    const ICSVCF_Formulaire         = 14846;
    const ICSVCF_CnxBDD             = 14847;
    const ICSVCF_ListeCorresp       = 14849;
    const ICSVCF_Source             = 14850;
    const ICSVCF_ListeColReference  = 15710;

    const CORRESPCOL_ColSimax               = 14399;
    const CORRESPCOL_ColExterne             = 14400;
    const CORRESPCOL_CreationAutorisee      = 15262;
    const CORRESPCOL_ExportValeurAffichee   = 15340;
    const CORRESPCOL_FormuleTransformation  = 15812;

    const CORRESPCLASSIQUE_ColSimax                 = 14503;
    const CORRESPCLASSIQUE_ColExterne               = 14504;
    const CORRESPCLASSIQUE_Espace                   = 14505;
    const CORRESPCLASSIQUE_CreationAutorisee        = 15263;
    const CORRESPCLASSIQUE_ExportValeurAffichee     = 15341;
    const CORRESPCLASSIQUE_FormuleTransformation    = 15811;
    const CORRESPCLASSIQUE_Ordre                    = 17039;

    const CORRESPDELIMITE_ColSimax              = 14538;
    const CORRESPDELIMITE_ColExterne            = 14539;
    const CORRESPDELIMITE_Position              = 14540;
    const CORRESPDELIMITE_Longueur              = 14541;
    const CORRESPDELIMITE_CreationAutorisee     = 15264;
    const CORRESPDELIMITE_ExportValeurAffichee  = 15342;
    const CORRESPDELIMITE_FormuleTransformation = 15813;
    const CORRESPDELIMITE_Ordre                 = 17038;

    //--------------  Fin Nouveau modele Connectivite -------------

    // Gestion Packet SMX
    const FICHIERSMX_Fichier                 = 15350;
    const FICHIERSMX_ListeDependance         = 15376;
    const FICHIERSMX_ListeFichierImport      = 15424;
    const FICHIERSMX_LimiterAuDomaine        = 15829;
    const FICHIERSMX_LimiterAuFormulaire     = 15830;

    const FICHIERIMPORTSMX_FichierSMX      = 15427;
    const FICHIERIMPORTSMX_Fichier         = 15428;
    const FICHIERIMPORTSMX_NomFormulaire   = 15476;
    const FICHIERIMPORTSMX_ListeDependance = 15466;

    const PAQUETSMX_Nom                 = 15387;
    const PAQUETSMX_MotDePasse          = 15388;
    const PAQUETSMX_ListeFichierSMX     = 15389;
    const PAQUETSMX_Infos               = 15390;
    const PAQUETSMX_InfosHTML           = 15465;


    const REQUETE_IDTableau = 16168;

    const REQUETEPERSONNELLE_ListeCond         = 16246;

    const CONDREQUETEPERSONNELLE_IDColonne     = 16212;
    const CONDREQUETEPERSONNELLE_Formule       = 16214;
    const CONDREQUETEPERSONNELLE_TypeCondition = 16213;
    const CONDREQUETEPERSONNELLE_TypeOperateur = 16211;
    const CONDREQUETEPERSONNELLE_CondEnsemble  = 16210;


    //--------------------------
    // colonnes du langage
    //--------------------------
    //ACTION
    const ACTION_IDAction    = 2057;
    const ACTION_IDModule       = 2059;
    const ACTION_Libelle       = 2060;
    const ACTION_TypeAction     = 2062;
    const ACTION_IDTableau      = 2063;
    const ACTION_AConfirmer = 2065;
    const ACTION_IDTableau2 = 5310;

    const ACTIONCLASSIQUE_IDAction       = 8687;
    const ACTIONCLASSIQUE_IDModule       = 8688;
    const ACTIONCLASSIQUE_Libelle        = 8689;
    const ACTIONCLASSIQUE_TypeAction     = 8690;
    const ACTIONCLASSIQUE_IDTableau      = 8691;
    const ACTIONCLASSIQUE_AConfirmer     = 8692;
    const ACTIONCLASSIQUE_IDTableau2     = 8695;
    const ACTIONCLASSIQUE_ListePhrase    = 8694;
    const ACTIONCLASSIQUE_ListeParametre = 8693;

    const ACTIONPARTICULIERE_IDAction       = 8698;
    const ACTIONPARTICULIERE_IDModule       = 8699;
    const ACTIONPARTICULIERE_Libelle        = 8700;
    const ACTIONPARTICULIERE_AConfirmer     = 8703;

    const ACTIONLANCEEXE_IDAction       = 8709;
    const ACTIONLANCEEXE_IDModule       = 8710;
    const ACTIONLANCEEXE_Libelle        = 8711;
    const ACTIONLANCEEXE_AConfirmer     = 8714;
    const ACTIONLANCEEXE_Executable     = 8718;
    const ACTIONLANCEEXE_LigneCmd       = 8719;
    const ACTIONLANCEEXE_Bloquant       = 15863;
    const ACTIONLANCEEXE_DummyID1       = 15911;

    //CALCUL
    const CALCUL_IDCalcul      = 3076;
    const CALCUL_IDColonne     = 9485;
    const CALCUL_Libelle       = 3078;
    const CALCUL_IDTableau     = 3077;
    const CALCUL_Ordre         = 3079;
    const CALCUL_Identifie     = 3082;
    const CALCUL_Detail        = 3083;
    const CALCUL_NonPerenise   = 6080;
    const CALCUL_Aide          = 5097;
    const CALCUL_Formule       = 9486;
    const CALCUL_IDModele      = 15044;
    const CALCUL_ListeModeAff  = 17143;

    const CALCULCOMPTEUR_IDCalcul       = 3125;
    const CALCULCOMPTEUR_IDTableau      = 3126;
    const CALCULCOMPTEUR_Libelle        = 3127;
    const CALCULCOMPTEUR_Ordre          = 3128;
    const CALCULCOMPTEUR_IDColonne      = 3135;
    const CALCULCOMPTEUR_Valeur         = 3136;
    const CALCULCOMPTEUR_Identifie      = 3129;
    const CALCULCOMPTEUR_Detail         = 3130;
    const CALCULCOMPTEUR_NonPerenise    = 6079;
    const CALCULCOMPTEUR_EstInvisible   = 6087;
    const CALCULCOMPTEUR_Imprime        = 6088;
    const CALCULCOMPTEUR_Aide           = 5103;
    const CALCULCOMPTEUR_NomRub         = 9423;
    const CALCULCOMPTEUR_DummyIDAuto1   = 13476;
    const CALCULCOMPTEUR_DummyBool1     = 13477;
    const CALCULCOMPTEUR_IDModele       = 15049;

    const CALCULFORMULE_IDCalcul     = 3119;
    const CALCULFORMULE_IDTableau    = 3120;
    const CALCULFORMULE_Libelle      = 3121;
    const CALCULFORMULE_Ordre        = 3122;
    const CALCULFORMULE_Formule      = 3138;
    const CALCULFORMULE_Identifie    = 3123;
    const CALCULFORMULE_Detail       = 3124;
    const CALCULFORMULE_NonPerenise  = 6078;
    const CALCULFORMULE_EstInvisible = 6089;
    const CALCULFORMULE_Imprime      = 6090;
    const CALCULFORMULE_Aide         = 5102;
    const CALCULFORMULE_NomRub       = 9422;
    const CALCULFORMULE_DummyBool1   = 13475;
    const CALCULFORMULE_IDModele     = 15048;

    const CALCULMAX_IDCalcul     = 3101;
    const CALCULMAX_IDTableau    = 3102;
    const CALCULMAX_Libelle      = 3103;
    const CALCULMAX_Ordre        = 3104;
    const CALCULMAX_IDColonne    = 3133;
    const CALCULMAX_Identifie    = 3105;
    const CALCULMAX_Detail       = 3106;
    const CALCULMAX_NonPerenise  = 6075;
    const CALCULMAX_EstInvisible = 6091;
    const CALCULMAX_Imprime      = 6092;
    const CALCULMAX_Aide         = 5099;
    const CALCULMAX_NomRub       = 9419;
    const CALCULMAX_DummyIDAuto1 = 13473;
    const CALCULMAX_DummyBool1   = 13474;
    const CALCULMAX_IDModele     = 15047;

    const CALCULMIN_IDCalcul     = 3095;
    const CALCULMIN_IDTableau    = 3096;
    const CALCULMIN_Libelle      = 3097;
    const CALCULMIN_Ordre        = 3098;
    const CALCULMIN_IDColonne    = 3132;
    const CALCULMIN_Identifie    = 3099;
    const CALCULMIN_Detail       = 3100;
    const CALCULMIN_NonPerenise  = 6074;
    const CALCULMIN_EstInvisible = 6093;
    const CALCULMIN_Imprime      = 6094;
    const CALCULMIN_Aide         = 5098;
    const CALCULMIN_NomRub       = 9418;
    const CALCULMIN_DummyIDAuto1 = 13471;
    const CALCULMIN_DummyBool1   = 13472;
    const CALCULMIN_IDModele     = 15057;

    const CALCULMOYENNE_IDCalcul     = 3107;
    const CALCULMOYENNE_IDTableau    = 3108;
    const CALCULMOYENNE_Libelle      = 3109;
    const CALCULMOYENNE_Ordre        = 3110;
    const CALCULMOYENNE_IDColonne    = 3134;
    const CALCULMOYENNE_Identifie    = 3111;
    const CALCULMOYENNE_Detail       = 3112;
    const CALCULMOYENNE_NonPerenise  = 6076;
    const CALCULMOYENNE_EstInvisible = 6095;
    const CALCULMOYENNE_Imprime      = 6096;
    const CALCULMOYENNE_Aide         = 5100;
    const CALCULMOYENNE_NomRub       = 9420;
    const CALCULMOYENNE_DummyIDAuto1 = 13469;
    const CALCULMOYENNE_DummyBool1   = 13470;
    const CALCULMOYENNE_IDModele     = 15046;

    const CALCULSOMME_IDCalcul     = 3113;
    const CALCULSOMME_IDTableau    = 3114;
    const CALCULSOMME_Libelle      = 3115;
    const CALCULSOMME_Ordre        = 3116;
    const CALCULSOMME_IDColonne    = 3139;
    const CALCULSOMME_Identifie    = 3117;
    const CALCULSOMME_Detail       = 3118;
    const CALCULSOMME_NonPerenise  = 6077;
    const CALCULSOMME_EstInvisible = 6085;
    const CALCULSOMME_Imprime      = 6086;
    const CALCULSOMME_Aide         = 5101;
    const CALCULSOMME_NomRub       = 9421;
    const CALCULSOMME_DummyIDAuto1 = 13467;
    const CALCULSOMME_DummyBool1   = 13468;
    const CALCULSOMME_IDModele     = 15045;


    const COLREFERENCE_IDColReference  = 15105;
    const COLREFERENCE_IDTableau       = 15103;
    const COLREFERENCE_Libelle         = 15104;
    const COLREFERENCE_Ordre           = 15101;
    const COLREFERENCE_IDColonne       = 15100;
    const COLREFERENCE_Identifie       = 15099;
    const COLREFERENCE_Detail          = 15098;
    const COLREFERENCE_NonPerenise     = 15097;
    const COLREFERENCE_EstInvisible    = 15096;
    const COLREFERENCE_Imprime         = 15095;
    const COLREFERENCE_Aide            = 15106;
    const COLREFERENCE_NomRub          = 15102;
    const COLREFERENCE_DummyBool1      = 15109;
    const COLREFERENCE_IDModele        = 15107;
    const COLREFERENCE_IDModeAffichage = 15108;

    const CHOIX_IDChoix     = 2363;
    const CHOIX_Libelle     = 2364;
    const CHOIX_Ordre       = 2365;
    const CHOIX_IDModele    = 3176;
    const CHOIX_IDImage     = 15041;

    const COLINFO_IDColInfo           = 2090;
    const COLINFO_IDTableau           = 2094;
    const COLINFO_IDModele            = 2097;
    const COLINFO_Libelle             = 2091;
    const COLINFO_Identifie           = 2092;
    const COLINFO_EstUnique           = 2093;
    const COLINFO_Obligatoire         = 2096;
    const COLINFO_Detail              = 2098;
    const COLINFO_Ordre               = 2095;
    const COLINFO_Aide                = 5095;
    const COLINFO_EstInvisible        = 5137;
    const COLINFO_Imprime             = 6084;
    const COLINFO_ValeurDefaut        = 5136;
    const COLINFO_NomRub              = 9417;
    const COLINFO_DummyBool1          = 13457;
    const COLINFO_DummyID1            = 15038;
    const COLINFO_UniquementFormu     = 16710;
    const COLINFO_FormuleINIT         = 16711;
    const COLINFO_AffichageFiche      = 17634;
    const COLINFO_AffichageListe      = 17622;
    const COLINFO_LectureSeule        = 17645;
    const COLINFO_IDModeAffNum        = 17742;
    const COLINFO_IDModeAffListe      = 17740;
    const COLINFO_RefuserTransmission = 17839;
    const COLINFO_ModifDirectListe    = 17840;
    const COLINFO_SansTransmission    = 18153;

    const COLONNE_IDColonne = 3069; //n'existe pas physiquement
    const COLONNE_Libelle   = 3071; //n'existe pas physiquement
    const COLONNE_Ordre     = 3072; //n'existe pas physiquement
    const COLONNE_IDTableau = 3070; //n'existe pas physiquement
    const COLONNE_Identifie = 3073; //n'existe pas physiquement
    const COLONNE_Detail    = 3074; //n'existe pas physiquement
    const COLONNE_Aide      = 5096; //n'existe pas physiquement
    const COLONNE_IDModele  = 6083; //n'existe pas physiquement
//    const COLONNE_Imprime   =;
    const COLONNE_EstInvisible   = 15697;
    const COLONNE_FormuleINIT    = 15700;
    const COLONNE_Formule        = 15701;
    const COLONNE_AffichageFiche = 17633;
    const COLONNE_AffichageListe = 17621;

    const COLBOUTONACTION_IDColonne      = 5129;
    const COLBOUTONACTION_IDTableau      = 5130;
    const COLBOUTONACTION_Libelle        = 5134;
    const COLBOUTONACTION_Ordre          = 5131;
    const COLBOUTONACTION_IDAction       = 5132;
    const COLBOUTONACTION_Aide           = 5133;
    const COLBOUTONACTION_Commande       = 13370;
    const COLBOUTONACTION_Detail         = 15490;
    const COLBOUTONACTION_ModeValidation = 16583;
    const COLBOUTONACTION_IDImage        = 16709;

    const COLLIBELLE_IDColonne     = 6098;
    const COLLIBELLE_IDTableau     = 6102;
    const COLLIBELLE_Libelle       = 6103;
    const COLLIBELLE_Ordre         = 6104;
    const COLLIBELLE_EstInvisible  = 6108;
    const COLLIBELLE_Imprime       = 6109;
    const COLLIBELLE_PositionH     = 6114;
    const COLLIBELLE_Aide          = 6100;
    const COLLIBELLE_DummyIDAuto1 = 13478;
    const COLLIBELLE_IDNiveau     = 13479;
    const COLLIBELLE_IDImage  = 15052;
    const COLLIBELLE_Detail        = 15491;
    const COLLIBELLE_IDSensMultiC  = 16309;
    const COLLIBELLE_IDModeMultiC  = 16310;
    const COLLIBELLE_CouleurFond   = 16312;

    const COLTEXTEIMAGE_Image = 17968;

    const CREAAUTO_IDCreation  = 2033;
    const CREAAUTO_IDTableau   = 2036;
    const CREAAUTO_Libelle     = 2034;

    const MODELE_IDModele      = 2076; //n'existe pas physiquement
    const MODELE_Libelle       = 2080; //n'existe pas physiquement
    const MODELE_Identifie     = 3148;
    const MODELE_Detail        = 3150;
    const MODELE_Obligatoire   = 3151;
    const MODELE_Unique        = 3172;

    const MODELECHOIXMULTIPLE_IDModele      = 2429;
    const MODELECHOIXMULTIPLE_Libelle       = 3143;
    const MODELECHOIXMULTIPLE_IDModule      = 9744;
    const MODELECHOIXMULTIPLE_Identifie     = 3164;
    const MODELECHOIXMULTIPLE_Detail        = 3165;
    const MODELECHOIXMULTIPLE_Obligatoire   = 3166;
    const MODELECHOIXMULTIPLE_Unique        = 4132;
    const MODELECHOIXMULTIPLE_Aide          = 13461;
    const MODELECHOIXMULTIPLE_DummyBool1    = 15042;
    const MODELECHOIXMULTIPLE_DummyBool2    = 15043;

    const MODELECLASSIQUE_IDModele    = 2423;
    const MODELECLASSIQUE_TypeModele  = 2424;
    const MODELECLASSIQUE_Libelle = 3141;
    const MODELECLASSIQUE_Identifie       = 3169;
    const MODELECLASSIQUE_Detail          = 3170;
    const MODELECLASSIQUE_Obligatoire     = 3171;
    const MODELECLASSIQUE_Unique          = 4133;
    const MODELECLASSIQUE_Aide            = 13466;
    const MODELECLASSIQUE_IDModeAffichage = 15058;
    const MODELECLASSIQUE_IDTypeTri       = 15820;

    const MODELEELEM_IDModele      = 2425;
    const MODELEELEM_IDTableau     = 2426;
    const MODELEELEM_Libelle       = 3147;
    const MODELEELEM_Identifie     = 3154;
    const MODELEELEM_Detail        = 3155;
    const MODELEELEM_Obligatoire   = 3156;
    const MODELEELEM_Unique        = 4134;
    const MODELEELEM_Aide          = 13465;
    const MODELEELEM_IDTypeTri     = 15821;

    const MODELELISTEELEM_IDModele      = 2427;
    const MODELELISTEELEM_IDTableau     = 2428;
    const MODELELISTEELEM_Libelle       = 3145;
    const MODELELISTEELEM_Identifie     = 3159;
    const MODELELISTEELEM_Detail        = 3160;
    const MODELELISTEELEM_Obligatoire   = 3161;
    const MODELELISTEELEM_Unique        = 4135;
    const MODELELISTEELEM_Aide          = 13462;
    const MODELELISTEELEM_ElementUnique = 13463;
    const MODELELISTEELEM_DummyEntier1  = 13464;
    const MODELELISTEELEM_IDTypeTri     = 15822;

    const MODELEFICHIER_IDModele        = 16018;
    const MODELEFICHIER_Libelle         = 16019;
    const MODELEFICHIER_TypeStockage    = 16026;
    const MODELEFICHIER_URL             = 16028;
    const MODELEFICHIER_Utilisateur     = 16030;
    const MODELEFICHIER_MotDePasse      = 16031;
    const MODELEFICHIER_Destination     = 16032;
    const MODELEFICHIER_Option          = 16033;
    const MODELEFICHIER_Identifie       = 16021;
    const MODELEFICHIER_Detail          = 16023;
    const MODELEFICHIER_Obligatoire     = 16022;
    const MODELEFICHIER_Unique          = 16024;
    const MODELEFICHIER_Aide            = 16207;
    const MODELEFICHIER_IDModeAffichage = 16025;

    const MODULE_IDModule       = 2051;
    const MODULE_NomModule      = 2052;
    const MODULE_NomPhysique    = 2053;
    const MODULE_TypeModule     = 2055;
    const MODULE_Version        = 2056;
    const MODULE_DummyEntier1   = 13458;
    const MODULE_Bloque         = 17301;
    const MODULE_IDIcone        = 17308;

    const MOT_IDMot            = 2037;
    const MOT_Mot              = 2038;
    const MOT_DummyEntier1     = 13460;

    const OPTION_IDOption       = 2082;
    const OPTION_IDModele       = 2088;
    const OPTION_Libelle        = 2083;
    const OPTION_Ordre          = 2084;
    const OPTION_NonAffiche     = 2085;
    const OPTION_Obligatoire    = 2086;
    const OPTION_Aide           = 2087;
    const OPTION_IDModule       = 2089;
    const OPTION_ValeurDefaut   = 9686;

    const PARAMETRE_IDParametre     = 2066;
    const PARAMETRE_IDAction        = 2068;
    const PARAMETRE_IDModele        = 2073;
    const PARAMETRE_Libelle         = 2071;
    const PARAMETRE_Ordre   = 2070;
    const PARAMETRE_Obligatoire = 2067;
    const PARAMETRE_Aide    = 2075;
    const PARAMETRE_ValeurDefaut = 8631;
    const PARAMETRE_EstInvisible     = 13372;
    const PARAMETRE_DummyID1     = 15039;
    const PARAMETRE_DummyBool1      = 15040;

    const PHRASE_IDPhrase       = 2046;
    const PHRASE_IDAction       = 2048;
    const PHRASE_Mot2Phrase     = 2050;

    const TABLEAU_IDTableau                 = 2099;
    const TABLEAU_Libelle                   = 2100;
    const TABLEAU_Aide                      = 2104;
    const TABLEAU_IDModule                  = 2105;
    const TABLEAU_Lieu_Stockage             = 2414; // peut etre 2413
    const TABLEAU_Pere                      = 3177;
    const TABLEAU_Colonne                   = 2107; //n'existe pas physiquement
    const TABLEAU_Couleur                   = 9676;
    const TABLEAU_NomFichier                = 9416;
    const TABLEAU_IDPersonnalisationTable   = 16981;
    const TABLEAU_IDDescExterne             = 15037;
    const TABLEAU_Commentaire               = 15492;
    const TABLEAU_DHCreation                = 15493;
    const TABLEAU_IDCreationPar             = 15494;
    const TABLEAU_DHModification            = 15495;
    const TABLEAU_IDModifPar                = 15496;
    const TABLEAU_IDIcone                   = 15569;
    const TABLEAU_FormuleSynchro            = 16585;

    const SYNONYME_IDSynonyme   = 2042;
    const SYNONYME_Synonyme     = 2043;
    const SYNONYME_IDMot        = 2045;

    const CREATION_AUTOMATIQUE_IDCreationAuto   = 2033;
    const CREATION_AUTOMATIQUE_IDTableau        = 2036;
    const CREATION_AUTOMATIQUE_Libelle          = 2034;

    const CONDITION_IDCondition     = 4828;
    const CONDITION_IDTablAvecCond  = 4830;
    const CONDITION_IDColonne       = 4831;
    const CONDITION_TypeCondition   = 4843;
    const CONDITION_Formule         = 4844;
    const CONDITION_Ordre           = 15050;
    const CONDITION_TypeOperateur   = 15051;

    const OPERATION_IDOperation     = 4846;
    const OPERATION_IDEvenement     = 4847;
    const OPERATION_IDColRes        = 4849;
    const OPERATION_TypeActionAuto  = 4854;
    const OPERATION_Formule         = 4855;
    const OPERATION_Ordre           = 4856;
    const OPERATION_ListeCond       = 10526;

    const ACTIONAUTOMATIQUE_IDActionAuto    = 7006;
    const ACTIONAUTOMATIQUE_IDEvenement     = 7008;
    const ACTIONAUTOMATIQUE_Ordre           = 7003;
    const ACTIONAUTOMATIQUE_Libelle         = 7009;

    const ENVMESSAGE_IDActionAuto   = 7016;
    const ENVMESSAGE_Ordre          = 7013;
    const ENVMESSAGE_IDEvenement    = 7014;
    const ENVMESSAGE_Destinataires  = 7018;
    const ENVMESSAGE_Sujet          = 7017;
    const ENVMESSAGE_TexteMessage   = 7019;
    const ENVMESSAGE_Urgence        = 8035;
    const ENVMESSAGE_FichierPJ      = 15854;
    const ENVMESSAGE_ModeleImpPJ    = 15855;
    const ENVMESSAGE_Expediteur     = 16706;

    const LANCEACTION_IDActionAuto  = 7032;
    const LANCEACTION_Ordre         = 7029;
    const LANCEACTION_IDEvenement   = 7030;
    const LANCEACTION_IDAction      = 7033;
    const LANCEACTION_Libelle       = 7031;
    const LANCEACTION_Commande      = 13456;
    const LANCEACTION_AConfirmer    = 15784;

    const EVENEMENT_IDEvenement     = 4823;
    const EVENEMENT_Libelle         = 7654;
    const EVENEMENT_AConfirmer      = 4826;
    const EVENEMENT_Conditions      = 4858;
    const EVENEMENT_ActionsAuto     = 4860;
    const EVENEMENT_ListeColBoucle  = 15055;
    const EVENEMENT_NePasExecuter   = 15497;
    const EVENEMENT_MsgConfirmation = 15498;
    const EVENEMENT_IDTypeMsgConf   = 15499;
    const EVENEMENT_MsgCptRendu     = 15500;
    const EVENEMENT_DHCreation      = 15501;
    const EVENEMENT_DHModification  = 15502;
    const EVENEMENT_IDCreationPar   = 15503;
    const EVENEMENT_IDModifPar      = 15504;
    const EVENEMENT_Commentaire     = 15505;
    const EVENEMENT_MsgAttente      = 15554;
    const EVENEMENT_IDDomaine       = 15698;
    const EVENEMENT_ListeBoucle     = 15880;

    const EVENEMENTACTION_IDEvenement       = 7631;
    const EVENEMENTACTION_IDAction          = 7630;
    const EVENEMENTACTION_Libelle           = 7653;
    const EVENEMENTACTION_AConfirmer        = 7629;
    const EVENEMENTACTION_Conditions        = 7628;
    const EVENEMENTACTION_ActionsAuto       = 7632;
    const EVENEMENTACTION_ListeColBoucle    = 12294;
    const EVENEMENTACTION_NePasExecuter     = 15506;
    const EVENEMENTACTION_MsgConfirmation   = 15507;
    const EVENEMENTACTION_IDTypeMsgConf     = 15508;
    const EVENEMENTACTION_MsgCptRendu       = 15509;
    const EVENEMENTACTION_DHCreation        = 15510;
    const EVENEMENTACTION_DHModification    = 15511;
    const EVENEMENTACTION_IDCreationPar     = 15512;
    const EVENEMENTACTION_IDModifPar        = 15513;
    const EVENEMENTACTION_Commentaire       = 15514;
    const EVENEMENTACTION_MsgAttente        = 15555;
    const EVENEMENTACTION_IDDomaine         = 15696;
    const EVENEMENTACTION_ListeBoucle       = 15808;
    const EVENEMENTACTION_IDTypeExecution   = 15857;
    const EVENEMENTACTION_Priorite          = 17114;

    const EVENEMENTTEMPOREL_IDEvenement         = 7638;
    const EVENEMENTTEMPOREL_Libelle             = 7651;
    const EVENEMENTTEMPOREL_Temporel            = 7650; //TMP_Xxxxxx
    const EVENEMENTTEMPOREL_Formule             = 7652;
    const EVENEMENTTEMPOREL_AConfirmer          = 7636;
    const EVENEMENTTEMPOREL_Conditions          = 7635;
    const EVENEMENTTEMPOREL_ActionsAuto         = 7639;
    const EVENEMENTTEMPOREL_EstParSIMAXService  = 15053;
    const EVENEMENTTEMPOREL_ListeColBoucle      = 15056;
    const EVENEMENTTEMPOREL_NePasExecuter       = 15515;
    const EVENEMENTTEMPOREL_MsgConfirmation     = 15516;
    const EVENEMENTTEMPOREL_IDTypeMsgConf       = 15517;
    const EVENEMENTTEMPOREL_MsgCptRendu         = 15518;
    const EVENEMENTTEMPOREL_DHCreation          = 15519;
    const EVENEMENTTEMPOREL_DHModification      = 15520;
    const EVENEMENTTEMPOREL_IDCreationPar       = 15521;
    const EVENEMENTTEMPOREL_IDModifPar          = 15522;
    const EVENEMENTTEMPOREL_Commentaire         = 15523;
    const EVENEMENTTEMPOREL_MsgAttente          = 15556;
    const EVENEMENTTEMPOREL_IDDomaine           = 15699;
    const EVENEMENTTEMPOREL_ListeBoucle         = 15809;

    const TABLEAUCROISE_IDTableau               = 9155;
    const TABLEAUCROISE_Libelle                 = 9156;
    const TABLEAUCROISE_IDModule                = 9157;
    const TABLEAUCROISE_Calculs                 = 9159;
    const TABLEAUCROISE_IDInfoV                 = 9262;
    const TABLEAUCROISE_IDInfoH                 = 9263;
    const TABLEAUCROISE_IDPersonnalisationTable = 16982;
    const TABLEAUCROISE_Aide                    = 9320;
    const TABLEAUCROISE_Commentaire             = 15524;
    const TABLEAUCROISE_DHCreation              = 15525;
    const TABLEAUCROISE_IDCreationPar           = 15526;
    const TABLEAUCROISE_DHModification          = 15527;
    const TABLEAUCROISE_IDModifPar              = 15528;
    const TABLEAUCROISE_ColTri                  = 16948;

    const AXETABLEAUCROISE_IDAxe          = 9161;
    const AXETABLEAUCROISE_IDColonne      = 9162;
    const AXETABLEAUCROISE_Libelle        = 9163;
    const AXETABLEAUCROISE_IDTableauRecap = 9313;
    const AXETABLEAUCROISE_TypeAffichage  = 9319;
    const AXETABLEAUCROISE_Aide           = 9314;
    const AXETABLEAUCROISE_Ordre          = 13480;

    const TABLEAUBASE_IDTableau                 = 9269;
    const TABLEAUBASE_Libelle                   = 9270;
    const TABLEAUBASE_IDModule                  = 9324;
    const TABLEAUBASE_Commentaire               = 15529;
    const TABLEAUBASE_IDPersonnalisationTable   = 16980;
    const TABLEAUBASE_DHCreation                = 15530;
    const TABLEAUBASE_IDCreationPar             = 15531;
    const TABLEAUBASE_DHModification            = 15532;
    const TABLEAUBASE_IDModifPar                = 15533;

    const ACTIONAFFICHETABLEAUCROISE_IDAction        = 9327;
    const ACTIONAFFICHETABLEAUCROISE_IDModule        = 9330;
    const ACTIONAFFICHETABLEAUCROISE_Libelle         = 9331;
    const ACTIONAFFICHETABLEAUCROISE_IDTableauCroise = 9333;
    const ACTIONAFFICHETABLEAUCROISE_Parametres      = 9335;
    const ACTIONAFFICHETABLEAUCROISE_ListePhrase     = 9336;
    const ACTIONAFFICHETABLEAUCROISE_AConfirmer      = 17309;
//    const ACTIONAFFICHETABLEAUCROISE_EvtDeclenches   = 9741;    // pas de sens
//    const ACTIONAFFICHETABLEAUCROISE_Automatismes    = 9742;    // pas de sens

    const TABLAVECCOND_IDTablCond   = 9977;
    const TABLAVECCOND_Libelle      = 9978;
    const TABLAVECCOND_Conditions   = 9979;

    const MISEENFORME_IDMiseEnForme     = 10040;
    const MISEENFORME_IDTableau         = 10043;
    const MISEENFORME_Conditions        = 10042;
    const MISEENFORME_Couleur           = 10044;
    const MISEENFORME_Gras              = 10046;
    const MISEENFORME_Italique          = 10048;
    const MISEENFORME_DummyTexte1       = 13481;
    const MISEENFORME_Couleur2          = 15054;
    const MISEENFORME_Libelle           = 16060;

    const CONTROLEVALIDITE_IDControle    = 10092;
    const CONTROLEVALIDITE_Libelle       = 16857;
    const CONTROLEVALIDITE_IDTableau     = 10095;
    const CONTROLEVALIDITE_Conditions    = 10094;
    const CONTROLEVALIDITE_MsgErr        = 10096;
    const CONTROLEVALIDITE_IDTypeMsgErr  = 15534;

    const QUESTIONREPONSE_IDQuestionReponse = 10155;
    const QUESTIONREPONSE_IDModule          = 10156;
    const QUESTIONREPONSE_Libelle           = 10157;
    const QUESTIONREPONSE_Phrases           = 10158;
    const QUESTIONREPONSE_Reponse           = 10159;

    const ACTIONOUQUESTION_IDQuestionReponse = 10141;
    const ACTIONOUQUESTION_IDModule          = 10142;
    const ACTIONOUQUESTION_Libelle           = 10143;

    const CONTROLEUNICITE_IDControle   = 11040;
    const CONTROLEUNICITE_Libelle      = 16859;
    const CONTROLEUNICITE_IDTableau    = 11043;
    const CONTROLEUNICITE_Colonne      = 11047;
    const CONTROLEUNICITE_MsgErr       = 11045;
    const CONTROLEUNICITE_IDTypeMsgErr = 15535;

    const TABLEAUPREV_IDUnique          = 11173;
    const TABLEAUPREV_Intitule          = 11174;
    const TABLEAUPREV_IDDomaine         = 11175;
    const TABLEAUPREV_IDTableauReel     = 11177;
    const TABLEAUPREV_IDAxeParcours     = 11179;
    const TABLEAUPREV_NbUnitesFiche     = 11195;
    const TABLEAUPREV_NbUnitesListe     = 11314;
    const TABLEAUPREV_LnTableauPrev     = 11267;
    const TABLEAUPREV_DHCreation        = 15536;
    const TABLEAUPREV_DHModification    = 15537;
    const TABLEAUPREV_IDCreationPar     = 15538;
    const TABLEAUPREV_IDModifPar        = 15539;
    const TABLEAUPREV_Commentaire       = 15540;

    const LNTABLEAUPREV_IDUnique      = 11231;
    const LNTABLEAUPREV_IDTableauPrev = 11233;
    const LNTABLEAUPREV_IDColonne     = 11235;
    const LNTABLEAUPREV_Formule       = 11237;

    const AXETABLEAUPREV_IDUnique       = 11277;
    const AXETABLEAUPREV_IDTableauPrev  = 11279;
    const AXETABLEAUPREV_IDColParcours  = 11281;
    const AXETABLEAUPREV_PasParcours    = 11184;
    const AXETABLEAUPREV_Formule        = 15541;

    const CONTROLEACTION_IDControle   = 13483;
    const CONTROLEACTION_IDAction     = 13485;
    const CONTROLEACTION_Libelle      = 16858;
    const CONTROLEACTION_Conditions   = 13487;
    const CONTROLEACTION_MsgErr       = 13489;
    const CONTROLEACTION_IDTypeMsgErr = 15542;

    const VUE_IDVue                     = 13616;
    const VUE_Intitule                  = 13617;
    const VUE_IDDomaine                 = 13618;
    const VUE_Donnees                   = 13620;
    const VUE_IDPersonnalisationTable   = 16983;
    const VUE_Aide                      = 13622;
    const VUE_DHCreation                = 15543;
    const VUE_DHModification            = 15544;
    const VUE_IDCreationPar             = 15545;
    const VUE_IDModifPar                = 15546;
    const VUE_Commentaire               = 15547;

    const ACTIONAFFICHEVUE_IDAction      = 13650;
    const ACTIONAFFICHEVUE_IDModule      = 13651;
    const ACTIONAFFICHEVUE_Libelle       = 13652;
    const ACTIONAFFICHEVUE_IDVue         = 13660;
    const ACTIONAFFICHEVUE_Parametres    = 13656;
    const ACTIONAFFICHEVUE_ListePhrase   = 13657;
    const ACTIONAFFICHEVUE_AConfirmer    = 17310;
//    const ACTIONAFFICHEVUE_EvtDeclenches = 13664; // pas de sens
//    const ACTIONAFFICHEVUE_Automatismes  = 13665; // pas de sens

    const RUPTURE_IDRupture             = 15642;
    const RUPTURE_IDColonne             = 15643;
    const RUPTURE_Libelle               = 15644;
    const RUPTURE_DummyBool1            = 0;
    const RUPTURE_DummyInt1             = 0;
    const RUPTURE_DummyID1              = 0;
    const RUPTURE_FormuleTransformation = 16201;
    const RUPTURE_Compteur              = 16202;
    const RUPTURE_Somme                 = 16203;
    const RUPTURE_Moyenne               = 16204;
    const RUPTURE_Maximum               = 16205;
    const RUPTURE_Minimum               = 16206;
    const RUPTURE_SautDePage            = 16296;

    const AFFECTATIONPARAMETRE_IDAffectParam   = 15576;
    const AFFECTATIONPARAMETRE_IDLanceAction   = 15577;
    const AFFECTATIONPARAMETRE_IDParametre     = 15579;
    const AFFECTATIONPARAMETRE_Valeur          = 15580;
    const AFFECTATIONPARAMETRE_DummyBool1      = 0;
    const AFFECTATIONPARAMETRE_DummyID1        = 0;

    const BOUCLEAUTO_IDBoucleAuto   = 15610;
    const BOUCLEAUTO_IDActionAuto   = 15611;
    const BOUCLEAUTO_Formule        = 15612;
    const BOUCLEAUTO_IDModele       = 15613;
    const BOUCLEAUTO_DummyBool1     = 0;
    const BOUCLEAUTO_DummyID1       = 0;
    const BOUCLEAUTO_Ordre          = 15879;
    const BOUCLEAUTO_NbTours        = 17222;


    const FONCTIONEXTERNE_IDFonction    = 15939;
    const FONCTIONEXTERNE_Intitule      = 15940;
    const FONCTIONEXTERNE_Prototype     = 15941;
    const FONCTIONEXTERNE_Description   = 15942;

    const FONCTIONEXTERNEWS_IDFonction      = 15969;
    const FONCTIONEXTERNEWS_Intitule        = 15976;
    const FONCTIONEXTERNEWS_Prototype       = 15977;
    const FONCTIONEXTERNEWS_Description     = 15978;
    const FONCTIONEXTERNEWS_Hote            = 15970;
    const FONCTIONEXTERNEWS_NomWS           = 15971;
    const FONCTIONEXTERNEWS_Operation       = 15972;
    const FONCTIONEXTERNEWS_TypeRequete     = 15973;
    const FONCTIONEXTERNEWS_Attribut        = 15974;
    const FONCTIONEXTERNEWS_FormuleTransf   = 15975;

    const MODELEEDITION_IDModeleImp             = 10962;
    const MODELEEDITION_DocumentModele          = 10964;
    const MODELEEDITION_IDTableau               = 10966;
    const MODELEEDITION_PourListe               = 12856;
    const MODELEEDITION_ListeCond               = 12981;
    const MODELEEDITION_CopieNonAssemblee       = 15337;
    const MODELEEDITION_Sortie                  = 15339;
    const MODELEEDITION_FormatSortie            = 16313;
    const MODELEEDITION_ImpressionFichierSortie = 16984;
    const MODELEEDITION_Intitule                = 17004;

    const DROIT_IDDroit               = 7656;
    const DROIT_IDUtilisateurOuGroupe = 7713;
    const DROIT_ModuleAutorise        = 7714;
    const DROIT_ActionEnMoins         = 7715;
    const DROIT_ListeDroitCol         = 8578;
    const DROIT_Intitule              = 16164;

    const DROITCOLONNE_IDDroitColonne   = 8528;
    const DROITCOLONNE_IDDroit          = 8529;
    const DROITCOLONNE_IDColonne        = 8530;
    const DROITCOLONNE_TypeDroitColonne = 8535;

    const MENUPOURTOUS_IDMenu   = 11655;
    const MENUPOURTOUS_Libelle      = 11656;
    const MENUPOURTOUS_OptionsMenu  = 11657;
    const MENUPOURTOUS_Ordre        = 11658;
    const MENUPOURTOUS_IDMenuParent = 16941;    //le menu pere de l'element menu pour tous.

    const OPTIONMENUPOURTOUS_IDOptionMenu = 11580;
    const OPTIONMENUPOURTOUS_IDMenuParent  = 11584;
    const OPTIONMENUPOURTOUS_Libelle       = 11582;
    const OPTIONMENUPOURTOUS_Raccourci = 11585;
    const OPTIONMENUPOURTOUS_IDAction  = 11581;
    const OPTIONMENUPOURTOUS_IDIcone   = 11586;
    const OPTIONMENUPOURTOUS_Ordre   = 11587;
    const OPTIONMENUPOURTOUS_Commande     = 11583;
    const OPTIONMENUPOURTOUS_HomeImg      = 17547;
    const OPTIONMENUPOURTOUS_HomeDesc = 17548;
    const OPTIONMENUPOURTOUS_HomeTitle     = 17550;




    const CONDREQUETEPOURTOUS_IDCondition   = 6196;
    const CONDREQUETEPOURTOUS_IDColonne     = 6198;
    const CONDREQUETEPOURTOUS_TypeCondition = 6199;
    const CONDREQUETEPOURTOUS_Formule       = 6200;
    const CONDREQUETEPOURTOUS_IDRequete     = 6290;
    const CONDREQUETEPOURTOUS_CondEnsemble  = 10871;
    const CONDREQUETEPOURTOUS_TypeOperateur = 15133;
    const CONDREQUETEPOURTOUS_Ordre         = 16199;

    const REQUETEPOURTOUS_IDRequete  = 6202;
    const REQUETEPOURTOUS_Libelle    = 6203;
    const REQUETEPOURTOUS_IDTableau  = 6204;
    const REQUETEPOURTOUS_ListeCond  = 6292;

    const CONTROLEETAT_IDControle       = 16521;
    const CONTROLEETAT_Libelle          = 16550;
    const CONTROLEETAT_IDTableau        = 16552;
    const CONTROLEETAT_Etat             = 16578;
    const CONTROLEETAT_ListeCol         = 16553;
    const CONTROLEETAT_Conditions       = 16551;

    const COLONNEBASE_IDColonneBase  = 16714;
    const COLONNEBASE_IDModele       = 16715;
    const COLONNEBASE_Libelle        = 16716;
    const COLONNEBASE_IDTableau      = 16717;
    const COLONNEBASE_Ordre          = 16718;
    const COLONNEBASE_EstInvisible   = 16720;
    const COLONNEBASE_Aide           = 16779;
    const COLONNEBASE_Formule        = 16719;

    const INDICATEUR_IDIndicateur   = 16747;
    const INDICATEUR_IDModele       = 16748;
    const INDICATEUR_Libelle        = 16749;
    const INDICATEUR_IDTableau      = 16750;
    const INDICATEUR_Ordre          = 16751;
    const INDICATEUR_EstInvisible   = 16753;
    const INDICATEUR_Aide           = 16780;
    const INDICATEUR_Formule        = 16752;

    const TABLEAUDEBORD_IDTableauDeBord = 16782;
    const TABLEAUDEBORD_Intitule        = 16783;
    const TABLEAUDEBORD_IDModule        = 16784;
    const TABLEAUDEBORD_Commentaire     = 16785;
    const TABLEAUDEBORD_DHCreation      = 16786;
    const TABLEAUDEBORD_IDCreationPar   = 16787;
    const TABLEAUDEBORD_DHModification  = 16788;
    const TABLEAUDEBORD_IDModifPar      = 16789;
    const TABLEAUDEBORD_ListeIndicateur = 16816;

    const MODEAFFICHAGE_IDModeAff     = 16861;
    const MODEAFFICHAGE_Libelle       = 16862;
    const MODEAFFICHAGE_IDContModeAff = 17054;
    const MODEAFFICHAGE_Ordre         = 17055;

    const MODEAFFICHAGELISTE_IDModeAff          = 16896;
    const MODEAFFICHAGELISTE_Libelle            = 16897;
    const MODEAFFICHAGELISTE_IDContModeAff      = 17051;
    const MODEAFFICHAGELISTE_Ordre              = 17052;
    const MODEAFFICHAGELISTE_PresentationDefaut = 17050;
    const MODEAFFICHAGELISTE_AvecListe          = 17064;
    const MODEAFFICHAGELISTE_AvecPlanning       = 17065;
    const MODEAFFICHAGELISTE_AvecGraphe         = 17067;
    const MODEAFFICHAGELISTE_AvecPlan           = 17068;
    const MODEAFFICHAGELISTE_AvecGantt          = 17069;
    const MODEAFFICHAGELISTE_AvecOrganigramme   = 17066;
    const MODEAFFICHAGELISTE_AvecArborescence   = 17147;
    const MODEAFFICHAGELISTE_AvecListeImage     = 17148;

    const PERSONNALISATIONTABLE_IDPersonnalisation = 16950;
    const PERSONNALISATIONTABLE_IDTableau          = 16954;
    const PERSONNALISATIONTABLE_IDColTri           = 16951;
    const PERSONNALISATIONTABLE_TriAsc             = 16952;
    const PERSONNALISATIONTABLE_NbMaxRes           = 16953;

    const CONTROLEANNULATION_IDControle       = 16826;
    const CONTROLEANNULATION_Libelle          = 16827;
    const CONTROLEANNULATION_IDTableau        = 16829;
    const CONTROLEANNULATION_Conditions       = 16828;
    const CONTROLEANNULATION_MsgErr           = 16830;
    const CONTROLEANNULATION_IDTypeMsgErr     = 16831;

    const TABLEAUAVECMODEAFFICHAGE_IDTabAvecModeAff = 17116;

    //ICI nouveau fichier du langage
    //!!!!!!!!!!!! On n'ajoute rien ICI !!!!!!!!!!!!!!!!!!!!!!!!!

    //--------------------------
    //******************** COLONNE DE MESSAGERIE ******************************
    //------- 16061<->16160 -------------------
    //colonne physique dans les tables
    const MESSAGERIE_IDMessage     = 16061;
    const MESSAGERIE_DateHeure         = 16062;
    const MESSAGERIE_ASynchroniser = 16063;
    const MESSAGERIE_Texte                  = 16064;
    const MESSAGERIE_IDMessagePrecedent     = 16065;
    const MESSAGERIE_IDTableauLie           = 16066;
    const MESSAGERIE_IDEnregLie    = 16067;
    const MESSAGERIE_PJ                = 16068;
    const MESSAGERIE_Destinataires      = 16069;
    const MESSAGERIE_Importance             = 16070;
    const MESSAGERIE_AccuseReceptionInt = 16071;
    const MESSAGERIE_AccuseReceptionEmail   = 16072;
    const MESSAGERIE_ConfirmLectInt         = 16073;
    const MESSAGERIE_ConfirmLectEmail       = 16074;
    const MESSAGERIE_ConfirmValidInt = 16075;
    const MESSAGERIE_AEnvoyer          = 16076;
    const MESSAGERIE_Mailing        = 16077;
    const MESSAGERIE_AdresseRetour    = 16078;
    const MESSAGERIE_Expediteur   = 16079;
    const MESSAGERIE_IDExpediteur = 16080;
    const MESSAGERIE_TypeDest = 16081;
    const MESSAGERIE_EmailSIMAX = 16082;
    const MESSAGERIE_Spam             = 16083;
    const MESSAGERIE_Email            = 16084;
    const MESSAGERIE_InterneSIMAX = 16085;
    const MESSAGERIE_Traite                 = 16086;
    const MESSAGERIE_Etat                   = 16087;
    const MESSAGERIE_Transfere              = 16088;
    const MESSAGERIE_CompteEmail            = 16089;
    const MESSAGERIE_UIDL_POP               = 16090;
    const MESSAGERIE_DescriptionAction      = 16091;
    const MESSAGERIE_AnnulationAction       = 16092;
    const MESSAGERIE_MessageAction          = 16093;
    const MESSAGERIE_IDDestinataire         = 16094;
    const MESSAGERIE_IDTableauDest          = 16095;
    const MESSAGERIE_IndiceDest             = 16096;
    const MESSAGERIE_IDUtilisateur          = 16097;
    const MESSAGERIE_IDProfil               = 16098;
    const MESSAGERIE_Profil        = 16099;
    const MESSAGERIE_IDDossier         = 16100;
    const MESSAGERIE_IDDossierPere = 16101;
    const MESSAGERIE_Libelle                = 16102;
    const MESSAGERIE_ListeUtil              = 16103;
    const MESSAGERIE_Recu                   = 16104;
    const MESSAGERIE_IMAP_UIDL              = 17243;
    const MESSAGERIE_IMAP_ASynchro          = 17244;
    const MESSAGERIE_IDDestinataires        = 17245;
    const MESSAGERIE_TypeDossier            = 17246;
    const MESSAGERIE_IMAP_Dossier           = 17247;
    const MESSAGERIE_IMAP_AvecSousDossier   = 17248;
    const MESSAGERIE_IMAP_LastUIDCheck      = 17249;
    const MESSAGERIE_IMAP_UIDVerify         = 17250;
    const MESSAGERIE_DerniereModif          = 17252;
    const MESSAGERIE_IMAP_LastSynchro       = 17253;

    //nouvelle collone ici

    const MESSAGERIE_IDMessageExt        = 16150; //pour la synchro
    const MESSAGERIE_DateHeureModif      = 16151; //pour la synchro
    //------------------------------------------

    //------- 15316<->15336 -------------------
    //colonne non physique
    const MESSAGERIE_Sujet                  = 15316;
    const MESSAGERIE_Destinataires_To     = 15317;
    const MESSAGERIE_IDDestinataires_To     = 15318;
    const MESSAGERIE_Destinataires_Cc      = 15319;
    const MESSAGERIE_IDDestinataires_Cc  = 15320;
    const MESSAGERIE_Destinataires_Cci   = 15321;
    const MESSAGERIE_IDDestinataires_Cci = 15322;
    const MESSAGERIE_MessageEnClair = 15323;
    const MESSAGERIE_MessageHTML    = 15324;
    const MESSAGERIE_PJ_ID      = 15325;
    const MESSAGERIE_ElementLie = 15326;
    const MESSAGERIE_NonLu       = 15327;
    const MESSAGERIE_AccuseReception  = 15328;
    const MESSAGERIE_ConfirmLect  = 15329;
    const MESSAGERIE_ConfirmValid = 15330;
    //------------------------------------------
}
