<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 02/11/2016
 * Time: 11:43
 */

namespace NOUT\Bundle\ContextsBundle\Entity;


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


    public function __construct($filename=null, $content=null, $mimetype=null, $size=0)
    {
        $this->filename = $filename;
        $this->mimetype = $mimetype;
        $this->size     = $size;
        $this->content  = $content;

        $this->no_cache = false;
        $this->last_modified = null;

        if (!empty($filename))
        {
            $info = new \SplFileInfo($filename);
            $this->extension = $info->getExtension();
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