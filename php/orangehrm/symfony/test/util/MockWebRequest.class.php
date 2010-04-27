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

class MockWebRequest extends sfWebRequest {

    protected $getParameters = array();

    protected $postParameters = array();

    protected $method;

    /*
     * Methods to set up mock object
     */
    public function setMethod($method) {
        $this->method = strtoupper($method);
    }

    public function setPostParameters(array $postParameters) {
        $this->postParameters = $postParameters;
    }

    public function setGetParameters(array $getParameters) {
        $this->getParameters = $getParameters;
    }

  /**
   * Class constructor.
   *
   * @see initialize()
   */
    public function __construct() {
    }

    protected function fixParameters() {
    }

    public function getRequestContext() {
    }

    protected function parseRequestParameters() {
    }

    public function checkCSRFProtection() {
    }

    public function getForwardedFor() {
    }

    public function getRemoteAddress() {
    }

    public function getUrlParameter($name, $default = null) {
    }

    public function getPostParameter($name, $default = null) {

        $value = $default;
        if (isset($this->postParameters[$name])) {
            $value = $this->postParameters[$name];
        }
        return $value;
    }

    public function getGetParameter($name, $default = null) {
        $value = $default;
        if (isset($this->getParameters[$name])) {
            $value = $this->getParameters[$name];
        }
        return $value;
    }

    public function getFiles($key = null) {
    }

    public function getRequestFormat() {
    }

    public function setRequestFormat($format) {
    }

    public function setFormat($format, $mimeTypes) {
    }

    public function getFormat($mimeType) {
    }

    public function getMimeType($format) {
    }

    public function getPathInfoArray() {
    }

    public function splitHttpAcceptHeader($header) {
    }

    public function setRelativeUrlRoot($value) {
    }

    public function getRelativeUrlRoot() {
    }

    public function isSecure() {
    }

    public function getCookie($name, $defaultValue = null) {
    }

    public function getHttpHeader($name, $prefix = 'http') {
    }

    public function isXmlHttpRequest() {
    }

    public function getAcceptableContentTypes() {
    }

    public function getCharsets() {
    }

    public function getLanguages() {
    }

    public function getPreferredCulture(array $cultures = null) {
    }

    public function getMethodName() {
        return $this->method;
    }

    public function isMethod($method) {
        return strtoupper($method) == $this->method;
    }

    public function getScriptName() {
    }

    public function getHost() {
    }

    public function getReferer() {
    }

    public function addRequestParameters($parameters) {
    }

    public function getRequestParameters() {
        $requestParameters = array_merge($this->getParameters, $this->postParameters);
        return $requestParameters;
    }

    public function getPostParameters() {
        return $this->postParameters;
    }

    public function getGetParameters() {
        return $this->getParameters;
    }

    public function getPathInfoPrefix() {
    }

    public function getPathInfo() {
    }

    public function getUriPrefix() {
    }

    public function isAbsUri() {
    }

    public function getUri() {
    }

    public function initialize(sfEventDispatcher $dispatcher, $parameters = array(), $attributes = array(), $options = array()) {
    }

    public function getParameter($name, $default = null) {
        $value = $default;

        $requestParameters = array_merge($this->getParameters, $this->postParameters);

        if (isset($requestParameters[$name])) {
            $value = $requestParameters[$name];
        }
        return $value;
    }

}
