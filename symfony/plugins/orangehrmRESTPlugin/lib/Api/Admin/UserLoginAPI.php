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

namespace Orangehrm\Rest\Api\Admin;

use Orangehrm\Rest\Api\Admin\Entity\User;
use Orangehrm\Rest\Api\EndPoint;
use Orangehrm\Rest\Api\Exception\BadRequestException;
use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Http\Response;

class UserLoginAPI extends EndPoint
{

    CONST PARAMETER_USERNAME = 'username';
    CONST PARAMETER_PASSWORD = 'password';

    protected $authenticationService;
    protected $loginService;
    protected $systemUserService;

    /**
     *
     * @return \AuthenticationService
     */
    public function getAuthenticationService()
    {
        if (!isset($this->authenticationService)) {
            $this->authenticationService = new \AuthenticationService();
        }
        return $this->authenticationService;
    }

    /**
     * @return \LoginService
     */
    public function getLoginService()
    {
        if (is_null($this->loginService)) {
            $this->loginService = new \LoginService();
        }
        return $this->loginService;
    }

    /**
     * @param mixed $authenticationService
     */
    public function setAuthenticationService($authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    /**
     * @param mixed $loginService
     */
    public function setLoginService($loginService)
    {
        $this->loginService = $loginService;
    }

    /**
     * @return \SystemUserService
     */
    public function getSystemUserService()
    {
        if (empty($this->systemUserService)) {
            $this->systemUserService = new \SystemUserService();
        }
        return $this->systemUserService;
    }

    /**
     * @param $systemUserService
     */
    public function setSystemUserService($systemUserService)
    {
        $this->systemUserService = $systemUserService;
    }

    /**
     * API login
     *
     * @return Response
     * @throws BadRequestException
     * @throws InvalidParamException
     */
    public function userLogin()
    {
        $filters = $this->getFilterParameters();
        $username = $filters[self::PARAMETER_USERNAME];
        $password = $filters[self::PARAMETER_PASSWORD];
        $additionalData = array();

        try {

            $success = $this->getAuthenticationService()->setCredentials($username, $password, $additionalData);

            if ($success) {

                $successResponse = array('login' => $success, 'user' => $this->getUser());

                return new Response($successResponse);

            } else {
                throw new InvalidParamException('Credentials Are Wrong Please Try Again');
            }

        } catch (\AuthenticationServiceException $e) {
            throw new BadRequestException('Login Failed');
        }

    }

    /**
     * Post validation rules
     *
     * @return array
     */
    public function postValidationRules()
    {
        return array(
            self::PARAMETER_USERNAME => array('StringType' => true, 'NotEmpty' => true),
            self::PARAMETER_PASSWORD => array('StringType' => true, 'NotEmpty' => true)
        );
    }

    /**
     * Get filter parameters
     *
     * @return array
     */
    protected function getFilterParameters()
    {
        $filters[] = array();

        $filters[self::PARAMETER_PASSWORD] = $this->getPostParam(self::PARAMETER_PASSWORD, $this->getRequestParams());
        $filters[self::PARAMETER_USERNAME] = $this->getPostParam(self::PARAMETER_USERNAME, $this->getRequestParams());

        return $filters;
    }

    /**
     * Getting post parameters
     *
     * @param $parameterName
     * @param $requestParams
     * @return param
     */
    protected function getPostParam($parameterName, $requestParams)
    {
        if (!empty($requestParams->getPostParam($parameterName))) {
            return $requestParams->getPostParam($parameterName);
        }
        return null;
    }

    /**
     * Get logged user
     *
     * @return array
     */
    protected function getUser()
    {
        $userId = $this->getAuthenticationService()->getLoggedInUserId(); // get logged user id
        $systemUser = $this->getSystemUserService()->getSystemUser($userId); // get logged user
        $apiUser = new User();
        $apiUser->buildUser($systemUser);
        return $apiUser->toArray();
    }

}
