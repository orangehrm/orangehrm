<?php
/**
 * OrangeHRM Enterprise is a closed sourced comprehensive Human Resource Management (HRM)
 * System that captures all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM Inc is the owner of the patent, copyright, trade secrets, trademarks and any
 * other intellectual property rights which subsist in the Licensed Materials. OrangeHRM Inc
 * is the owner of the media / downloaded OrangeHRM Enterprise software files on which the
 * Licensed Materials are received. Title to the Licensed Materials and media shall remain
 * vested in OrangeHRM Inc. For the avoidance of doubt title and all intellectual property
 * rights to any design, new software, new protocol, new interface, enhancement, update,
 * derivative works, revised screen text or any other items that OrangeHRM Inc creates for
 * Customer shall remain vested in OrangeHRM Inc. Any rights not expressly granted herein are
 * reserved to OrangeHRM Inc.
 *
 * Please refer http://www.orangehrm.com/Files/OrangeHRM_Commercial_License.pdf for the license which includes terms
 * and conditions on using this software.
 *
 */

namespace Api;

use OrangeHRM\Admin\Api\LocationAPI;
use OrangeHRM\Admin\Api\NationalityAPI;
use OrangeHRM\Admin\Service\CountryService;
use OrangeHRM\Admin\Service\LocationService;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Framework\Services;
use OrangeHRM\Pim\Dto\LocationSearchFilterParams;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\TestDataService;
use Symfony\Component\Yaml\Yaml;

class LocationAPITest extends EndpointTestCase
{

    private LocationAPI $locationApi;

    protected function setUp(): void
    {
        $this->locationApi = new LocationAPI($this->getRequest());
        TestDataService::populate(
            Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/LocationDao.yml'
        );
        $this->getContainer()->register(
            Services::COUNTRY_SERVICE,
            CountryService::class
        );
    }

    protected function getTestCasesByKey($testCaseKey)
    {
        $testCases = Yaml::parseFile(
            Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/testcases/LocationAPI.yml'
        );
        if (array_key_exists($testCaseKey, $testCases)) {
            return $testCases[$testCaseKey];
        }
        return [];
    }

    public function testGettersAndSetters()
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

    public function dataProviderForTestGetOne()
    {
        return $this->getTestCasesByKey('GetOne');
    }

    /**
     * @dataProvider dataProviderForTestGetOne
     */
    public function testGetOne($params, $result, $exception = false)
    {
        if ($exception) {
            $this->expectException($exception['class']);
            $this->expectExceptionMessage($exception['message']);
        }
        $this->locationApi = new LocationAPI($this->getRequest([], [], [CommonParams::PARAMETER_ID => $params['id']]));
        $location = $this->locationApi->getOne();
        $this->assertEquals($result, $location->normalize());
    }

    public function dataProviderForTestGetValidationRuleForGetOne()
    {
        return $this->getTestCasesByKey('GetValidationRuleForGetOne');
    }

    /**
     * @dataProvider dataProviderForTestGetValidationRuleForGetOne
     */
    public function testGetValidationRuleForGetOne($params, $exception = false)
    {
        if ($exception) {
            $this->expectException($exception['class']);
            $this->expectExceptionMessage($exception['message']);
        }
        $validationRule = $this->locationApi->getValidationRuleForGetOne();
        $this->assertTrue($this->validate($params, $validationRule));
    }

    public function dataProviderForTestGetAll()
    {
        return $this->getTestCasesByKey('GetAll');
    }

    /**
     * @dataProvider dataProviderForTestGetAll
     */
    public function testGetAll($params, $result, $exception = false)
    {
        if ($exception) {
            $this->expectException($exception['class']);
            $this->expectExceptionMessage($exception['message']);
        }
        $this->locationApi = new LocationAPI($this->getRequest($params));
        $locations = $this->locationApi->getAll();
        $this->assertEquals($result, $locations->normalize());
    }

    public function dataProviderForTestGetValidationRuleForGetAll()
    {
        return $this->getTestCasesByKey('GetValidationRuleForGetAll');
    }

    /**
     * @dataProvider dataProviderForTestGetValidationRuleForGetAll
     */
    public function testGetValidationRuleForGetAll($params, $exception = false)
    {
        if ($exception) {
            $this->expectException($exception['class']);
            $this->expectExceptionMessage($exception['message']);
        }
        $validationRule = $this->locationApi->getValidationRuleForGetAll();
        $this->assertTrue($this->validate($params, $validationRule));
    }

    public function dataProviderForTestCreate()
    {
        return $this->getTestCasesByKey('Create');
    }

    /**
     * @dataProvider dataProviderForTestCreate
     */
    public function testCreate($params, $result, $exception = false)
    {
        if ($exception) {
            $this->expectException($exception['class']);
            $this->expectExceptionMessage($exception['message']);
        }
        $this->locationApi = new LocationAPI($this->getRequest([], $params));
        $location = $this->locationApi->create();
        $this->assertEquals($result, $location->normalize());
    }

    public function dataProviderForTestGetValidationRuleForCreate()
    {
        return $this->getTestCasesByKey('GetValidationRuleForCreate');
    }

    /**
     * @dataProvider dataProviderForTestGetValidationRuleForCreate
     */
    public function testGetValidationRuleForCreate($params, $exception = false)
    {
        if ($exception) {
            $this->expectException($exception['class']);
            $this->expectExceptionMessage($exception['message']);
        }
        $validationRule = $this->locationApi->getValidationRuleForCreate();
        $this->assertTrue($this->validate($params, $validationRule));
    }

    public function dataProviderForTestUpdate()
    {
        return $this->getTestCasesByKey('Update');
    }

    /**
     * @dataProvider dataProviderForTestUpdate
     */
    public function testUpdate($params, $result, $exception = false)
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

    public function dataProviderForTestGetValidationRuleForUpdate()
    {
        return $this->getTestCasesByKey('GetValidationRuleForUpdate');
    }

    /**
     * @dataProvider dataProviderForTestGetValidationRuleForUpdate
     */
    public function testGetValidationRuleForUpdate($params, $exception = false)
    {
        if ($exception) {
            $this->expectException($exception['class']);
            $this->expectExceptionMessage($exception['message']);
        }
        $validationRule = $this->locationApi->getValidationRuleForUpdate();
        $this->assertTrue($this->validate($params, $validationRule));
    }

    public function dataProviderForTestDelete()
    {
        return $this->getTestCasesByKey('Delete');
    }

    /**
     * @dataProvider dataProviderForTestDelete
     */
    public function testDelete($params, $result, $exception = false)
    {
        if ($exception) {
            $this->expectException($exception['class']);
            $this->expectExceptionMessage($exception['message']);
        }
        $this->locationApi = new LocationAPI($this->getRequest([], $params));
        $locationSearchFilterParams = new LocationSearchFilterParams();
        $this->assertEquals($result['preCount'], count($this->locationApi->getLocationService()->searchLocations($locationSearchFilterParams)));
        $location = $this->locationApi->delete();
        $this->assertEquals($result['postCount'], count($this->locationApi->getLocationService()->searchLocations($locationSearchFilterParams)));
        $this->assertEquals($result['ids'], $location->normalize());
    }

    public function dataProviderForTestGetValidationRuleForDelete()
    {
        return $this->getTestCasesByKey('GetValidationRuleForDelete');
    }

    /**
     * @dataProvider dataProviderForTestGetValidationRuleForDelete
     */
    public function testGetValidationRuleForDelete($params, $exception = false)
    {
        if ($exception) {
            $this->expectException($exception['class']);
            $this->expectExceptionMessage($exception['message']);
        }
        $validationRule = $this->locationApi->getValidationRuleForDelete();
        $this->assertTrue($this->validate($params, $validationRule));
    }


}
