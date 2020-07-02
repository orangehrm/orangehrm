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

class Request{

    protected $actionRequest;

    /**
     * @param \sfWebRequest $request
     */
    public function __construct($request){
        $this->actionRequest = $request;
    }

    /**
     * @return \sfWebRequest
     */
    public function getActionRequest()
    {
        return $this->actionRequest;
    }

    /**
     * @param \sfWebRequest $actionRequest
     */
    public function setActionRequest($actionRequest)
    {
        $this->actionRequest = $actionRequest;
    }

    /**
     * @return string
     */
    public function getMethod(){
        return $this->getActionRequest()->getMethod();
    }

    /**
     * @return array
     */
    public function getAllParameters() {
        return array_merge($this->getActionRequest()->getGetParameters(),
            $this->getActionRequest()->getPostParameters(),
            array('id'=>$this->getActionRequest()->getParameter('id'))
            );

    }
    /**
     * Checks if the request method is the given one.
     *
     * @param  string $method  The method name
     *
     * @return bool true if the current method is the given one, false otherwise
     */
    public function isMethod($method)
    {
        return strtoupper($method) == $this->getActionRequest()->getMethod();
    }

    /**
     * Check whether HTTP request `application/json`
     * @return bool
     */
    public function isJsonHttpRequest()
    {
        return $this->getActionRequest()->getContentType() === 'application/json';
    }
}
