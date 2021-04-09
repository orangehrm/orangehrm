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

namespace OrangeHRM\Authentication\Api;

use LoginService;
use Orangehrm\Rest\Api\EndPoint;
use Orangehrm\Rest\Http\Response;
use OrangeHRM\Authentication\Auth\User;
use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Authentication\Service\AuthenticationService;

class LoginAPI extends EndPoint
{
    public const PARAMETER_USERNAME = 'username';
    public const PARAMETER_PASSWORD = 'password';

    protected ?LoginService $loginService = null;
    protected ?AuthenticationService $authenticationService = null;

    public function getLoginService()
    {
        if (is_null($this->loginService)) {
            $this->loginService = new LoginService();
        }
        return $this->loginService;
    }

    /**
     * @return AuthenticationService
     */
    public function getAuthenticationService(): ?AuthenticationService
    {
        if (!isset($this->authenticationService)) {
            $this->authenticationService = new AuthenticationService();
        }
        return $this->authenticationService;
    }

    public function login(): Response
    {
        $username = $this->getRequestParams()->getPostParam(self::PARAMETER_USERNAME, '');
        $password = $this->getRequestParams()->getPostParam(self::PARAMETER_PASSWORD, '');

        $additionalData = [
            //'timeZoneOffset' => $request->getParameter('hdnUserTimeZoneOffset', 0),
        ];

        $credentials = new UserCredential($username, $password);
        $success = $this->getAuthenticationService()->setCredentials($credentials, $additionalData);
        User::getInstance()->setIsAuthenticated($success);

        return new Response(['success' => $success]);
    }
}
