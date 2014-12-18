<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 17/12/14
 * Time: 14:07
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Controller;

use PhpCsFixer\Fixer\AllmanCurlyBracesFixer;
use PhpCsFixer\Tests\AllmanCurlyBracesFixerTest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

// this imports the annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;



/**
 * Class AutresController
 *
 * @package NOUT\Bundle\NOUTOnlineBundle\Controller
 * @Route("/autres")
 */
class AutresController extends Controller
{
	/**
	 * @Route("/", name="online_autres_index")
	 */
	public function indexAction()
	{
		return $this->render('NOUTOnlineBundle:Autres:index.html.twig');
	}

	protected function _sVarDump($var)
	{
		ob_start();
		var_dump($var);

		$containt = ob_get_contents();
		ob_get_clean();
		return $containt;
	}

	/**
	 * @Route("/allman", name="online_autres_allmanfixer")
	 */
	public function allmanAction()
	{

		$clAllman = new AllmanCurlyBracesFixer();
		$clAllmanTest = new AllmanCurlyBracesFixerTest();


		$file = __DIR__.'/../Resources/public/test/fixer/allman.php';
		$clQplFileInfo = new \SplFileInfo($file);

		$containt='<div class="container">';

		foreach($clAllmanTest->provideFixCases() as $aArray)
		{
			$clExpected = array_shift($aArray);
			$clInput = array_shift($aArray);

			if (isset($clInput))
			{
				$clFixInput = $clAllman->fix($clQplFileInfo, $clInput);

				$ok = str_replace("\r\n", "\n", $clFixInput)===str_replace("\r\n", "\n", $clExpected);
				$class=($ok ? 'bg-success' : 'bg-danger');


				$containt.='<div class="row '.$class.'"><div class="col-md-3">'.$this->_sVarDump($clInput).
						   '</div><div class="col-md-3">'.$this->_sVarDump($clFixInput).
						   '</div><div class="col-md-3">'.$this->_sVarDump($clExpected).
						   '</div><div class="col-md-1">'.
						   '</div><div class="col-md-2">'.$this->_sVarDump($ok).
						   '</div></div>';


			}
			else
			{
				$clFixExpected = $clAllman->fix($clQplFileInfo, $clExpected);

				$ok = str_replace("\r\n", "\n", $clFixExpected)===str_replace("\r\n", "\n", $clExpected);
				$class=($ok ? 'bg-success' : 'bg-danger');

				$containt.='<div class="row '.$class.'"><div class="col-md-5">'.$this->_sVarDump($clFixExpected).
						   '</div><div class="col-md-5">'.$this->_sVarDump($clExpected).
						   '</div><div class="col-md-2">'.$this->_sVarDump($ok).
							'</div></div>';
			}

		}
		$containt.='</div>';

		return $this->render('NOUTOnlineBundle:Autres:debug.html.twig', array('containt'=>$containt));
	}


} 