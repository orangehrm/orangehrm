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

namespace OrangeHRM\LDAP\Dto;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Ldap\Entry;

class LDAPEmployeeSelectorMapping extends ParameterBag
{
    public const ALLOWED_FIELDS = [
        'employeeId',
        'workEmail',
        'drivingLicenseNo',
        'otherId',
        'otherEmail',
        'ssnNumber',
        'sinNumber',
    ];

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->all();
    }

    /**
     * @return string[]
     */
    public function getAllAttributeNames(): array
    {
        return array_column($this->toArray(), 'attributeName');
    }

    /**
     * @param Entry $entry
     * @return LDAPEmployeeSearchFilterParams|null
     */
    public function extractAttributeValuesToSearchFilterParam(Entry $entry): ?LDAPEmployeeSearchFilterParams
    {
        $employeeSearchFilterParams = null;
        foreach ($this->toArray() as $employeeSelector) {
            $setter = 'set' . ucfirst($employeeSelector['field']);
            $attributeValue = $entry->getAttribute($employeeSelector['attributeName'])[0] ?? null;
            if ($attributeValue !== null) {
                $employeeSearchFilterParams === null
                    ? ($employeeSearchFilterParams = new LDAPEmployeeSearchFilterParams())->$setter($attributeValue)
                    : $employeeSearchFilterParams->$setter($attributeValue);
            }
        }
        return $employeeSearchFilterParams;
    }

    /**
     * @param array $employeeSelectorMapping e.g. [["field" => "employeeId", "attributeName" => "employeeNumber"], ["field" => "workEmail", "attributeName" => "mail"]]
     * @return static
     */
    public static function createFromArray(array $employeeSelectorMapping): self
    {
        foreach ($employeeSelectorMapping as $employeeSelectorMap) {
            $field = $employeeSelectorMap['field'] ?? null;
            if (empty($employeeSelectorMap['attributeName'])) {
                throw new InvalidArgumentException('Invalid `attributeName`');
            }
            if (!in_array($field, self::ALLOWED_FIELDS)) {
                throw new InvalidArgumentException("Invalid field name: `$field`");
            }
        }
        $ldapEmployeeSelectorMapping = new self();
        $ldapEmployeeSelectorMapping->replace($employeeSelectorMapping);
        return $ldapEmployeeSelectorMapping;
    }
}
