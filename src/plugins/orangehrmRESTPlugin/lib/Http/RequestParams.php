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

    public const PARAM_NAME_SEARCH = 'search';

    /**
     * @var Request
     */
    private ?Request $request = null;

    /**
     * Support `application/json` as post parameters
     * @var array|null
     */
    private ?array $postParameters = null;

    /**
     * RequestParams constructor.
     * @param Request $request
     */
    public function __construct(Request $request) {
        $this->setRequest($request);
        $this->postParameters = $request->getPostParameters();
    }

    /**
     * @return Request
     */
    public function getRequest():Request
    {
        return $this->request;
    }

    /**
     * @param Request $request
     * @return RequestParams
     */
    private function setRequest(Request $request): RequestParams
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @param string $paramName
     * @param mixed $default
     * @return mixed
     */
    public function getQueryParam(string $paramName, $default = null) {

        return $this->getRequest()->getActionRequest()->get($paramName, $default);
    }

    /**
     * @param $paramName
     * @return mixed
     */
    public function getUrlParam(string $paramName) {

       return  $this->getRequest()->getActionRequest()->get($paramName);
    }

    /**
     * get sf request URI
     * @return string
     */
    public function getRequestUri():string {

        return $this->getRequest()->getActionRequest()->getUri();
    }

    /**
     * get post parameters
     *
     * @param string $paramName
     * @return mixed
     */
    public function getPostParam(string $paramName)
    {
        if(!isset($this->postParameters[$paramName])) {
            return null;
        }
        return $this->postParameters[$paramName];
    }

    /**
     * Get all post parameters
     * @return array|null
     */
    public function getPostParams(): ?array
    {
        return $this->postParameters;
    }

    /**
     * Get JSON content
     *
     * @return string
     */
    public function getContent(): string
    {
        return $this->getRequest()->getActionRequest()->getContent();
    }

    /**
     * Override query or URI parameter
     * @param string $name
     * @param $value
     */
    public function setParam(string $name, $value) {
        $this->getRequest()->getActionRequest()->attributes->set($name, $value);
    }

    /**
     * Override post parameter
     * @param string $paramName
     * @param $value
     */
    public function setPostParam(string $paramName, $value) {
        $this->postParameters[$paramName] = $value;
    }
}
