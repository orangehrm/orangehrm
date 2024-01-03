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

namespace OrangeHRM\Core\Report\DisplayField\Personal\Dto;

use OrangeHRM\Core\Report\DisplayField\Stringable;
use OrangeHRM\Entity\Employee;

class EmployeeGender implements Stringable
{
    private ?string $gender = null;

    /**
     * @param int|null $gender
     */
    public function __construct(?int $gender)
    {
        $this->gender = $gender;
    }

    /**
     * @inheritDoc
     */
    public function toString(): ?string
    {
        switch ($this->gender) {
            case Employee::GENDER_MALE:
                return 'Male';
            case Employee::GENDER_FEMALE:
                return 'Female';
            case Employee::GENDER_OTHER:
                return 'Other';
            default:
                return null;
        }
    }
}
