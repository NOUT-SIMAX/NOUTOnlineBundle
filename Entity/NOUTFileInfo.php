<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 02/11/2016
 * Time: 11:43
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity;


use NOUT\Bundle\NOUTOnlineBundle\REST\HTTPResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class NOUTFileInfo
{
    /**
     * @var string
     */
    public $filename;

    /**
     * @var string
     */
    public $extension;


    /**
     * @var string
     */
    public $mimetype;


    /**
     * @var string
     */
    public $content;


    /**
     * @var integer
     */
    public $size;

    /**
     * @var boolean
     */
    protected $no_cache;

    /**
     * @var
     */
    public $last_modified;


    public function __construct()
    {
        $this->filename = null;
        $this->mimetype = null;
        $this->size     = null;
        $this->content  = null;

        $this->no_cache = false;
        $this->last_modified = null;

    }


    public function initFromUploadedFile(UploadedFile $file)
    {
        $this->filename     = $file->getClientOriginalName();
        $this->extension    = $file->getClientOriginalExtension();
        $this->mimetype     = $file->getClientMimeType();
        $this->size         = $file->getClientSize();
        $this->content      = file_get_contents($file->getRealPath());

        //$file->getCTime();
    }

    public function initFromHTTPResponse(HTTPResponse $response)
    {
        $this->filename = $response->getFilename();

        $info = new \SplFileInfo($this->filename);
        $this->extension = $info->getExtension();

        $this->mimetype = $response->getContentType();
        $this->size = $response->getContentLength();

        $this->content = $response->content;
        $this->last_modified = $response->getLastModified();
    }

    public function initFromDataTag(\SimpleXMLElement $ndData, $aAttributes)
    {
        $this->mimetype = (string)$aAttributes['typemime'];
        $this->filename = basename((string)$aAttributes['filename']);
        $this->size = (int)$aAttributes['size'];

        $info = new \SplFileInfo($this->filename);
        $this->extension = $info->getExtension();

        $encoding = (string)$aAttributes['encoding'];
        if ($encoding == 'base64')
        {
            $this->content = base64_decode((string)$ndData);
        }
        else
        {
            $this->content = (string)$ndData;
        }
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



    public function getFilename()
    {
        return $this->filename;
    }

    public function getContentType()
    {
        return $this->mimetype;
    }

    public function getContentLength()
    {
        return $this->size;
    }

    public function setLastModifiedIfNotExists()
    {
        if (empty($this->last_modified))
        {
            $this->last_modified = gmdate('D, d M Y H:i:s T');
        }
    }


    public function resetLastModified()
    {
        $this->last_modified = gmdate('D, d M Y H:i:s T');
    }

    public function getDTLastModified()
    {
        if (!empty($this->last_modified))
        {
            $sLastModified = str_replace(' GMT', '', $this->last_modified);
            return \DateTime::createFromFormat('D, d M Y H:i:s', $sLastModified, new \DateTimeZone("UTC"));
        }

        return new \DateTime('now', new \DateTimeZone("UTC"));
    }

}