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

namespace OrangeHRM\Tests\Admin\Service;

use OrangeHRM\Admin\Dao\CountryDao;
use OrangeHRM\Admin\Service\CountryService;
use OrangeHRM\Core\Service\NormalizerService;
use OrangeHRM\Entity\Country;
use OrangeHRM\Entity\Province;
use OrangeHRM\Tests\Util\TestCase;

/**
 * @group Admin
 * @group Service
 */
class CountryServiceTest extends TestCase
{
    /**
     * @var CountryService
     */
    protected CountryService $service;

    protected function setUp(): void
    {
        $this->service = new CountryService();
    }

    public function testGetCountryDao(): void
    {
        $result = $this->service->getCountryDao();
        $this->assertTrue($result instanceof CountryDao);

        $countryDao = new CountryDao();
        $this->service->setCountryDao($countryDao);
        $result = $this->service->getCountryDao();
        $this->assertEquals($countryDao, $result);
    }

    public function testSetCountryDao(): void
    {
        $countryDao = new CountryDao();
        $this->service->setCountryDao($countryDao);
        $result = $this->service->getCountryDao();
        $this->assertEquals($countryDao, $result);
    }

    public function testGetCountryList(): void
    {
        $country = new Country();
        $country->setCountryCode('SG');
        $country->setName('SINGAPORE');

        $countryDao = $this->getMockBuilder(CountryDao::class)
            ->onlyMethods(['getCountryList'])
            ->getMock();
        $countryDao->expects($this->once())
            ->method('getCountryList')
            ->will($this->returnValue([$country]));

        $this->service->setCountryDao($countryDao);
        $countries = $this->service->getCountryList();
        $this->assertCount(1, $countries);
        $this->assertEquals('SG', $countries[0]->getCountryCode());
        $this->assertEquals('SINGAPORE', $countries[0]->getName());
    }

    public function testGetProvinceList(): void
    {
        $province = new Province();
        $province->setProvinceCode('AL');
        $province->setProvinceName('Alabama');

        $countryDao = $this->getMockBuilder(CountryDao::class)
            ->onlyMethods(['getProvinceList'])
            ->getMock();
        $countryDao->expects($this->once())
            ->method('getProvinceList')
            ->will($this->returnValue([$province]));

        $this->service->setCountryDao($countryDao);
        $provinces = $this->service->getProvinceList();
        $this->assertCount(1, $provinces);
        $this->assertEquals('AL', $provinces[0]->getProvinceCode());
        $this->assertEquals('Alabama', $provinces[0]->getProvinceName());
    }

    public function testGetCountryByCountryName(): void
    {
        $country = new Country();
        $country->setCountryCode('SG');
        $country->setName('SINGAPORE');

        $countryMap = [
            ['SINGAPORE', $country],
            ['NotExists', null],
        ];
        $countryDao = $this->getMockBuilder(CountryDao::class)
            ->onlyMethods(['getCountryByCountryName'])
            ->getMock();
        $countryDao->expects($this->exactly(2))
            ->method('getCountryByCountryName')
            ->will($this->returnValueMap($countryMap));

        $this->service->setCountryDao($countryDao);
        $country = $this->service->getCountryByCountryName('SINGAPORE');
        $this->assertEquals('SG', $country->getCountryCode());
        $this->assertEquals('SINGAPORE', $country->getName());

        $country = $this->service->getCountryByCountryName('NotExists');
        $this->assertNull($country);
    }

    public function testGetCountryByCountryCode(): void
    {
        $country = new Country();
        $country->setCountryCode('SG');
        $country->setName('SINGAPORE');

        $countryMap = [
            ['SG', $country],
            ['NotExists', null],
        ];
        $countryDao = $this->getMockBuilder(CountryDao::class)
            ->onlyMethods(['getCountryByCountryCode'])
            ->getMock();
        $countryDao->expects($this->exactly(2))
            ->method('getCountryByCountryCode')
            ->will($this->returnValueMap($countryMap));

        $this->service->setCountryDao($countryDao);
        $country = $this->service->getCountryByCountryCode('SG');
        $this->assertEquals('SG', $country->getCountryCode());
        $this->assertEquals('SINGAPORE', $country->getName());

        $country = $this->service->getCountryByCountryCode('NotExists');
        $this->assertNull($country);
    }

    public function testGetProvinceByProvinceCode(): void
    {
        $province = new Province();
        $province->setProvinceCode('AK');
        $province->setProvinceName('Alaska');

        $provinceMap = [
            ['AK', $province],
            ['NotExists', null],
        ];
        $countryDao = $this->getMockBuilder(CountryDao::class)
            ->onlyMethods(['getProvinceByProvinceCode'])
            ->getMock();
        $countryDao->expects($this->exactly(2))
            ->method('getProvinceByProvinceCode')
            ->will($this->returnValueMap($provinceMap));

        $this->service->setCountryDao($countryDao);
        $province = $this->service->getProvinceByProvinceCode('AK');
        $this->assertEquals('AK', $province->getProvinceCode());
        $this->assertEquals('Alaska', $province->getProvinceName());

        $province = $this->service->getProvinceByProvinceCode('NotExists');
        $this->assertNull($province);
    }

    public function testGetCountryArray(): void
    {
        $country = new Country();
        $country->setCountryCode('SG');
        $country->setName('SINGAPORE');
        $country->setCountryName('Singapore');

        $countryDao = $this->getMockBuilder(CountryDao::class)
            ->onlyMethods(['getCountryList'])
            ->getMock();
        $countryDao->expects($this->once())
            ->method('getCountryList')
            ->will($this->returnValue([$country]));

        $countryService = $this->getMockBuilder(CountryService::class)
            ->onlyMethods(['getNormalizerService'])
            ->getMock();
        $countryService->expects($this->once())
            ->method('getNormalizerService')
            ->will($this->returnValue(new NormalizerService()));

        $countryService->setCountryDao($countryDao);
        $countries = $countryService->getCountryArray();
        $this->assertCount(1, $countries);
        $this->assertEquals('SG', $countries[0]['id']);
        $this->assertEquals('Singapore', $countries[0]['label']);
    }
}
