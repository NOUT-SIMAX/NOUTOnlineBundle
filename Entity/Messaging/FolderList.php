<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 09/08/2017
 * Time: 10:33
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Messaging;


use Ratchet\Wamp\Exception;

class FolderList
{
    /** @var  Folder $root */
    private $root;

    public function __construct() {
        $this->root = new Folder(-1, "");
    }

    public function add($id, $name, $parentID) {
        if(empty($parentID)) {
            $folder = new Folder($id, $name);
            $this->root->addChild($folder);
        }
        else {
            try {
                $parent = $this->root->find($parentID);
                $parent->addChild(new Folder($id, $name));
            }
            catch(Exception $e) {
                return false;
            }
        }
        return true;
    }

    public function toJSON() {
        //TODO: Implement
    }
}