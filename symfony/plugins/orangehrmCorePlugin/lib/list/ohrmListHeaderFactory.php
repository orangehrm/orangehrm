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
    protected static $permissions;

    public function getHeaderGroups() {

        $this->__init();
        return $this->headerGroups;
    }

    public function setHeaderGroups(array $headerGroups) {
        $this->headerGroups = $headerGroups;
    }

    /**
     * Checks if group headers should be displayed in report.
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

    /**
     *
     * @return array 
     */
    public function getHeaders() {

        $this->__init();
        $headers = array();

        foreach ($this->headerGroups as $headerGroup) {
            $headers = array_merge($headers, $headerGroup->getHeaders());
        }

        return $headers;
    }

    /**
     *
     * @param array $headers 
     */
    public function setHeaders(array $headers) {
        $this->headerGroups = array(new ListHeaderGroup($headers));
    }

    /**
     *
     * @param string $index
     * @return ListHeader
     * @throws Exception 
     */
    public function getHeader($index) {

        $this->__init();

        $headers = $this->getHeaders();

        if (isset($headers[$index])) {
            return $headers[$index];
        } else {
            throw new Exception('No headers set at index ' . $index);
        }
    }

    /**
     *
     * @return array
     */
    public function getRuntimeDefinitions() {
        return $this->runtimeDefinitions;
    }

    /**
     *
     * @param array $runtimeDefinitions 
     */
    public function setRuntimeDefinitions(array $runtimeDefinitions) {
        $this->runtimeDefinitions = $runtimeDefinitions;
    }

    /**
     *
     * @param type $userType 
     */
    public static function setUserType($userType) {
        self::$userType = $userType;
    }

    /**
     *
     * @param string $userId 
     */
    public static function setUserId($userId) {
        self::$userId = $userId;
    }

    /**
     *
     * @param array $permissions 
     */
    public static function setPermissions($permissions) {
        self::$permissions = $permissions;
    }

    /**
     * Initializes the properties 
     */
    protected function init() {
        $this->headerGroups = array(new ListHeaderGroup(array()));
    }

    /**
     * Property setter
     * 
     * @param string $name
     * @param mixed $value 
     */
    public function __set($name, $value) {
        if ($name === 'headers') {
            $this->setHeaders($value);
        }
    }

    /**
     * Property getter
     * 
     * @param string $name
     * @return mixed 
     */
    public function __get($name) {
        if ($name === 'headers') {
            return $this->getHeaders();
        } else {
            return null;
        }
    }

    /**
     * Init class 
     */
    private function __init() {

        if (!$this->initialized) {
            $this->init();
            $this->initialized = true;
        }
    }

}
