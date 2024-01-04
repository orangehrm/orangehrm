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

namespace OrangeHRM\Entity\Decorator;

use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\User;
use OrangeHRM\Entity\UserRole;

class UserDecorator
{
    use EntityManagerHelperTrait;

    /**
     * @var User
     */
    private User $user;

    /**
     * @var string|null
     */
    private ?string $nonHashedPassword = null;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    protected function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param int $empNumber
     */
    public function setEmployeeByEmpNumber(int $empNumber): void
    {
        /** @var Employee|null $employee */
        $employee = $this->getReference(Employee::class, $empNumber);
        $this->getUser()->setEmployee($employee);
    }

    /**
     * @param int $id
     */
    public function setUserRoleById(int $id): void
    {
        /** @var UserRole|null $userRole */
        $userRole = $this->getReference(UserRole::class, $id);
        $this->getUser()->setUserRole($userRole);
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->getUser()->getUserRole()->getName() === 'Admin';
    }

    /**
     * @return string|null
     */
    public function getNonHashedPassword(): ?string
    {
        return $this->nonHashedPassword;
    }

    /**
     * @param string|null $nonHashedPassword
     */
    public function setNonHashedPassword(?string $nonHashedPassword): void
    {
        $this->nonHashedPassword = $nonHashedPassword;
    }
}
