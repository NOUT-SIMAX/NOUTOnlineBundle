<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 28/07/14
 * Time: 14:20
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService;

class OnlineError
{
	protected $m_nCode;
	protected $m_nErreur;
	protected $m_nCategorie;
	protected $m_sMessage;
	protected $m_TabParametres;

	public function __construct($nCode, $nErreur, $nCategorie, $sMessage)
	{
		$this->m_nCode      = (string) $nCode;
		$this->m_nCategorie = (int) $nCategorie;
		$this->m_nErreur    = (int) $nErreur;
		$this->m_sMessage   = (string) $sMessage;
	}

	public function AddParameter(OnlineErrorParameter $clError)
	{
		$this->m_TabParametres[] = $clError;
	}

	/**
	 * @param mixed $m_TabParametres
	 */
	public function setTabParametres($TabParametres)
	{
		$this->m_TabParametres = $TabParametres;
	}

	/**
	 * @return mixed
	 */
	public function getTabParametres()
	{
		return $this->m_TabParametres;
	}

	/**
	 * @param int $m_nCategorie
	 */
	public function setCategorie($nCategorie)
	{
		$this->m_nCategorie = $nCategorie;
	}

	/**
	 * @return int
	 */
	public function getCategorie()
	{
		return $this->m_nCategorie;
	}

	/**
	 * @param string $m_nCode
	 */
	public function setCode($nCode)
	{
		$this->m_nCode = $nCode;
	}

	/**
	 * @return string
	 */
	public function getCode()
	{
		return $this->m_nCode;
	}

	/**
	 * @param int $m_nErreur
	 */
	public function setErreur($nErreur)
	{
		$this->m_nErreur = $nErreur;
	}

	/**
	 * @return int
	 */
	public function getErreur()
	{
		return $this->m_nErreur;
	}

	/**
	 * @param string $m_sMessage
	 */
	public function setMessage($sMessage)
	{
		$this->m_sMessage = $sMessage;
	}

	/**
	 * @return string
	 */
	public function getMessage()
	{
		return $this->m_sMessage;
	}

    const ERR_NOUTONLINE_OFF                = 1;

	//constante pour les codes d'erreurs
	const ERR_AUTRE                         = 1000;
	const ERR_LANGOBJECT_GETVALCOL          = 1001;
	const ERR_LANGOBJECT_NULL               = 1002;

	const ERR_LANGACTION_NULL               = 1011;
	const ERR_LANGACTION_TABPARAM_NULL      = 1012;
	const ERR_LANGACTION_PARAM_NULL         = 1013;

	const ERR_LANGMANIP_NULL                = 1021;
	const ERR_LANGMANIP_REMPTABACTION       = 1022;

	const ERR_INTERMAX_NULL             = 1031;
	const ERR_INTERMAX_CHERCOLTAB       = 1032;
	const ERR_INTERMAX_GETCOLENREG      = 1033;
	const ERR_INTERMAX_CHERTAB          = 1034;
	const ERR_INTERMAX_REQTAB           = 1035;

	const ERR_FDS_NULL            = 1041;
	const ERR_DS_NULL             = 1045;
	const ERR_IM_NULL             = 1046;
	const ERR_INIT_GP             = 1047;

	const ERR_TRAITEDEM_NULL              = 1051;
	const ERR_GESTIONDEM_NULL             = 1052;
	const ERR_GESTIONPARAMACTION_NULL     = 1053;

	const ERR_LANGMODELE_NULL           = 1061;
	const ERR_LANGVUE_NULL              = 1062;
	const ERR_LANGVUE_IDTABREL          = 1063;

	const ERR_INFOMODELE          = 1071;
	const ERR_IUTIL_NULL          = 1072;
	const ERR_TYPEINFO            = 1073;

	const ERR_CONVERTIMG        = 1081;
	const ERR_CONVERTENC        = 1082;

	const ERR_CHEMININTROUVABLE          = 1091;

	//------------------
	const ERR_ARCHI_INCONNUE              = 1301;
	const ERR_METH_NONGERE                = 1302;
	const ERR_CONTENTTYPE_NONGERE         = 1303;
	const ERR_URI404                      = 1304;
	const ERR_REST_ERR                    = 1305;
	const ERR_NO_SESSION                  = 1306;

	const ERR_REORDER_NOLISTE             = 1350;
	const ERR_REORDER_NOFICHE             = 1351;

	//------------------= ;
	const ERR_UTIL_TROPCHOIX             = 1401;
	const ERR_UTIL_INCONNU               = 1402;
	const ERR_UTIL_PASSERRINTRA          = 1403;
	const ERR_UTIL_NONRESOLU             = 1404;
	const ERR_UTIL_FORM_NONAUTORISE      = 1405;
	const ERR_UTIL_NONAUTORISE           = 1406;
	const ERR_UTIL_DECONNECTE            = 1407;
	const ERR_UTIL_PASSERREXTRA          = 1408;

	const ERR_APPLI_NONAUTORISE          = 1411;
	const ERR_REST_DESACTIVE             = 1412;
	const ERR_SOAP_DESACTIVE             = 1413;
	const ERR_EXTRANET_NONACTIVE         = 1414;
	const ERR_CONNEXION_NONAUTORISE      = 1415;
	const ERR_EXTRANET_NOFORMULAIRE      = 1416;

	const ERR_UTILBIS_FORM_NONAUTORISE     = 1421;
	const ERR_UTILBIS_INCONNU              = 1422;
	const ERR_UTILBIS_NONRESOLU            = 1423;
	const ERR_UTILBIS_PASSERR              = 1424;
	const ERR_UTILBIS_TROPCHOIX            = 1425;
	const ERR_UTILBIS_NONAUTORISE          = 1426;

	const ERR_SESSION_TOKENINCONNU          = 1431;
	const ERR_SESSION_TOKENPERIME           = 1432;
	const ERR_SESSION_NEEDCTXACTION         = 1433;

	//------------------

	const ERR_ACTION_PARTIEL                = 1502;
	const ERR_ACTION_NONRESOLUINTER         = 1503;
	const ERR_ACTION_INCONNU                = 1504;
	const ERR_ACTION_NONRESOLU              = 1505;
	const ERR_ACTION_ACONFIRMER             = 1506;
	const ERR_ACTION_IMPOSSIBLE             = 1507;
	const ERR_ACTION_AMBIGUE                = 1508;
	const ERR_ACTION_INCOMPATIBLE           = 1509;

	const ERR_FORMULAIRE_INCONNU           = 1511;
	const ERR_FORMULAIRE_N0NRESOLU         = 1512;
	const ERR_FORMULAIRE_TROPCHOIX         = 1513;

	const ERR_ENREG_INCONNU                = 1521;
	const ERR_ENREG_N0NRESOLUINTER         = 1522;
	const ERR_ENREG_N0NRESOLU              = 1523;
	const ERR_ENREG_TROPCHOIX              = 1524;
	const ERR_ENREG_IGNORE                 = 1525;
	const ERR_ENREG_INVALIDE               = 1526;

	const ERR_PARAM_INCONNU              = 1531;
	const ERR_PARAM_NONRESOLU            = 1532;
	const ERR_PARAM_OBLIGATOIRE          = 1533;
	const ERR_PARAM_ERREUR_GEN           = 1534;
	const ERR_PARAM_CONFIRM              = 1535;
	const ERR_PARAM_PASID                = 1536;
	const ERR_PARAM_PASIDCOLFROMTAB      = 1537;
	const ERR_PARAM_ENTREE               = 1538;
	const ERR_PARAM_DOUBLON              = 1539;

	const ERR_VALPARAM_OK                = 1541;
	const ERR_VALPARAM_NONRESOLU         = 1542;
	const ERR_VALPARAM_INCONNU           = 1543;
	const ERR_VALPARAM_TROPCHOIX         = 1544;
	const ERR_VALPARAM_CONVERSION        = 1545;
	const ERR_VALPARAM_PASCONTENUFICHIER = 1546;

	const ERR_COLONNE_INCONNU              = 1551;
	const ERR_COLONNE_NONDETERMINE         = 1552;

	const ERR_CREATION_IMPOSSIBLE         = 1561;
	const ERR_CREATION_AMBIGUE            = 1562;

	const ERR_IMPRESSION_REST               = 1571;
	const ERR_IMPRESSION_REST_PARAM         = 1572;
	const ERR_IMPRESSION_REST_MB            = 1573;

	const ERR_UPDATE_BADENREG             = 1581;
	const ERR_GETCHART_INVALIDCONTEXT     = 1586;

	const ERR_GETCALCULATION_NOLIST      = 1591;

	const ERR_DRILLTHROUGHT_BADLIST       = 1595;
	const ERR_DRILLTHROUGHT_BADPARAM      = 1596;
	const ERR_DRILLTHROUGHT_VOIRFICHE     = 1597;
	const ERR_DRILLTHROUGHT_NOACTIONLISTE = 1598;

	//------------------

	const ERR_SOAP_PASDANSBODY             = 1601;
	const ERR_SOAP_PASDANSHEADER           = 1602;
	const ERR_SOAP_BODYVIDE                = 1603;
	const ERR_SOAP_HEADERVIDE              = 1604;
	const ERR_SOAP_BALISEACTIONINCONNUE    = 1605;
	const ERR_SOAP_NOUSERNAMETOKEN         = 1606;
	const ERR_SOAP_PASDEHEADER             = 1607;
	const ERR_SOAP_MODIF_PASDEDONNEES      = 1608;
	const ERR_SOAP_MALFORME                = 1609;
	const ERR_SOAP_INFOIDABSENTE           = 1610;
	const ERR_SOAP_NOIDCONTEXTACTION       = 1611;
	const ERR_SOAP_MAUVAISFORMAT           = 1612;
	const ERR_SOAP_REFERENCE_INCONNUE      = 1613;
	const ERR_SOAP_BALISETABLEINCONNUE     = 1614;
	const ERR_SOAP_BALISERESSOURCEINCONNUE = 1615;
	const ERR_SOAP_CONSTRUCTIONREPONSE     = 1616;

	const ERR_REST_OPERATIONINCONNUE		= 1701;

	const ERR_RPF_NOEMAIL					= 2001;
	//-----------------


	//---------------------------------------
	//gestion des licences
	const ERR_NOSERVEUR                         = 4001;
	const ERR_NONOMME_NOSIMULTANE               = 4003;
	const ERR_UTIL_INVALIDE                     = 4004;
	const ERR_NOMME_DEJACONNECTE                = 4101;
	const ERR_NOMME_HORSNBUTIL                  = 4102;
	const ERR_SIMULTANE_HORSNBCONNEXION         = 4151;
	const ERR_SIMULTANE_HORSNBCONNEXION_SUP     = 4152;
	const ERR_SIMULTANE_HORSNBCONNEXION_SUPMAX  = 4153;
	const ERR_EXTRANET_HORSNBCONNEXION          = 4201;
	const ERR_EXTRANET_HORSLICENCE              = 4202;
	const ERR_EXTRANET_NOCONFIG                 = 4203;

	//-------------------------------------------
	// Erreurs perso
	const ERR_MEMORY_OVERFLOW					= 100001;

	const CAT_GLOBAL        = 0;
	const CAT_ROUTAGE       = 1;
	const CAT_CALCUL        = 2;
	const CAT_FORMULE       = 3;
	const CAT_PASSERELLE    = 4;
	const CAT_IHM           = 5;
	const CAT_ACTION        = 6;
	const CAT_MAX           = 7;
	const CAT_RESEAUX       = 8;
	const CAT_FICHIER       = 9;
	const CAT_APPLI         = 10;
	const CAT_DATASOURCE    = 11;
	const CAT_SIMAXSERVICE  = 12;
	const CAT_PUBLICATION   = 13;
	const CAT_SESSION       = 14;
	const CAT_REQUETE       = 15;
	const CAT_POS           = 16;
	const CAT_WEBSERVICE    = 17;
	const CAT_GRAPHE        = 18;
	const CAT_GRAPHEDOT     = 19;
	const CAT_MESSAGERIE    = 20;


}
