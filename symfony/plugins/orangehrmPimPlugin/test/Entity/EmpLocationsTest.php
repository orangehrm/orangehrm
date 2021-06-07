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

use OrangeHRM\Entity\Country;
use OrangeHRM\Entity\EmpLocations;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Location;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group Entity
 */
class EmpLocationsTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables(
            [Location::class, Employee::class, Country::class, EmpLocations::class]
        );
    }

    public function testEmpLocationsEntity(): void
    {
        $employee = new Employee();
        $employee->setFirstName('Kayla');
        $employee->setLastName('Abbey');
        $employee->setEmployeeId('0001');
        $this->persist($employee);

        $country = new Country();
        $country->setCountryCode('US');
        $country->setName('UNITED STATES');
        $country->setCountryName('United States');
        $this->persist($country);

        $location = new Location();
        $location->setName('Texas R&D');
        $location->setCountry($country);
        $location->setProvince('TX');
        $location->setAddress('Address');
        $location->setZipCode('+1');
        $location->setPhone('1 (866) 791-7204');
        $location->setFax('1 (866) 791-7204');
        $location->setNote('Note');
        $this->persist($location);

        $empLocations = new EmpLocations();
        $empLocations->setEmployee($employee);
        $empLocations->setLocation($location);
        $this->persist($empLocations);

        /** @var EmpLocations[] $empLocations */
        $empLocations = $this->getRepository(EmpLocations::class)->findBy(['employee' => 1, 'location' => 1]);
        $empLocation = $empLocations[0];
        $this->assertEquals('0001', $empLocation->getEmployee()->getEmployeeId());
        $this->assertEquals('Texas R&D', $empLocation->getLocation()->getName());
    }
}
