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
    public $content;
    public $headers;
    public $no_cache;

    public function __construct($content, $headers)
    {
        $this->content = $content;
        $this->headers = $headers;
        $this->no_cache = false;

        if (!is_array($this->headers))
        {
            $this->headers = array();
        }

        $this->_UTF8EncodeOptions();
        $this->headers = $this->_aParseHeaders($this->headers);
    }

    /**
     * @return boolean
     */
    public function isNoCache()
    {
        return $this->no_cache;
    }

    /**
     * @param boolean $no_cache
     */
    public function setNoCache($no_cache)
    {
        $this->no_cache = $no_cache;
    }



    /**
     * @param $aOptions
     * @return array
     */
    protected function _UTF8EncodeOptions()
    {
        foreach($this->headers as $optionName => $option)
        {
            $this->headers[$optionName] = utf8_encode($option);
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
        if (!array_key_exists('Last-Modified', $this->headers))
        {
            //on ajoute le last modified à aujourd'hui
            $this->headers['Last-Modified']=new \stdClass();
            $this->headers['Last-Modified']->value = gmdate('D, d M Y H:i:s T');
            $this->headers['Last-Modified']->options = array();
        }
    }

    public function resetLastModified()
    {
        if (!array_key_exists('Last-Modified', $this->headers))
        {
            //on ajoute le last modified à aujourd'hui
            $this->headers['Last-Modified']=new \stdClass();
            $this->headers['Last-Modified']->options = array();
        }
        $this->headers['Last-Modified']->value = gmdate('D, d M Y H:i:s T');
    }

    /**
     * @return string|null
     */
    public function getLastModified()
    {
        if (array_key_exists('Last-Modified', $this->headers))
        {
            return $this->headers['Last-Modified']->value;
        }

        return null;
    }

    public function getDTLastModified()
    {
        if (array_key_exists('Last-Modified', $this->headers))
        {
            $sLastModified = $this->headers['Last-Modified']->value;
            $sLastModified = str_replace(' GMT', '', $sLastModified);
            return \DateTime::createFromFormat('D, d M Y H:i:s', $sLastModified, new \DateTimeZone("UTC"));
        }

        return new \DateTime('now', new \DateTimeZone("UTC"));
    }

    public function getFilename()
    {
        if (array_key_exists('Content-Disposition', $this->headers))
        {
            $contentDisposition = $this->headers['Content-Disposition'];
            if (array_key_exists('filename', $contentDisposition->options))
            {
                return $contentDisposition->options['filename'];
            }
        }

        return '';
    }

    public function getStatus()
    {
        if(array_key_exists ('status', $this->headers))
        {
            return (int)$this->headers['status']->value;
        }

        return 200;
    }

    public function getContentType()
    {
        if(array_key_exists ('Content-Type', $this->headers))
        {
            return $this->headers['Content-Type']->value;
        }
    }

    public function getContentLength()
    {
        if(array_key_exists ('Content-Length', $this->headers))
        {
            return $this->headers['Content-Length']->value;
        }

    }
}