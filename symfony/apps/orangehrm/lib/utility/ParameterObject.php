<?php
class ParameterObject {
    private $parameters;

    public function __construct($parameters = array()) {    	        
        $this->parameters = is_array($parameters) ? $parameters : array();
    }

    public function setParameter($name, $value) {
        $this->parameters[$name] = $value;
    }

    public function getParameter($name, $default = null) {
        return array_key_exists($name, $this->parameters) ? $this->parameters[$name] : $default;
    }

}