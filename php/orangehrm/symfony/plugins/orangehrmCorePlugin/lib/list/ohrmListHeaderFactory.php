<?php

abstract class ohrmListConfigurationFactory implements ListConfigurationFactory {
    const BEFORE_TABLE = 'before-data';
    const AFTER_TABLE = 'after-data';
    
    const RECORD = 'record';

    protected $headerGroups;
    protected $className = 'stdClass';
    protected $runtimeDefinitions = array();
    protected $initialized = false;
    protected static $userType;
    protected static $userId;

    public function getHeaderGroups() {
        
        $this->__init();
        return $this->headerGroups;
    }
    
    public function setHeaderGroups(array $headerGroups) {
        $this->headerGroups = $headerGroups;
    }
    
    /**
     * Checks if group headers should be displayed in report.
     * 
     * Returns true if at least one header group needs to be displayed.
     * 
     * @return type bool true if group headers need to be displayed, false if not.
     */
    public function showGroupHeaders() {        
        $this->__init();
        
        foreach ($this->headerGroups as $group) {
            
            if ($group->showHeader()) {
                return true;
            }
        }
        return false;
    }
    
    public function getHeaders() {
        
        $this->__init();
        $headers = array();

        foreach ($this->headerGroups as $headerGroup) {
            $headers = array_merge($headers, $headerGroup->getHeaders());
        }

        return $headers;
    }

    public function setHeaders(array $headers) {
        $this->headerGroups = array(new ListHeaderGroup($headers));
    }

    public function getHeader($index) {

        $this->__init();
        
        $headers = $this->getHeaders();

        if (isset($headers[$index])) {
            return $headers[$index];
        } else {
            throw new Exception('No headers set at index ' . $index);
        }
    }

    public function getRuntimeDefinitions() {
        return $this->runtimeDefinitions;
    }
    
    public function setRuntimeDefinitions(array $runtimeDefinitions) {
        $this->runtimeDefinitions = $runtimeDefinitions;
    }

    public static function setUserType($userType) {
        self::$userType = $userType;
    }

    public static function setUserId($userId) {
        self::$userId = $userId;
    }

    protected function init() {
        $this->headerGroups = array(new ListHeaderGroup(array()));
    }
    
    public function __set($name, $value) {
        if ($name === 'headers') {
            $this->setHeaders($value);
        }
    }
    
    public function __get($name) {
        if ($name === 'headers') {
            return $this->getHeaders();
        } else {
            return null;
        }
    }
    
    private function __init() {
        
        if (!$this->initialized) {
            $this->init();
            $this->initialized = true;
        }
    }

}
