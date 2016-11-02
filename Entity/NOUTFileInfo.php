<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 02/11/2016
 * Time: 11:43
 */

namespace NOUT\Bundle\ContextsBundle\Entity;


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


    public function initFromUploadedFile(UploadedFile $file)
    {
        $this->filename = $file->getClientOriginalName();
        $this->extension = $file->getClientOriginalExtension();
        $this->mimetype = $file->getClientMimeType();
        $this->size = $file->getClientSize();
        $this->content = file_get_contents($file->getRealPath());
    }

}