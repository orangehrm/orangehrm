<?php

/**
 * OrangeHRM Enterprise is a closed sourced comprehensive Human Resource Management (HRM)
 * System that captures all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM Inc is the owner of the patent, copyright, trade secrets, trademarks and any
 * other intellectual property rights which subsist in the Licensed Materials. OrangeHRM Inc
 * is the owner of the media / downloaded OrangeHRM Enterprise software files on which the
 * Licensed Materials are received. Title to the Licensed Materials and media shall remain
 * vested in OrangeHRM Inc. For the avoidance of doubt title and all intellectual property
 * rights to any design, new software, new protocol, new interface, enhancement, update,
 * derivative works, revised screen text or any other items that OrangeHRM Inc creates for
 * Customer shall remain vested in OrangeHRM Inc. Any rights not expressly granted herein are
 * reserved to OrangeHRM Inc.
 *
 * Please refer http://www.orangehrm.com/Files/OrangeHRM_Commercial_License.pdf for the license which includes terms and conditions on using this software.
 *
 */ 

class WSRequestParameters {

    protected $requestMethod;
    protected $method;
    protected $parameters;
    protected $appId;
    protected $appToken;
    protected $sessionToken;

    protected $wrapperObject;
    /**
     *
     * @return string
     */
    public function getWrapperObject() {
        return $this->wrapperObject;
    }

    /**
     *
     * @param string $wrapperObject 
     */
    public function setWrapperObject($wrapperObject) {
        $this->wrapperObject = $wrapperObject;
    }
    
    /**
     *
     * @return string
     */
    public function getRequestMethod() {
        return $this->requestMethod;
    }

    /**
     *
     * @param string $requestMethod 
     */
    public function setRequestMethod($requestMethod) {
        $this->requestMethod = $requestMethod;
    }

    /**
     *
     * @return string
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     *
     * @param string $method 
     */
    public function setMethod($method) {
        $this->method = $method;
    }

    /**
     *
     * @return array
     */
    public function getParameters() {
        return $this->parameters;
    }

    /**
     *
     * @param array $parameters 
     */
    public function setParameters(array $parameters) {
        $this->parameters = $parameters;
    }

    /**
     *
     * @return int
     */
    public function getAppId() {
        return $this->appId;
    }

    /**
     *
     * @param int $appId 
     */
    public function setAppId($appId) {
        $this->appId = $appId;
    }

    /**
     *
     * @return string
     */
    public function getAppToken() {
        return $this->appToken;
    }

    /**
     *
     * @param string $appToken 
     */
    public function setAppToken($appToken) {
        $this->appToken = $appToken;
    }

    /**
     *
     * @return string
     */
    public function getSessionToken() {
        return $this->sessionToken;
    }

    /**
     *
     * @param string $sessionToken 
     */
    public function setSessionToken($sessionToken) {
        $this->sessionToken = $sessionToken;
    }

}
