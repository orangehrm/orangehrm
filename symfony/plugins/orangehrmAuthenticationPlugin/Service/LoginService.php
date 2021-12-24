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

namespace OrangeHRM\Authentication\Service;

use OrangeHRM\Admin\Traits\Service\UserServiceTrait;
use OrangeHRM\Authentication\Dao\LoginLogDao;
use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\Exception\ServiceException;
use OrangeHRM\Entity\LoginLog;

class LoginService
{
    use UserServiceTrait;

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
     * @throws ServiceException
     * @throws DaoException
     */
    public function addLogin(UserCredential $credentials): LoginLog
    {
        $user = $this->getUserService()->getCredentials($credentials);
        $loginLog = new LoginLog();
        $loginLog->setUserId($user->getId());
        $loginLog->setUserName($user->getUserName());
        $loginLog->setUserRoleName($user->getUserRole()->getName());
        $loginLog->setUserRolePredefined($user->getUserRole()->isPredefined());
        return $this->getLoginLogDao()->saveLoginLog($loginLog);
    }
}
