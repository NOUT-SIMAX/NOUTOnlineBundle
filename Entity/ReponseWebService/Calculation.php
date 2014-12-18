<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 07/08/14
 * Time: 11:28
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService;

/**
 * Class Calculation qui permet de stocker les calculs pour une colonne
 * @package NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService
 */
class Calculation
{
	protected $m_nIDColonne; //Identifiant de la colonne
	protected $m_MapCalcul; //tableau associatif type de calcul => valeur

	public function __construct($nIDColonne)
	{
		$this->m_nIDColonne = $nIDColonne;
		$this->m_MapCalcul  = array();
	}

	public function AddCacul($sTypeCalcul, $sValeur)
	{
		$this->m_MapCalcul[$sTypeCalcul] = $sValeur;
	}

	public function GetCalcul($sTypeCalcul)
	{
		return $this->m_MapCalcul[$sTypeCalcul];
	}

	public function getIDColonne()
	{
		return $this->m_nIDColonne;
	}
}
