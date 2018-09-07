<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 28/10/2016
 * Time: 11:51
 */

namespace NOUT\Bundle\NOUTOnlineBundle\REST;

abstract class AbstractRestEntity implements RestEntityInterface {
    /** @var array $allowed_options */
    protected $allowed_options;

    /** @var array $options */
    protected $options;

    /** @var array $actionParams */
    private $actionParams;

    /** @var string $restIdentification */
    private $restIdentification;

    /** @var string $host */
    private $host;

    public function __construct($host) {
        $this->host = $host;
        $this->actionParams = array();
        $this->setAllowedOptions();
        $this->options = array();
        $this->restIdentification = '';
    }

    public function setIdentification($restIdentification)
    {
        $this->restIdentification = $restIdentification;

        return $this;
    }

    public function addActionParam($name, $value) {
        $this->actionParams[$name] = $value;

        return $this;
    }

    public function addOption($key, $value) {
        if(!in_array(strtolower($key), array_map('strtolower', $this->allowed_options))) {
            $message = "The option $key is not allowed for " . $this->getRouteName() . ". Allowed options are :";

            foreach($this->allowed_options as $option) {
                $message .= " $option,";
            }

            $message = trim($message, ',');

            throw new \InvalidArgumentException($message);
        }

        $this->options[$key] = $value;

        return $this;
    }

    public function generateRoute() {
        return
            $this->generateBaseRoute() . "?" .
            self::generateRouteOptions($this->actionParams) . ";" .
            self::generateRouteOptions($this->options) .
            $this->restIdentification;
    }

    private function generateBaseRoute() {
        $routeParams = $this->getRouteParams();
        sort($routeParams);

        $uri = $this->host;

        foreach($routeParams as $param) {
            $uri .= "$param/";
        }

        $uri .= $this->getRouteName();

        return $uri;
    }

    private static function generateRouteOptions($options) {
        $uriParams = '';
        foreach($options as $name => $value) {
            $uriParams .= urlencode(utf8_decode($name)) . "=" . urlencode(utf8_decode($value)) . "&";
        }
        trim($uriParams, "&");

        return $uriParams;
    }
}