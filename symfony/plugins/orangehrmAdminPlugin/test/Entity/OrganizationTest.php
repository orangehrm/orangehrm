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

namespace OrangeHRM\Tests\Admin\Entity;

use OrangeHRM\Entity\Organization;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group @Admin
 * @group @Entity
 */
class OrganizationTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateTables([Organization::class]);
    }

    public function testOrganizationEntity(): void
    {
        $organization = new Organization();
        $organization->setId(1);
        $organization->setName('OHRM');
        $organization->setTaxId('12345');
        $organization->setRegistrationNumber('45678');
        $organization->setPhone('+96123456789');
        $organization->setFax('+96123456789');
        $organization->setEmail('admin@orangehrm.com');
        $organization->setCountry('USA');
        $organization->setProvince('Western');
        $organization->setCity('city');
        $organization->setZipCode('12500');
        $organization->setStreet1('temp street 1');
        $organization->setStreet2('temp street 2');
        $organization->setNote('This is a note');
        $this->persist($organization);

        /** @var Organization $organization */
        $organization = $this->getRepository(Organization::class)->find(1);
        $this->assertEquals('OHRM', $organization->getName());
        $this->assertEquals('12345', $organization->getTaxId());
        $this->assertEquals('45678', $organization->getRegistrationNumber());
        $this->assertEquals('+96123456789', $organization->getPhone());
        $this->assertEquals('+96123456789', $organization->getFax());
        $this->assertEquals('admin@orangehrm.com', $organization->getEmail());
        $this->assertEquals('USA', $organization->getCountry());
        $this->assertEquals('Western', $organization->getProvince());
        $this->assertEquals('city', $organization->getCity());
        $this->assertEquals('12500', $organization->getZipCode());
        $this->assertEquals('temp street 1', $organization->getStreet1());
        $this->assertEquals('temp street 2', $organization->getStreet2());
        $this->assertEquals('This is a note', $organization->getNote());
    }
}
