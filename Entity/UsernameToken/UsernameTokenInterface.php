<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Entity\UsernameToken;


interface UsernameTokenInterface
{
    /**
     * si le username token est valide
     * @return bool
     */
    public function bIsValid() : bool;

    /**
     * Crypte les différents éléments
     */
    public function Compute() : void;

    /**
     * pour la serialisation
     * @return array
     */
    public function forSerialization() : array;

    /**
     * pour l'init suivant à la deserialization
     * @param array $data
     */
    public function fromSerialization(array $data) : void;
}