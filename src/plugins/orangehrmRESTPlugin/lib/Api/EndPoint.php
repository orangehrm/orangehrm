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

namespace Orangehrm\Rest\Api;

use Orangehrm\Rest\Http\Request;
use Orangehrm\Rest\Http\RequestParams;

class EndPoint {

    /**
     * @var Request
     */
    protected $request = null;

    /**
     * @var RequestParams
     */
    protected $requestParams = null ;

    /**
     *
     * @param Request $request
     */
    public function __construct(Request $request) {
        $this->setRequest($request);
        $this->setRequestParams(new RequestParams($request));
    }

    /**
     * @return RequestParams
     */
    public function getRequestParams() {
        return $this->requestParams;
    }

    /**
     * @param RequestParams $requestParams
     */
    public function setRequestParams($requestParams) {
        $this->requestParams = $requestParams;
    }

    /**
     * @return Request
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     * @param Request $request
     */
    public function setRequest($request) {
        $this->request = $request;
    }
}
