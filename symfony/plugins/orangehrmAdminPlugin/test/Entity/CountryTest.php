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
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group @Admin
 * @group @Entity
 */
class CountryTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateTables([Country::class]);
    }

    public function testCountryEntity(): void
    {
        $country = new Country();
        $country->setCountryCode('AD');
        $country->setName('ANDORRA');
        $country->setCountryName('Andorra');
        $country->setIso3('AND');
        $country->setNumCode(20);
        $this->persist($country);

        /** @var Country $country */
        $country = $this->getRepository(Country::class)->find('AD');
        $this->assertEquals('AD', $country->getCountryCode());
        $this->assertEquals('ANDORRA', $country->getName());
        $this->assertEquals('Andorra', $country->getCountryName());
        $this->assertEquals('AND', $country->getIso3());
        $this->assertEquals(20, $country->getNumCode());
    }
}
