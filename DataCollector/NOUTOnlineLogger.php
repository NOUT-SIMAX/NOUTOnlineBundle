<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 25/07/14
 * Time: 10:22
 */

namespace NOUT\Bundle\NOUTOnlineBundle\DataCollector;

/**
 * Class NOUTOnlineLogger
 * @package NOUT\Bundle\NOUTOnlineBundle\DataCollector
 */
class NOUTOnlineLogger
{
	/**
	 * Executed NOUTOnline queries.
	 *
	 * @var array
	 */
	public $m_TabQueries = array();

	/**
	 * If NOUTOnline Logger is enabled (log queries) or not.
	 *
	 * @var boolean
	 */
	public $m_bEnabled = true;

	/**
	 * @var float|null
	 */
	public $m_fStart = null;

	/**
	 * tableau qui contient les temps intermédiaire
	 * @var array|null
	 */
	public $m_fSend = null;

	/**
	 * @var $m_clMonolog : monolog pour voir les traces
	 */
	protected $m_clMonolog;

	/**
	 * @param $logger : l'instance monolog de symfony
	 * @param $debug : si site en debug
	 */
	public function __construct($logger, $debug)
	{
		$this->m_clMonolog = $logger;
		$this->m_bEnabled  = $debug;
	}

	public function getEnabled()
	{
		return $this->m_bEnabled;
	}

	public function startQuery()
	{
		if ($this->m_bEnabled)
		{
			$this->m_fStart = microtime(true);
		}
	}

	public function startSend()
	{
		if ($this->m_bEnabled)
		{
			$this->m_fSend = microtime(true);
		}
	}

	public function stopSend()
	{
		if ($this->m_bEnabled)
		{
			$this->m_fSend = microtime(true) - $this->m_fSend;
		}
	}


    /**
     * @param $bTo
     * @param $sOperation
     * @param $bSOAP
     * @param $extra
     * @return mixed
     */
    protected function _getContext($bTo, $sOperation, $bSOAP, $extra)
    {
        $oCtxt = new \stdClass();
        $oCtxt->way = $bTo ? 'send' : 'receive';
        $oCtxt->operation = $sOperation;
        $oCtxt->soap = $bSOAP;
        $oCtxt->rest = !$bSOAP;
        $oCtxt->extra = $extra;

        if (is_array($extra) && array_key_exists(self::EXTRA_Http_Headers, $extra))
        {
            $http_headers  = $extra[self::EXTRA_Http_Headers];

            if (is_array($http_headers) && array_key_exists('Content-Type', $http_headers))
            {
                $oCtxt->content_type = $http_headers['Content-Type']->value;
            }
        }

		//transforme en array
		$stringjson = json_encode($oCtxt);

		if ($stringjson == false){
			switch (json_last_error()) {
				case JSON_ERROR_NONE:
					$erreur = ' - Aucune erreur';
					break;
				case JSON_ERROR_DEPTH:
					$erreur =  ' - Profondeur maximale atteinte';
					break;
				case JSON_ERROR_STATE_MISMATCH:
					$erreur =  ' - Inadéquation des modes ou underflow';
					break;
				case JSON_ERROR_CTRL_CHAR:
					$erreur =  ' - Erreur lors du contrôle des caractères';
					break;
				case JSON_ERROR_SYNTAX:
					$erreur =  ' - Erreur de syntaxe ; JSON malformé';
					break;
				case JSON_ERROR_UTF8:
					$erreur =  ' - Caractères UTF-8 malformés, probablement une erreur d\'encodage';
					break;
				default:
					$erreur =  ' - Erreur inconnue';
					break;
			}

		}
		$aRet = json_decode($stringjson, true);
        return $aRet;
    }

	/**
	 * @param $sTo
	 * @param $sFrom
	 */
	public function stopQuery($sTo, $sFrom, $sOperation, $bSOAP, $extra)
	{
		if ($this->m_bEnabled)
		{
			if ($bSOAP)
			{
				$sTo = str_replace('><', ">\r\n<", $sTo);
			}

			$this->m_clMonolog->debug(
				$sTo,
				$this->_getContext(true, $sOperation, $bSOAP, $extra)
			);

			//il faut
            $sFromPourLog = $sFrom;

            if (!$bSOAP &&
                isset($extra['http-headers']) &&
                isset($extra['http-headers']['Content-Type']) &&
                (strncmp($extra['http-headers']['Content-Type']->value, 'text/', strlen('text/'))!=0) &&
                (strcmp($extra['http-headers']['Content-Type']->value, 'application/json')!=0)
            ){
                $sFromPourLog = base64_encode($sFrom);
            }

			$this->m_clMonolog->debug(
                $sFromPourLog,
				$this->_getContext(false, $sOperation, $bSOAP, $extra)
			);

			if ($bSOAP)
			{
				$sSeparateur = "\r\n\r\n";
				$nPosT       = strpos($sTo, $sSeparateur);
				$nPosF       = strpos($sFrom, $sSeparateur);

				if ($nPosT)
				{
					$sRequestHeader = substr($sTo, 0, $nPosT);
					$sRequest       = substr($sTo, $nPosT+strlen($sSeparateur));
				}
				else
				{
					$sRequestHeader = '';
					$sRequest       = $sTo;
				}

				if ($nPosF)
				{
					$sResponseHeader = substr($sFrom, 0, $nPosF);
					$sResponse       = substr($sFrom, $nPosF+strlen($sSeparateur));
				}
				else
				{
					$sResponseHeader = '';
					$sResponse       = $sFrom;
				}
			}
			else
			{
				$sRequest  = $sTo;
                $sResponse = $sFrom;

                if (isset($extra['http-headers']) &&
                    isset($extra['http-headers']['Content-Type']) &&
                    (strncmp($extra['http-headers']['Content-Type']->value, 'image/', strlen('image/'))==0)
                ){
                    $sResponse = '<img src="data:'.$extra['http-headers']['Content-Type']->value.';base64,'.$sFromPourLog.'"/>';
                }
			}

			$this->m_TabQueries[] = array(
				'request'         => $sRequest,
				'response'        => $sResponse,
				'request_header'  => $bSOAP ? $sRequestHeader : '',
				'response_header' => $bSOAP ? $sResponseHeader : '',
				'executionMS'     => microtime(true) - $this->m_fStart,
				'sendMS'          => $this->m_fSend,
				'operation'       => $sOperation,
				'soap'            => $bSOAP,
				'xml'             => $bSOAP ? true : false,
			);
		}
	}


	const EXTRA_TokenSession    = 'tokensession';
	const EXTRA_ActionContext   = 'actioncontext';
	const EXTRA_Http_Headers    = 'http-headers';
}
