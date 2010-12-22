<?php
class ParameterObject {
    private $parameters;

    public function __construct($parameters) {
    	
        $this->parameters = empty($parameters) ? array() : $parameters;
    }

    public function setParameter($name, $value) {
        $this->parameters[$name] = $value;
    }

    public function getParameter($name) {
        return array_key_exists($name, $this->parameters) ? $this->parameters[$name] : null;
    }

}