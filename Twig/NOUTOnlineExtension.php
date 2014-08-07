<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 25/07/14
 * Time: 14:07
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * This class contains the needed functions in order to do the query highlighting
 *
 * @author Florin Patan <florinpatan@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
class NOUTOnlineExtension extends \Twig_Extension {
	/**
	 * Get the name of the extension
	 *
	 * @return string
	 */
	public function getName()
	{
		return 'nout_online_extension';
	}

	/**
	 * Define our functions
	 *
	 * @return array
	 */
	public function getFilters()
	{
		return array(
			'noutonline_beautify_soap_query' => new \Twig_Filter_Method($this, 'beautifySOAPQuery'),
		);
	}

	/**
	 * Minify the query
	 *
	 * @param string $query
	 *
	 * @return string
	 */
	public function beautifySOAPQuery($query)
	{
		$nPos = strpos($query, '<?xml ');
		$header = substr($query, 0, $nPos);
		$xml = substr($query, $nPos);

		$doc = new \DOMDocument();
		$doc->preserveWhiteSpace = false;
		$doc->formatOutput = true;
		$doc->loadXML($xml);

		$result = $header.$doc->saveXML();
		return $result;
	}
} 