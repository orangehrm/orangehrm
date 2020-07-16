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

namespace Orangehrm\Rest\Http;

class RequestParams {

    const PARAM_NAME_SEARCH = 'search' ;

    /**
     * @var Request
     */
    private $request = null;

    /**
     * Support `application/json` as post parameters
     * @var array|null
     */
    private $postParameters = null;

    /**
     * RequestParams constructor.
     * @param Request $request
     */
    public function __construct(Request $request) {
        $this->setRequest($request);
        if ($request->isJsonHttpRequest()){
            $this->postParameters = json_decode($this->getContent(),true);
        }else{
            // `application/x-www-form-urlencoded` already handled in sfWebRequest
            $this->postParameters = $this->getRequest()->getActionRequest()->getPostParameters();
        }
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Request $request
     * @return $this;
     */
    private function setRequest($request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @param $paramName
     * @param mixed $default
     * @return mixed
     */
    public function getQueryParam($paramName, $default = null) {

        return $this->getRequest()->getActionRequest()->getParameter($paramName, $default);
    }

    /**
     * @param $paramName
     */
    public function getUrlParam($paramName) {

       return  $this->getRequest()->getActionRequest()->getParameter($paramName);
    }

    /**
     * get sf request URI
     */
    public function getRequestUri() {

        return $this->getRequest()->getActionRequest()->getUri();
    }

    /**
     * get post parameters
     *
     * @param string $paramName
     * @return string
     */
    public function getPostParam($paramName)
    {
        return $this->postParameters[$paramName];
    }

    /**
     * Get all post parameters
     * @return array|null
     */
    public function getPostParams()
    {
        return $this->postParameters;
    }

    /**
     * Get JSON content
     *
     * @return json object
     */
    public function getContent()
    {
        return $this->getRequest()->getActionRequest()->getContent();
    }

    /**
     * Override query or URI parameter
     * @param $name
     * @param $value
     */
    public function setParam($name, $value) {
        $this->getRequest()->getActionRequest()->setParameter($name, $value);
    }

    /**
     * Override post parameter
     * @param $paramName
     * @param $value
     */
    public function setPostParam($paramName, $value) {
        $this->postParameters[$paramName] = $value;
    }
}
