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

namespace OrangeHRM\Leave\Service;

use OrangeHRM\Core\Traits\Service\NormalizerServiceTrait;
use OrangeHRM\Entity\LeaveType;
use OrangeHRM\Leave\Dao\LeaveTypeDao;
use OrangeHRM\Leave\Service\Model\LeaveTypeModel;
use OrangeHRM\Leave\Traits\Service\LeaveEntitlementServiceTrait;

class LeaveTypeService
{
    use LeaveEntitlementServiceTrait;
    use NormalizerServiceTrait;

    /**
     * @var LeaveTypeDao|null
     */
    private ?LeaveTypeDao $leaveTypeDao = null;

    /**
     * @return LeaveTypeDao|null
     */
    public function getLeaveTypeDao(): LeaveTypeDao
    {
        if (!($this->leaveTypeDao instanceof LeaveTypeDao)) {
            $this->leaveTypeDao = new LeaveTypeDao();
        }
        return $this->leaveTypeDao;
    }

    /**
     * @param int $empNumber
     * @param bool $includeAllocated
     * @return LeaveType[]
     */
    public function getEligibleLeaveTypesByEmpNumber(int $empNumber, bool $includeAllocated = false): array
    {
        $leaveTypes = $this->getLeaveTypeDao()->getLeaveTypeList();
        $leaveTypeList = [];

        foreach ($leaveTypes as $leaveType) {
            if ($includeAllocated && $this->getLeaveTypeDao()->hasEmployeeAllocatedLeavesForLeaveType($empNumber, $leaveType->getId())) {
                array_push($leaveTypeList, $leaveType);
                continue;
            }
            $balance = $this->getLeaveEntitlementService()->getLeaveBalance($empNumber, $leaveType->getId());

            if ($balance->getEntitled() > 0) {
                array_push($leaveTypeList, $leaveType);
            }
        }
        return $leaveTypeList;
    }

    /**
     * @param int $leaveTypeId
     * @return array|null
     */
    public function getLeaveTypeAsArray(int $leaveTypeId): ?array
    {
        $leaveType = $this->getLeaveTypeDao()->getLeaveTypeById($leaveTypeId);
        if (!$leaveType instanceof LeaveType) {
            return null;
        }
        return $this->getNormalizerService()->normalize(LeaveTypeModel::class, $leaveType);
    }
}
