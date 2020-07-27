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

use OAuth2\GrantType\RefreshToken;
use OAuth2\RequestInterface;
use OAuth2\ResponseInterface;

class OhrmRefreshToken extends RefreshToken
{
    /**
     * @var null|SystemUserService
     */
    private $systemUserService = null;

    /**
     * @var null|AuthenticationService
     */
    private $authenticationService = null;

    /**
     * Get system user service
     *
     * @return SystemUserService
     */
    public function getSystemUserService(): SystemUserService
    {
        if (is_null($this->systemUserService)) {
            $this->systemUserService = new SystemUserService();
        }
        return $this->systemUserService;
    }

    /**
     * @return AuthenticationService
     */
    public function getAuthenticationService(): AuthenticationService
    {
        if (is_null($this->authenticationService)) {
            $this->authenticationService = new AuthenticationService();
        }
        return $this->authenticationService;
    }

    /**
     * @inheritDoc
     */
    public function validateRequest(RequestInterface $request, ResponseInterface $response)
    {
        $basicValidation = parent::validateRequest($request, $response);
        if (!$basicValidation) {
            return $basicValidation;
        }

        $systemUser = $this->getSystemUserService()->getSystemUser($this->getUserId());
        if ($systemUser instanceof SystemUser) {
            $this->getAuthenticationService()->setCredentialsForUser($systemUser, []);
        } else {
            $response->setError(HttpResponseCode::HTTP_BAD_REQUEST, 'invalid_request', 'No Bound User');
            return null;
        }

        return true;
    }
}
