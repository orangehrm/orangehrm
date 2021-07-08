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

namespace OrangeHRM\Pim\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Entity\Employee;

class EmployeePersonalDetailModel implements Normalizable
{
    use ModelTrait;
    use ConfigServiceTrait;

    /**
     * @param Employee $employee
     */
    public function __construct(Employee $employee)
    {
        $showDeprecatedFields = $this->getConfigService()->showPimDeprecatedFields();
        $showSsn = $this->getConfigService()->showPimSSN();
        $showSin = $this->getConfigService()->showPimSIN();
        $this->setEntity($employee);
        $filter = [
            'empNumber',
            'lastName',
            'firstName',
            'middleName',
            'employeeId',
            'otherId',
            'drivingLicenseNo',
            ['getDecorator', 'getDrivingLicenseExpiredDate'],
            'gender',
            'maritalStatus',
            ['getDecorator', 'getBirthday'],
            ['getEmployeeTerminationRecord', 'getId'],
            ['getNationality', 'getId'],
            ['getNationality', 'getName'],
        ];

        $attributeNames = [
            'empNumber',
            'lastName',
            'firstName',
            'middleName',
            'employeeId',
            'otherId',
            'drivingLicenseNo',
            'drivingLicenseExpiredDate',
            'gender',
            'maritalStatus',
            'birthday',
            'terminationId',
            ['nationality', 'id'],
            ['nationality', 'name'],
        ];

        if ($showSsn) {
            $filter[] = 'ssnNumber';
            $attributeNames[] = 'ssnNumber';
        }
        if ($showSin) {
            $filter[] = 'sinNumber';
            $attributeNames[] = 'sinNumber';
        }
        if ($showDeprecatedFields) {
            $filter[] = 'nickName';
            $filter[] = ['getDecorator', 'getSmoker'];
            $filter[] = 'militaryService';

            $attributeNames[] = 'nickname';
            $attributeNames[] = 'smoker';
            $attributeNames[] = 'militaryService';
        }

        $this->setFilters($filter);
        $this->setAttributeNames($attributeNames);
    }
}
