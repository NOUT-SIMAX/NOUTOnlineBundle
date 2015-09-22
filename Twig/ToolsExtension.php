<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 25/07/14
 * Time: 14:07
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Twig;

/**
 * This class contains the needed functions in order to do the query highlighting
 *
 * @author Florin Patan <florinpatan@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
class ToolsExtension extends \Twig_Extension
{

	/**
	 * Get the name of the extension
	 *
	 * @return string
	 */
	public function getName()
	{
		return 'tools_extension';
	}

	/**
	 * Define our functions
	 *
	 * @return array
	 */
//	public function getFilters()
//	{
//		return array(
//		);
//	}

	public function getFunctions()
	{
		return array(
			'uniqueid' => new \Twig_Function_Method($this, 'tools_uniqueid'),
		);
	}

	/**
	 * genere un identifiant unique
	 * @return string
	 */
	public function tools_uniqueid()
	{
        return uniqid();
	}

} 