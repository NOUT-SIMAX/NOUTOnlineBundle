<?php
namespace NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity;

interface SerializableEntity {
    /**
     * @return WSDLEntityDefinition
     */
    static function getEntityDefinition();
}