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

namespace OrangeHRM\Tests\Admin\Dao;

use OrangeHRM\Admin\Dao\CountryDao;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\Country;
use OrangeHRM\Entity\Province;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Admin
 * @group Dao
 */
class CountryDaoTest extends TestCase
{
    /**
     * @var CountryDao
     */
    protected CountryDao $dao;

    /**
     * @var string
     */
    protected string $fixture;

    /**
     *
     * @var array
     */
    protected $sampleCountries;

    protected function setUp(): void
    {
        $this->dao = new CountryDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/CountryDao.yml';

        $this->sampleCountries = TestDataService::loadFixtures($this->fixture, 'Country');
        TestDataService::populate($this->fixture);
    }

    public function testGetCountryList(): void
    {
        $result = $this->dao->getCountryList();

        $this->assertCount(5, $result);
        foreach ($result as $index => $country) {
            $this->assertTrue($country instanceof Country);
            $this->assertEquals($this->sampleCountries[$index]['cou_code'], $country->getCountryCode());
            $this->assertEquals($this->sampleCountries[$index]['name'], $country->getName());
            $this->assertEquals($this->sampleCountries[$index]['cou_name'], $country->getCountryName());
            $this->assertEquals($this->sampleCountries[$index]['iso3'], $country->getIso3());
            $this->assertEquals($this->sampleCountries[$index]['numcode'], $country->getNumcode());
        }
    }

    public function testGetProvinceList(): void
    {
        $result = $this->dao->getProvinceList('US');

        $sampleProvince = TestDataService::loadObjectList(Province::class, $this->fixture, 'Province');
        $this->assertCount(4, $result);
        foreach ($result as $index => $province) {
            $this->assertTrue($province instanceof Province);
            $this->assertEquals($sampleProvince[$index]->getProvinceName(), $province->getProvinceName());
            $this->assertEquals($sampleProvince[$index]->getProvinceCode(), $province->getProvinceCode());
            $this->assertEquals($sampleProvince[$index]->getCountryCode(), $province->getCountryCode());
        }
    }

    public function testGetCountryByCountryName(): void
    {
        $result = $this->dao->getCountryByCountryName('Not Exists');
        $this->assertNull($result);

        $result = $this->dao->getCountryByCountryName('SINGAPORE');
        $this->assertTrue($result instanceof Country);
        $this->assertEquals('SG', $result->getCountryCode());
        $this->assertEquals('Singapore', $result->getCountryName());
        $this->assertEquals('SGP', $result->getIso3());
        $this->assertEquals(702, $result->getNumCode());
    }

    public function testGetCountryByCountryCode(): void
    {
        $result = $this->dao->getCountryByCountryCode('NotExists');
        $this->assertNull($result);

        $result = $this->dao->getCountryByCountryCode('SG');
        $this->assertTrue($result instanceof Country);
        $this->assertEquals('SINGAPORE', $result->getName());
        $this->assertEquals('Singapore', $result->getCountryName());
        $this->assertEquals('SGP', $result->getIso3());
        $this->assertEquals(702, $result->getNumCode());
    }

    public function testGetProvinceByProvinceCode(): void
    {
        $result = $this->dao->getProvinceByProvinceCode('NotExists');
        $this->assertNull($result);

        $result = $this->dao->getProvinceByProvinceCode('AK');
        $this->assertTrue($result instanceof Province);
        $this->assertEquals('Alaska', $result->getProvinceName());
        $this->assertEquals('US', $result->getCountryCode());
    }

    public function testGetProvinceByProvinceName(): void
    {
        $result = $this->dao->getProvinceByProvinceName('NotExists');
        $this->assertNull($result);

        $result = $this->dao->getProvinceByProvinceName('Alaska');
        $this->assertTrue($result instanceof Province);
        $this->assertEquals('Alaska', $result->getProvinceName());
        $this->assertEquals('AK', $result->getProvinceCode());
        $this->assertEquals('US', $result->getCountryCode());
    }
}
