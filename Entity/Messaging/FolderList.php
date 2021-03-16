<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 09/08/2017
 * Time: 10:33
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Messaging;



class FolderList
{
    /** @var  Folder $root */
    private $root;

    public function __construct() {
        $this->root = new Folder(-1, "");
    }

    /**
     * @param $id
     * @param $name
     * @param string $parentID
     * @return bool
     */
    public function add($id, string $name, $parentID): bool
    {
        if(empty($parentID)) {
            $folder = new Folder($id, $name);
            $this->root->addChild($folder);
        }
        else {
            try {
                $parent = $this->root->find($parentID);
                $parent->addChild(new Folder($id, $name));
            }
            catch(\Exception $e) {
                return false;
            }
        }
        return true;
    }

    public function toJSON() {
        //TODO: Implement
    }
}