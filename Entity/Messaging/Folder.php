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

    /**
     * Folder constructor.
     * @param $id
     * @param string $name
     */
    public function __construct($id, string $name){
        $this->id = $id;
        $this->name = $name;
        $this->children = array();
    }

    /**
     * @param Folder $folder
     */
    public function addChild(Folder $folder) {
        array_push($this->children, $folder);
    }

    /**
     * @param $id
     * @return $this
     */
    public function find($id): Folder
    {
        if($id == $this->id) {
            return $this;
        }
        foreach($this->children as $child) {
            try {
                return $child->find($id);
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