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

namespace OrangeHRM\Core\Report\DisplayField\Membership\Dto;

use OrangeHRM\Core\Report\DisplayField\NormalizableDTO;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\Employee;

class Membership extends NormalizableDTO
{
    use EntityManagerHelperTrait;

    private ?int $empNumber = null;

    /**
     * @param int|null $empNumber
     */
    public function __construct(?int $empNumber)
    {
        $this->empNumber = $empNumber;
    }

    /**
     * @inheritDoc
     */
    public function toArray(array $fields): ?array
    {
        /** @var Employee $employee */
        $employee = $this->getReference(Employee::class, $this->empNumber);
        return $this->normalizeArray($employee->getMemberships(), $fields);
    }

    /**
     * @inheritDoc
     */
    protected function getFieldGetterMap(): array
    {
        return [
            'membershipName' => ['getMembership', 'getName'],
            'subscriptionPaidBy' => ['getSubscriptionPaidBy'],
            'subscriptionAmount' => ['getSubscriptionFee'],
            'membershipCurrency' => ['getDecorator', 'getCurrencyName'],
            'subscriptionCommenceDate' => ['getDecorator', 'getSubscriptionCommenceDate'],
            'subscriptionRenewalDate' => ['getDecorator', 'getSubscriptionRenewalDate'],
            'membershipCode' => ['getMembership', 'getId'],
        ];
    }
}
