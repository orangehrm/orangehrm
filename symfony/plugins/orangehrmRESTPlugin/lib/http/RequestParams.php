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

use Orangehrm\Rest\Http\Request;

class RequestParams {

    const PARAM_NAME_SEARCH = 'search' ;

    /**
     * @var Request
     */
    private $request = null;

    /**
     * RequestParams constructor.
     * @param \Orangehrm\Rest\Http\Request $request
     */
    public function __construct( Request $request) {
        $this->setRequest($request);
    }

    /**
     * @return \Orangehrm\Rest\Http\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param \Orangehrm\Rest\Http\Request $request
     * @return $this;
     */
    private function setRequest($request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @param $paramName
     */
    public function getQueryParam($paramName) {

        return $this->getRequest()->getActionRequest()->getParameter($paramName);
    }

    /**
     * @param $paramName
     */
    public function getUrlParam($paramName) {

       return  $this->getRequest()->getActionRequest()->getParameter($paramName);
    }

    /**
     * get sf request URI
     *
     * @param $paramName
     */
    public function getRequestUri() {

        return $this->getRequest()->getActionRequest()->getUri();
    }

    /**
     * get post parameters
     *
     * @param $paramName
     */
    public function getPostParam($paramName)
    {

        return $this->getRequest()->getActionRequest()->getPostParameter($paramName);
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
}
