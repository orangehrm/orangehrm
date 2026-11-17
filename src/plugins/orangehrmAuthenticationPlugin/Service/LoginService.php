<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Authentication\Service;

use OrangeHRM\Admin\Traits\Service\UserServiceTrait;
use OrangeHRM\Authentication\Dao\LoginLogDao;
use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Entity\LoginLog;
use OrangeHRM\Entity\User;
use OrangeHRM\Framework\Http\RequestStack;
use OrangeHRM\Framework\Services;
use Throwable;

class LoginService
{
    use UserServiceTrait;
    use ServiceContainerTrait;

    /**
     * @var LoginLogDao|null
     */
    private ?LoginLogDao $loginLogDao = null;

    /**
     * @return LoginLogDao|null
     */
    public function getLoginLogDao(): ?LoginLogDao
    {
        if (!($this->loginLogDao instanceof LoginLogDao)) {
            $this->loginLogDao = new LoginLogDao();
        }
        return $this->loginLogDao;
    }

    /**
     * @param UserCredential $credentials
     * @return LoginLog
     */
    public function addLogin(UserCredential $credentials): LoginLog
    {
        $user = $this->getUserService()
            ->geUserDao()
            ->getUserByUserName($credentials->getUsername());
        $loginLog = new LoginLog();
        $loginLog->setUserId($user->getId());
        $loginLog->setUserName($user->getUserName());
        $loginLog->setUserRoleName($user->getUserRole()->getName());
        $loginLog->setUserRolePredefined($user->getUserRole()->isPredefined());
        $loginLog->setBaseUrl($this->getCurrentBaseUrl());
        return $this->getLoginLogDao()->saveLoginLog($loginLog);
    }

    /**
     * @param User $user
     * @return LoginLog
     */
    public function addOIDCLogin(User $user): LoginLog
    {
        $loginLog = new LoginLog();
        $loginLog->setUserId($user->getId());
        $loginLog->setUserName($user->getUserName());
        $loginLog->setUserRoleName($user->getUserRole()->getName());
        $loginLog->setUserRolePredefined($user->getUserRole()->isPredefined());
        $loginLog->setBaseUrl($this->getCurrentBaseUrl());
        return $this->getLoginLogDao()->saveLoginLog($loginLog);
    }

    /**
     * Base URL of the current web request, e.g. https://hr.example.com/web/index.php:
     * scheme, host, port (when not the default) and the path to the front controller.
     * Null outside a web request, or when the request host is not a valid host name.
     *
     * @return string|null
     */
    public function getCurrentBaseUrl(): ?string
    {
        if (!$this->getContainer()->has(Services::REQUEST_STACK)) {
            return null;
        }
        /** @var RequestStack $requestStack */
        $requestStack = $this->getContainer()->get(Services::REQUEST_STACK);
        $request = $requestStack->getMainRequest();
        if ($request === null) {
            return null;
        }
        try {
            $baseUrl = $request->getSchemeAndHttpHost() . $request->getBaseUrl();
        } catch (Throwable $e) {
            // e.g. Symfony refuses a Host header that is not a valid host name
            return null;
        }
        return strlen($baseUrl) <= LoginLog::BASE_URL_MAX_LENGTH ? $baseUrl : null;
    }
}
