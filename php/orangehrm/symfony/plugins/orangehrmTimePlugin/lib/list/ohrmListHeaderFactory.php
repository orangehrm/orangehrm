<?php

abstract class ohrmListConfigurationFactory implements ListConfigurationFactory {
    const BEFORE_TABLE = 'before-data';
    const AFTER_TABLE = 'after-data';
    
    const RECORD = 'record';

    protected $headers;
    protected $className = 'stdClass';
    protected $runtimeDefinitions = array();
    protected static $userType;
    protected static $userId;

    public function getHeaders() {
        if (empty($this->headers)) {
            $this->init();
        }

        return $this->headers;
    }

    public function setHeaders(array $headers) {
        $this->headers = $headers;
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
        $this->headers = array();
    }

}
