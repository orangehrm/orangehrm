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

namespace OrangeHRM\Pim\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\Employee;

/**
 * @OA\Schema(
 *     schema="Pim-EmployeeJobDetailModel",
 *     type="object",
 *     @OA\Property(property="empNumber", type="integer"),
 *     @OA\Property(property="joinedDate", type="string", format="date"),
 *     @OA\Property(property="jobTitle", type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="title", type="string"),
 *         @OA\Property(property="isDeleted", type="boolean")
 *     ),
 *     @OA\Property(property="jobSpecificationAttachment", type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="filename", type="string")
 *     ),
 *     @OA\Property(property="empStatus", type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="name", type="string")
 *     ),
 *     @OA\Property(property="jobCategory", type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="name", type="string")
 *     ),
 *     @OA\Property(property="subunit", type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="name", type="string"),
 *         @OA\Property(property="unitId", type="integer")
 *     ),
 *     @OA\Property(property="location", type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="name", type="string")
 *     ),
 *     @OA\Property(property="employeeTerminationRecord", type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="date", type="string", format="date")
 *     )
 * )
 */
class EmployeeJobDetailModel implements Normalizable
{
    use ModelTrait;

    public function __construct(Employee $employee)
    {
        $this->setEntity($employee);
        $this->setFilters(
            [
                'empNumber',
                ['getDecorator', 'getJoinedDate'],
                ['getJobTitle', 'getId'],
                ['getJobTitle', 'getJobTitleName'],
                ['getJobTitle', 'isDeleted'],
                ['getJobTitle', 'getJobSpecificationAttachment', 'getId'],
                ['getJobTitle', 'getJobSpecificationAttachment', 'getFileName'],
                ['getEmpStatus', 'getId'],
                ['getEmpStatus', 'getName'],
                ['getJobCategory', 'getId'],
                ['getJobCategory', 'getName'],
                ['getSubDivision', 'getId'],
                ['getSubDivision', 'getName'],
                ['getSubDivision', 'getUnitId'],
                ['getDecorator', 'getLocation', 'getId'],
                ['getDecorator', 'getLocation', 'getName'],
                ['getEmployeeTerminationRecord', 'getId'],
                ['getEmployeeTerminationRecord', 'getDecorator', 'getDate'],
            ]
        );
        $this->setAttributeNames(
            [
                'empNumber',
                'joinedDate',
                ['jobTitle', 'id'],
                ['jobTitle', 'title'],
                ['jobTitle', 'isDeleted'],
                ['jobSpecificationAttachment', 'id'],
                ['jobSpecificationAttachment', 'filename'],
                ['empStatus', 'id'],
                ['empStatus', 'name'],
                ['jobCategory', 'id'],
                ['jobCategory', 'name'],
                ['subunit', 'id'],
                ['subunit', 'name'],
                ['subunit', 'unitId'],
                ['location', 'id'],
                ['location', 'name'],
                ['employeeTerminationRecord', 'id'],
                ['employeeTerminationRecord', 'date'],
            ]
        );
    }
}
