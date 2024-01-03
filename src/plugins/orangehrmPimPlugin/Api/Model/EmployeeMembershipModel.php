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
use OrangeHRM\Entity\EmployeeMembership;

/**
 * @OA\Schema(
 *     schema="Pim-EmployeeMembershipModel",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="membership", type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="name", type="string")
 *     ),
 *     @OA\Property(property="subscriptionFee", type="number"),
 *     @OA\Property(property="subscriptionPaidBy", type="string"),
 *     @OA\Property(property="currencyType", type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="name", type="string")
 *     ),
 *     @OA\Property(property="subscriptionCommenceDate", type="string", format="date"),
 *     @OA\Property(property="subscriptionRenewalDate", type="string", format="date")
 * )
 */
class EmployeeMembershipModel implements Normalizable
{
    use ModelTrait;

    /**
     * @param EmployeeMembership $employeeMembership
     */
    public function __construct(EmployeeMembership $employeeMembership)
    {
        $this->setEntity($employeeMembership);
        $this->setFilters(
            [
                'id',
                ['getMembership', 'getId'],
                ['getMembership', 'getName'],
                'subscriptionFee',
                'subscriptionPaidBy',
                'subscriptionCurrency',
                ['getDecorator', 'getCurrencyName'],
                ['getDecorator', 'getSubscriptionCommenceDate'],
                ['getDecorator', 'getSubscriptionRenewalDate'],
            ]
        );
        $this->setAttributeNames(
            [
                'id',
                ['membership', 'id'],
                ['membership', 'name'],
                'subscriptionFee',
                'subscriptionPaidBy',
                ['currencyType', 'id'],
                ['currencyType', 'name'],
                'subscriptionCommenceDate',
                'subscriptionRenewalDate',
            ]
        );
    }
}
