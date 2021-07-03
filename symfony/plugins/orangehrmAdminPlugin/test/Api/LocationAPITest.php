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
 * Please refer http://www.orangehrm.com/Files/OrangeHRM_Commercial_License.pdf for the license which includes terms and conditions on using this software.
 *
 */

namespace Api;

use OrangeHRM\Admin\Api\LocationAPI;
use OrangeHRM\Admin\Api\NationalityAPI;
use OrangeHRM\Admin\Service\LocationService;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\RequestParams;
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
            'locationService' => LocationService::class
        ];
        foreach ($classFieldTypeMap as $field => $type) {
            $setter = 'set' . ucfirst($field);
            $getter = 'get' . ucfirst($field);
            $this->assertInstanceOf($type, $this->locationApi->$getter());
            $this->locationApi->$setter(new $type());
            $this->assertInstanceOf($type, $this->locationApi->$getter());
        }
    }

    public function dataProviderForTestOne()
    {
        return $this->getTestCasesByKey('GetOne');
    }

    /**
     * @dataProvider dataProviderForTestOne
     */
    public function testOne($params, $result, $exception = false)
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
    public function testGetValidationRuleForGetOne($params, $isValid, $exception = false)
    {
        if ($exception) {
            $this->expectException($exception['class']);
            $this->expectExceptionMessage($exception['message']);
        }
        $validationRule = $this->locationApi->getValidationRuleForGetOne();
        $this->assertTrue($this->validate($params, $validationRule));
    }

}
