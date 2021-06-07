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
use OrangeHRM\Entity\EmployeeSalary;

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
