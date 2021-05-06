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

use OrangeHRM\Admin\Service\UserService;
use OrangeHRM\Authentication\Auth\User as AuthUser;
use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Authentication\Exception\AuthenticationServiceException;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\Exception\ServiceException;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\User;

class AuthenticationService
{
    /**
     * @var null|UserService
     */
    private ?UserService $systemUserService = null;

    /**
     * @param UserService $service
     */
    public function setSystemUserService(UserService $service)
    {
        $this->systemUserService = $service;
    }

    /**
     * @return UserService
     */
    public function getSystemUserService(): UserService
    {
        if (!isset($this->systemUserService)) {
            $this->systemUserService = new UserService();
        }
        return $this->systemUserService;
    }

    /**
     * @param User|null $user
     * @param array $additionalData
     * @return bool
     * @throws AuthenticationServiceException
     */
    public function setCredentialsForUser(?User $user, array $additionalData): bool
    {
        if (!$user instanceof User) {
            return false;
        } else {
            if ($user->getIsAdmin() == 'No' &&
                !$user->getEmployee() instanceof Employee &&
                $user->getEmployee()->getEmpNumber() == '') {
                throw new AuthenticationServiceException('Employee not assigned');
            } elseif ($user->getEmployee() instanceof Employee && !is_null(
                    $user->getEmployee()->getEmployeeTerminationRecord()
                )) {
                throw new AuthenticationServiceException('Employee is terminated');
            } elseif ($user->getStatus() == 0) {
                throw new AuthenticationServiceException('Account disabled');
            }

            $this->setUserAttributes($user);

            return true;
        }
    }

    /**
     * @param UserCredential $credentials
     * @param $additionalData
     * @return bool
     * @throws AuthenticationServiceException
     * @throws DaoException
     * @throws ServiceException
     */
    public function setCredentials(UserCredential $credentials, $additionalData): bool
    {
        $user = $this->getSystemUserService()->getCredentials($credentials);
        return $this->setCredentialsForUser($user, $additionalData);
    }

    /**
     * @param User $user
     */
    protected function setUserAttributes(User $user): void
    {
        AuthUser::getInstance()->setUserId($user->getId());
        AuthUser::getInstance()->setUserRoleId($user->getUserRole()->getId());
        AuthUser::getInstance()->setUserRoleName($user->getUserRole()->getName());
        if ($user->getEmployee() instanceof Employee) {
            AuthUser::getInstance()->setEmpNumber($user->getEmployee()->getEmpNumber());
        }
    }

    /**
     * @return int|null
     */
    public function getLoggedInUserId(): ?int
    {
        return AuthUser::getInstance()->getUserId();
    }

    /**
     * @return User|null
     * @throws ServiceException
     */
    public function getLoggedInUser(): ?User
    {
        return $this->getSystemUserService()->getSystemUser($this->getLoggedInUserId());
    }
}
