<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 28/10/2016
 * Time: 11:51
 */

namespace NOUT\Bundle\NOUTOnlineBundle\REST;


class HTTPResponse
{
    public $httpCode;
    public $content;
    public array $httpHeaders;
    public array $aHeaders;
    public bool $noCache = false;

    /**
     * @param        $content
     * @param string $sHeaders
     */
    public function __construct($httpCode, $content, string $sHeaders, bool $bEncode)
    {
        $this->httpCode = $httpCode;
        $this->content = $content;
        $this->httpHeaders = $this::s_aParseHTTPHeaders($sHeaders); //parse depuis string

        if ($bEncode){
            $this->_UTF8EncodeOptions();
        }
        $this->aHeaders = $this->_aParseHeaders($this->httpHeaders); //reparse pour prendre les infos

        array_pop($this->httpHeaders); //on enlève la ligne de status
    }

    /**
     * @return boolean
     */
    public function isNoCache()
    {
        return $this->noCache;
    }

    /**
     * @param boolean $noCache
     */
    public function setNoCache($noCache)
    {
        $this->noCache = $noCache;
    }



    /**
     * @param $aOptions
     * @return array
     */
    protected function _UTF8EncodeOptions()
    {
        foreach($this->httpHeaders as $optionName => $option)
        {
            $this->httpHeaders[$optionName] = utf8_encode($option);
        }
    }

    /**
     * Parse a set of HTTP headers
     *
     * @param array     // The php headers to be parsed
     * @param [string]  // The name of the header to be retrieved
     * @return          // A header value if a header is passed
     *                  // An array with all the headers otherwise
     */
    protected function _aParseHeaders(array $headers, $header = null)
    {
        if(empty($headers))
        {
            return array();
        }

        $output = array();

        if ('HTTP' === substr($headers[0], 0, 4))
        {
            list(, $output['status'], $output['status_text']) = explode(' ', $headers[0]);
            unset($headers[0]);
        }
        elseif ((count($headers)==1) && (empty($headers[0]))){
            return array();
        }

        foreach ($headers as $v)
        {
            $h = preg_split('/:\s*/', $v);
            $output[$h[0]] = $h[1];
        }

        if (null !== $header)
        {
            if (isset($output[$header]))
            {
                return $output[$header];
            }
            return '';
        }

        // Parser les options
        foreach ($output as $headerKey => $headerValue)
        {
            $pattern = '/(?:.+(?:=".*")?[;$])|(?:.+(?:=".*")$)|(?:.+(?:=.*)?)$/U';
            preg_match_all($pattern, $headerValue, $options);
            if(is_array($options[0]))
            {
                $options = array_map(
                    function($value) {
                        return rtrim($value, ";");
                    },
                    $options[0]
                );
            }

            // if(count($options) > 1)
            {
                $headerWithOptions          = new \stdClass();
                $headerWithOptions->value   = array_shift($options); // Retire le premier élément
                $headerWithOptions->options = array();

                foreach ($options as $optionKey => $optionValue)
                {
                    $split = explode('=', $optionValue);
                    $cleanOption = str_replace('"', "", $split[1]); // Retirer les quotes
                    $headerWithOptions->options[$split[0]] = $cleanOption;
                }

                $output[$headerKey] = $headerWithOptions;
            }
        }

        return $output;
    }

    public function setLastModifiedIfNotExists()
    {
        if (!array_key_exists(self::HEADER_LastModified, $this->aHeaders))
        {
            //on ajoute le last modified à aujourd'hui
            $this->aHeaders[self::HEADER_LastModified]=new \stdClass();
            $this->aHeaders[self::HEADER_LastModified]->value = gmdate('D, d M Y H:i:s T');
            $this->aHeaders[self::HEADER_LastModified]->options = array();
        }
    }

    public function resetLastModified()
    {
        if (!array_key_exists(self::HEADER_LastModified, $this->aHeaders))
        {
            //on ajoute le last modified à aujourd'hui
            $this->aHeaders[self::HEADER_LastModified]=new \stdClass();
            $this->aHeaders[self::HEADER_LastModified]->options = array();
        }
        $this->aHeaders[self::HEADER_LastModified]->value = gmdate('D, d M Y H:i:s T');
    }

    /**
     * @return string|null
     */
    public function getLastModified()
    {
        if (array_key_exists(self::HEADER_LastModified, $this->aHeaders))
        {
            return $this->aHeaders[self::HEADER_LastModified]->value;
        }

        return null;
    }

    public function getDTLastModified()
    {
        if (array_key_exists(self::HEADER_LastModified, $this->aHeaders))
        {
            $sLastModified = $this->aHeaders[self::HEADER_LastModified]->value;
            $sLastModified = str_replace(' GMT', '', $sLastModified);
            return \DateTime::createFromFormat('D, d M Y H:i:s', $sLastModified, new \DateTimeZone("UTC"));
        }

        return new \DateTime('now', new \DateTimeZone("UTC"));
    }

    public function getFilename()
    {
        if (array_key_exists(self::HEADER_ContentDisposition, $this->aHeaders))
        {
            $header = $this->aHeaders[self::HEADER_ContentDisposition];
            if (array_key_exists(self::OPTION_filename, $header->options))
            {
                return $header->options[self::OPTION_filename];
            }
        }

        return '';
    }

    public function getStatus()
    {
        if(array_key_exists (self::HEADER_Status, $this->aHeaders))
        {
            return (int)$this->aHeaders[self::HEADER_Status]->value;
        }

        return 200;
    }

    public function getContentType()
    {
        if(array_key_exists (self::HEADER_ContentType, $this->aHeaders))
        {
            return $this->aHeaders[self::HEADER_ContentType]->value;
        }
    }

    public function getContentLength()
    {
        if(array_key_exists (self::HEADER_ContentLength, $this->aHeaders))
        {
            return $this->aHeaders[self::HEADER_ContentLength]->value;
        }
    }

    public function getXNOUTOnlineInfoCnx()
    {
        if(array_key_exists (self::HEADER_XNOUTOnlineInfoCnx, $this->aHeaders))
        {
            return $this->aHeaders[self::HEADER_XNOUTOnlineInfoCnx]->value;
        }
    }

    public function getIVForInfoCnx()
    {
        if (array_key_exists(self::HEADER_XNOUTOnlineInfoCnx, $this->aHeaders))
        {
            $header = $this->aHeaders[self::HEADER_XNOUTOnlineInfoCnx];
            if (array_key_exists(self::OPTION_iv, $header->options))
            {
                return $header->options[self::OPTION_iv];
            }
        }
    }

    /**
     * Parse les entêtes pour fournir une sortie au format natif
     *
     * @param string $response
     * @return array
     * @throws \Exception
     */
    public static function s_aParseHTTPHeaders(string $response): array
    {
        $headers = [];

        $headerText = substr($response, 0, strpos($response, "\r\n\r\n"));

        foreach (explode("\r\n", $headerText) as /*$i =>*/ $line) {
            array_push($headers, $line);
        }

        return $headers;
    }

    protected const HEADER_LastModified = 'Last-Modified';
    protected const HEADER_ContentDisposition = 'Content-Disposition';
    protected const HEADER_ContentType = 'Content-Type';
    protected const HEADER_ContentLength = 'Content-Length';
    protected const HEADER_Status = 'status';
    protected const HEADER_XNOUTOnlineInfoCnx = 'X-NOUTOnline-InfoCnx';

    protected const OPTION_filename = 'filename';
    protected const OPTION_iv = 'iv';
}
