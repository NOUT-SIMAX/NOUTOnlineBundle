<?php
namespace NOUT\Bundle\NOUTOnlineBundle\SOAP;



use NOUT\Bundle\NOUTOnlineBundle\SOAP\NUSOAP\SOAPClient;

/***
 * Classe qui surcharge la classe NUSOAPClient, afin de pouvoir lui apporté des modification mineur :
 * 	- gestions des content-type de retour "application/soap+xml"
 *  - gestion de l'encodage 
 *  - appel de call simplifié et forcé en document litteral
 *  - gestion des erreur soap 1.2 qui ne sont pas gérer par nuSoap
 * 
 * 
 * @version 1.0.1. last modification : 2011-10-29
 * @author   richard deguilhem <richard@nout.fr>
 */
class ModifiedNusoapClient extends SOAPClient
{
	//definition de variable pour l'utilisation de document litteral
	//TODO: Permettre la modification (document litteral et rpc encoded via fonction
	private $m_sSerialisationStyle = 'document';
	private $m_sSerialisationUse   = 'literal';

	private $m_sDefaultEncoding = 'UTF-8';

	/**
 	 * constructeur permettant d'instancier les classe de communication soap avec les bonne question
 	 * @param $sEndpoint
 	 * @param $bWsdl
 	 * @param $sProxyHost
 	 * @param $sProxyPort
 	 * @return unknown_type
 	 */
 	public function __construct($sEndpoint, $bWsdl = false, $sProxyHost = false, $sProxyPort = false)
 	{
 		parent::__construct($sEndpoint, $bWsdl, $sProxyHost, $sProxyPort);
 	}
    //---
			
	/**
	 * Surcharge de la methode call de NUSOAPClient avec afin :
	 *  - de simplifier son appel 
	 *  - de forcer l'utilisation en document litteral.
	 *  - de gérer les retour d'erreur format soap 1.2
	 * 
	 * @param string $sOperation SOAP server URL or path
	 * @param mixed $mParams An array, associative or simple, of the parameters
	 *			              for the method call, or a string that is the XML
	 *			              for the call.  For document
	 *			              style, this will only wrap with the Envelope and Body.
	 *			              IMPORTANT: when using an array with document style,
	 *			              in which case there
	 *                         is really one parameter, the root of the fragment
	 *                         used in the call, which encloses what programmers
	 *                         normally think of parameters.  A parameter array
	 *                         *must* include the wrapper.
	 * @param	mixed $headers optional string of XML with SOAP header content, or array of soapval objects for SOAP headers, or associative array
	 * @return	mixed	response from SOAP call, normally an associative array mirroring the structure of the XML response, false for certain fatal errors
	 * @access   public
	 * 
	 * pour plus d'information :
	 * @see lib/NUSOAPClient#call($operation, $params, $namespace, $soapAction, $headers, $rpcParams, $style, $use)
	 * 
	 * note : $sStyle et $sUse sont des parametre inutile reporter uniquement pour avoir la meme signature de methode.
	 */
	public function call($sOperation, $mParams = array(), $sNamespace = null, $sSoapAction = null, $mHeaders = false, $mRpcParams = null, $sStyle = 'rpc', $sUse = 'encoded')
	{
		//on definit l'encodage de la requete		
		$this->soap_defencoding = $this->m_sDefaultEncoding;
		$this->http_encoding    = $this->m_sDefaultEncoding;
		if ($this->m_sDefaultEncoding == 'UTF-8')
		{
			$this->decode_utf8 = false; // on est en utf-8 on n'as donc pas de decode neccessaire.
		}
		
		//on appel l'operation en forcant le document literal
		$mReturn =  parent::call($sOperation, $mParams, $sNamespace, $sSoapAction, $mHeaders, $mRpcParams, $this->m_sSerialisationStyle, $this->m_sSerialisationUse);

		
		//-- on test la présence d'erreur soap 1.2 :
		//le retour d'erreur soap 1.2 aura Code et Reason obligatoirement et Node role et detail de facon optionnel
		//http://www.w3.org/2002/07/soap-translation/soap12-part1.html#soapfault
		if (is_array($mReturn) && isset($mReturn['Code']) && isset($mReturn['Reason']))
		{
			$this->fault = true;

			$sErrCode = -1;
			$sErrMsg  = '';
			if (is_array($mReturn['Detail']) && 
				is_array($mReturn['Detail']['ListErr']) && 
				is_array($mReturn['Detail']['ListErr']['Error']) &&
				isset($mReturn['Detail']['ListErr']['Error'][0]) && 
				is_array($mReturn['Detail']['ListErr']['Error'][0]) && 
				isset($mReturn['Detail']['ListErr']['Error'][0]['Code'])
			)
			{
				foreach ($mReturn['Detail']['ListErr']['Error'] as $tabError)
				{
					$sErrCode = $tabError['Code']['Numero'];
					$sErrMsg = utf8_encode($tabError['Message']);
				}
			}
			else
			{
				$sErrCode = $mReturn['Detail']['ListErr']['Error']['Code']['Numero'];
				$sErrMsg = htmlspecialchars($mReturn['Detail']['ListErr']['Error']['Message']);
			}
			
			$this->setError(htmlspecialchars($sErrMsg));
		}

		if ($mReturn === '')
		{
			$mReturn = true;
		}
		
		if ($this->fault || $mReturn === false)
		{
			if (!isset($sErrCode))
			{
				$sErrCode = -1;
			}


			throw new SOAPException($this->getError(), $sErrCode);
            // Appeler la fenêtre modale Bootstrap

            // Se fait en JS
//            app.api.modal.show(
//                'Suppression',                      // Titre de la fenêtre
//                jsonDataFromPhp.data.boxMessage,    // Message de la fenêtre
//                'info',                             // Type de fenêtre
//                callbacksTab,                       // Les boutons. La key est le type et la value le callack
//                true                                // Les boutons contiennent des objets pour appel Ajax
//            );
		}

		return $mReturn;
	}
    //---

	/**
	 * Surchargé pour modification des content type de retour "application/soap+xml"
	 *
	 * processes SOAP message returned from server
	 *
	 * @param	array	$headers	The HTTP headers
	 * @param	string	$data		unprocessed response data from server
	 * @return	mixed	value of the message, decoded into a PHP type
	 * @access   public
	 *
	 * * @see lib/NUSOAPClient#parseResponse
	 */
	public function parseResponse($headers, $data)
	{
		if (!isset($headers['content-type']))
		{
			$this->setError('Response not of type text/xml (no content-type header)');

			return false;
		}
		if (!strstr($headers['content-type'], 'text/xml') && !strstr($headers['content-type'], 'application/soap+xml'))
		{
			$this->setError('Response not of type text/xml: '.$headers['content-type']);

			return false;
		}
		if (strpos($headers['content-type'], '='))
		{
			$enc = str_replace('"', '', substr(strstr($headers["content-type"], '='), 1));
			if (preg_match('/^(ISO-8859-1|US-ASCII|UTF-8)$/i', $enc))
			{
				$this->xml_encoding = strtoupper($enc);
			}
			else
			{
				$this->xml_encoding = 'US-ASCII';
			}
		}
		else
		{
			// should be US-ASCII for HTTP 1.0 or ISO-8859-1 for HTTP 1.1
			$this->xml_encoding = 'ISO-8859-1';
		}

		return $this->parseData($data);
	}
	//---
}
//****
