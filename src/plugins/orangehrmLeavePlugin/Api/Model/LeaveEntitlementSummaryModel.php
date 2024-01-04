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

namespace OrangeHRM\Leave\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\LeaveEntitlement;
use OrangeHRM\Leave\Traits\Service\LeaveEntitlementServiceTrait;

/**
 * @OA\Schema(
 *     schema="Leave-LeaveEntitlementSummaryModel",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(
 *         property="usageBreakdown",
 *         type="object",
 *         @OA\Property(property="entitlement", type="number"),
 *         @OA\Property(property="used", type="number"),
 *         @OA\Property(property="scheduled", type="number"),
 *         @OA\Property(property="pending", type="number"),
 *         @OA\Property(property="taken", type="number"),
 *         @OA\Property(property="balance", type="number"),
 *     ),
 *     @OA\Property(
 *         property="leaveType",
 *         type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="name", type="string"),
 *         @OA\Property(property="deleted", type="boolean")
 *     ),
 *     @OA\Property(property="fromDate", type="string"),
 *     @OA\Property(property="toDate", type="string"),
 * )
 */
class LeaveEntitlementSummaryModel implements Normalizable
{
    use ModelTrait;
    use LeaveEntitlementServiceTrait;

    public function __construct(LeaveEntitlement $leaveEntitlement)
    {
        $this->setEntity($leaveEntitlement);
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        /** @var LeaveEntitlement $entity */
        $entity = $this->getEntity();
        $balance = $this->getLeaveEntitlementService()->getLeaveBalance(
            $entity->getEmployee()->getEmpNumber(),
            $entity->getLeaveType()->getId(),
            $entity->getFromDate(),
            $entity->getToDate()
        );

        return [
            'id' => $entity->getId(),
            'usageBreakdown' => [
                'entitlement' => $balance->getEntitled(),
                'used' => $balance->getUsed(),
                'scheduled' => $balance->getScheduled(),
                'pending' => $balance->getPending(),
                'taken' => $balance->getTaken(),
                'balance' => $balance->getBalance(),
            ],
            'leaveType' => [
                'id' => $entity->getLeaveType()->getId(),
                'name' => $entity->getLeaveType()->getName(),
                'deleted' => $entity->getLeaveType()->isDeleted(),
            ],
            'fromDate' => $balance->getYmdAsAtDate(),
            'toDate' => $balance->getYmdEndDate(),
        ];
    }
}
