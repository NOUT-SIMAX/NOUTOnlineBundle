<?php

/**
 * Created by PhpStorm.
 * User: simon
 * Date: 09/08/2017
 * Time: 10:02
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Messaging;

class Folder
{
    /** @var  int $id */
    private $id;
    /** @var  string $name */
    private $name;
    /** @var Folder[] $children */
    private $children;

    public function __construct($id, $name){
        $this->id = $id;
        $this->name = $name;
        $this->children = array();
    }

    public function addChild(Folder $folder) {
        array_push($this->children, $folder);
    }

    public function find($id) {
        if($id == $this->id) {
            return $this;
        }
        foreach($this->children as $child) {
            try {
                $folder = $child->find($id);
                return $folder;
            }
            catch(\RuntimeException $e) {
                continue;
            }
        }
        throw new \RuntimeException("Folder not found"); //TODO: Proper exception
    }

    public function toJSON() {

    }
}