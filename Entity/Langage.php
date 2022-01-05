<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 13/11/14
 * Time: 11:37
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity;

/**
 * classe qui contient les identifiants du langage ainsi que les différents checksum de version pour l'IHM
 * Class Langage
 * @package NOUT\Bundle\NOUTOnlineBundle\Entity
 */
class Langage
{
	protected $m_sVersion_Langage='';
	protected $m_sVersion_Icone='';
	protected $m_bWithUndo = false;
	protected $m_bWithRedo = false;


	/**
	 * @return array
	 */
	public function forSerialization() : array
	{
		return [$this->m_sVersion_Langage, $this->m_sVersion_Icone, $this->m_bWithUndo, $this->m_bWithRedo];
	}

    /**
     * @param array $data
     */
	public function fromSerialization(array $data)
    {
        if (count($data)<=2){
            list($this->m_sVersion_Langage, $this->m_sVersion_Icone) = $data;
            return;
        }
        list($this->m_sVersion_Langage, $this->m_sVersion_Icone, $this->m_bWithUndo, $this->m_bWithRedo) = $data;
    }

	/**
	 * @param $serialized
	 */
	public function unserialize($serialized)
	{
		list($this->m_sVersion_Langage, $this->m_sVersion_Icone) = unserialize($serialized);
	}

	/**
	 * @param $sVLangage string
	 * @param $sVIcone string
     * @param bool $bUndo
     * @param bool $bRedo
	 */
	public function __construct($sVLangage='', $sVIcone='', $bUndo=false, $bRedo=false)
	{
		$this->m_sVersion_Langage = $sVLangage;
		$this->m_sVersion_Icone   = $sVIcone;
		$this->m_bWithUndo = $bUndo;
		$this->m_bWithRedo = $bRedo;
	}

	/**
	 * @return string
	 */
	public function getVersionLangage(): string
    {
		return $this->m_sVersion_Langage;
	}

	/**
	 * @param $sVersion string
	 * @return $this
	 */
	public function setVersionLangage(string $sVersion): Langage
    {
		$this->m_sVersion_Langage = $sVersion;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getVersionIcone(): string
    {
		return $this->m_sVersion_Icone;
	}

	/**
	 * @param $sVersion string
	 * @return $this
	 */
	public function setVersionIcone(string $sVersion): Langage
    {
		$this->m_sVersion_Icone = $sVersion;

		return $this;
	}

    /**
     * @return bool
     */
	public function getWithUndo(): bool
    {
	    return $this->m_bWithUndo;
    }

    /**
     * @return bool
     */
    public function getWithRedo(): bool
    {
        return $this->m_bWithRedo;
    }

    /**
     * @param bool $bUndo
     * @param bool $bRedo
     * @return $this
     */
	public function setUndoRedo(bool $bUndo, bool $bRedo){
	    $this->m_bWithRedo = $bRedo;
	    $this->m_bWithUndo = $bUndo;
	    return $this;
    }

	public static function s_isActionReadOnly($eTYPEACTION)
	{
        // Indique quels boutons sont dispo en readOnly
		$isReadOnlyButton =[
            self::eTYPEACTION_Unknown       => false,
            self::eTYPEACTION_Creation      => false,
            self::eTYPEACTION_Modification  => false,
            self::eTYPEACTION_Liste         => false,
			self::eTYPEACTION_Suppression   => false,
            self::eTYPEACTION_AjouterA      => false,
            self::eTYPEACTION_EnleverDe     => false,
            self::eTYPEACTION_Particuliere  => false,
            self::eTYPEACTION_Exporter      => true,
            self::eTYPEACTION_Importer      => false,

            self::eTYPEACTION_Consultation  => true,
            self::eTYPEACTION_Impression    => true,
		];

        return $isReadOnlyButton[$eTYPEACTION];
	}

	//typeAction
	const eTYPEACTION_Unknown               = 0;		//Action inconnue (ne manipule pas un objet de façon générique)
	const eTYPEACTION_DescEnreg             = 1;		//Description d'un enreg
	const eTYPEACTION_Creation              = 2386;
	const eTYPEACTION_Modification          = 2387;
	const eTYPEACTION_Liste                 = 2388;
	const eTYPEACTION_Recherche             = 2389;
	const eTYPEACTION_Consultation          = 2390;
	const eTYPEACTION_Suppression           = 2391;
	const eTYPEACTION_Impression            = 2392;
	const OBS_eTYPEACTION_Info              = 2393;		//Action qui va afficher une information a partir d'une phrase (genre "Aujourd'hui")
	const eTYPEACTION_Particuliere          = 2394;
	const eTYPEACTION_EnleverDe             = 3182;
	const eTYPEACTION_AjouterA              = 3183;
	const eTYPEACTION_CreerAPartirDe        = 5303;
	const eTYPEACTION_LanceExe              = 8684;		// Action lancement d'exe n'est pas un choix du modele type action, n'existe pas dans le langage
	const eTYPEACTION_TransformerEn         = 8720;
	const eTYPEACTION_AfficheTableauCroise  = 9338;
	const eTYPEACTION_AfficheVue            = 15135;
	const eTYPEACTION_Planning              = 10790;
	const eTYPEACTION_Exporter              = 15477;
	const eTYPEACTION_Importer              = 15478;
	const eTYPEACTION_DeclencherAuto        = 15702;
	// Si ajout de boutons, penser à modifier méthode s_isActionReadOnly pour préciser si le bouton est visible


	/*************************************************************
	 * IDENTIFIANT DE FORMULAIRE
	 *************************************************************/

	const COL_GENERIQUE_TYPEFORMULAIRE  = 15134;
	const COL_GENERIQUE_MINIDESC        = 15311;
	const COL_GENERIQUE_RUPTURE         = 15910;

	// organigramme
	const TABL_AxeOrganigramme = 13734;
	const TABL_Organigramme    = 13781;

	// Pabx historisation des appels entrant
	const TABL_AppelRecu   = 13429;

	// tableau pour messagerie
	const TABL_Destinataire   = 1390;
	const TABL_Contact        = 1003;
	const TABL_GR_Contact     = 1004;
	const TABL_Utilisateur    = 1169;
	const TABL_GR_Utilisateur = 1175;
	const TABL_CompteEmail    = 1224;
	const TABL_ReponseType    = 12344;
	const TABL_PieceJointe    = 13299;

	// Donnees Externe
	const TABL_ConnexionBDD        = 14076;
	const TABL_ImportExportSynchro = 15881;
	const TABL_Export              = 14884;
	const TABL_Import              = 14387;
	const TABL_Synchro             = 14966;
	const TABL_SynchroUtilisateur  = 17073; //tableau de dernière synchro utilisateur
	const TABL_DescDonneeExterne   = 14391;
	const TABL_DescCsv             = 14666;
	const TABL_DescXls             = 14718;
	const TABL_DescPocket          = 14761;
	const TABL_DescOutlook         = 14802;
	const TABL_DescBdd             = 14615;
	const TABL_DescTexteDelimite   = 14572;
	const TABL_DescIcsVcf          = 14843;
	const TABL_DescExchange        = 15145;
	const TABL_DescXml             = 15269;
	const TABL_DescXmlServeur      = 16612;
	const TABL_DescXmlSepa         = 16659;
	const TABL_CorrespColonne      = 14397;
	const TABL_CorrespClassique    = 14501;
	const TABL_CorrespDelimite     = 14536;
	const OLD_TABL_CorrespColonne  = 7567;

	// pour import outlook / pocket
	const TABL_ContactPerso      = 1394;
	const TABL_ContactPro        = 1403;
	const TABL_Tache             = 3017;
	const TABL_RendezVous        = 7945;

	// planification
	const TABL_MaSociete                = 9619;
	const TABL_HoraireOuverture         = 9569;
	const TABL_JourFerie                = 9629;
	const TABL_Ressource                = 8267;		//ressource pour la planification
	const TABL_ReservationRessource     = 8372;
	const TABL_PlanificationRessource   = 17151;
	const TABL_Periodicite              = 9787;
	const TABL_PeriodiciteJSemaine      = 9789;


	// menu
	const TABL_MenuPerso           = 10246;
	const TABL_OptionMenuPerso     = 10291;
	const TABL_Menu                = 11615;
	const TABL_OptionMenu          = 11540;
	const TABL_ImageCatalogue      = 11508; //tableau des icones


	// impression
	const TABL_Document             = 10543;
	const TABL_ImpressionTicket     = 10703;
	const TABL_ImpressionEtiquette  = 10762;

	// formule / fonction
	const TABL_Fonction = 11727;

	// historique
	const TABL_Historique = 12691;

	//--- Tableau de vocabulaire
	const TABL_Pays         = 9495;
	const TABL_Ville        = 9494;
	const TABL_Departement  = 10654;
	const TABL_Prenom       = 9931;


	// Gestion Packet SMX
	const TABL_FichierSMX           = 15348;
	const TABL_FichierImportSMX     = 15425;
	const TABL_PaquetSMX            = 15385;

	// Requete personnelle
	const TABL_Requete                = 16165; //entonoir
	const TABL_RequetePersonnelle     = 16242;
	const TABL_CondRequetePersonnelle = 16208;


	//--- Tableau du langage
	const TABL_Mot                          = 2020;
	const TABL_Synonyme                     = 2021;
	const TABL_Phrase                       = 2022;
	const TABL_Module                       = 2023;
	const TABL_Parametre                    = 2025;
	const TABL_Modele                       = 2026;
	const TABL_Option                       = 2027;
	const TABL_ColInfo                      = 2028;
	const TABL_Tableau                      = 2029;
	const TABL_CreationAuto                 = 2032;
	const TABL_Choix                        = 2362;
	const TABL_ModeleClassique              = 2419;
	const TABL_ModeleElem                   = 2420;
	const TABL_ModeleListeElem              = 2421;
	const TABL_ModeleChoixMult              = 2422;
	const TABL_Colonne                      = 3068;
	const TABL_Calcul                       = 3075;
	const TABL_CalculSomme                  = 3087;
	const TABL_CalculMoyenne                = 3086;
	const TABL_CalculMin                    = 3080;
	const TABL_CalculMax                    = 3085;
	const TABL_CalculFormule                = 3088;
	const TABL_CalculCompteur               = 3089;
	const TABL_ColReference                 = 15067;
	const TABL_Operation                    = 4845;
	const TABL_Condition                    = 4827;
	const TABL_ColBoutonAction              = 5128;
	const TABL_ColLibelle                   = 6097;
	const TABL_LanceAction                  = 7020;
	const TABL_EnvMessage                   = 7011;
	const TABL_ActionAutomatique            = 7001;
	const TABL_Evenement                    = 4822;
	const TABL_EvenementAction              = 7626;
	const TABL_EvenementTemporel            = 7633;
	const TABL_Action                       = 2024;
	const TABL_ActionClassique              = 8685;
	const TABL_ActionParticuliere           = 8696;
	const TABL_ActionLanceExe               = 8707;
	const TABL_ActionAfficheTableauCroise   = 9326;
	const TABL_TableauCroise                = 9153;
	const TABL_AxeTableauCroise             = 9160;
	const TABL_TableauBase                  = 9268;
	const TABL_TableauAvecCond              = 9976;
	const TABL_MiseEnForme                  = 10038;
	const TABL_ControleValidite             = 10090;
	const TABL_QuestionReponse              = 10138;
	const TABL_ActionOuQuestion             = 10139;
	const TABL_ControleUnicite              = 11039;
	const TABL_TableauPrev                  = 11172;
	const TABL_LigneTableauPrev             = 11230;
	const TABL_AxeTableauPrev               = 11276;
	const TABL_ControleAction               = 13482;
	const TABL_Vue                          = 13615;
	const TABL_ActionAfficheVue             = 13649;
	const TABL_AffectParametre              = 15575;
	const TABL_BoucleAutomatisme            = 15609;
	const TABL_Rupture                      = 15641;
	const TABL_ModeleFichier                = 16017;
	const TABL_FonctionExterne              = 15938;
	const TABL_FonctionExtWS                = 15968;
	const TABL_ModeleEdition                = 10961;
	const TABL_RequetePourTous              = 6201;
	const TABL_CondRequetePourTous          = 6195;
	const TABL_Droit                        = 7655;
	const TABL_DroitColonne                 = 8527;
	const TABL_MenuPourTous                 = 11654;
	const TABL_OptionMenuPourTous           = 11579;
	const TABL_ControleEtat                 = 16520;
	const TABL_ColonneBase                  = 16713;
	const TABL_Indicateur                   = 16746;
	const TABL_TableauDeBord                = 16781;
	const TABL_ModeAffichage                = 16860;
	const TABL_ControleAnnulation           = 16825;
	const TABL_ModeAffichageListe           = 16895;
	const TABL_PersonnalisationTable        = 16949;
	const TABL_TableauAvecModeAffichage     = 17115;


	/*************************************************************
	 * IDENTIFIANT DE COLONNE
	 *************************************************************/

	const COL_AXEORGANIGRAMME_IDAxe         = 13735;
	const COL_AXEORGANIGRAMME_Orientation   = 13737;
	const COL_AXEORGANIGRAMME_Depart        = 13744;
	const COL_AXEORGANIGRAMME_Arrivee       = 13746;

	const COL_ORGANIGRAMME_IDOrganigramme       = 13782;
	const COL_ORGANIGRAMME_Libelle              = 13784;
	const COL_ORGANIGRAMME_Repertoire           = 13786;
	const COL_ORGANIGRAMME_Axes                 = 13788;
	const COL_ORGANIGRAMME_IDModule             = 13790;
	const COL_ORGANIGRAMME_OrganigrammesAnnexes = 13899;
	const COL_ORGANIGRAMME_IDPaquetages         = 13963;

	const COL_PAQUETAGE_IDPaquetage    = 13922;
	const COL_PAQUETAGE_Domaine        = 13926;
	const COL_PAQUETAGE_Libelle        = 13924;
	const COL_PAQUETAGE_Formulaires    = 13928;


	const COL_UTILISATEUR_IDUtilisateur     = 1170;
	const COL_UTILISATEUR_Pseudo            = 1171;
	const COL_UTILISATEUR_Nom               = 1172;
	const COL_UTILISATEUR_Prenom            = 1173;
	const COL_UTILISATEUR_EMail             = 1174;
	const COL_UTILISATEUR_Identifiant1      = 9424;
	const COL_UTILISATEUR_Identifiant2      = 9426;
	const COL_UTILISATEUR_Invalide          = 10545;
	const COL_UTILISATEUR_Photo             = 7623;
	const COL_UTILISATEUR_EstSuperviseur    = 7624;
	const COL_UTILISATEUR_TypeLicence       = 15913; // eTYPELICENCE
	const COL_UTILISATEUR_Parametre         = 7622; //???

	const COL_GRUTILISATEUR_Nom               = 1177;
	const COL_GRUTILISATEUR_ListeUtilisateurs = 1972;

	const COL_MASOCIETE_Logo                = 10535;
	const COL_MASOCIETE_ImageEnteteEtat     = 10536;
	const COL_MASOCIETE_PiedDePageEtat      = 10537;
	const COL_MASOCIETE_PoliceTitreEtat     = 10532;
	const COL_MASOCIETE_PoliceNormaleEtat   = 10533;
	const COL_MASOCIETE_PoliceEnteteCol     = 10534;
	const COL_MASOCIETE_PoliceLibSeparateur = 10538;
	const COL_MASOCIETE_PoliceEtiAdresse    = 15189;
	const COL_MASOCIETE_PoliceXxxxx         = 15190;
	const COL_MASOCIETE_Nom                 = 10747;
	const COL_MASOCIETE_Telephone           = 10748;
	const COL_MASOCIETE_Adresse             = 10750;
	const COL_MASOCIETE_CodePostal          = 10751;
	const COL_MASOCIETE_Ville               = 10752;
	const COL_MASOCIETE_NumeroTVA           = 10757;
	const COL_MASOCIETE_FormeJuridique      = 10760;
	const COL_MASOCIETE_CapitalSocial       = 10761;
	const COL_MASOCIETE_PAYPAL_LOGIN        = 17182;
	const COL_MASOCIETE_PAYPAL_PASSWORD     = 17183;
	const COL_MASOCIETE_PAYPAL_SIGNATURE    = 17184;
	const COL_MASOCIETE_PAYPAL_SANDBOX      = 17185;

	const COL_HORAIREOUVERTURE_JourSemaine = 9685;
	const COL_HORAIREOUVERTURE_HeureDeb    = 9573;
	const COL_HORAIREOUVERTURE_HeureFin    = 9574;

	const COL_PERJOURSEM_IDPeriodicite     = 9791;
	const COL_PERJOURSEM_ResRessource      = 9792;
	const COL_PERJOURSEM_JourSemaine       = 9793;
	const COL_PERJOURSEM_Frequence         = 9802;
	const COL_PERJOURSEM_Mois              = 9817;
	const COL_PERJOURSEM_DateDebut         = 10524;
	const COL_PERJOURSEM_DateFin           = 10525;

	const COL_JOURFERIE_Jour                = 9631;
	const COL_JOURFERIE_Mois                = 9632;
	const COL_JOURFERIE_Libelle             = 9633;
	const COL_JOURFERIE_Annee               = 11036;

	//---menu------
	const COL_MENU_IDMenu               = 11616;
	const COL_MENU_Libelle              = 11617;
	const COL_MENU_OptionsMenu          = 11618;
	const COL_MENU_Ordre                = 11619;

	const COL_OPTIONMENU_IDOptionMenu   = 11541;
	const COL_OPTIONMENU_IDMenuParent   = 11544;
	const COL_OPTIONMENU_Libelle        = 11543;
	const COL_OPTIONMENU_Raccourci      = 11546;
	const COL_OPTIONMENU_IDAction       = 11542;
	const COL_OPTIONMENU_Icone          = 11551;
	const COL_OPTIONMENU_Ordre          = 11547;
	const COL_OPTIONMENU_Commande       = 11549;

	const COL_MENUPERSO_IDMenu           = 10247;
	const COL_MENUPERSO_Libelle          = 10248;
	const COL_MENUPERSO_OptionsMenu      = 10344;
	const COL_MENUPERSO_Ordre            = 10249;
	const COL_MENUPERSO_IDMenuParent     = 16942;

	const COL_OPTIONMENUPERSO_IDOptionMenu = 10293;
	const COL_OPTIONMENUPERSO_IDMenuParent = 10296;
	const COL_OPTIONMENUPERSO_Libelle      = 10294;
	const COL_OPTIONMENUPERSO_Raccourci    = 10297;
	const COL_OPTIONMENUPERSO_IDAction     = 10299;
	const COL_OPTIONMENUPERSO_Icone        = 11696;
	const COL_OPTIONMENUPERSO_Ordre        = 10298;
	const COL_OPTIONMENUPERSO_Commande     = 11695;

	const COL_IMAGECATALOGUE_Image       = 11513;
	const COL_IMAGECATALOGUE_ImageGrande = 17295;
     //-------------//

	const COL_PUBLIPOSTAGE_IDPubli       = 10345;
	const COL_PUBLIPOSTAGE_DateCrea      = 10346;
	const COL_PUBLIPOSTAGE_TypePubli     = 10347;
	const COL_PUBLIPOSTAGE_ListeDest     = 10348;
	const COL_PUBLIPOSTAGE_Message       = 10349;

	const COL_PUBLIPOSTAGEWORD_IDPubliWord  = 10873;
	const COL_PUBLIPOSTAGEWORD_FichierDoc   = 10879;
	const COL_PUBLIPOSTAGEWORD_ListeDest    = 10876;
	const COL_PUBLIPOSTAGEWORD_ListeBalise  = 10950;

	const COL_BALISEWORD_IDBalise           = 10914;
	const COL_BALISEWORD_IDPubliWord        = 10916;
	const COL_BALISEWORD_Balise             = 10918;
	const COL_BALISEWORD_Correspondance     = 10921;
	const COL_BALISEWORD_ALister            = 10960;



	// colonnes des tableaux reponse type
	const COL_REPONSETYPE_Sujet             = 12347;
	const COL_REPONSETYPE_Message           = 12349;
	const COL_REPONSETYPE_ListePieceJointe  = 13335;

	const COL_PIECEJOINTE_Libelle       = 13304;
	const COL_PIECEJOINTE_Fichier       = 13306;
	const COL_PIECEJOINTE_Description   = 13308;

	const COL_DESTINATAIRE_Nom          = 10627;
	const COL_DESTINATAIRE_Prenom       = 10628;
	const COL_DESTINATAIRE_Societe      = 10629;
	const COL_DESTINATAIRE_AdresseEmail = 10626;

	const COL_CONTACT_Email        = 3178;
	const COL_CONTACT_Tel          = 6034;
	const COL_CONTACT_Nom          = 3180;
	const COL_CONTACT_Prenom       = 3181;
	const COL_CONTACT_Civilite     = 5306;

	const COL_CONTACTPERSO_IdUnique = 1395;
	const COL_CONTACTPERSO_Pseudo   = 3051;
	const COL_CONTACTPERSO_Prenom   = 3052;
	const COL_CONTACTPERSO_Nom      = 3053;
	const COL_CONTACTPERSO_Email    = 3054;
	const COL_CONTACTPERSO_Tel      = 1397;

	const COL_CONTACTPRO_IdUnique = 1404;
	const COL_CONTACTPRO_Prenom   = 3055;
	const COL_CONTACTPRO_Nom      = 3056;
	const COL_CONTACTPRO_Email    = 3057;
	const COL_CONTACTPRO_Tel      = 3058;

//	const COL_TACHE_IdUnique      = 3018;
	const COL_TACHE_Utilisateur   = 3021;
	const COL_TACHE_Createur      = 3022;

	const COL_RDV_IdUnique      = 7946;
	const COL_RDV_Utilisateur   = 8194;
	const COL_RDV_Createur      = 8195;


//	//Compte email (petit idauto non utilisé 17231<->17233 (données locales uniquement)
//	const COL_COMPTEEMAIL_IDUnique     = 1226;
//	const COL_COMPTEEMAIL_AdresseEMail = 1227;
//
//	const COL_COMPTEEMAIL_FAI           = 1231;
	const COL_COMPTEEMAIL_Signature             = 15259;
    const COL_COMPTEEMAIL_SignatureNouveau      = 17803;
    const COL_COMPTEEMAIL_SignatureRepondre     = 17804;
//	const COL_COMPTEEMAIL_Pseudo        = 15261;
//	const COL_COMPTEEMAIL_AdresseDefaut = 15866;
//
//     //reception
//	const COL_COMPTEEMAIL_Recep_Login           = 10995;
//	const COL_COMPTEEMAIL_Recep_Password        = 1228;
//	const COL_COMPTEEMAIL_Recep_Server          = 1229;
//	const COL_COMPTEEMAIL_Recep_Port            = 15256;
//	const COL_COMPTEEMAIL_Recep_NoDeletePOP     = 15258;
//	const COL_COMPTEEMAIL_Recep_Protocole       = 17226;
//	const COL_COMPTEEMAIL_Recep_Securite        = 17227;
//	const COL_COMPTEEMAIL_Recep_DossierPerso    = 17228;
//	const COL_COMPTEEMAIL_Recep_DossierIMAP     = 17230;
//	const COL_COMPTEEMAIL_Recep_DureeTraite     = 17296;
//
//     //envoi
//	const COL_COMPTEEMAIL_Send_Login        = 10996;
//	const COL_COMPTEEMAIL_Send_Password     = 10994;
//	const COL_COMPTEEMAIL_Send_Server       = 1230;
//	const COL_COMPTEEMAIL_Send_Port         = 15257;
//	const COL_COMPTEEMAIL_Send_Securite     = 17229;

	//--------------------------
	//colonnes du vocabulaire
	//--------------------------
	//COL_PAYS_Nom = 0;

	const COL_VILLE_Nom        = 9489;	//gen
	const COL_VILLE_CodePostal = 9490;	//gen
//	const COL_VILLE_CodeINSEE  = 7617;


	//----
	const COL_CONNEXIONBDD_IDAuto           = 14077;
	const COL_CONNEXIONBDD_Serveur          = 14078;
	const COL_CONNEXIONBDD_NomBase          = 14082;
	const COL_CONNEXIONBDD_TypeConnexion    = 14079;
	const COL_CONNEXIONBDD_Utilisateur      = 14083;
	const COL_CONNEXIONBDD_MotDePasse       = 14084;
	const COL_CONNEXIONBDD_InfoSuppl        = 14085;
	const COL_CONNEXIONBDD_DecalageTemps    = 15260;

	//--------------  Nouveau modele Connectivite -------------

	const COL_EXPORT_Libelle             = 14886;
	const COL_EXPORT_IDDescDonneeExterne = 14887;
	const COL_EXPORT_IDTableau           = 14888;
	const COL_EXPORT_ListeCondition      = 15122;
	const COL_EXPORT_FormuleRepSortie    = 15851;
	const COL_EXPORT_AjouterEnFin        = 17072;

	const COL_IMPORT_Libelle                = 14389;
	const COL_IMPORT_DescDonneeExterne      = 14390;
	const COL_IMPORT_Formulaire             = 14464;
	const COL_IMPORT_ListeCondition         = 15113;
	const COL_IMPORT_DesactiverAutomatismes = 17142;

	const COL_SYNCHRO_Libelle             = 14968;
	const COL_SYNCHRO_DescDonneeExterne   = 14969;
	const COL_SYNCHRO_Formulaire          = 14970;
	const COL_SYNCHRO_DateDerniereSynchro = 15186;
	const COL_SYNCHRO_ModeSynchro         = 15248;
	const COL_SYNCHRO_Priorite            = 15036;
	const COL_SYNCHRO_ListeCondition      = 15225;

	const COL_SYNCHROUTIL_IDSynchro         = 17075;
	const COL_SYNCHROUTIL_IDUtilisateur     = 17076;
	const COL_SYNCHROUTIL_DateHeureDerniere = 17077;

	const COL_DESCDONNEE_Localisation   = 14395;
	const COL_DESCDONNEE_Libelle        = 14393;
//	const COL_DESCDONNEE_Formulaire     = 14394;  // uniquement pour l'utilisateur
	const COL_DESCDONNEE_ListeCorresp          = 14396;
	const COL_DESCDONNEE_ListeColReference     = 15703;
	const COL_DESCDONNEE_ImportExportSynchro   = 16318;

	const COL_CSV_Libelle                 = 14668;
	const COL_CSV_Formulaire              = 14669;
	const COL_CSV_CnxBDD                  = 14670;
	const COL_CSV_ListeCorresp            = 14672;
	const COL_CSV_Source                  = 14673;
	const COL_CSV_LigneEntete             = 14674;
	const COL_CSV_Separateur              = 14675;
	const COL_CSV_DelimiteurTexte         = 15224;
	const COL_CSV_ListeColReference       = 15706;
	const COL_CSV_LigneVideEnFin          = 17255;

	const COL_XLS_Libelle                 = 14720;
	const COL_XLS_Formulaire              = 14721;
	const COL_XLS_CnxBDD                  = 14722;
	const COL_XLS_ListeCorresp            = 14727;
	const COL_XLS_Source                  = 14724;
	const COL_XLS_LigneEntete             = 14726;
	const COL_XLS_Feuille                 = 14725;
	const COL_XLS_ListeColReference       = 15707;

	const COL_POCKET_Libelle                = 14763;
	const COL_POCKET_Formulaire             = 14764;
	const COL_POCKET_ListeCorresp           = 14768;
	const COL_POCKET_Dossier                = 14767;
	const COL_POCKET_CnxBDD                 = 15014;
	const COL_POCKET_TypeSynchro            = 15109;
	const COL_POCKET_Prioritaire            = 15111;
	const COL_POCKET_ListeColReference      = 15708;

	const COL_OUTLOOK_Libelle                = 14804;
	const COL_OUTLOOK_Formulaire             = 14805;
	const COL_OUTLOOK_ListeCorresp           = 14809;
	const COL_OUTLOOK_Dossier                = 14808; //15033;
	const COL_OUTLOOK_CnxBDD                 = 15013;
	const COL_OUTLOOK_TypeSynchro            = 15108;
	const COL_OUTLOOK_Prioritaire            = 15110;
	const COL_OUTLOOK_ListeColReference      = 15709;

	const COL_EXCHANGE_Libelle            = 15147;
	const COL_EXCHANGE_Formulaire         = 15148;
	const COL_EXCHANGE_CnxBDD             = 15149;
	const COL_EXCHANGE_ListeCorresp       = 15150;
	const COL_EXCHANGE_Dossier            = 15151;
//	const COL_EXCHANGE_ListeColReference  = ?????;

	const COL_XML_Libelle                 = 15271;
	const COL_XML_Formulaire              = 15272;
	const COL_XML_ListeCorresp            = 15276;
	const COL_XML_Source                  = 15310;
	const COL_XML_BaliseEnreg             = 17280;
	const COL_XML_ListeColReference       = 15711;

	const COL_XMLSERVEUR_Libelle               = 16614;
	const COL_XMLSERVEUR_Formulaire            = 16624;
	const COL_XMLSERVEUR_CnxBDD                = 16616;
	const COL_XMLSERVEUR_ListeCorresp          = 16619;
	const COL_XMLSERVEUR_Source                = 16615;
	const COL_XMLSERVEUR_LigneEntete           = 16621;
	const COL_XMLSERVEUR_Separateur            = 16622;
	const COL_XMLSERVEUR_DelimiteurTexte       = 16623;
	const COL_XMLSERVEUR_ListeColReference     = 16620;

	const COL_XMLSEPA_Libelle             = 16661;
	const COL_XMLSEPA_FichierSource       = 16662;
	const COL_XMLSEPA_FichierXsd          = 17034;
	const COL_XMLSEPA_ListeCorresp        = 16666;
	const COL_XMLSEPA_LigneEntete         = 16668;


	const COL_BDD_Libelle             = 14617;
	const COL_BDD_Formulaire          = 14618;
	const COL_BDD_ListeCorresp        = 14709;
	const COL_BDD_CnxBDD              = 14619;
	const COL_BDD_NomTable            = 14622;
	const COL_BDD_ColIdent            = 14621;
	const COL_BDD_ModeCnx             = 14623;
	const COL_BDD_ListeColReference   = 15705;

	const COL_TEXTEDELIM_Libelle           = 14574;
	const COL_TEXTEDELIM_Formulaire        = 14575;
	const COL_TEXTEDELIM_CnxBDD            = 14576;
	const COL_TEXTEDELIM_ListeCorresp      = 14578;
	const COL_TEXTEDELIM_Source            = 14579;
	const COL_TEXTEDELIM_LigneEntete       = 14580;
	const COL_TEXTEDELIM_LigneParEnreg     = 14581;
	const COL_TEXTEDELIM_ListeColReference = 15704;

	const COL_ICSVCF_Libelle            = 14845;
	const COL_ICSVCF_Formulaire         = 14846;
	const COL_ICSVCF_CnxBDD             = 14847;
	const COL_ICSVCF_ListeCorresp       = 14849;
	const COL_ICSVCF_Source             = 14850;
	const COL_ICSVCF_ListeColReference  = 15710;

	const COL_CORRESPCOL_ColSimax               = 14399;
	const COL_CORRESPCOL_ColExterne             = 14400;
	const COL_CORRESPCOL_CreationAutorisee      = 15262;
	const COL_CORRESPCOL_ExportValeurAffichee   = 15340;
	const COL_CORRESPCOL_FormuleTransformation  = 15812;

	const COL_CORRESPCLASSIQUE_ColSimax                 = 14503;
	const COL_CORRESPCLASSIQUE_ColExterne               = 14504;
	const COL_CORRESPCLASSIQUE_Espace                   = 14505;
	const COL_CORRESPCLASSIQUE_CreationAutorisee        = 15263;
	const COL_CORRESPCLASSIQUE_ExportValeurAffichee     = 15341;
	const COL_CORRESPCLASSIQUE_FormuleTransformation    = 15811;
	const COL_CORRESPCLASSIQUE_Ordre                    = 17039;

	const COL_CORRESPDELIMITE_ColSimax              = 14538;
	const COL_CORRESPDELIMITE_ColExterne            = 14539;
	const COL_CORRESPDELIMITE_Position              = 14540;
	const COL_CORRESPDELIMITE_Longueur              = 14541;
	const COL_CORRESPDELIMITE_CreationAutorisee     = 15264;
	const COL_CORRESPDELIMITE_ExportValeurAffichee  = 15342;
	const COL_CORRESPDELIMITE_FormuleTransformation = 15813;
	const COL_CORRESPDELIMITE_Ordre                 = 17038;

	//--------------  Fin Nouveau modele Connectivite -------------

	// Gestion Packet SMX
	const COL_FICHIERSMX_Fichier                 = 15350;
	const COL_FICHIERSMX_ListeDependance         = 15376;
	const COL_FICHIERSMX_ListeFichierImport      = 15424;
	const COL_FICHIERSMX_LimiterAuDomaine        = 15829;
	const COL_FICHIERSMX_LimiterAuFormulaire     = 15830;

	const COL_FICHIERIMPORTSMX_FichierSMX      = 15427;
	const COL_FICHIERIMPORTSMX_Fichier         = 15428;
	const COL_FICHIERIMPORTSMX_NomFormulaire   = 15476;
	const COL_FICHIERIMPORTSMX_ListeDependance = 15466;

	const COL_PAQUETSMX_Nom                 = 15387;
	const COL_PAQUETSMX_MotDePasse          = 15388;
	const COL_PAQUETSMX_ListeFichierSMX     = 15389;
	const COL_PAQUETSMX_Infos               = 15390;
	const COL_PAQUETSMX_InfosHTML           = 15465;


	const COL_REQUETE_IDTableau                    = 16168;

	const COL_REQUETEPERSONNELLE_ListeCond         = 16246;

	const COL_CONDREQUETEPERSONNELLE_IDColonne     = 16212;
	const COL_CONDREQUETEPERSONNELLE_Formule       = 16214;
	const COL_CONDREQUETEPERSONNELLE_TypeCondition = 16213;
	const COL_CONDREQUETEPERSONNELLE_TypeOperateur = 16211;
	const COL_CONDREQUETEPERSONNELLE_CondEnsemble  = 16210;


	//--------------------------
	// colonnes du langage
	//--------------------------
	//ACTION
	const COL_ACTION_IDAction   = 2057;
	const COL_ACTION_IDModule   = 2059;
	const COL_ACTION_Libelle    = 2060;
	const COL_ACTION_TypeAction = 2062;
	const COL_ACTION_IDTableau  = 2063;
	const COL_ACTION_AConfirmer = 2065;
	const COL_ACTION_IDTableau2 = 5310;

	const COL_ACTIONCLASSIQUE_IDAction       = 8687;
	const COL_ACTIONCLASSIQUE_IDModule       = 8688;
	const COL_ACTIONCLASSIQUE_Libelle        = 8689;
	const COL_ACTIONCLASSIQUE_TypeAction     = 8690;
	const COL_ACTIONCLASSIQUE_IDTableau      = 8691;
	const COL_ACTIONCLASSIQUE_AConfirmer     = 8692;
	const COL_ACTIONCLASSIQUE_IDTableau2     = 8695;
	const COL_ACTIONCLASSIQUE_ListePhrase    = 8694;
	const COL_ACTIONCLASSIQUE_ListeParametre = 8693;

	const COL_ACTIONPARTICULIERE_IDAction       = 8698;
	const COL_ACTIONPARTICULIERE_IDModule       = 8699;
	const COL_ACTIONPARTICULIERE_Libelle        = 8700;
	const COL_ACTIONPARTICULIERE_AConfirmer     = 8703;

	const COL_ACTIONLANCEEXE_IDAction       = 8709;
	const COL_ACTIONLANCEEXE_IDModule       = 8710;
	const COL_ACTIONLANCEEXE_Libelle        = 8711;
	const COL_ACTIONLANCEEXE_AConfirmer     = 8714;
	const COL_ACTIONLANCEEXE_Executable     = 8718;
	const COL_ACTIONLANCEEXE_LigneCmd       = 8719;
	const COL_ACTIONLANCEEXE_Bloquant       = 15863;
	const COL_ACTIONLANCEEXE_DummyID1       = 15911;

 	//CALCUL
	const COL_CALCUL_IDCalcul      = 3076;
	const COL_CALCUL_IDColonne     = 9485;
	const COL_CALCUL_Libelle       = 3078;
	const COL_CALCUL_IDTableau     = 3077;
	const COL_CALCUL_Ordre         = 3079;
	const COL_CALCUL_Identifie     = 3082;
	const COL_CALCUL_Detail        = 3083;
	const COL_CALCUL_NonPerenise   = 6080;
	const COL_CALCUL_Aide          = 5097;
	const COL_CALCUL_Formule       = 9486;
	const COL_CALCUL_IDModele      = 15044;
	const COL_CALCUL_ListeModeAff  = 17143;

	const COL_CALCULCOMPTEUR_IDCalcul       = 3125;
	const COL_CALCULCOMPTEUR_IDTableau      = 3126;
	const COL_CALCULCOMPTEUR_Libelle        = 3127;
	const COL_CALCULCOMPTEUR_Ordre          = 3128;
	const COL_CALCULCOMPTEUR_IDColonne      = 3135;
	const COL_CALCULCOMPTEUR_Valeur         = 3136;
	const COL_CALCULCOMPTEUR_Identifie      = 3129;
	const COL_CALCULCOMPTEUR_Detail         = 3130;
	const COL_CALCULCOMPTEUR_NonPerenise    = 6079;
	const COL_CALCULCOMPTEUR_EstInvisible   = 6087;
	const COL_CALCULCOMPTEUR_Imprime        = 6088;
	const COL_CALCULCOMPTEUR_Aide           = 5103;
	const COL_CALCULCOMPTEUR_NomRub         = 9423;
	const COL_CALCULCOMPTEUR_DummyIDAuto1   = 13476;
	const COL_CALCULCOMPTEUR_DummyBool1     = 13477;
	const COL_CALCULCOMPTEUR_IDModele       = 15049;

	const COL_CALCULFORMULE_IDCalcul     = 3119;
	const COL_CALCULFORMULE_IDTableau    = 3120;
	const COL_CALCULFORMULE_Libelle      = 3121;
	const COL_CALCULFORMULE_Ordre        = 3122;
	const COL_CALCULFORMULE_Formule      = 3138;
	const COL_CALCULFORMULE_Identifie    = 3123;
	const COL_CALCULFORMULE_Detail       = 3124;
	const COL_CALCULFORMULE_NonPerenise  = 6078;
	const COL_CALCULFORMULE_EstInvisible = 6089;
	const COL_CALCULFORMULE_Imprime      = 6090;
	const COL_CALCULFORMULE_Aide         = 5102;
	const COL_CALCULFORMULE_NomRub       = 9422;
	const COL_CALCULFORMULE_DummyBool1   = 13475;
	const COL_CALCULFORMULE_IDModele     = 15048;

	const COL_CALCULMAX_IDCalcul     = 3101;
	const COL_CALCULMAX_IDTableau    = 3102;
	const COL_CALCULMAX_Libelle      = 3103;
	const COL_CALCULMAX_Ordre        = 3104;
	const COL_CALCULMAX_IDColonne    = 3133;
	const COL_CALCULMAX_Identifie    = 3105;
	const COL_CALCULMAX_Detail       = 3106;
	const COL_CALCULMAX_NonPerenise  = 6075;
	const COL_CALCULMAX_EstInvisible = 6091;
	const COL_CALCULMAX_Imprime      = 6092;
	const COL_CALCULMAX_Aide         = 5099;
	const COL_CALCULMAX_NomRub       = 9419;
	const COL_CALCULMAX_DummyIDAuto1 = 13473;
	const COL_CALCULMAX_DummyBool1   = 13474;
	const COL_CALCULMAX_IDModele     = 15047;

	const COL_CALCULMIN_IDCalcul     = 3095;
	const COL_CALCULMIN_IDTableau    = 3096;
	const COL_CALCULMIN_Libelle      = 3097;
	const COL_CALCULMIN_Ordre        = 3098;
	const COL_CALCULMIN_IDColonne    = 3132;
	const COL_CALCULMIN_Identifie    = 3099;
	const COL_CALCULMIN_Detail       = 3100;
	const COL_CALCULMIN_NonPerenise  = 6074;
	const COL_CALCULMIN_EstInvisible = 6093;
	const COL_CALCULMIN_Imprime      = 6094;
	const COL_CALCULMIN_Aide         = 5098;
	const COL_CALCULMIN_NomRub       = 9418;
	const COL_CALCULMIN_DummyIDAuto1 = 13471;
	const COL_CALCULMIN_DummyBool1   = 13472;
	const COL_CALCULMIN_IDModele     = 15057;

	const COL_CALCULMOYENNE_IDCalcul     = 3107;
	const COL_CALCULMOYENNE_IDTableau    = 3108;
	const COL_CALCULMOYENNE_Libelle      = 3109;
	const COL_CALCULMOYENNE_Ordre        = 3110;
	const COL_CALCULMOYENNE_IDColonne    = 3134;
	const COL_CALCULMOYENNE_Identifie    = 3111;
	const COL_CALCULMOYENNE_Detail       = 3112;
	const COL_CALCULMOYENNE_NonPerenise  = 6076;
	const COL_CALCULMOYENNE_EstInvisible = 6095;
	const COL_CALCULMOYENNE_Imprime      = 6096;
	const COL_CALCULMOYENNE_Aide         = 5100;
	const COL_CALCULMOYENNE_NomRub       = 9420;
	const COL_CALCULMOYENNE_DummyIDAuto1 = 13469;
	const COL_CALCULMOYENNE_DummyBool1   = 13470;
	const COL_CALCULMOYENNE_IDModele     = 15046;

	const COL_CALCULSOMME_IDCalcul     = 3113;
	const COL_CALCULSOMME_IDTableau    = 3114;
	const COL_CALCULSOMME_Libelle      = 3115;
	const COL_CALCULSOMME_Ordre        = 3116;
	const COL_CALCULSOMME_IDColonne    = 3139;
	const COL_CALCULSOMME_Identifie    = 3117;
	const COL_CALCULSOMME_Detail       = 3118;
	const COL_CALCULSOMME_NonPerenise  = 6077;
	const COL_CALCULSOMME_EstInvisible = 6085;
	const COL_CALCULSOMME_Imprime      = 6086;
	const COL_CALCULSOMME_Aide         = 5101;
	const COL_CALCULSOMME_NomRub       = 9421;
	const COL_CALCULSOMME_DummyIDAuto1 = 13467;
	const COL_CALCULSOMME_DummyBool1   = 13468;
	const COL_CALCULSOMME_IDModele     = 15045;


	const COL_COLREFERENCE_IDColReference  = 15105;
	const COL_COLREFERENCE_IDTableau       = 15103;
	const COL_COLREFERENCE_Libelle         = 15104;
	const COL_COLREFERENCE_Ordre           = 15101;
	const COL_COLREFERENCE_IDColonne       = 15100;
	const COL_COLREFERENCE_Identifie       = 15099;
	const COL_COLREFERENCE_Detail          = 15098;
	const COL_COLREFERENCE_NonPerenise     = 15097;
	const COL_COLREFERENCE_EstInvisible    = 15096;
	const COL_COLREFERENCE_Imprime         = 15095;
	const COL_COLREFERENCE_Aide            = 15106;
	const COL_COLREFERENCE_NomRub          = 15102;
	const COL_COLREFERENCE_DummyBool1      = 15109;
	const COL_COLREFERENCE_IDModele        = 15107;
	const COL_COLREFERENCE_IDModeAffichage = 15108;

	const COL_CHOIX_IDChoix     = 2363;
	const COL_CHOIX_Libelle     = 2364;
	const COL_CHOIX_Ordre       = 2365;
	const COL_CHOIX_IDModele    = 3176;
	const COL_CHOIX_IDImage     = 15041;

	const COL_COLINFO_IDColInfo        = 2090;
	const COL_COLINFO_IDTableau        = 2094;
	const COL_COLINFO_IDModele         = 2097;
	const COL_COLINFO_Libelle          = 2091;
	const COL_COLINFO_Identifie        = 2092;
	const COL_COLINFO_EstUnique        = 2093;
	const COL_COLINFO_Obligatoire      = 2096;
	const COL_COLINFO_Detail           = 2098;
	const COL_COLINFO_Ordre            = 2095;
	const COL_COLINFO_Aide             = 5095;
	const COL_COLINFO_EstInvisible     = 5137;
	const COL_COLINFO_Imprime          = 6084;
	const COL_COLINFO_ValeurDefaut     = 5136;
	const COL_COLINFO_NomRub           = 9417;
	const COL_COLINFO_DummyBool1       = 13457;
	const COL_COLINFO_DummyID1         = 15038;
	const COL_COLINFO_UniquementFormu  = 16710;
	const COL_COLINFO_FormuleINIT      = 16711;

	const COL_COLONNE_IDColonne = 3069; //n'existe pas physiquement
	const COL_COLONNE_Libelle   = 3071; //n'existe pas physiquement
	const COL_COLONNE_Ordre     = 3072; //n'existe pas physiquement
	const COL_COLONNE_IDTableau = 3070; //n'existe pas physiquement
	const COL_COLONNE_Identifie = 3073; //n'existe pas physiquement
	const COL_COLONNE_Detail    = 3074; //n'existe pas physiquement
	const COL_COLONNE_Aide      = 5096; //n'existe pas physiquement
	const COL_COLONNE_IDModele  = 6083; //n'existe pas physiquement
//	const COL_COLONNE_Imprime   =;
	const COL_COLONNE_EstInvisible = 15697;
	const COL_COLONNE_FormuleINIT  = 15700;
	const COL_COLONNE_Formule      = 15701;

	const COL_COLBOUTONACTION_IDColonne      = 5129;
	const COL_COLBOUTONACTION_IDTableau      = 5130;
	const COL_COLBOUTONACTION_Libelle        = 5134;
	const COL_COLBOUTONACTION_Ordre          = 5131;
	const COL_COLBOUTONACTION_IDAction       = 5132;
	const COL_COLBOUTONACTION_Aide           = 5133;
	const COL_COLBOUTONACTION_Commande       = 13370;
	const COL_COLBOUTONACTION_Detail         = 15490;
	const COL_COLBOUTONACTION_ModeValidation = 16583;
	const COL_COLBOUTONACTION_IDImage        = 16709;

	const COL_COLLIBELLE_IDColonne     = 6098;
	const COL_COLLIBELLE_IDTableau     = 6102;
	const COL_COLLIBELLE_Libelle       = 6103;
	const COL_COLLIBELLE_Ordre         = 6104;
	const COL_COLLIBELLE_EstInvisible  = 6108;
	const COL_COLLIBELLE_Imprime       = 6109;
	const COL_COLLIBELLE_PositionH     = 6114;
	const COL_COLLIBELLE_Aide          = 6100;
	const COL_COLLIBELLE_DummyIDAuto1  = 13478;
	const COL_COLLIBELLE_IDNiveau      = 13479;
	const COL_COLLIBELLE_IDImage       = 15052;
	const COL_COLLIBELLE_Detail        = 15491;
	const COL_COLLIBELLE_IDSensMultiC  = 16309;
	const COL_COLLIBELLE_IDModeMultiC  = 16310;
	const COL_COLLIBELLE_CouleurFond   = 16312;

	const COL_CREAAUTO_IDCreation  = 2033;
	const COL_CREAAUTO_IDTableau   = 2036;
	const COL_CREAAUTO_Libelle     = 2034;

	const COL_MODELE_IDModele      = 2076; //n'existe pas physiquement
	const COL_MODELE_Libelle       = 2080; //n'existe pas physiquement
	const COL_MODELE_Identifie     = 3148;
	const COL_MODELE_Detail        = 3150;
	const COL_MODELE_Obligatoire   = 3151;
	const COL_MODELE_Unique        = 3172;

	const COL_MODELECHOIXMULTIPLE_IDModele      = 2429;
	const COL_MODELECHOIXMULTIPLE_Libelle       = 3143;
	const COL_MODELECHOIXMULTIPLE_IDModule      = 9744;
	const COL_MODELECHOIXMULTIPLE_Identifie     = 3164;
	const COL_MODELECHOIXMULTIPLE_Detail        = 3165;
	const COL_MODELECHOIXMULTIPLE_Obligatoire   = 3166;
	const COL_MODELECHOIXMULTIPLE_Unique        = 4132;
	const COL_MODELECHOIXMULTIPLE_Aide          = 13461;
	const COL_MODELECHOIXMULTIPLE_DummyBool1    = 15042;
	const COL_MODELECHOIXMULTIPLE_DummyBool2    = 15043;

	const COL_MODELECLASSIQUE_IDModele        = 2423;
	const COL_MODELECLASSIQUE_TypeModele      = 2424;
	const COL_MODELECLASSIQUE_Libelle         = 3141;
	const COL_MODELECLASSIQUE_Identifie       = 3169;
	const COL_MODELECLASSIQUE_Detail          = 3170;
	const COL_MODELECLASSIQUE_Obligatoire     = 3171;
	const COL_MODELECLASSIQUE_Unique          = 4133;
	const COL_MODELECLASSIQUE_Aide            = 13466;
	const COL_MODELECLASSIQUE_IDModeAffichage = 15058;
	const COL_MODELECLASSIQUE_IDTypeTri       = 15820;

	const COL_MODELEELEM_IDModele      = 2425;
	const COL_MODELEELEM_IDTableau     = 2426;
	const COL_MODELEELEM_Libelle       = 3147;
	const COL_MODELEELEM_Identifie     = 3154;
	const COL_MODELEELEM_Detail        = 3155;
	const COL_MODELEELEM_Obligatoire   = 3156;
	const COL_MODELEELEM_Unique        = 4134;
	const COL_MODELEELEM_Aide          = 13465;
	const COL_MODELEELEM_IDTypeTri     = 15821;

	const COL_MODELELISTEELEM_IDModele      = 2427;
	const COL_MODELELISTEELEM_IDTableau     = 2428;
	const COL_MODELELISTEELEM_Libelle       = 3145;
	const COL_MODELELISTEELEM_Identifie     = 3159;
	const COL_MODELELISTEELEM_Detail        = 3160;
	const COL_MODELELISTEELEM_Obligatoire   = 3161;
	const COL_MODELELISTEELEM_Unique        = 4135;
	const COL_MODELELISTEELEM_Aide          = 13462;
	const COL_MODELELISTEELEM_ElementUnique = 13463;
	const COL_MODELELISTEELEM_DummyEntier1  = 13464;
	const COL_MODELELISTEELEM_IDTypeTri     = 15822;

	const COL_MODELEFICHIER_IDModele        = 16018;
	const COL_MODELEFICHIER_Libelle         = 16019;
	const COL_MODELEFICHIER_TypeStockage    = 16026;
	const COL_MODELEFICHIER_URL             = 16028;
	const COL_MODELEFICHIER_Utilisateur     = 16030;
	const COL_MODELEFICHIER_MotDePasse      = 16031;
	const COL_MODELEFICHIER_Destination     = 16032;
	const COL_MODELEFICHIER_Option          = 16033;
	const COL_MODELEFICHIER_Identifie       = 16021;
	const COL_MODELEFICHIER_Detail          = 16023;
	const COL_MODELEFICHIER_Obligatoire     = 16022;
	const COL_MODELEFICHIER_Unique          = 16024;
	const COL_MODELEFICHIER_Aide            = 16207;
	const COL_MODELEFICHIER_IDModeAffichage = 16025;

	const COL_MODULE_IDModule       = 2051;
	const COL_MODULE_NomModule      = 2052;
	const COL_MODULE_NomPhysique    = 2053;
	const COL_MODULE_TypeModule     = 2055;
	const COL_MODULE_Version        = 2056;
	const COL_MODULE_DummyEntier1   = 13458;
	const COL_MODULE_Bloque         = 17301;
	const COL_MODULE_IDIcone        = 17308;

	const COL_MOT_IDMot            = 2037;
	const COL_MOT_Mot              = 2038;
	const COL_MOT_DummyEntier1     = 13460;

	const COL_OPTION_IDOption       = 2082;
	const COL_OPTION_IDModele       = 2088;
	const COL_OPTION_Libelle        = 2083;
	const COL_OPTION_Ordre          = 2084;
	const COL_OPTION_NonAffiche     = 2085;
	const COL_OPTION_Obligatoire    = 2086;
	const COL_OPTION_Aide           = 2087;
	const COL_OPTION_IDModule       = 2089;
	const COL_OPTION_ValeurDefaut   = 9686;

	const COL_PARAMETRE_IDParametre     = 2066;
	const COL_PARAMETRE_IDAction        = 2068;
	const COL_PARAMETRE_IDModele        = 2073;
	const COL_PARAMETRE_Libelle         = 2071;
	const COL_PARAMETRE_Ordre           = 2070;
	const COL_PARAMETRE_Obligatoire     = 2067;
	const COL_PARAMETRE_Aide            = 2075;
	const COL_PARAMETRE_ValeurDefaut    = 8631;
	const COL_PARAMETRE_EstInvisible    = 13372;
	const COL_PARAMETRE_DummyID1        = 15039;
	const COL_PARAMETRE_DummyBool1      = 15040;

	const COL_PHRASE_IDPhrase       = 2046;
	const COL_PHRASE_IDAction       = 2048;
	const COL_PHRASE_Mot2Phrase     = 2050;

	const COL_TABLEAU_IDTableau                 = 2099;
	const COL_TABLEAU_Libelle                   = 2100;
	const COL_TABLEAU_Aide                      = 2104;
	const COL_TABLEAU_IDModule                  = 2105;
	const COL_TABLEAU_Lieu_Stockage             = 2414; // peut etre 2413
	const COL_TABLEAU_Pere                      = 3177;
	const COL_TABLEAU_Colonne                   = 2107; //n'existe pas physiquement
	const COL_TABLEAU_Couleur                   = 9676;
	const COL_TABLEAU_NomFichier                = 9416;
	const COL_TABLEAU_IDPersonnalisationTable   = 16981;
	const COL_TABLEAU_IDDescExterne             = 15037;
	const COL_TABLEAU_Commentaire               = 15492;
	const COL_TABLEAU_DHCreation                = 15493;
	const COL_TABLEAU_IDCreationPar             = 15494;
	const COL_TABLEAU_DHModification            = 15495;
	const COL_TABLEAU_IDModifPar                = 15496;
	const COL_TABLEAU_IDIcone                   = 15569;
	const COL_TABLEAU_FormuleSynchro            = 16585;

	const COL_SYNONYME_IDSynonyme   = 2042;
	const COL_SYNONYME_Synonyme     = 2043;
	const COL_SYNONYME_IDMot        = 2045;

	const COL_CREATION_AUTOMATIQUE_IDCreationAuto   = 2033;
	const COL_CREATION_AUTOMATIQUE_IDTableau        = 2036;
	const COL_CREATION_AUTOMATIQUE_Libelle          = 2034;

	const COL_CONDITION_IDCondition     = 4828;
	const COL_CONDITION_IDTablAvecCond  = 4830;
	const COL_CONDITION_IDColonne       = 4831;
	const COL_CONDITION_TypeCondition   = 4843;
	const COL_CONDITION_Formule         = 4844;
	const COL_CONDITION_Ordre           = 15050;
	const COL_CONDITION_TypeOperateur   = 15051;

	const COL_OPERATION_IDOperation     = 4846;
	const COL_OPERATION_IDEvenement     = 4847;
	const COL_OPERATION_IDColRes        = 4849;
	const COL_OPERATION_TypeActionAuto  = 4854;
	const COL_OPERATION_Formule         = 4855;
	const COL_OPERATION_Ordre           = 4856;
	const COL_OPERATION_ListeCond       = 10526;

	const COL_ACTIONAUTOMATIQUE_IDActionAuto    = 7006;
	const COL_ACTIONAUTOMATIQUE_IDEvenement     = 7008;
	const COL_ACTIONAUTOMATIQUE_Ordre           = 7003;
	const COL_ACTIONAUTOMATIQUE_Libelle         = 7009;

	const COL_ENVMESSAGE_IDActionAuto   = 7016;
	const COL_ENVMESSAGE_Ordre          = 7013;
	const COL_ENVMESSAGE_IDEvenement    = 7014;
	const COL_ENVMESSAGE_Destinataires  = 7018;
	const COL_ENVMESSAGE_Sujet          = 7017;
	const COL_ENVMESSAGE_TexteMessage   = 7019;
	const COL_ENVMESSAGE_Urgence        = 8035;
	const COL_ENVMESSAGE_FichierPJ      = 15854;
	const COL_ENVMESSAGE_ModeleImpPJ    = 15855;
	const COL_ENVMESSAGE_Expediteur     = 16706;

	const COL_LANCEACTION_IDActionAuto  = 7032;
	const COL_LANCEACTION_Ordre         = 7029;
	const COL_LANCEACTION_IDEvenement   = 7030;
	const COL_LANCEACTION_IDAction      = 7033;
	const COL_LANCEACTION_Libelle       = 7031;
	const COL_LANCEACTION_Commande      = 13456;
	const COL_LANCEACTION_AConfirmer    = 15784;

	const COL_EVENEMENT_IDEvenement     = 4823;
	const COL_EVENEMENT_Libelle         = 7654;
	const COL_EVENEMENT_AConfirmer      = 4826;
	const COL_EVENEMENT_Conditions      = 4858;
	const COL_EVENEMENT_ActionsAuto     = 4860;
	const COL_EVENEMENT_ListeColBoucle  = 15055;
	const COL_EVENEMENT_NePasExecuter   = 15497;
	const COL_EVENEMENT_MsgConfirmation = 15498;
	const COL_EVENEMENT_IDTypeMsgConf   = 15499;
	const COL_EVENEMENT_MsgCptRendu     = 15500;
	const COL_EVENEMENT_DHCreation      = 15501;
	const COL_EVENEMENT_DHModification  = 15502;
	const COL_EVENEMENT_IDCreationPar   = 15503;
	const COL_EVENEMENT_IDModifPar      = 15504;
	const COL_EVENEMENT_Commentaire     = 15505;
	const COL_EVENEMENT_MsgAttente      = 15554;
	const COL_EVENEMENT_IDDomaine       = 15698;
	const COL_EVENEMENT_ListeBoucle     = 15880;

	const COL_EVENEMENTACTION_IDEvenement       = 7631;
	const COL_EVENEMENTACTION_IDAction          = 7630;
	const COL_EVENEMENTACTION_Libelle           = 7653;
	const COL_EVENEMENTACTION_AConfirmer        = 7629;
	const COL_EVENEMENTACTION_Conditions        = 7628;
	const COL_EVENEMENTACTION_ActionsAuto       = 7632;
	const COL_EVENEMENTACTION_ListeColBoucle    = 12294;
	const COL_EVENEMENTACTION_NePasExecuter     = 15506;
	const COL_EVENEMENTACTION_MsgConfirmation   = 15507;
	const COL_EVENEMENTACTION_IDTypeMsgConf     = 15508;
	const COL_EVENEMENTACTION_MsgCptRendu       = 15509;
	const COL_EVENEMENTACTION_DHCreation        = 15510;
	const COL_EVENEMENTACTION_DHModification    = 15511;
	const COL_EVENEMENTACTION_IDCreationPar     = 15512;
	const COL_EVENEMENTACTION_IDModifPar        = 15513;
	const COL_EVENEMENTACTION_Commentaire       = 15514;
	const COL_EVENEMENTACTION_MsgAttente        = 15555;
	const COL_EVENEMENTACTION_IDDomaine         = 15696;
	const COL_EVENEMENTACTION_ListeBoucle       = 15808;
	const COL_EVENEMENTACTION_IDTypeExecution   = 15857;
	const COL_EVENEMENTACTION_Priorite          = 17114;

	const COL_EVENEMENTTEMPOREL_IDEvenement         = 7638;
	const COL_EVENEMENTTEMPOREL_Libelle             = 7651;
	const COL_EVENEMENTTEMPOREL_Temporel            = 7650; //TMP_Xxxxxx
	const COL_EVENEMENTTEMPOREL_Formule             = 7652;
	const COL_EVENEMENTTEMPOREL_AConfirmer          = 7636;
	const COL_EVENEMENTTEMPOREL_Conditions          = 7635;
	const COL_EVENEMENTTEMPOREL_ActionsAuto         = 7639;
	const COL_EVENEMENTTEMPOREL_EstParSIMAXService  = 15053;
	const COL_EVENEMENTTEMPOREL_ListeColBoucle      = 15056;
	const COL_EVENEMENTTEMPOREL_NePasExecuter       = 15515;
	const COL_EVENEMENTTEMPOREL_MsgConfirmation     = 15516;
	const COL_EVENEMENTTEMPOREL_IDTypeMsgConf       = 15517;
	const COL_EVENEMENTTEMPOREL_MsgCptRendu         = 15518;
	const COL_EVENEMENTTEMPOREL_DHCreation          = 15519;
	const COL_EVENEMENTTEMPOREL_DHModification      = 15520;
	const COL_EVENEMENTTEMPOREL_IDCreationPar       = 15521;
	const COL_EVENEMENTTEMPOREL_IDModifPar          = 15522;
	const COL_EVENEMENTTEMPOREL_Commentaire         = 15523;
	const COL_EVENEMENTTEMPOREL_MsgAttente          = 15556;
	const COL_EVENEMENTTEMPOREL_IDDomaine           = 15699;
	const COL_EVENEMENTTEMPOREL_ListeBoucle         = 15809;

	const COL_TABLEAUCROISE_IDTableau               = 9155;
	const COL_TABLEAUCROISE_Libelle                 = 9156;
	const COL_TABLEAUCROISE_IDModule                = 9157;
	const COL_TABLEAUCROISE_Calculs                 = 9159;
	const COL_TABLEAUCROISE_IDInfoV                 = 9262;
	const COL_TABLEAUCROISE_IDInfoH                 = 9263;
	const COL_TABLEAUCROISE_IDPersonnalisationTable = 16982;
	const COL_TABLEAUCROISE_Aide                    = 9320;
	const COL_TABLEAUCROISE_Commentaire             = 15524;
	const COL_TABLEAUCROISE_DHCreation              = 15525;
	const COL_TABLEAUCROISE_IDCreationPar           = 15526;
	const COL_TABLEAUCROISE_DHModification          = 15527;
	const COL_TABLEAUCROISE_IDModifPar              = 15528;
	const COL_TABLEAUCROISE_ColTri                  = 16948;

	const COL_AXETABLEAUCROISE_IDAxe          = 9161;
	const COL_AXETABLEAUCROISE_IDColonne      = 9162;
	const COL_AXETABLEAUCROISE_Libelle        = 9163;
	const COL_AXETABLEAUCROISE_IDTableauRecap = 9313;
	const COL_AXETABLEAUCROISE_TypeAffichage  = 9319;
	const COL_AXETABLEAUCROISE_Aide           = 9314;
	const COL_AXETABLEAUCROISE_Ordre          = 13480;

	const COL_TABLEAUBASE_IDTableau                 = 9269;
	const COL_TABLEAUBASE_Libelle                   = 9270;
	const COL_TABLEAUBASE_IDModule                  = 9324;
	const COL_TABLEAUBASE_Commentaire               = 15529;
	const COL_TABLEAUBASE_IDPersonnalisationTable   = 16980;
	const COL_TABLEAUBASE_DHCreation                = 15530;
	const COL_TABLEAUBASE_IDCreationPar             = 15531;
	const COL_TABLEAUBASE_DHModification            = 15532;
	const COL_TABLEAUBASE_IDModifPar                = 15533;

	const COL_ACTIONAFFICHETABLEAUCROISE_IDAction        = 9327;
	const COL_ACTIONAFFICHETABLEAUCROISE_IDModule        = 9330;
	const COL_ACTIONAFFICHETABLEAUCROISE_Libelle         = 9331;
	const COL_ACTIONAFFICHETABLEAUCROISE_IDTableauCroise = 9333;
	const COL_ACTIONAFFICHETABLEAUCROISE_Parametres      = 9335;
	const COL_ACTIONAFFICHETABLEAUCROISE_ListePhrase     = 9336;
	const COL_ACTIONAFFICHETABLEAUCROISE_AConfirmer      = 17309;
//	const COL_ACTIONAFFICHETABLEAUCROISE_EvtDeclenches   = 9741;	// pas de sens
//	const COL_ACTIONAFFICHETABLEAUCROISE_Automatismes    = 9742;	// pas de sens

	const COL_TABLAVECCOND_IDTablCond   = 9977;
	const COL_TABLAVECCOND_Libelle      = 9978;
	const COL_TABLAVECCOND_Conditions   = 9979;

	const COL_MISEENFORME_IDMiseEnForme     = 10040;
	const COL_MISEENFORME_IDTableau         = 10043;
	const COL_MISEENFORME_Conditions        = 10042;
	const COL_MISEENFORME_Couleur           = 10044;
	const COL_MISEENFORME_Gras              = 10046;
	const COL_MISEENFORME_Italique          = 10048;
	const COL_MISEENFORME_DummyTexte1       = 13481;
	const COL_MISEENFORME_Couleur2          = 15054;
	const COL_MISEENFORME_Libelle           = 16060;

	const COL_CONTROLEVALIDITE_IDControle    = 10092;
	const COL_CONTROLEVALIDITE_Libelle       = 16857;
	const COL_CONTROLEVALIDITE_IDTableau     = 10095;
	const COL_CONTROLEVALIDITE_Conditions    = 10094;
	const COL_CONTROLEVALIDITE_MsgErr        = 10096;
	const COL_CONTROLEVALIDITE_IDTypeMsgErr  = 15534;

	const COL_QUESTIONREPONSE_IDQuestionReponse = 10155;
	const COL_QUESTIONREPONSE_IDModule          = 10156;
	const COL_QUESTIONREPONSE_Libelle           = 10157;
	const COL_QUESTIONREPONSE_Phrases           = 10158;
	const COL_QUESTIONREPONSE_Reponse           = 10159;

	const COL_ACTIONOUQUESTION_IDQuestionReponse = 10141;
	const COL_ACTIONOUQUESTION_IDModule          = 10142;
	const COL_ACTIONOUQUESTION_Libelle           = 10143;

	const COL_CONTROLEUNICITE_IDControle   = 11040;
	const COL_CONTROLEUNICITE_Libelle      = 16859;
	const COL_CONTROLEUNICITE_IDTableau    = 11043;
	const COL_CONTROLEUNICITE_Colonne      = 11047;
	const COL_CONTROLEUNICITE_MsgErr       = 11045;
	const COL_CONTROLEUNICITE_IDTypeMsgErr = 15535;

	const COL_TABLEAUPREV_IDUnique          = 11173;
	const COL_TABLEAUPREV_Intitule          = 11174;
	const COL_TABLEAUPREV_IDDomaine         = 11175;
	const COL_TABLEAUPREV_IDTableauReel     = 11177;
	const COL_TABLEAUPREV_IDAxeParcours     = 11179;
	const COL_TABLEAUPREV_NbUnitesFiche     = 11195;
	const COL_TABLEAUPREV_NbUnitesListe     = 11314;
	const COL_TABLEAUPREV_LnTableauPrev     = 11267;
	const COL_TABLEAUPREV_DHCreation        = 15536;
	const COL_TABLEAUPREV_DHModification    = 15537;
	const COL_TABLEAUPREV_IDCreationPar     = 15538;
	const COL_TABLEAUPREV_IDModifPar        = 15539;
	const COL_TABLEAUPREV_Commentaire       = 15540;

	const COL_LNTABLEAUPREV_IDUnique      = 11231;
	const COL_LNTABLEAUPREV_IDTableauPrev = 11233;
	const COL_LNTABLEAUPREV_IDColonne     = 11235;
	const COL_LNTABLEAUPREV_Formule       = 11237;

	const COL_AXETABLEAUPREV_IDUnique       = 11277;
	const COL_AXETABLEAUPREV_IDTableauPrev  = 11279;
	const COL_AXETABLEAUPREV_IDColParcours  = 11281;
	const COL_AXETABLEAUPREV_PasParcours    = 11184;
	const COL_AXETABLEAUPREV_Formule        = 15541;

	const COL_CONTROLEACTION_IDControle   = 13483;
	const COL_CONTROLEACTION_IDAction     = 13485;
	const COL_CONTROLEACTION_Libelle      = 16858;
	const COL_CONTROLEACTION_Conditions   = 13487;
	const COL_CONTROLEACTION_MsgErr       = 13489;
	const COL_CONTROLEACTION_IDTypeMsgErr = 15542;

	const COL_VUE_IDVue                     = 13616;
	const COL_VUE_Intitule                  = 13617;
	const COL_VUE_IDDomaine                 = 13618;
	const COL_VUE_Donnees                   = 13620;
	const COL_VUE_IDPersonnalisationTable   = 16983;
	const COL_VUE_Aide                      = 13622;
	const COL_VUE_DHCreation                = 15543;
	const COL_VUE_DHModification            = 15544;
	const COL_VUE_IDCreationPar             = 15545;
	const COL_VUE_IDModifPar                = 15546;
	const COL_VUE_Commentaire               = 15547;

	const COL_ACTIONAFFICHEVUE_IDAction      = 13650;
	const COL_ACTIONAFFICHEVUE_IDModule      = 13651;
	const COL_ACTIONAFFICHEVUE_Libelle       = 13652;
	const COL_ACTIONAFFICHEVUE_IDVue         = 13660;
	const COL_ACTIONAFFICHEVUE_Parametres    = 13656;
	const COL_ACTIONAFFICHEVUE_ListePhrase   = 13657;
	const COL_ACTIONAFFICHEVUE_AConfirmer    = 17310;
//	const COL_ACTIONAFFICHEVUE_EvtDeclenches = 13664; // pas de sens
//	const COL_ACTIONAFFICHEVUE_Automatismes  = 13665; // pas de sens

	const COL_RUPTURE_IDRupture             = 15642;
	const COL_RUPTURE_IDColonne             = 15643;
	const COL_RUPTURE_Libelle               = 15644;
	const COL_RUPTURE_DummyBool1            = 0;
	const COL_RUPTURE_DummyInt1             = 0;
	const COL_RUPTURE_DummyID1              = 0;
	const COL_RUPTURE_FormuleTransformation = 16201;
	const COL_RUPTURE_Compteur              = 16202;
	const COL_RUPTURE_Somme                 = 16203;
	const COL_RUPTURE_Moyenne               = 16204;
	const COL_RUPTURE_Maximum               = 16205;
	const COL_RUPTURE_Minimum               = 16206;
	const COL_RUPTURE_SautDePage            = 16296;

	const COL_AFFECTATIONPARAMETRE_IDAffectParam   = 15576;
	const COL_AFFECTATIONPARAMETRE_IDLanceAction   = 15577;
	const COL_AFFECTATIONPARAMETRE_IDParametre     = 15579;
	const COL_AFFECTATIONPARAMETRE_Valeur          = 15580;
	const COL_AFFECTATIONPARAMETRE_DummyBool1      = 0;
	const COL_AFFECTATIONPARAMETRE_DummyID1        = 0;

	const COL_BOUCLEAUTO_IDBoucleAuto   = 15610;
	const COL_BOUCLEAUTO_IDActionAuto   = 15611;
	const COL_BOUCLEAUTO_Formule        = 15612;
	const COL_BOUCLEAUTO_IDModele       = 15613;
	const COL_BOUCLEAUTO_DummyBool1     = 0;
	const COL_BOUCLEAUTO_DummyID1       = 0;
	const COL_BOUCLEAUTO_Ordre          = 15879;
	const COL_BOUCLEAUTO_NbTours        = 17222;


	const COL_FONCTIONEXTERNE_IDFonction    = 15939;
	const COL_FONCTIONEXTERNE_Intitule      = 15940;
	const COL_FONCTIONEXTERNE_Prototype     = 15941;
	const COL_FONCTIONEXTERNE_Description   = 15942;

	const COL_FONCTIONEXTERNEWS_IDFonction      = 15969;
	const COL_FONCTIONEXTERNEWS_Intitule        = 15976;
	const COL_FONCTIONEXTERNEWS_Prototype       = 15977;
	const COL_FONCTIONEXTERNEWS_Description     = 15978;
	const COL_FONCTIONEXTERNEWS_Hote            = 15970;
	const COL_FONCTIONEXTERNEWS_NomWS           = 15971;
	const COL_FONCTIONEXTERNEWS_Operation       = 15972;
	const COL_FONCTIONEXTERNEWS_TypeRequete     = 15973;
	const COL_FONCTIONEXTERNEWS_Attribut        = 15974;
	const COL_FONCTIONEXTERNEWS_FormuleTransf   = 15975;

	const COL_MODELEEDITION_IDModeleImp             = 10962;
	const COL_MODELEEDITION_DocumentModele          = 10964;
	const COL_MODELEEDITION_IDTableau               = 10966;
	const COL_MODELEEDITION_PourListe               = 12856;
	const COL_MODELEEDITION_ListeCond               = 12981;
	const COL_MODELEEDITION_CopieNonAssemblee       = 15337;
	const COL_MODELEEDITION_Sortie                  = 15339;
	const COL_MODELEEDITION_FormatSortie            = 16313;
	const COL_MODELEEDITION_ImpressionFichierSortie = 16984;
	const COL_MODELEEDITION_Intitule                = 17004;

	const COL_DROIT_IDDroit               = 7656;
	const COL_DROIT_IDUtilisateurOuGroupe = 7713;
	const COL_DROIT_ModuleAutorise        = 7714;
	const COL_DROIT_ActionEnMoins         = 7715;
	const COL_DROIT_ListeDroitCol         = 8578;
	const COL_DROIT_Intitule              = 16164;

	const COL_DROITCOLONNE_IDDroitColonne   = 8528;
	const COL_DROITCOLONNE_IDDroit          = 8529;
	const COL_DROITCOLONNE_IDColonne        = 8530;
	const COL_DROITCOLONNE_TypeDroitColonne = 8535;

	const COL_MENUPOURTOUS_IDMenu           = 11655;
	const COL_MENUPOURTOUS_Libelle          = 11656;
	const COL_MENUPOURTOUS_OptionsMenu      = 11657;
	const COL_MENUPOURTOUS_Ordre            = 11658;
	const COL_MENUPOURTOUS_IDMenuParent     = 16941;    //le menu pere de l'element menu pour tous.

	const COL_OPTIONMENUPOURTOUS_IDOptionMenu  = 11580;
	const COL_OPTIONMENUPOURTOUS_IDMenuParent  = 11584;
	const COL_OPTIONMENUPOURTOUS_Libelle       = 11582;
	const COL_OPTIONMENUPOURTOUS_Raccourci     = 11585;
	const COL_OPTIONMENUPOURTOUS_IDAction      = 11581;
	const COL_OPTIONMENUPOURTOUS_IDIcone       = 11586;
	const COL_OPTIONMENUPOURTOUS_Ordre         = 11587;
	const COL_OPTIONMENUPOURTOUS_Commande      = 11583;
    const COL_OPTIONMENUPOURTOUS_HomeImg       = 17547;
    const COL_OPTIONMENUPOURTOUS_HomeDesc      = 17548;
    const COL_OPTIONMENUPOURTOUS_HomeTitle     = 17550;




	const COL_CONDREQUETEPOURTOUS_IDCondition   = 6196;
	const COL_CONDREQUETEPOURTOUS_IDColonne     = 6198;
	const COL_CONDREQUETEPOURTOUS_TypeCondition = 6199;
	const COL_CONDREQUETEPOURTOUS_Formule       = 6200;
	const COL_CONDREQUETEPOURTOUS_IDRequete     = 6290;
	const COL_CONDREQUETEPOURTOUS_CondEnsemble  = 10871;
	const COL_CONDREQUETEPOURTOUS_TypeOperateur = 15133;
	const COL_CONDREQUETEPOURTOUS_Ordre         = 16199;

	const COL_REQUETEPOURTOUS_IDRequete  = 6202;
	const COL_REQUETEPOURTOUS_Libelle    = 6203;
	const COL_REQUETEPOURTOUS_IDTableau  = 6204;
	const COL_REQUETEPOURTOUS_ListeCond  = 6292;

	const COL_CONTROLEETAT_IDControle       = 16521;
	const COL_CONTROLEETAT_Libelle          = 16550;
	const COL_CONTROLEETAT_IDTableau        = 16552;
	const COL_CONTROLEETAT_Etat             = 16578;
	const COL_CONTROLEETAT_ListeCol         = 16553;
	const COL_CONTROLEETAT_Conditions       = 16551;

	const COL_COLONNEBASE_IDColonneBase  = 16714;
	const COL_COLONNEBASE_IDModele       = 16715;
	const COL_COLONNEBASE_Libelle        = 16716;
	const COL_COLONNEBASE_IDTableau      = 16717;
	const COL_COLONNEBASE_Ordre          = 16718;
	const COL_COLONNEBASE_EstInvisible   = 16720;
	const COL_COLONNEBASE_Aide           = 16779;
	const COL_COLONNEBASE_Formule        = 16719;

	const COL_INDICATEUR_IDIndicateur   = 16747;
	const COL_INDICATEUR_IDModele       = 16748;
	const COL_INDICATEUR_Libelle        = 16749;
	const COL_INDICATEUR_IDTableau      = 16750;
	const COL_INDICATEUR_Ordre          = 16751;
	const COL_INDICATEUR_EstInvisible   = 16753;
	const COL_INDICATEUR_Aide           = 16780;
	const COL_INDICATEUR_Formule        = 16752;

	const COL_TABLEAUDEBORD_IDTableauDeBord = 16782;
	const COL_TABLEAUDEBORD_Intitule        = 16783;
	const COL_TABLEAUDEBORD_IDModule        = 16784;
	const COL_TABLEAUDEBORD_Commentaire     = 16785;
	const COL_TABLEAUDEBORD_DHCreation      = 16786;
	const COL_TABLEAUDEBORD_IDCreationPar   = 16787;
	const COL_TABLEAUDEBORD_DHModification  = 16788;
	const COL_TABLEAUDEBORD_IDModifPar      = 16789;
	const COL_TABLEAUDEBORD_ListeIndicateur = 16816;

	const COL_MODEAFFICHAGE_IDModeAff     = 16861;
	const COL_MODEAFFICHAGE_Libelle       = 16862;
	const COL_MODEAFFICHAGE_IDContModeAff = 17054;
	const COL_MODEAFFICHAGE_Ordre         = 17055;

	const COL_MODEAFFICHAGELISTE_IDModeAff          = 16896;
	const COL_MODEAFFICHAGELISTE_Libelle            = 16897;
	const COL_MODEAFFICHAGELISTE_IDContModeAff      = 17051;
	const COL_MODEAFFICHAGELISTE_Ordre              = 17052;
	const COL_MODEAFFICHAGELISTE_PresentationDefaut = 17050;
	const COL_MODEAFFICHAGELISTE_AvecListe          = 17064;
	const COL_MODEAFFICHAGELISTE_AvecPlanning       = 17065;
	const COL_MODEAFFICHAGELISTE_AvecGraphe         = 17067;
	const COL_MODEAFFICHAGELISTE_AvecPlan           = 17068;
	const COL_MODEAFFICHAGELISTE_AvecGantt          = 17069;
	const COL_MODEAFFICHAGELISTE_AvecOrganigramme   = 17066;
	const COL_MODEAFFICHAGELISTE_AvecArborescence   = 17147;
	const COL_MODEAFFICHAGELISTE_AvecListeImage     = 17148;

	const COL_PERSONNALISATIONTABLE_IDPersonnalisation = 16950;
	const COL_PERSONNALISATIONTABLE_IDTableau          = 16954;
	const COL_PERSONNALISATIONTABLE_IDColTri           = 16951;
	const COL_PERSONNALISATIONTABLE_TriAsc             = 16952;
	const COL_PERSONNALISATIONTABLE_NbMaxRes           = 16953;

	const COL_CONTROLEANNULATION_IDControle       = 16826;
	const COL_CONTROLEANNULATION_Libelle          = 16827;
	const COL_CONTROLEANNULATION_IDTableau        = 16829;
	const COL_CONTROLEANNULATION_Conditions       = 16828;
	const COL_CONTROLEANNULATION_MsgErr           = 16830;
	const COL_CONTROLEANNULATION_IDTypeMsgErr     = 16831;

	const COL_TABLEAUAVECMODEAFFICHAGE_IDTabAvecModeAff = 17116;

	//ICI nouveau fichier du langage
	//!!!!!!!!!!!! On n'ajoute rien ICI !!!!!!!!!!!!!!!!!!!!!!!!!

	//--------------------------
	//******************** COLONNE DE MESSAGERIE ******************************
	//------- 16061<->16160 -------------------
	//colonne physique dans les tables
	const COL_MESSAGERIE_IDMessage              = 16061;
	const COL_MESSAGERIE_DateHeure              = 16062;
	const COL_MESSAGERIE_ASynchroniser          = 16063;
	const COL_MESSAGERIE_Texte                  = 16064;
	const COL_MESSAGERIE_IDMessagePrecedent     = 16065;
	const COL_MESSAGERIE_IDTableauLie           = 16066;
	const COL_MESSAGERIE_IDEnregLie             = 16067;
	const COL_MESSAGERIE_PJ                     = 16068;
	const COL_MESSAGERIE_Destinataires          = 16069;
	const COL_MESSAGERIE_Importance             = 16070;
	const COL_MESSAGERIE_AccuseReceptionInt     = 16071;
	const COL_MESSAGERIE_AccuseReceptionEmail   = 16072;
	const COL_MESSAGERIE_ConfirmLectInt         = 16073;
	const COL_MESSAGERIE_ConfirmLectEmail       = 16074;
	const COL_MESSAGERIE_ConfirmValidInt        = 16075;
	const COL_MESSAGERIE_AEnvoyer               = 16076;
	const COL_MESSAGERIE_Mailing                = 16077;
	const COL_MESSAGERIE_AdresseRetour          = 16078;
	const COL_MESSAGERIE_Expediteur             = 16079;
	const COL_MESSAGERIE_IDExpediteur           = 16080;
	const COL_MESSAGERIE_TypeDest               = 16081;
	const COL_MESSAGERIE_EmailSIMAX             = 16082;
	const COL_MESSAGERIE_Spam                   = 16083;
	const COL_MESSAGERIE_Email                  = 16084;
	const COL_MESSAGERIE_InterneSIMAX           = 16085;
	const COL_MESSAGERIE_Traite                 = 16086;
	const COL_MESSAGERIE_Etat                   = 16087;
	const COL_MESSAGERIE_Transfere              = 16088;
	const COL_MESSAGERIE_CompteEmail            = 16089;
	const COL_MESSAGERIE_UIDL_POP               = 16090;
	const COL_MESSAGERIE_DescriptionAction      = 16091;
	const COL_MESSAGERIE_AnnulationAction       = 16092;
	const COL_MESSAGERIE_MessageAction          = 16093;
	const COL_MESSAGERIE_IDDestinataire         = 16094;
	const COL_MESSAGERIE_IDTableauDest          = 16095;
	const COL_MESSAGERIE_IndiceDest             = 16096;
	const COL_MESSAGERIE_IDUtilisateur          = 16097;
	const COL_MESSAGERIE_IDProfil               = 16098;
	const COL_MESSAGERIE_Profil                 = 16099;
	const COL_MESSAGERIE_IDDossier              = 16100;
	const COL_MESSAGERIE_IDDossierPere          = 16101;
	const COL_MESSAGERIE_Libelle                = 16102;
	const COL_MESSAGERIE_ListeUtil              = 16103;
	const COL_MESSAGERIE_Recu                   = 16104;
	const COL_MESSAGERIE_IMAP_UIDL              = 17243;
	const COL_MESSAGERIE_IMAP_ASynchro          = 17244;
	const COL_MESSAGERIE_IDDestinataires        = 17245;
	const COL_MESSAGERIE_TypeDossier            = 17246;
	const COL_MESSAGERIE_IMAP_Dossier           = 17247;
	const COL_MESSAGERIE_IMAP_AvecSousDossier   = 17248;
	const COL_MESSAGERIE_IMAP_LastUIDCheck      = 17249;
	const COL_MESSAGERIE_IMAP_UIDVerify         = 17250;
	const COL_MESSAGERIE_DerniereModif          = 17252;
	const COL_MESSAGERIE_IMAP_LastSynchro       = 17253;

 	//nouvelle collone ici

	const COL_MESSAGERIE_IDMessageExt        = 16150; //pour la synchro
	const COL_MESSAGERIE_DateHeureModif      = 16151; //pour la synchro
	//------------------------------------------

 	//------- 15316<->15336 -------------------
 	//colonne non physique
	const COL_MESSAGERIE_Sujet                  = 15316;
	const COL_MESSAGERIE_Destinataires_To       = 15317;
	const COL_MESSAGERIE_IDDestinataires_To     = 15318;
	const COL_MESSAGERIE_Destinataires_Cc       = 15319;
	const COL_MESSAGERIE_IDDestinataires_Cc     = 15320;
	const COL_MESSAGERIE_Destinataires_Cci      = 15321;
	const COL_MESSAGERIE_IDDestinataires_Cci    = 15322;
	const COL_MESSAGERIE_MessageEnClair         = 15323;
	const COL_MESSAGERIE_MessageHTML            = 15324;
	const COL_MESSAGERIE_PJ_ID                  = 15325;
	const COL_MESSAGERIE_ElementLie             = 15326;
	const COL_MESSAGERIE_NonLu                  = 15327;
	const COL_MESSAGERIE_AccuseReception        = 15328;
	const COL_MESSAGERIE_ConfirmLect            = 15329;
	const COL_MESSAGERIE_ConfirmValid           = 15330;
	//------------------------------------------

	/*************************************************************
	 * enum JOUR SEMAINE
	 *************************************************************/

	const JS_Invalide = 0;
	const JS_Lundi    = 9678;
	const JS_Mardi    = 9679;
	const JS_Mercredi = 9680;
	const JS_Jeudi    = 9681;
	const JS_Vendredi = 9682;
	const JS_Samedi   = 9683;
	const JS_Dimanche = 9684;

    /*************************************************************
     * IDENTIFIANT DE MODELE Particulier
     *************************************************************/
    const MT_TypeDAction = 2061;

	/*************************************************************
	 * IDENTIFIANT D'ACTION
	 *************************************************************/

	const ACTION_CreerPlannificationRessource                   = 8458;
	const ACTION_CreerPlannificationSansReservationRessource    = 17159;

	const ACTION_Preference                 = 1000;
	const ACTION_Maintenance                = 4666;
	const ACTION_MaintenancePlus            = -1;
	const ACTION_MaintenancePlusLocal       = -2;
	const ACTION_MaintenancePlusFicRel      = -3;
	const ACTION_MaintenancePlusFicRelLocal = -4;
	const ACTION_CopieColonne               = 6063;
	const ACTION_CopieLigne                 = 6177;
	const ACTION_Import                     = 7558;
	const ACTION_Export                     = 11495;
	const ACTION_Synchro                    = 14370;
	const ACTION_MonPlanning                = 8508;
	const ACTION_PlanningRes                = 8499;
	const ACTION_PlanningMultiRes           = 8483;
	const ACTION_SauvegardeDonnees          = 9747;
	const ACTION_SauveFichierInit           = 9759;
	const ACTION_GenereSMX                  = 9773;
	const ACTION_ImportSMX                  = 9920;
	const ACTION_ExtraitToutSMX             = 13572;
	const ACTION_Traduction                 = 10564;
	const ACTION_RemplaceColonne            = 10630;
	const ACTION_Annulation                 = 10850;
	const ACTION_Refaire                    = 10853;
	const ACTION_GenererModeleEdition       = 11090;
	const ACTION_FusionEnreg                = 11133;
	const ACTION_MAJEnreg                   = 11315;
	const ACTION_RejoueAutomatismes         = 11710;
	const ACTION_RemplaceElement            = 11966;
	const ACTION_GenerationDonneesTest      = 12744;
	const ACTION_ParametrerAvecImport       = 13267;
	const ACTION_PasserUnAppel              = 13349;
	const ACTION_AppelEntrant               = 17267;
//	const ACTION_Stock                      = 11776;
//	const ACTION_Publipostage               = 10618;
    const ACTION_ListeChoix                 = 2549;
    const ACTION_ListeFormulaire            = 2317;
    const ACTION_ListeUtilisateur           = 1496;

	const ACTION_ExporterOrganigramme    = 13774;
	const ACTION_Messagerie_ListeMessage = 0;
	const ACTION_Messagerie_CheckMessage = 0;
	const ACTION_SuppressionComplete     = 14373;

	const ACTION_CreeTableauRecapitulatif       = 9164;
	const ACTION_CreeAxeTableauRecapitulatif    = 9221;
	const ACTION_CreeCalculCompteur             = 4312;

	const ACTION_Aide             = 15018;
	const ACTION_DemarrageSIMAX   = 15242;
	const ACTION_ArretSIMAX       = 15244;

	const ACTION_CreerPaquetSMX   = 15344;
	const ACTION_InitSynchro      = 15482;

	const ACTION_RechercheGlobale  = 15557;

	const ACTION_AuditParametrage  = 15832;
	const ACTION_ExporterVersBDD   = 15869;

	const ACTION_DupliquerFormulaire              = 15921;
	const ACTION_SupprimerCompletementFormulaire  = 15934;

	const ACTION_ExporterGrapheDependancesCalcul                = 16929;
	const ACTION_ExporterGrapheDependancesCalcul_Colonne        = 16930;
	const ACTION_ExporterGrapheDependancesCalcul_DossierSortie  = 16933;
	const ACTION_ExporterGrapheDependancesCalcul_FormatSortie   = 16934;

	const ACTION_InitBaseUtilisateur = 16943;

	const ACTION_ListeDestinataireMessagerie        = 1989;
	const ACTION_RechercherDestinataireMessagerie   = 1991;
	const ACTION_RechercherReponseType              = 12359;

	const ACTION_PayementPaypal                     = 17186;

	const ACTION_AfficherFichier_NomFichier         = 17203;    //Affichage d'un fichier en "IFRAME" modele nom de fichier avec repertoire
	const ACTION_AfficherFichier_ModeleFichier      = 17207;    //affichage d'un fichier en "IFRAME" modele fichier

	const ACTION_ConnexionExtranet                  = 17216;    //passage du mode anonyme au mode connecté extranet (demande de login au client.)

	const ACTION_Messagerie_Nouveau         = 17040;
	const ACTION_Messagerie_Liste           = 17041;
	const ACTION_Messagerie_Consulter       = 17042;
	const ACTION_Maintenance_AnnulerRefaire = 17282;

	const ACTION_ViderTableBDD = 17238;

	const ACTION_ActionClassiqueGenerique = 17272;

    const ACTION_ListeRequetePourTous = 6274;

    const ACTION_Horaires_Ouverture = 9600;


	/*************************************************************
	 * IDENTIFIANT PARAMETRE D'ACTION
	 *************************************************************/

	const PA_PlanningRes_IDRessource        = 8507;		// Optionnel
	const PA_PlanningMutliRes_IDTableau     = 8521;		// Optionnel

	const PA_CopieColonne_IDColSource = 6070;
	const PA_CopieColonne_IDColDest   = 6071;
	const PA_CopieLigne_IDTabSource   = 6188;
	const PA_CopieLigne_IDTabDest     = 6189;
	const PA_CopieLigne_IDEnreg       = 6192;

	const PA_RemplaceColonne_IDCol     = 10648;
	const PA_RemplaceColonne_OldValeur = 10649;
	const PA_RemplaceColonne_NewValeur = 10650;

	const PA_RemplaceElem_IDTableau  = 11968;
	const PA_RemplaceElem_IDOldElem  = 11970;
	const PA_RemplaceElem_IDNewElem  = 11972;

	const PA_Exporter_Repertoire        = 11505;
	const PA_Exporter_IDExport          = 15010;
	const PA_Importer_IDImport          = 15009;
//	const PA_Exporter_FormatExport      = 11501;
//	const PA_Exporter_IDTableau         = 12343;
//	const PA_Exporter_Requete           = 11499;

	const PA_Recherche_Global = 16990; //id de la checkbox permettant de preciser que la recherche est global dans filtre de liste.

	const PA_PaiementPaypal_Montant     = 17189;
	const PA_PaiementPaypal_Devise      = 17190;
	const PA_PaiementPaypal_Token       = 17191;
	const PA_PaiementPaypal_Payeur      = 17192;
	const PA_PaiementPaypal_Login       = 17194;
	const PA_PaiementPaypal_Password    = 17195;
	const PA_PaiementPaypal_Signature   = 17196;
	const PA_PaiementPaypal_SandBox     = 17197;
	const PA_PaiementPaypal_URL         = 17198;

	const PA_MajEnreg_AvecAutomatisme        = 17210;
	const PA_MajEnreg_AvecAnnulation         = 17212;
	const PA_MajEnreg_AvecCalculLie          = 17213;
	const PA_MajEnreg_UniquementCalculStocke = 17215;

	const PA_ConnexionExtranet_Extranet_Pseudo = 17217;
	const PA_ConnexionExtranet_Extranet_Mdp    = 17218;
	const PA_ConnexionExtranet_Intranet_Pseudo = 17299;
	const PA_ConnexionExtranet_Intranet_Mdp    = 17300;
	const PA_ConnexionExtranet_Formulaire      = 18020;
	const PA_ConnexionExtranet_CodeLangue      = 18021;
	const PA_ConnexionExtranet_FromLogin       = 18022;
	const PA_ConnexionExtranet_Hachage         = 18023;

	const PA_Requete_Tous_Form                  = 6293;

	const PA_ListeChoix_Modele                  = 2554;
	const PA_ListeFormulaire_SousModule         = 2532;



	//les mimes types
	const MIMETYPE_png = 'image/png';

	//les couleurs définies
	const COLOR_Magenta = 'ff00ff';

    const SMC_Horizontal = 1; //dans le paramétrage SIMAX 16308
    const SMC_Vertical = 2; //dans le paramétrage SIMAX 16307


    const ICONCENTRAL_TitreImgHelp  = 1;
    const ICONCENTRAL_ImgTitreHelp  = 2;
    const ICONCENTRAL_OverlayTop    = 3;
    const ICONCENTRAL_OverlayMiddle = 4;
    const ICONCENTRAL_OverlayBottom = 5;

    const PASSWORD_ENCODAGE_plaintext = 'plaintext';
    const PASSWORD_ENCODAGE_sha1 = 'sha-1';
    const PASSWORD_ENCODAGE_sha256 = 'sha-2-256';
    const PASSWORD_ENCODAGE_md5 = 'md5';


    //-------------------------------------------------
    const TABL_Messagerie_Message		= 16510;
    const TABL_Messagerie_Dossier		= 16511;
}
