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

namespace OrangeHRM\Tests\Leave\Api;

use DateTime;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Core\Service\NormalizerService;
use OrangeHRM\Entity\LeavePeriodHistory;
use OrangeHRM\Framework\Services;
use OrangeHRM\Leave\Api\LeavePeriodAPI;
use OrangeHRM\Leave\Dto\LeavePeriod;
use OrangeHRM\Leave\Service\LeaveConfigurationService;
use OrangeHRM\Leave\Service\LeavePeriodService;
use OrangeHRM\Tests\Util\EndpointIntegrationTestCase;
use OrangeHRM\Tests\Util\Integration\TestCaseParams;
use OrangeHRM\Tests\Util\MockObject;

/**
 * @group Leave
 * @group APIv2
 */
class LeavePeriodAPITest extends EndpointIntegrationTestCase
{
    public function testGetOne(): void
    {
        $leavePeriodHistory = new LeavePeriodHistory();
        $leavePeriodHistory->setStartMonth(2);
        $leavePeriodHistory->setStartDay(3);
        $leavePeriodHistory->setCreatedAt(new DateTime('2021-08-19'));

        $service = $this->getMockBuilder(LeavePeriodService::class)
            ->onlyMethods(['getCurrentLeavePeriodStartDateAndMonth', 'getCurrentLeavePeriod'])
            ->getMock();
        $service->expects($this->once())
            ->method('getCurrentLeavePeriodStartDateAndMonth')
            ->willReturn($leavePeriodHistory);
        $service->expects($this->once())
            ->method('getCurrentLeavePeriod')
            ->willReturn(new LeavePeriod(new DateTime('2021-01-01'), new DateTime('2021-12-31')));

        $configService = $this->getMockBuilder(LeaveConfigurationService::class)
            ->onlyMethods(['isLeavePeriodDefined'])
            ->getMock();
        $configService->expects($this->once())
            ->method('isLeavePeriodDefined')
            ->willReturn(true);

        /** @var MockObject&LeavePeriodAPI $api */
        $api = $this->getApiEndpointMockBuilder(LeavePeriodAPI::class)
            ->onlyMethods(['getLeavePeriodService', 'getLeaveConfigService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getLeavePeriodService')
            ->willReturn($service);
        $api->expects($this->once())
            ->method('getLeaveConfigService')
            ->willReturn($configService);

        $this->createKernelWithMockServices(
            [
                Services::NORMALIZER_SERVICE => new NormalizerService(),
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );
        $result = $api->getOne();
        $this->assertEquals(
            [
                'startMonth' => 2,
                'startDay' => 3,
                'createdAt' => '2021-08-19',
            ],
            $result->normalize()
        );
        $this->assertEquals(
            [
                'leavePeriodDefined' => true,
                'currentLeavePeriod' => [
                    'startDate' => '2021-01-01',
                    'endDate' => '2021-12-31',
                ]
            ],
            $result->getMeta()->all()
        );
    }

    public function testGetOneLeavePeriodUndefined(): void
    {
        $service = $this->getMockBuilder(LeavePeriodService::class)
            ->onlyMethods(['getCurrentLeavePeriodStartDateAndMonth', 'getCurrentLeavePeriod'])
            ->getMock();
        $service->expects($this->once())
            ->method('getCurrentLeavePeriodStartDateAndMonth')
            ->willReturn(null);
        $service->expects($this->never())
            ->method('getCurrentLeavePeriod');

        $configService = $this->getMockBuilder(LeaveConfigurationService::class)
            ->onlyMethods(['isLeavePeriodDefined'])
            ->getMock();
        $configService->expects($this->once())
            ->method('isLeavePeriodDefined')
            ->willReturn(false);

        /** @var MockObject&LeavePeriodAPI $api */
        $api = $this->getApiEndpointMockBuilder(LeavePeriodAPI::class)
            ->onlyMethods(['getLeavePeriodService', 'getLeaveConfigService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getLeavePeriodService')
            ->willReturn($service);
        $api->expects($this->once())
            ->method('getLeaveConfigService')
            ->willReturn($configService);

        $this->createKernelWithMockServices(
            [
                Services::NORMALIZER_SERVICE => new NormalizerService(),
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );
        $result = $api->getOne();
        $this->assertEquals(
            [
                'startMonth' => 1,
                'startDay' => 1,
                'createdAt' => (new DateTime())->format('Y-m-d'),
            ],
            $result->normalize()
        );
        $this->assertEquals(
            [
                'leavePeriodDefined' => false,
                'currentLeavePeriod' => null,
            ],
            $result->getMeta()->all()
        );
    }

    public function testGetValidationRuleForGetOne(): void
    {
        $api = new LeavePeriodAPI($this->getRequest());
        $rules = $api->getValidationRuleForGetOne();
        $this->assertTrue(
            $this->validate(
                [CommonParams::PARAMETER_ID => 1],
                $rules
            )
        );

        $this->expectInvalidParamException();
        $this->validate(
            ['invalid' => ''],
            $rules
        );
    }

    public function testGetAll(): void
    {
        $leavePeriods = [
            new LeavePeriod(new DateTime('2021-01-01'), new DateTime('2021-12-31')),
            new LeavePeriod(new DateTime('2022-01-01'), new DateTime('2022-12-31'))
        ];

        $service = $this->getMockBuilder(LeavePeriodService::class)
            ->onlyMethods(['getGeneratedLeavePeriodList', 'getCurrentLeavePeriod'])
            ->getMock();
        $service->expects($this->once())
            ->method('getGeneratedLeavePeriodList')
            ->willReturn($leavePeriods);
        $service->expects($this->once())
            ->method('getCurrentLeavePeriod')
            ->willReturn(new LeavePeriod(new DateTime('2021-01-01'), new DateTime('2021-12-31')));

        $configService = $this->getMockBuilder(LeaveConfigurationService::class)
            ->onlyMethods(['isLeavePeriodDefined'])
            ->getMock();
        $configService->expects($this->once())
            ->method('isLeavePeriodDefined')
            ->willReturn(true);

        /** @var MockObject&LeavePeriodAPI $api */
        $api = $this->getApiEndpointMockBuilder(LeavePeriodAPI::class)
            ->onlyMethods(['getLeavePeriodService', 'getLeaveConfigService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getLeavePeriodService')
            ->willReturn($service);
        $api->expects($this->once())
            ->method('getLeaveConfigService')
            ->willReturn($configService);

        $this->createKernelWithMockServices(
            [
                Services::NORMALIZER_SERVICE => new NormalizerService(),
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );
        $result = $api->getAll();
        $this->assertEquals(
            [
                ['startDate' => '2021-01-01', 'endDate' => '2021-12-31'],
                ['startDate' => '2022-01-01', 'endDate' => '2022-12-31'],
            ],
            $result->normalize()
        );
        $this->assertEquals(
            [
                'leavePeriodDefined' => true,
                'currentLeavePeriod' => [
                    'startDate' => '2021-01-01',
                    'endDate' => '2021-12-31',
                ]
            ],
            $result->getMeta()->all()
        );
    }

    /**
     * @dataProvider dataProviderForTestUpdate
     */
    public function testUpdate(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('LeavePeriodDao.yml');
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);

        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(LeavePeriodAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'update', $testCaseParams);
    }

    public function dataProviderForTestUpdate(): array
    {
        return $this->getTestCases('LeavePeriodAPITestCases.yaml', 'Update');
    }
}
