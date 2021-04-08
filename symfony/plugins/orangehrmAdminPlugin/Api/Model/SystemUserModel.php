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

namespace OrangeHRM\Admin\Api\Model;

use OrangeHRM\Entity\SystemUser;
use Orangehrm\Rest\Api\Entity\Serializable;
use Orangehrm\Rest\Api\Model\ModelTrait;

class SystemUserModel implements Serializable
{
    use ModelTrait;

    /**
     * @param SystemUser $systemUser
     */
    public function __construct(SystemUser $systemUser)
    {
        $this->setEntity($systemUser);
        $this->setFilters(
            [
                'id',
                'userName',
                ['isDeleted'],
                'status',
                ['getEmployee', 'getEmpNumber'],
                ['getEmployee', 'getEmployeeId'],
                ['getEmployee', 'getFirstName'],
                ['getEmployee', 'getMiddleName'],
                ['getEmployee', 'getLastName'],
                ['getUserRole', 'getId'],
                ['getUserRole', 'getName'],
                ['getUserRole', 'getDisplayName'],
            ]
        );
        $this->setAttributeNames(
            [
                'id',
                'userName',
                'deleted',
                'status',
                ['employee', 'empNumber'],
                ['employee', 'employeeId'],
                ['employee', 'firstName'],
                ['employee', 'middleName'],
                ['employee', 'lastName'],
                ['userRole', 'id'],
                ['userRole', 'name'],
                ['userRole', 'displayName'],
            ]
        );
    }
}
