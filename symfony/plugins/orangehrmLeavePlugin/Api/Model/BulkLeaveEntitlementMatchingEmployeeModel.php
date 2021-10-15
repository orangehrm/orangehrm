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

namespace OrangeHRM\Leave\Api\Model;

use LogicException;
use OrangeHRM\Core\Api\V2\Serializer\CollectionNormalizable;
use OrangeHRM\Core\Api\V2\Serializer\ModelConstructorArgsAwareInterface;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\LeaveEntitlement;

class BulkLeaveEntitlementMatchingEmployeeModel implements CollectionNormalizable, ModelConstructorArgsAwareInterface
{
    /**
     * @var Employee[]
     */
    private array $employees;

    /**
     * @var LeaveEntitlement[]
     */
    private array $entitlements;

    /**
     * @var float
     */
    private float $entitlement;

    /**
     * @param Employee[] $employees
     * @param LeaveEntitlement[] $entitlements
     * @param float|null $entitlement
     */
    public function __construct(array $employees, array $entitlements, ?float $entitlement)
    {
        $this->employees = $employees;
        $this->entitlements = $entitlements;
        $this->entitlement = $entitlement ?? 0;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $result = [];
        $entitlements = $this->getEntitlementArrayWithEmpNumberAsKey();
        foreach ($this->employees as $employee) {
            $empNumber = $employee->getEmpNumber();
            $entitlement = 0;
            if (isset($entitlements[$empNumber])) {
                $entitlement = $entitlements[$empNumber]->getNoOfDays();
            }

            $result[] = [
                'empNumber' => $empNumber,
                'lastName' => $employee->getLastName(),
                'firstName' => $employee->getFirstName(),
                'middleName' => $employee->getMiddleName(),
                'employeeId' => $employee->getEmployeeId(),
                'terminationId' => $employee->getEmployeeTerminationRecord() ?
                    $employee->getEmployeeTerminationRecord()->getId() : null,
                'entitlement' => [
                    'current' => $entitlement,
                    'updateAs' => $entitlement + $this->entitlement,
                ],
            ];
        }

        return $result;
    }

    /**
     * @return array<int, LeaveEntitlement>
     */
    private function getEntitlementArrayWithEmpNumberAsKey(): array
    {
        $entitlements = [];
        foreach ($this->entitlements as $entitlement) {
            $empNumber = $entitlement->getEmployee()->getEmpNumber();
            if (isset($entitlements[$empNumber])) {
                throw new LogicException(
                    "Shouldn't get multiple entitlements under same employee for particular leave type"
                );
            }

            $entitlements[$empNumber] = $entitlement;
        }
        return $entitlements;
    }
}
