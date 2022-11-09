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

use OrangeHRM\Entity\Country;
use OrangeHRM\Entity\Location;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group @Admin
 * @group @Entity
 */
class LocationTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([Country::class, Location::class]);
    }

    public function testLocationEntity(): void
    {
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

        /** @var Location $location */
        $location = $this->getRepository(Location::class)->find(1);
        $this->assertEquals('Texas R&D', $location->getName());
        $this->assertEquals('TX', $location->getProvince());
        $this->assertEquals('Address', $location->getAddress());
        $this->assertEquals('+1', $location->getZipCode());
        $this->assertEquals('1 (866) 791-7204', $location->getPhone());
        $this->assertEquals('1 (866) 791-7204', $location->getFax());
        $this->assertEquals('Note', $location->getNote());
        $this->assertEquals('US', $location->getCountry()->getCountryCode());
    }
}
