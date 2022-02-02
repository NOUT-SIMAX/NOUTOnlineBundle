<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 28/07/14
 * Time: 14:20
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService;

class OnlineError implements \JsonSerializable
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


    public function jsonSerialize(): array
    {
        return array(
            'code'       => $this->m_nCode,
            'error'      => $this->m_nErreur,
            'category'   => $this->m_nCategorie,
            'message'    => $this->m_sMessage,
            'parameters' => $this->m_TabParametres,
        );
    }

	public function AddParameter(OnlineErrorParameter $clError)
	{
		$this->m_TabParametres[] = $clError;
	}

	/**
	 * @param mixed $TabParametres
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
     * @param int $nCategorie
     */
	public function setCategorie(int $nCategorie)
	{
		$this->m_nCategorie = $nCategorie;
	}

	/**
	 * @return int
	 */
	public function getCategorie(): int
    {
		return $this->m_nCategorie;
	}

    /**
     * @param string $nCode
     */
	public function setCode(string $nCode)
	{
		$this->m_nCode = $nCode;
	}

	/**
	 * @return string
	 */
	public function getCode(): string
    {
		return $this->m_nCode;
	}

    /**
     * @param int $nErreur
     */
	public function setErreur(int $nErreur)
	{
		$this->m_nErreur = $nErreur;
	}

	/**
	 * @return int
	 */
	public function getErreur(): int
    {
		return $this->m_nErreur;
	}

    /**
     * @param string $sMessage
     */
	public function setMessage(string $sMessage)
	{
		$this->m_sMessage = $sMessage;
	}

	/**
	 * @return string
	 */
	public function getMessage(): string
    {
		return $this->m_sMessage;
	}

    public function parseFromREST($output)
    {
        $tabLines = explode("\r\n", $output);

        $re = '/([0-9]+)\(([0-9]+)\|([0-9]+)\)(.*)/';
        preg_match_all($re, $tabLines[0], $matches);

        $this->m_nErreur = $matches[1][0];
        $this->m_nCategorie = $matches[2][0];
        $this->m_nCode = $matches[3][0];

        $this->m_sMessage = strip_tags($tabLines[1]);
    }

    const ERR_NOUTONLINE_OFF                = 1;

	const ERR_ACTION_NOCTXT  = 1001;

	//constante pour les codes d'erreurs

    //----
    const ERR_MAX_ANALYSEMEASSAGE_PASDROITACTION = 403;
    const ERR_MAX_ANALYSEMEASSAGE_ACTIONAMBIGUE = 404;

    //----
	const ERR_WS_AUTRE                         = 1000;
	const ERR_WS_LANGOBJECT_GETVALCOL          = 1001;
	const ERR_WS_LANGOBJECT_NULL               = 1002;

	const ERR_WS_LANGACTION_NULL               = 1011;
	const ERR_WS_LANGACTION_TABPARAM_NULL      = 1012;
	const ERR_WS_LANGACTION_PARAM_NULL         = 1013;

	const ERR_WS_LANGMANIP_NULL                = 1021;
	const ERR_WS_LANGMANIP_REMPTABACTION       = 1022;

	const ERR_WS_INTERMAX_NULL             = 1031;
	const ERR_WS_INTERMAX_CHERCOLTAB       = 1032;
	const ERR_WS_INTERMAX_GETCOLENREG      = 1033;
	const ERR_WS_INTERMAX_CHERTAB          = 1034;
	const ERR_WS_INTERMAX_REQTAB           = 1035;

	const ERR_WS_FDS_NULL            = 1041;
	const ERR_WS_DS_NULL             = 1045;
	const ERR_WS_IM_NULL             = 1046;
	const ERR_WS_INIT_GP             = 1047;

	const ERR_WS_TRAITEDEM_NULL              = 1051;
	const ERR_WS_GESTIONDEM_NULL             = 1052;
	const ERR_WS_GESTIONPARAMACTION_NULL     = 1053;

	const ERR_WS_LANGMODELE_NULL           = 1061;
	const ERR_WS_LANGVUE_NULL              = 1062;
	const ERR_WS_LANGVUE_IDTABREL          = 1063;

	const ERR_WS_INFOMODELE          = 1071;
	const ERR_WS_IUTIL_NULL          = 1072;
	const ERR_WS_TYPEINFO            = 1073;

	const ERR_WS_CONVERTIMG        = 1081;
	const ERR_WS_CONVERTENC        = 1082;

	const ERR_WS_CHEMININTROUVABLE          = 1091;

	//------------------
	const ERR_WS_ARCHI_INCONNUE              = 1301;
	const ERR_WS_METH_NONGERE                = 1302;
	const ERR_WS_CONTENTTYPE_NONGERE         = 1303;
	const ERR_WS_URI404                      = 1304;
	const ERR_WS_REST_ERR                    = 1305;
	const ERR_WS_NO_SESSION                  = 1306;

	const ERR_WS_REORDER_NOLISTE             = 1350;
	const ERR_WS_REORDER_NOFICHE             = 1351;

	//------------------= ;
	const ERR_WS_UTIL_TROPCHOIX             = 1401;
	const ERR_WS_UTIL_INCONNU               = 1402;
	const ERR_WS_UTIL_PASSERRINTRA          = 1403;
	const ERR_WS_UTIL_NONRESOLU             = 1404;
	const ERR_WS_UTIL_FORM_NONAUTORISE      = 1405;
	const ERR_WS_UTIL_NONAUTORISE           = 1406;
	const ERR_WS_UTIL_DECONNECTE            = 1407;
	const ERR_WS_UTIL_PASSERREXTRA          = 1408;

	const ERR_WS_APPLI_NONAUTORISE          = 1411;
	const ERR_WS_REST_DESACTIVE             = 1412;
	const ERR_WS_SOAP_DESACTIVE             = 1413;
	const ERR_WS_EXTRANET_NONACTIVE         = 1414;
	const ERR_WS_CONNEXION_NONAUTORISE      = 1415;
	const ERR_WS_EXTRANET_NOFORMULAIRE      = 1416;

	const ERR_WS_UTILBIS_FORM_NONAUTORISE     = 1421;
	const ERR_WS_UTILBIS_INCONNU              = 1422;
	const ERR_WS_UTILBIS_NONRESOLU            = 1423;
	const ERR_WS_UTILBIS_PASSERR              = 1424;
	const ERR_WS_UTILBIS_TROPCHOIX            = 1425;
	const ERR_WS_UTILBIS_NONAUTORISE          = 1426;

	const ERR_WS_SESSION_TOKENINCONNU          = 1431;
	const ERR_WS_SESSION_TOKENPERIME           = 1432;
	const ERR_WS_SESSION_NEEDCTXACTION         = 1433;

	//------------------

	const ERR_WS_ACTION_PARTIEL                = 1502;
	const ERR_WS_ACTION_NONRESOLUINTER         = 1503;
	const ERR_WS_ACTION_INCONNU                = 1504;
	const ERR_WS_ACTION_NONRESOLU              = 1505;
	const ERR_WS_ACTION_ACONFIRMER             = 1506;
	const ERR_WS_ACTION_IMPOSSIBLE             = 1507;
	const ERR_WS_ACTION_AMBIGUE                = 1508;
	const ERR_WS_ACTION_INCOMPATIBLE           = 1509;

	const ERR_WS_FORMULAIRE_INCONNU           = 1511;
	const ERR_WS_FORMULAIRE_N0NRESOLU         = 1512;
	const ERR_WS_FORMULAIRE_TROPCHOIX         = 1513;

	const ERR_WS_ENREG_INCONNU                = 1521;
	const ERR_WS_ENREG_N0NRESOLUINTER         = 1522;
	const ERR_WS_ENREG_N0NRESOLU              = 1523;
	const ERR_WS_ENREG_TROPCHOIX              = 1524;
	const ERR_WS_ENREG_IGNORE                 = 1525;
	const ERR_WS_ENREG_INVALIDE               = 1526;

	const ERR_WS_PARAM_INCONNU              = 1531;
	const ERR_WS_PARAM_NONRESOLU            = 1532;
	const ERR_WS_PARAM_OBLIGATOIRE          = 1533;
	const ERR_WS_PARAM_ERREUR_GEN           = 1534;
	const ERR_WS_PARAM_CONFIRM              = 1535;
	const ERR_WS_PARAM_PASID                = 1536;
	const ERR_WS_PARAM_PASIDCOLFROMTAB      = 1537;
	const ERR_WS_PARAM_ENTREE               = 1538;
	const ERR_WS_PARAM_DOUBLON              = 1539;

	const ERR_WS_VALPARAM_OK                = 1541;
	const ERR_WS_VALPARAM_NONRESOLU         = 1542;
	const ERR_WS_VALPARAM_INCONNU           = 1543;
	const ERR_WS_VALPARAM_TROPCHOIX         = 1544;
	const ERR_WS_VALPARAM_CONVERSION        = 1545;
	const ERR_WS_VALPARAM_PASCONTENUFICHIER = 1546;

	const ERR_WS_COLONNE_INCONNU              = 1551;
	const ERR_WS_COLONNE_NONDETERMINE         = 1552;

	const ERR_WS_CREATION_IMPOSSIBLE         = 1561;
	const ERR_WS_CREATION_AMBIGUE            = 1562;

	const ERR_WS_IMPRESSION_REST               = 1571;
	const ERR_WS_IMPRESSION_REST_PARAM         = 1572;
	const ERR_WS_IMPRESSION_REST_MB            = 1573;

	const ERR_WS_UPDATE_BADENREG             = 1581;
	const ERR_WS_GETCHART_INVALIDCONTEXT     = 1586;

	const ERR_WS_GETCALCULATION_NOLIST      = 1591;

	const ERR_WS_DRILLTHROUGHT_BADLIST       = 1595;
	const ERR_WS_DRILLTHROUGHT_BADPARAM      = 1596;
	const ERR_WS_DRILLTHROUGHT_VOIRFICHE     = 1597;
	const ERR_WS_DRILLTHROUGHT_NOACTIONLISTE = 1598;

	//------------------

	const ERR_WS_SOAP_PASDANSBODY             = 1601;
	const ERR_WS_SOAP_PASDANSHEADER           = 1602;
	const ERR_WS_SOAP_BODYVIDE                = 1603;
	const ERR_WS_SOAP_HEADERVIDE              = 1604;
	const ERR_WS_SOAP_BALISEACTIONINCONNUE    = 1605;
	const ERR_WS_SOAP_NOUSERNAMETOKEN         = 1606;
	const ERR_WS_SOAP_PASDEHEADER             = 1607;
	const ERR_WS_SOAP_MODIF_PASDEDONNEES      = 1608;
	const ERR_WS_SOAP_MALFORME                = 1609;
	const ERR_WS_SOAP_INFOIDABSENTE           = 1610;
	const ERR_WS_SOAP_NOIDCONTEXTACTION       = 1611;
	const ERR_WS_SOAP_MAUVAISFORMAT           = 1612;
	const ERR_WS_SOAP_REFERENCE_INCONNUE      = 1613;
	const ERR_WS_SOAP_BALISETABLEINCONNUE     = 1614;
	const ERR_WS_SOAP_BALISERESSOURCEINCONNUE = 1615;
	const ERR_WS_SOAP_CONSTRUCTIONREPONSE     = 1616;

	const ERR_WS_REST_OPERATIONINCONNUE		= 1701;

	const ERR_WS_RPF_NOEMAIL					= 2001;
	//-----------------


	//---------------------------------------
	//gestion des licences
	const ERR_WS_NOSERVEUR                         = 4001;
	const ERR_WS_NONOMME_NOSIMULTANE               = 4003;
	const ERR_WS_UTIL_INVALIDE                     = 4004;
	const ERR_WS_NOMME_DEJACONNECTE                = 4101;
	const ERR_WS_NOMME_HORSNBUTIL                  = 4102;
	const ERR_WS_SIMULTANE_HORSNBCONNEXION         = 4151;
	const ERR_WS_SIMULTANE_HORSNBCONNEXION_SUP     = 4152;
	const ERR_WS_SIMULTANE_HORSNBCONNEXION_SUPMAX  = 4153;
	const ERR_WS_EXTRANET_HORSNBCONNEXION          = 4201;
	const ERR_WS_EXTRANET_HORSLICENCE              = 4202;
	const ERR_WS_EXTRANET_NOCONFIG                 = 4203;

	//-------------------------------------------
	// Erreurs perso
	const ERR_MEMORY_OVERFLOW                   = 100001;
	const ERR_VALIDATE_ERROR                    = 100002;

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
