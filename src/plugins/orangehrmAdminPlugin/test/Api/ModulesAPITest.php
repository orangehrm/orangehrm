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

use OrangeHRM\Admin\Api\ModulesAPI;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Service\ModuleService;
use OrangeHRM\Entity\User;
use OrangeHRM\Framework\Services;
use OrangeHRM\OAuth\Service\OAuthService;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\Mock\MockAuthUser;
use OrangeHRM\Tests\Util\TestDataService;
use Symfony\Component\Yaml\Yaml;

/**
 * @group Admin
 * @group APIv2
 */
class ModulesAPITest extends EndpointTestCase
{
    private ModulesAPI $modulesApi;

    protected function setUp(): void
    {
        $this->modulesApi = new ModulesAPI($this->getRequest());
        TestDataService::truncateSpecificTables(['OAuthClient']);
        TestDataService::populate(
            Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/ModuleDao.yml'
        );
    }

    protected function getTestCasesByKey($testCaseKey): array
    {
        $testCases = Yaml::parseFile(
            Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/testcases/ModulesAPI.yml'
        );
        if (array_key_exists($testCaseKey, $testCases)) {
            return $testCases[$testCaseKey];
        }
        return [];
    }

    public function testGettersAndSetters(): void
    {
        $classFieldTypeMap = [
            'moduleService' => ModuleService::class,
            'oAuthService' => OAuthService::class
        ];
        foreach ($classFieldTypeMap as $field => $type) {
            $setter = 'set' . ucfirst($field);
            $getter = 'get' . ucfirst($field);
            $this->assertInstanceOf($type, $this->modulesApi->$getter());
            $this->modulesApi->$setter(new $type());
            $this->assertInstanceOf($type, $this->modulesApi->$getter());
        }
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
        $this->modulesApi = new ModulesAPI($this->getRequest($params));
        $modules = $this->modulesApi->getAll();
        $this->assertEquals($result, $modules->normalize());
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
        $validationRule = $this->modulesApi->getValidationRuleForGetAll();
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

        $authUser = $this->getMockBuilder(MockAuthUser::class)
            ->onlyMethods(['getUserId', 'getEmpNumber', 'removeAttribute', 'getAttribute'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->method('getUserId')
            ->willReturn(1);
        $authUser->method('removeAttribute')
            ->willReturnCallback(function (string $key) {
            });
        $authUser->method('getAttribute')
            ->willReturnCallback(fn (string $key, $default) => $default);
        $authUser->method('getEmpNumber')
            ->willReturn(
                $this->getEntityReference(
                    User::class,
                    1
                )->getEmployee()->getEmpNumber()
            );

        $this->createKernelWithMockServices([Services::AUTH_USER => $authUser]);

        $this->modulesApi = new ModulesAPI($this->getRequest([], $params, []));
        $modules = $this->modulesApi->update();
        $this->assertEquals($result, $modules->normalize());
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
        $validationRule = $this->modulesApi->getValidationRuleForUpdate();
        $this->assertTrue($this->validate($params, $validationRule));
    }
}
