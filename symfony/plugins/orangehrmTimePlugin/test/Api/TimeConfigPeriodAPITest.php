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

namespace OrangeHRM\Tests\Time\Api;

use OrangeHRM\Entity\Config;
use OrangeHRM\Entity\User;
use OrangeHRM\Framework\Services;
use OrangeHRM\ORM\Doctrine;
use OrangeHRM\Tests\Util\EndpointIntegrationTestCase;
use OrangeHRM\Tests\Util\Integration\TestCaseParams;
use OrangeHRM\Tests\Util\Mock\MockAuthUser;
use OrangeHRM\Time\Api\TimeConfigPeriodAPI;

/**
 * @group Time
 * @group APIv2
 */
class TimeConfigPeriodAPITest extends EndpointIntegrationTestCase
{
    /**
     * @dataProvider dataProviderForTestGetOne
     */
    public function testGetOne(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('TimeConfig.yaml');
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(TimeConfigPeriodAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'getOne', $testCaseParams);
    }

    public static function getOnePreHook(TestCaseParams $testCaseParams)
    {
        if ($testCaseParams->getName() == 'Timesheet period update') {
            /** @var Config $config */
            $config = Doctrine::getEntityManager()->getRepository(Config::class)->find('timesheet_period_set');
            $config->setValue('No');
            Doctrine::getEntityManager()->persist($config);
            Doctrine::getEntityManager()->flush($config);
        }
    }

    public function dataProviderForTestGetOne(): array
    {
        return $this->getTestCases('TimeConfigPeriodTestcase.yaml', 'GetOne');
    }

    /**
     * @dataProvider dataProviderForTestUpdate
     */
    public function testUpdate(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('TimeConfig.yaml');
        $authUser = $this->getMockBuilder(MockAuthUser::class)
            ->onlyMethods(['getUserId', 'getEmpNumber', 'removeAttribute', 'getAttribute'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->method('getUserId')
            ->willReturn($testCaseParams->getUserId());
        $authUser->method('removeAttribute')
            ->willReturnCallback(function (string $key) {
            });
        $authUser->method('getAttribute')
            ->willReturnCallback(fn (string $key, $default) => $default);
        $authUser->method('getEmpNumber')
            ->willReturn(
                $this->getEntityReference(
                    User::class,
                    $testCaseParams->getUserId()
                )->getEmployee()->getEmpNumber()
            );

        $this->createKernelWithMockServices([Services::AUTH_USER => $authUser]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(TimeConfigPeriodAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'update', $testCaseParams);
    }

    public function dataProviderForTestUpdate(): array
    {
        return $this->getTestCases('TimeConfigPeriodTestcase.yaml', 'Update');
    }

    public function testDelete(): void
    {
        $api = new TimeConfigPeriodAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->delete();
    }

    public function testGetValidationRuleForDelete(): void
    {
        $api = new TimeConfigPeriodAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForDelete();
    }
}
