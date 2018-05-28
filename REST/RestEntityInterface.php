<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 28/10/2016
 * Time: 11:51
 */

namespace NOUT\Bundle\NOUTOnlineBundle\REST;

interface RestEntityInterface {
    function generateRoute();

    function addOption($key, $value);

    function getRouteName();

    function getRouteParams();

    function setAllowedOptions();

    function setIdentification($restIdentification);
}