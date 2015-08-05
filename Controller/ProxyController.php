<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 05/08/2015
 * Time: 13:39
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Controller;


use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS;



use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route; // this imports the annotations

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class DefaultController
 * @package NOUT\Bundle\NOUTOnlineBundle\Controller
 */
abstract class ProxyController extends Controller
{
	protected function _clGetConfiguration($host)
	{
		$clConfiguration = $this->get('nout_online.configuration_dialogue');
		if (!empty($host))
		{
			//! (null, '', false, 0, !isset, [])

			list($sAddress, $sPort) = explode(':', $host);
			$clConfiguration->SetHost($sAddress, $sPort);
		}

		return $clConfiguration;
	}

	protected function _VarDumpRes($sOperation, $ret)
	{
		echo '<h1>'.$sOperation.'</h1>';
		if ($ret instanceof XMLResponseWS)
		{
			echo '<pre>'.htmlentities($ret->sGetXML()).'</pre>';
		}
		else
		{
			var_dump($ret);
		}
	}

	protected function _SupprAccents($str, $encoding = 'utf-8')
	{
		// transformer les caractères accentués en entités HTML
		$str = htmlentities($str, ENT_NOQUOTES, $encoding);

		// remplacer les entités HTML pour avoir juste le premier caractères non accentués
		// Exemple : "&ecute;" => "e", "&Ecute;" => "E", "Ã " => "a" ...
		$str = preg_replace('#&([A-za-z])(?:acute|grave|cedil|circ|orn|ring|slash|th|tilde|uml);#', '\1', $str);

		// Remplacer les ligatures tel que : Œ, Æ ...
		// Exemple "Å“" => "oe"
		$str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
		// Supprimer tout le reste
		$str = preg_replace('#&[^;]+;#', '', $str);

		return $str;
	}

	protected function _bEstNumerique($form)
	{
		return empty(str_replace(array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9), array('', '', '', '', '', '', '', '', '', ''), $form));
	}
}