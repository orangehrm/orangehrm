<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
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
