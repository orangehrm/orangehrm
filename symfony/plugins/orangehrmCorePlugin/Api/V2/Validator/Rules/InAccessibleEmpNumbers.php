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

namespace OrangeHRM\Core\Api\V2\Validator\Rules;

use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\Employee;

class InAccessibleEmpNumbers extends AbstractRule
{
    use UserRoleManagerTrait;
    use AuthUserTrait;

    /**
     * @var bool
     */
    protected bool $includeLoggedInEmpNumber;

    /**
     * @param bool $includeLoggedInEmpNumber
     */
    public function __construct(bool $includeLoggedInEmpNumber = true)
    {
        $this->includeLoggedInEmpNumber = $includeLoggedInEmpNumber;
    }

    /**
     * @param mixed $input
     * @return bool
     */
    public function validate($input): bool
    {
        if (!(is_numeric($input) && $input > 0)) {
            return false;
        }

        if ($this->includeLoggedInEmpNumber && $input == $this->getAuthUser()->getEmpNumber()) {
            return true;
        }
        if (!$this->includeLoggedInEmpNumber && $input == $this->getAuthUser()->getEmpNumber()) {
            return false;
        }

        $accessibleEmpNumbers = $this->getUserRoleManager()->getAccessibleEntityIds(Employee::class);
        return in_array($input, $accessibleEmpNumbers);
    }
}
