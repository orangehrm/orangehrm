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

namespace OrangeHRM\Dashboard\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\CollectionNormalizable;
use OrangeHRM\Core\Api\V2\Serializer\ModelConstructorArgsAwareInterface;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Leave;

/**
 * @OA\Schema(
 *     schema="Dashboard-EmployeeOnLeaveListModel",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="date", type="string", format="date"),
 *     @OA\Property(property="lengthHours", type="number"),
 *     @OA\Property(
 *         property="employee",
 *         type="object",
 *         @OA\Property(property="empNumber", type="integer"),
 *         @OA\Property(property="employeeId", type="string"),
 *         @OA\Property(property="firstName", type="string"),
 *         @OA\Property(property="lastName", type="string"),
 *         @OA\Property(property="middleName", type="string"),
 *         @OA\Property(property="terminationId", type="integer"),
 *     ),
 *     @OA\Property(property="duration", type="string"),
 *     @OA\Property(property="endTime", type="string"),
 *     @OA\Property(property="startTime", type="string"),
 *     @OA\Property(
 *         property="leaveType",
 *         type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="name", type="string"),
 *         @OA\Property(property="deleted", type="boolean")
 *     ),
 * )
 */
class EmployeesOnLeaveListModel implements CollectionNormalizable, ModelConstructorArgsAwareInterface
{
    use UserRoleManagerTrait;
    use AuthUserTrait;

    private array $leaves;

    /**
     * @param Leave[] $leaves
     */
    public function __construct(array $leaves)
    {
        $this->leaves = $leaves;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $normalizedLeaves = [];
        foreach ($this->leaves as $leave) {
            $normalizedLeave = [
                'id' => $leave->getId(),
                'date' => $leave->getDecorator()->getLeaveDate(),
                'lengthHours' => $leave->getLengthHours(),
                'employee' => [
                    'empNumber' => $leave->getEmployee()->getEmpNumber(),
                    'lastName' => $leave->getEmployee()->getLastName(),
                    'firstName' => $leave->getEmployee()->getFirstName(),
                    'middleName' => $leave->getEmployee()->getMiddleName(),
                    'employeeId' => $leave->getEmployee()->getEmployeeId(),
                    'terminationId' => $leave->getEmployee()->getEmployeeTerminationRecord() ? $leave->getEmployee()
                        ->getEmployeeTerminationRecord()->getId() : null
                ],
                'duration' => $leave->getDecorator()->getLeaveDuration(),
                'endTime' => $leave->getDecorator()->getEndTime(),
                'startTime' => $leave->getDecorator()->getStartTime(),
            ];
            if ($this->getUserRoleManager()
                ->isEntityAccessible(Employee::class, $leave->getEmployee()->getEmpNumber())
            ) {
                $normalizedLeave['leaveType'] = [
                    'id' => $leave->getLeaveType()->getId(),
                    'name' => $leave->getLeaveType()->getName(),
                    'deleted' => $leave->getLeaveType()->isDeleted()
                ];
            }
            $normalizedLeaves[] = $normalizedLeave;
        }
        return $normalizedLeaves;
    }
}
