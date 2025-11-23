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

namespace OrangeHRM\Tests\LDAP\Dto;

use InvalidArgumentException;
use OrangeHRM\LDAP\Dto\LDAPEmployeeSelectorMapping;
use OrangeHRM\Tests\Util\TestCase;
use Symfony\Component\Ldap\Entry;

/**
 * @group Admin
 * @group LDAP
 * @group Dto
 */
class LDAPEmployeeSelectorMappingTest extends TestCase
{
    public function testCreateFromArray(): void
    {
        $employeeSelectorMapping = [
            ['field' => 'employeeId', 'attributeName' => 'employeeNumber'],
            ['field' => 'workEmail', 'attributeName' => 'mail']
        ];
        $ldapEmployeeSelectorMapping = LDAPEmployeeSelectorMapping::createFromArray($employeeSelectorMapping);
        $this->assertEquals($employeeSelectorMapping, $ldapEmployeeSelectorMapping->toArray());
        $this->assertEquals(['employeeNumber', 'mail'], $ldapEmployeeSelectorMapping->getAllAttributeNames());
    }

    public function testCreateFromArrayWithInvalidField(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid field name: `invalid`');
        LDAPEmployeeSelectorMapping::createFromArray([['field' => 'invalid', 'attributeName' => 'employeeNumber']]);
    }

    public function testCreateFromArrayWithEmptyField(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid field name: ``');
        LDAPEmployeeSelectorMapping::createFromArray([['attributeName' => 'employeeNumber']]);
    }

    public function testCreateFromArrayWithEmptyAttributeName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid `attributeName`');
        LDAPEmployeeSelectorMapping::createFromArray([['field' => 'invalid']]);
    }

    public function testExtractAttributeValuesToSearchFilterParam(): void
    {
        $ldapEmployeeSelectorMapping = LDAPEmployeeSelectorMapping::createFromArray(
            [
                ['field' => 'employeeId', 'attributeName' => 'employeeNumber'],
                ['field' => 'workEmail', 'attributeName' => 'mail']
            ]
        );

        $entry = new Entry('uid=Linda.Anderson,ou=admin,ou=users,dc=example,dc=org');
        $entry->setAttribute('employeeNumber', ['0001']);
        $ldapEmployeeSearchFilterParams = $ldapEmployeeSelectorMapping
            ->extractAttributeValuesToSearchFilterParam($entry);
        $this->assertNull($ldapEmployeeSearchFilterParams->getEmpNumber());
        $this->assertNull($ldapEmployeeSearchFilterParams->getWorkEmail());
        $this->assertNull($ldapEmployeeSearchFilterParams->getOtherId());
        $this->assertNull($ldapEmployeeSearchFilterParams->getOtherEmail());
        $this->assertNull($ldapEmployeeSearchFilterParams->getDrivingLicenseNo());
        $this->assertNull($ldapEmployeeSearchFilterParams->getSsnNumber());
        $this->assertNull($ldapEmployeeSearchFilterParams->getSinNumber());
        $this->assertEquals('0001', $ldapEmployeeSearchFilterParams->getEmployeeId());

        // Only get first attribute
        $entry = new Entry('uid=Linda.Anderson,ou=admin,ou=users,dc=example,dc=org');
        $entry->setAttribute('mail', ['linda@example.org', 'anderson@example.com']);
        $ldapEmployeeSearchFilterParams = $ldapEmployeeSelectorMapping
            ->extractAttributeValuesToSearchFilterParam($entry);
        $this->assertNull($ldapEmployeeSearchFilterParams->getEmpNumber());
        $this->assertNull($ldapEmployeeSearchFilterParams->getEmployeeId());
        $this->assertNull($ldapEmployeeSearchFilterParams->getOtherId());
        $this->assertNull($ldapEmployeeSearchFilterParams->getOtherEmail());
        $this->assertNull($ldapEmployeeSearchFilterParams->getDrivingLicenseNo());
        $this->assertNull($ldapEmployeeSearchFilterParams->getSsnNumber());
        $this->assertNull($ldapEmployeeSearchFilterParams->getSinNumber());
        $this->assertEquals('linda@example.org', $ldapEmployeeSearchFilterParams->getWorkEmail());
    }

    public function testExtractAttributeValuesToSearchFilterParamWithoutMapping(): void
    {
        $ldapEmployeeSelectorMapping = LDAPEmployeeSelectorMapping::createFromArray([]);

        $entry = new Entry('uid=Linda.Anderson,ou=admin,ou=users,dc=example,dc=org');
        $entry->setAttribute('employeeNumber', ['0001']);
        $ldapEmployeeSearchFilterParams = $ldapEmployeeSelectorMapping
            ->extractAttributeValuesToSearchFilterParam($entry);
        $this->assertNull($ldapEmployeeSearchFilterParams);
    }
}
