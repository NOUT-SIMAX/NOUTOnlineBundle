<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 06/08/2015
 * Time: 09:26
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService;

// C'est la classe mère abstraite
abstract class Parser
{
	const NAMESPACE_XSD = 'http://www.w3.org/2001/XMLSchema';
	const NAMESPACE_NOUT_XSD = 'http://www.nout.fr/XMLSchema';
	const NAMESPACE_NOUT_XML = 'http://www.nout.fr/XML/';
	const NAMESPACE_NOUT_LAYOUT = 'http://www.nout.fr/XML/layout';
	const NAMESPACE_NOUTONLINE = 'http://www.nout.fr/wsdl/SimaxService.wsdl/';
}