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

    /**
     *
     */
    public function __construct()
    {
        $this->filename = null;
        $this->mimetype = null;
        $this->size     = null;
        $this->content  = null;

        $this->no_cache = false;
        $this->last_modified = null;

    }

    /**
     * @param UploadedFile $file
     */
    public function initFromUploadedFile(UploadedFile $file)
    {
        $this->filename     = $file->getClientOriginalName();
        $this->extension    = $file->getClientOriginalExtension();
        $this->mimetype     = $file->getClientMimeType();
        $this->size         = filesize($file->getRealPath());
        $this->content      = file_get_contents($file->getRealPath());
    }

    /**
     * @param UploadedFile $file
     */
    public function initImgFromUploadedBase64Data($dataBase64, $mimetype, $idcolonne)
    {
        //data:image/png;
        $this->mimetype     = substr($dataBase64, 5,strpos($dataBase64,  ';')-5);
        $this->extension = substr($this->mimetype, strpos($this->mimetype, '/')+1);
        $this->content      = file_get_contents($dataBase64);

        $filetemp = tempnam(sys_get_temp_dir(), 'drawing');
        file_put_contents($filetemp, $this->content);

        if ((strpos($mimetype, '/')!==false) && ($mimetype != $this->mimetype)){
            $newExtension = substr($mimetype, strpos($mimetype, '/')+1);

            $srcIsJpeg = preg_match('/jpg|jpeg/i', $this->mimetype);
            $destIsJpeg = preg_match('/jpg|jpeg/i', $newExtension);

            if (!($srcIsJpeg && $destIsJpeg)){

                //il faut convertir l'image
                $im = imagecreatefromstring($this->content);

                if (preg_match('/jpg|jpeg/i', $newExtension)){
                    $res = imagejpeg($im, $filetemp);
                }
                else if (preg_match('/png/i', $newExtension)){
                    $res = imagepng($im, $filetemp);
                }
                else if (preg_match('/gif/i', $newExtension)){
                    $res = imagegif($im, $filetemp);
                }
                else if (preg_match('/bmp/i', $newExtension)){
                    $res = imagebmp($im, $filetemp);
                }

                $this->mimetype = $mimetype;
                $this->extension = $newExtension;
                $this->content = file_get_contents($filetemp);
                imagedestroy($im);
            }


        }

        $this->filename     = $idcolonne.'.'.$this->extension;
        $this->size         = filesize($filetemp);
        unlink($filetemp);
    }

    /**
     * @param HTTPResponse $response
     */
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

    /**
     * @param \SimpleXMLElement $ndData
     * @param $aAttributes
     */
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

        if (strcasecmp($this->mimetype, 'text/html')==0){
            $meta = $this->_getMetaTags($this->content);
            if (array_key_exists('Content-Type', $meta)){
                $this->mimetype = $meta['Content-Type'];
            }
        }

    }

    protected function _getMetaTags($str)
    {
        $pattern = '
  ~<\s*meta\s

  # using lookahead to capture type to $1
    (?=[^>]*?
    \b(?:name|property|http-equiv)\s*=\s*
    (?|"\s*([^"]*?)\s*"|\'\s*([^\']*?)\s*\'|
    ([^"\'>]*?)(?=\s*/?\s*>|\s\w+\s*=))
  )

  # capture content to $2
  [^>]*?\bcontent\s*=\s*
    (?|"\s*([^"]*?)\s*"|\'\s*([^\']*?)\s*\'|
    ([^"\'>]*?)(?=\s*/?\s*>|\s\w+\s*=))
  [^>]*>

  ~ix';

        if(preg_match_all($pattern, $str, $out))
            return array_combine($out[1], $out[2]);
        return array();
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