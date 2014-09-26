<?php

class DatabaseSessionManager {

    protected $sessionVariables = array();

    public function setSessionVariables(array $sessionVariables) {
        $this->sessionVariables = $sessionVariables;
    }

    public function  registerVariables() {
        $conn = Doctrine_Manager::getInstance()->getCurrentConnection();
        foreach ($this->sessionVariables as $key => $value) {
            $conn->execute("SET @{$key} = '{$value}';");
        }
    }
    
}

