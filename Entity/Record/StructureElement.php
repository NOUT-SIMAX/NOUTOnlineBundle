<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 04/08/14
 * Time: 11:42
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Record;


class StructureElement
{
/*
	const NV_Complet                    = 0;	// l'enreg et toutes ses colonnes (filtre, record)
	const NV_SansEntete                 = 1;	// l'enreg et toutes ses colonnes sans l'entete xml
	const NV_List                       = 2;	// permet d'utiliser un cache (demande choix action, liste)
	const NV_ToutSaufDetail             = 3;	// Toutes les colonnes sauf detail
	const NV_CompletSansDetailElemList  = 4;	// Toutes les colonnes mais sans détailler le contenu des listes (cas des listes asynchrones)
*/
	const NV_XSD_Enreg                  = 0;
	const NV_XSD_SousEnreg              = 1;
	const NV_XSD_EnregSansEntete        = 2;


	public $m_nID;
	public $m_sLibelle;
	public $m_nNiveau;
	public $m_TabStructureColonne;

	public function __construct()
	{
		$this->m_nID = '';
		$this->m_sLibelle = '';
		$this->m_nNiveau = -1;
		$this->m_TabStructureColonne = array();
	}
} 