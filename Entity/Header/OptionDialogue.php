<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 31/07/14
 * Time: 16:08
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Header;

use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\OptionDialogue as WSDLOptionDialogue;

class OptionDialogue extends WSDLOptionDialogue
{
	//flag pour la valeur affichée
	const DISPLAY_TM_Texte      = 0x0001;		// 000000000000001   01- Chaine sur 100
	const DISPLAY_TM_TexteLong  = 0x0002;		// 000000000000010   02- Chaine multiligne, taille non limite (sauf par bdd)
	const DISPLAY_TM_Entier     = 0x0004;		// 000000000000100   03- Entier
	const DISPLAY_TM_Reel       = 0x0008;		// 000000000001000   04- Float
	const DISPLAY_TM_Monetaire  = 0x0010;		// 000000000010000   05- Float à plus de précision dans les positifs
	const DISPLAY_TM_Date       = 0x0020;		// 000000000100000   06- Chaine de la forme AAAAMMJJ
	const DISPLAY_TM_Heure      = 0x0040;		// 000000001000000   07- Chaine de la forme HHMMSS
	const DISPLAY_TM_DateHeure  = 0x0080;		// 000000010000000   08- Chaine de la forme AAAAMMJJHHMMSS
	const DISPLAY_TM_Booleen    = 0x0100;		// 000000100000000   09- 0 ou 1 (1 octet)
	const DISPLAY_TM_IDAuto     = 0x0200;		// 000001000000000   10- Entier non signé sur 8
	const DISPLAY_TM_Fichier    = 0x0400;		// 000010000000000   11- Mémo binaire
	const DISPLAY_TM_Combo      = 0x0800;		// 000100000000000   12- Entier non signé sur 8 qui représente l'ID de choix de la combo
	const DISPLAY_TM_Tableau    = 0x1000;		// 001000000000000   13- Entier non signé sur 8 qui représente l'enregistrement choisi
	const DISPLAY_TM_ListeElem  = 0x2000;		// 010000000000000   14- Ce n'est pas une rubrique réelle, Il peux y avoir un fichier de relation si c'est un ensemble d'element indep
	const DISPLAY_TM_Duree      = 0x4000;		// 100000000000000   15- Entier non signé sur 8, nb de secondes

	//masque générique
	const DISPLAY_None              = 0;
	const DISPLAY_No_ID             = 0x000040FF;           // 100000011111111
	const DISPLAY_No_ID_TL          = 0x000040FD;           // 100000011111101
	const DISPLAY_No_ID_TL_T        = 0x000040FC;           // 100000011111100
	const DISPLAY_No_ID_DH          = 0x0000407F;           // 100000001111111
	const DISPLAY_No_ID_DH_TL       = 0x0000407D;           // 100000001111101
	const DISPLAY_No_ID_DH_TL_T     = 0x0000407C;           // 100000001111100

    //masque pour les fantômes
    const GHOST_VALID               = 0x1;
    const GHOST_INVALID             = 0x2;
    const GHOST_ALL                 = 0x3;


	public function __construct()
	{
		//----------------------------------
		//NE PAS MODIFIER CES VALEURS
		$this->Readable        = 0; // integer TOUJOURS laisser 0, on considère que c'est toujours non lisible dans le bundle
		$this->EncodingOutput  = 0; // integer
		$this->HTTPForceReturn = 0; // integer
        $this->VersionMin = 0; // integer
        $this->VersionPref = 0; // integer
		//----------------------------------

		$this->ReturnValue           = null; // integer
		$this->ReturnXSD             = null; // integer
		$this->Ghost                 = null; // integer
		$this->DefaultPagination     = null; // integer
		$this->DisplayValue          = null;
		$this->LanguageCode          = null; // integer
		$this->WithFieldStateControl = null; // integer
		$this->ListContentAsync      = null; // integer
	}

	/**
	 * @return $this
	 */
	public function InitDefault()
	{
		$this->ReturnValue           = 1; 	// integer
		$this->ReturnXSD             = 1; 	// integer
		$this->Ghost                 = OptionDialogue::GHOST_VALID;
		$this->DefaultPagination     = 20; 	// integer
		$this->DisplayValue          = 0; 	// integer
		$this->LanguageCode          = 12; 	// integer
		$this->WithFieldStateControl = 1; 	// integer
		$this->ListContentAsync      = 0; 	// integer
        $this->VersionMin            = 1; // integer

		return $this;
	}

}
