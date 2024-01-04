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
use OrangeHRM\Leave\Entitlement\LeaveBalance;

/**
 * @OA\Schema(
 *     schema="Leave-LeaveBalanceModel",
 *     type="object",
 *     @OA\Property(property="entitled", type="integer"),
 *     @OA\Property(property="used", type="integer"),
 *     @OA\Property(property="scheduled", type="integer"),
 *     @OA\Property(property="pending", type="integer"),
 *     @OA\Property(property="taken", type="integer"),
 *     @OA\Property(property="balance", type="integer"),
 *     @OA\Property(property="asAtDate", type="number"),
 *     @OA\Property(property="endDate", type="number")
 * )
 */
class LeaveBalanceModel implements Normalizable
{
    use ModelTrait;

    public function __construct(LeaveBalance $leaveBalance)
    {
        $this->setEntity($leaveBalance);
        $this->setFilters(
            [
                "entitled",
                "used",
                "scheduled",
                "pending",
                "taken",
                "balance",
                ["getYmdAsAtDate"],
                ["getYmdEndDate"],
            ]
        );
        $this->setAttributeNames(
            [
                "entitled",
                "used",
                "scheduled",
                "pending",
                "taken",
                "balance",
                "asAtDate",
                "endDate",
            ]
        );
    }
}
