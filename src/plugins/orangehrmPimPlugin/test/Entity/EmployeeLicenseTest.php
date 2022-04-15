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

namespace OrangeHRM\Tests\Pim\Entity;

use DateTime;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmployeeLicense;
use OrangeHRM\Entity\License;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

class EmployeeLicenseTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([EmployeeLicense::class, Employee::class, License::class]);
    }

    public function testEmployeeLicenseEntity(): void
    {
        $employee = new Employee();
        $employee->setFirstName('Kayla');
        $employee->setLastName('Abbey');
        $employee->setEmployeeId('0001');
        $this->persist($employee);

        $license = new License();
        $license->setId(1);
        $license->setName('CIMA');
        $this->persist($license);

        $employeeLicense = new EmployeeLicense();
        $employeeLicense->setEmployee($employee);
        $employeeLicense->setLicense($license);
        $employeeLicense->setLicenseNo('no1');
        $employeeLicense->setLicenseIssuedDate(new DateTime('2019-05-23'));
        $employeeLicense->setLicenseExpiryDate(new DateTime('2020-05-23'));
        $this->persist($employeeLicense);

        /** @var EmployeeLicense[] $employeeLicenses */
        $employeeLicenses = $this->getRepository(EmployeeLicense::class)->findBy(['employee' => 1, 'license' => 1]);
        $employeeLicense = $employeeLicenses[0];
        $this->assertEquals('0001', $employeeLicense->getEmployee()->getEmployeeId());
        $this->assertEquals(1, $employeeLicense->getLicense()->getId());
        $this->assertEquals('no1', $employeeLicense->getLicenseNo());
        $this->assertEquals('2019-05-23', $employeeLicense->getLicenseIssuedDate()->format('Y-m-d'));
        $this->assertEquals('2020-05-23', $employeeLicense->getLicenseExpiryDate()->format('Y-m-d'));
    }
}
