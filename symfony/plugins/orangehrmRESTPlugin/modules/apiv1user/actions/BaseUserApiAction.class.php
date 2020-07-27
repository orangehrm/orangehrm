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

use Orangehrm\Rest\Api\Exception\BadRequestException;

abstract class BaseUserApiAction extends baseRestAction
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
    public function getSystemUserService()
    {
        if (is_null($this->systemUserService)) {
            $this->systemUserService = new SystemUserService();
        }
        return $this->systemUserService;
    }

    /**
     * @param SystemUserService $systemUserService
     */
    public function setSystemUserService(SystemUserService $systemUserService)
    {
        $this->systemUserService = $systemUserService;
    }

    /**
     * @return SystemUser
     * @throws BadRequestException
     * @throws ServiceException
     */
    public function getSystemUser(): SystemUser
    {
        $tokenData = $this->getAccessTokenData();
        $systemUser = $this->getSystemUserService()->getSystemUser($tokenData['user_id']);
        if ($systemUser instanceof \SystemUser) {
            return $systemUser;
        } else {
            throw  new BadRequestException("No Bound User");
        }
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
     * Set logged in user attributes for system user
     * @throws AuthenticationServiceException
     * @throws BadRequestException
     * @throws ServiceException
     */
    public function setUserToContext()
    {
        $systemUser = $this->getSystemUser();
        $this->getAuthenticationService()->setCredentialsForUser($systemUser, []);
        \UserRoleManagerFactory::updateUserRoleManager();
    }

    /**
     * @inheritDoc
     */
    public function verifyAllowedScope()
    {
        $oauthRequest = $this->getOAuthRequest();
        $oauthResponse = $this->getOAuthResponse();
        if (!$this->getOAuthServer()->verifyResourceRequest($oauthRequest, $oauthResponse, Scope::SCOPE_USER)) {
            $oauthResponse->send();
            throw new sfStopException();
        }
    }
}
