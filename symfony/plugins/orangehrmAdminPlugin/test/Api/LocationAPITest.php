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

namespace Api;

use OrangeHRM\Admin\Api\LocationAPI;
use OrangeHRM\Admin\Dto\LocationSearchFilterParams;
use OrangeHRM\Admin\Service\CountryService;
use OrangeHRM\Admin\Service\LocationService;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Admin
 * @group APIv2
 */
class LocationAPITest extends EndpointTestCase
{
    private LocationAPI $locationApi;

    protected function setUp(): void
    {
        $this->locationApi = new LocationAPI($this->getRequest());
        TestDataService::populate(
            Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/LocationDao.yml'
        );
        $this->createKernelWithMockServices(
            [
                Services::COUNTRY_SERVICE => new CountryService(),
            ]
        );
    }

    protected function getTestCasesByKey($testCaseKey): array
    {
        return TestDataService::loadFixtures(
            Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/testcases/LocationAPI.yml',
            $testCaseKey
        );
    }

    public function testGettersAndSetters(): void
    {
        $classFieldTypeMap = [
            'locationService' => LocationService::class,
        ];
        foreach ($classFieldTypeMap as $field => $type) {
            $setter = 'set' . ucfirst($field);
            $getter = 'get' . ucfirst($field);
            $this->assertInstanceOf($type, $this->locationApi->$getter());
            $this->locationApi->$setter(new $type());
            $this->assertInstanceOf($type, $this->locationApi->$getter());
        }
    }

    public function dataProviderForTestGetOne(): array
    {
        return $this->getTestCasesByKey('GetOne');
    }

    /**
     * @dataProvider dataProviderForTestGetOne
     */
    public function testGetOne($params, $result, $exception = false): void
    {
        if ($exception) {
            $this->expectException($exception['class']);
            $this->expectExceptionMessage($exception['message']);
        }
        $this->locationApi = new LocationAPI($this->getRequest([], [], [CommonParams::PARAMETER_ID => $params['id']]));
        $location = $this->locationApi->getOne();
        $this->assertEquals($result, $location->normalize());
    }

    public function dataProviderForTestGetValidationRuleForGetOne(): array
    {
        return $this->getTestCasesByKey('GetValidationRuleForGetOne');
    }

    /**
     * @dataProvider dataProviderForTestGetValidationRuleForGetOne
     */
    public function testGetValidationRuleForGetOne($params, $exception = false): void
    {
        if ($exception) {
            $this->expectException($exception['class']);
            $this->expectExceptionMessage($exception['message']);
        }
        $validationRule = $this->locationApi->getValidationRuleForGetOne();
        $this->assertTrue($this->validate($params, $validationRule));
    }

    public function dataProviderForTestGetAll(): array
    {
        return $this->getTestCasesByKey('GetAll');
    }

    /**
     * @dataProvider dataProviderForTestGetAll
     */
    public function testGetAll($params, $result, $exception = false): void
    {
        if ($exception) {
            $this->expectException($exception['class']);
            $this->expectExceptionMessage($exception['message']);
        }
        $this->locationApi = new LocationAPI($this->getRequest($params));
        $locations = $this->locationApi->getAll();
        $this->assertEquals($result, $locations->normalize());
    }

    public function dataProviderForTestGetValidationRuleForGetAll(): array
    {
        return $this->getTestCasesByKey('GetValidationRuleForGetAll');
    }

    /**
     * @dataProvider dataProviderForTestGetValidationRuleForGetAll
     */
    public function testGetValidationRuleForGetAll($params, $exception = false): void
    {
        if ($exception) {
            $this->expectException($exception['class']);
            $this->expectExceptionMessage($exception['message']);
        }
        $validationRule = $this->locationApi->getValidationRuleForGetAll();
        $this->assertTrue($this->validate($params, $validationRule));
    }

    public function dataProviderForTestCreate(): array
    {
        return $this->getTestCasesByKey('Create');
    }

    /**
     * @dataProvider dataProviderForTestCreate
     */
    public function testCreate($params, $result, $exception = false): void
    {
        if ($exception) {
            $this->expectException($exception['class']);
            $this->expectExceptionMessage($exception['message']);
        }
        $this->locationApi = new LocationAPI($this->getRequest([], $params));
        $location = $this->locationApi->create();
        $this->assertEquals($result, $location->normalize());
    }

    public function dataProviderForTestGetValidationRuleForCreate(): array
    {
        return $this->getTestCasesByKey('GetValidationRuleForCreate');
    }

    /**
     * @dataProvider dataProviderForTestGetValidationRuleForCreate
     */
    public function testGetValidationRuleForCreate($params, $exception = false): void
    {
        if ($exception) {
            $this->expectException($exception['class']);
            $this->expectExceptionMessage($exception['message']);
        }
        $validationRule = $this->locationApi->getValidationRuleForCreate();
        $this->assertTrue($this->validate($params, $validationRule));
    }

    public function dataProviderForTestUpdate(): array
    {
        return $this->getTestCasesByKey('Update');
    }

    /**
     * @dataProvider dataProviderForTestUpdate
     */
    public function testUpdate($params, $result, $exception = false): void
    {
        if ($exception) {
            $this->expectException($exception['class']);
            $this->expectExceptionMessage($exception['message']);
        }
        $id = $params['id'];
        unset($params['id']);
        $this->locationApi = new LocationAPI($this->getRequest([], $params, ['id' => $id]));
        $location = $this->locationApi->update();
        $this->assertEquals($result, $location->normalize());
    }

    public function dataProviderForTestGetValidationRuleForUpdate(): array
    {
        return $this->getTestCasesByKey('GetValidationRuleForUpdate');
    }

    /**
     * @dataProvider dataProviderForTestGetValidationRuleForUpdate
     */
    public function testGetValidationRuleForUpdate($params, $exception = false): void
    {
        if ($exception) {
            $this->expectException($exception['class']);
            $this->expectExceptionMessage($exception['message']);
        }
        $validationRule = $this->locationApi->getValidationRuleForUpdate();
        $this->assertTrue($this->validate($params, $validationRule));
    }

    public function dataProviderForTestDelete(): array
    {
        return $this->getTestCasesByKey('Delete');
    }

    /**
     * @dataProvider dataProviderForTestDelete
     */
    public function testDelete($params, $result, $exception = false): void
    {
        if ($exception) {
            $this->expectException($exception['class']);
            $this->expectExceptionMessage($exception['message']);
        }
        $this->locationApi = new LocationAPI($this->getRequest([], $params));
        $locationSearchFilterParams = new LocationSearchFilterParams();
        $this->assertEquals(
            $result['preCount'],
            count(
                $this->locationApi->getLocationService()->searchLocations($locationSearchFilterParams)
            )
        );
        $location = $this->locationApi->delete();
        $this->assertEquals(
            $result['postCount'],
            count(
                $this->locationApi->getLocationService()->searchLocations($locationSearchFilterParams)
            )
        );
        $this->assertEquals($result['ids'], $location->normalize());
    }

    public function dataProviderForTestGetValidationRuleForDelete(): array
    {
        return $this->getTestCasesByKey('GetValidationRuleForDelete');
    }

    /**
     * @dataProvider dataProviderForTestGetValidationRuleForDelete
     */
    public function testGetValidationRuleForDelete($params, $exception = false): void
    {
        if ($exception) {
            $this->expectException($exception['class']);
            $this->expectExceptionMessage($exception['message']);
        }
        $validationRule = $this->locationApi->getValidationRuleForDelete();
        $this->assertTrue($this->validate($params, $validationRule));
    }
}
