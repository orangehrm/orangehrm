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
use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Authentication\Exception\AuthenticationException;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\Exception\ServiceException;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\User;

class AuthenticationService
{
    use AuthUserTrait;
    use UserServiceTrait;

    /**
     * @param User|null $user
     * @param array $additionalData
     * @return bool
     * @throws AuthenticationException
     */
    public function setCredentialsForUser(?User $user, array $additionalData): bool
    {
        if (!$user instanceof User) {
            return false;
        } else {
            if (!$user->getDecorator()->isAdmin() && is_null($user->getEmpNumber())) {
                throw AuthenticationException::employeeNotAssigned();
            } elseif ($user->getEmployee() instanceof Employee &&
                !is_null($user->getEmployee()->getEmployeeTerminationRecord())
            ) {
                throw AuthenticationException::employeeTerminated();
            } elseif (!$user->getStatus()) {
                throw AuthenticationException::userDisabled();
            }

            $this->setUserAttributes($user);

            return true;
        }
    }

    /**
     * @param UserCredential $credentials
     * @param $additionalData
     * @return bool
     * @throws AuthenticationException
     * @throws DaoException
     * @throws ServiceException
     */
    public function setCredentials(UserCredential $credentials, $additionalData): bool
    {
        $user = $this->getUserService()->getCredentials($credentials);
        return $this->setCredentialsForUser($user, $additionalData);
    }

    /**
     * @param User $user
     */
    protected function setUserAttributes(User $user): void
    {
        $this->getAuthUser()->setUserId($user->getId());
        $this->getAuthUser()->setUserRoleId($user->getUserRole()->getId());
        $this->getAuthUser()->setUserRoleName($user->getUserRole()->getName());
        if ($user->getEmployee() instanceof Employee) {
            $this->getAuthUser()->setEmpNumber($user->getEmployee()->getEmpNumber());
        }
    }
}
