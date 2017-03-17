<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 25/07/14
 * Time: 14:07
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Twig;

/**
 * This class contains tools functions
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
			new \Twig_SimpleFunction('uniqueid', array($this, 'tools_uniqueid')),
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


    public function getFilters(){
        return array(
            new \Twig_SimpleFilter('base64_encode', 'base64_encode'),
            new \Twig_SimpleFilter('base64_decode', 'base64_decode')
        );
    }

} 