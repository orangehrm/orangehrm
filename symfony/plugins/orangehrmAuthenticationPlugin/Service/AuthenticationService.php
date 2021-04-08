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

namespace OrangeHRM\SecurityAuthentication\Service;

use OrangeHRM\Admin\Service\SystemUserService;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\SystemUser;
use OrangeHRM\SecurityAuthentication\Dto\UserCredential;
use OrangeHRM\SecurityAuthentication\Exception\AuthenticationServiceException;

class AuthenticationService
{
    /**
     * @var null|SystemUserService
     */
    private ?SystemUserService $systemUserService = null;

    /**
     * @param SystemUserService $service
     */
    public function setSystemUserService(SystemUserService $service)
    {
        $this->systemUserService = $service;
    }

    /**
     * @return SystemUserService
     */
    public function getSystemUserService(): SystemUserService
    {
        if (!isset($this->systemUserService)) {
            $this->systemUserService = new SystemUserService();
        }
        return $this->systemUserService;
    }

    /**
     * @param SystemUser|null $user
     * @param array $additionalData
     * @return bool
     * @throws AuthenticationServiceException
     */
    public function setCredentialsForUser(?SystemUser $user, array $additionalData):bool
    {
        if (!$user instanceof SystemUser) {
            return false;
        } else {
            if ($user->getIsAdmin() == 'No' &&
                !$user->getEmployee() instanceof Employee &&
                $user->getEmployee()->getEmpNumber() == '') {
                throw new AuthenticationServiceException('Employee not assigned');
            } elseif (!is_null($user->getEmployee()->getEmployeeTerminationRecord())) {
                throw new AuthenticationServiceException('Employee is terminated');
            } elseif ($user->getStatus() == 0) {
                throw new AuthenticationServiceException('Account disabled');
            }

            return true;
        }
    }

    /**
     * @param UserCredential $credentials
     * @param $additionalData
     * @return bool
     * @throws AuthenticationServiceException
     * @throws \OrangeHRM\Core\Exception\DaoException
     * @throws \OrangeHRM\Core\Exception\ServiceException
     */
    public function setCredentials(UserCredential $credentials, $additionalData):bool
    {
        $user = $this->getSystemUserService()->getCredentials($credentials);
        return $this->setCredentialsForUser($user, $additionalData);
    }
}
