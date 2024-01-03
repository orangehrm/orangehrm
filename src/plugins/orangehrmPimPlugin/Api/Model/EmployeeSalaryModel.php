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
use OrangeHRM\Entity\EmployeeSalary;

/**
 * @OA\Schema(
 *     schema="Pim-EmployeeSalaryModel",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="amount", type="number"),
 *     @OA\Property(property="salaryName", type="string"),
 *     @OA\Property(property="comment", type="string"),
 *     @OA\Property(property="payPeriod", type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="name", type="string")
 *     ),
 *     @OA\Property(property="payGrade", type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="name", type="string")
 *     ),
 *     @OA\Property(property="currencyType", type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="name", type="string")
 *     ),
 *     @OA\Property(property="directDebit", type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="routingNumber", type="string"),
 *         @OA\Property(property="account", type="string"),
 *         @OA\Property(property="amount", type="number"),
 *         @OA\Property(property="accountType", type="string")
 *     )
 * )
 */
class EmployeeSalaryModel implements Normalizable
{
    use ModelTrait;

    /**
     * @param EmployeeSalary $employeeSalary
     */
    public function __construct(EmployeeSalary $employeeSalary)
    {
        $this->setEntity($employeeSalary);
        $this->setFilters(
            [
                'id',
                'amount',
                'salaryName',
                'comment',
                ['getPayPeriod', 'getCode'],
                ['getPayPeriod', 'getName'],
                ['getPayGrade', 'getId'],
                ['getPayGrade', 'getName'],
                ['getCurrencyType', 'getId'],
                ['getCurrencyType', 'getName'],
                ['getDirectDebit', 'getId'],
                ['getDirectDebit', 'getRoutingNumber'],
                ['getDirectDebit', 'getAccount'],
                ['getDirectDebit', 'getAmount'],
                ['getDirectDebit', 'getAccountType'],
            ]
        );
        $this->setAttributeNames(
            [
                'id',
                'amount',
                'salaryName',
                'comment',
                ['payPeriod', 'id'],
                ['payPeriod', 'name'],
                ['payGrade', 'id'],
                ['payGrade', 'name'],
                ['currencyType', 'id'],
                ['currencyType', 'name'],
                ['directDebit', 'id'],
                ['directDebit', 'routingNumber'],
                ['directDebit', 'account'],
                ['directDebit', 'amount'],
                ['directDebit', 'accountType'],
            ]
        );
    }
}
