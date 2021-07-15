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

namespace OrangeHRM\Tests\Pim\Api;

use DateTime;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmployeeImmigrationRecord;
use OrangeHRM\Framework\Services;
use OrangeHRM\Pim\Api\EmployeeImmigrationRecordAPI;
use OrangeHRM\Pim\Dao\EmployeeImmigrationRecordDao;
use OrangeHRM\Pim\Service\EmployeeImmigrationRecordService;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\MockObject;

/**
 * @group Pim
 * @group APIv2
 */
class EmployeeImmigrationRecordAPITest extends EndpointTestCase
{
    public function testGetEmployeeImmigrationRecordService(): void
    {
        $api = new EmployeeImmigrationRecordAPI($this->getRequest());
        $this->assertTrue($api->getEmployeeImmigrationRecordService() instanceof EmployeeImmigrationRecordService);
    }

    public function testGetOne(): void
    {
        $empNumber = 1;
        $employeeImmigrationRecordDao = $this->getMockBuilder(EmployeeImmigrationRecordDao::class)
            ->onlyMethods(['getEmployeeImmigrationRecord'])
            ->getMock();

        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $immigrationRecord = new EmployeeImmigrationRecord();
        $immigrationRecord->setEmployee($employee);
        $immigrationRecord->setRecordId(1);
        $immigrationRecord->setNumber('RTF33323411');
        $immigrationRecord->setType(2);
        $immigrationRecord->setIssuedDate(new DateTime('2020-12-12'));
        $immigrationRecord->setExpiryDate(new DateTime('2021-12-12'));
        $immigrationRecord->setStatus('some status1');
        $immigrationRecord->setComment('test Comment1');
        $immigrationRecord->setReviewDate(new DateTime('2021-12-30'));
        $immigrationRecord->setCountryCode('LK');

        $employeeImmigrationRecordDao->expects($this->exactly(1))
            ->method('getEmployeeImmigrationRecord')
            ->with(1,1)
            ->will($this->returnValue($immigrationRecord));
        $employeeImmigrationRecordService = $this->getMockBuilder(EmployeeImmigrationRecordService::class)
            ->onlyMethods(['getEmployeeImmigrationRecordDao'])
            ->getMock();
        $employeeImmigrationRecordService->expects($this->exactly(1))
            ->method('getEmployeeImmigrationRecordDao')
            ->willReturn($employeeImmigrationRecordDao);

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => $employeeImmigrationRecordService,
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );

        /** @var MockObject&EmployeeImmigrationRecordAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeImmigrationRecordAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                    CommonParams::PARAMETER_ID => 1
                ]
            ]
        )->onlyMethods(['getEmployeeImmigrationRecordService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getEmployeeImmigrationRecordService')
            ->will($this->returnValue($employeeImmigrationRecordService));

        $result = $api->getOne();
        $this->assertEquals(
            [
                "id" => 1,
                "number" => "RTF33323411",
                "issuedDate" => "2020-12-12",
                "expiryDate" => "2021-12-12",
                "type" => 2,
                "status" => "some status1",
                "reviewDate" => "2021-12-30",
                "countryCode" => "LK",
                "comment" => "test Comment1",
            ],
            $result->normalize()
        );
    }

    public function testGetValidationRuleForGetOne(): void
    {
        $api = new EmployeeImmigrationRecordAPI($this->getRequest());
        $rules = $api->getValidationRuleForGetOne();
        $this->assertTrue(
            $this->validate(
                [CommonParams::PARAMETER_EMP_NUMBER => 1,CommonParams::PARAMETER_ID => 1],
                $rules
            )
        );
    }

    public function testDelete()
    {
        $empNumber = 1;
        $employeeImmigrationRecordDao = $this->getMockBuilder(EmployeeImmigrationRecordDao::class)
            ->onlyMethods(['deleteEmployeeImmigrationRecords'])
            ->getMock();

        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $immigrationRecord = new EmployeeImmigrationRecord();
        $immigrationRecord->setEmployee($employee);
        $immigrationRecord->setRecordId(1);
        $immigrationRecord->setNumber('RTF33323411');
        $immigrationRecord->setType(2);
        $immigrationRecord->setIssuedDate(new DateTime('2020-12-12'));
        $immigrationRecord->setExpiryDate(new DateTime('2021-12-12'));
        $immigrationRecord->setStatus('some status1');
        $immigrationRecord->setComment('test Comment1');
        $immigrationRecord->setReviewDate(new DateTime('2021-12-30'));
        $immigrationRecord->setCountryCode('LK');

        $employeeImmigrationRecordDao->expects($this->exactly(1))
            ->method('deleteEmployeeImmigrationRecords')
            ->with(1,[1])
            ->willReturn(1);
        $employeeImmigrationRecordService = $this->getMockBuilder(EmployeeImmigrationRecordService::class)
            ->onlyMethods(['getEmployeeImmigrationRecordDao'])
            ->getMock();
        $employeeImmigrationRecordService->expects($this->exactly(1))
            ->method('getEmployeeImmigrationRecordDao')
            ->willReturn($employeeImmigrationRecordDao);

        /** @var MockObject&EmployeeImmigrationRecordAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeImmigrationRecordAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    CommonParams::PARAMETER_IDS => [1],
                ]
            ]
        )->onlyMethods(['getEmployeeImmigrationRecordService'])
            ->getMock();
        $api->expects($this->exactly(1))
            ->method('getEmployeeImmigrationRecordService')
            ->will($this->returnValue($employeeImmigrationRecordService));

        $result = $api->delete();
        $this->assertEquals(
            [
                1
            ],
            $result->normalize()
        );
        $this->assertEquals(
            [
                "empNumber" => 1,
            ],
            $result->getMeta()->all()
        );
    }

    public function testGetValidationRuleForDelete(): void
    {
        $api = new EmployeeImmigrationRecordAPI($this->getRequest());
        $rules = $api->getValidationRuleForDelete();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => 1,
                    CommonParams::PARAMETER_IDS => [1],
                ],
                $rules
            )
        );
    }

    public function testUpdate()
    {
        $empNumber = 1;
        $employeeImmigrationRecordDao = $this->getMockBuilder(EmployeeImmigrationRecordDao::class)
            ->onlyMethods(['saveEmployeeImmigrationRecord', 'getEmployeeImmigrationRecord'])
            ->getMock();

        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $immigrationRecord = new EmployeeImmigrationRecord();
        $immigrationRecord->setEmployee($employee);
        $immigrationRecord->setRecordId(1);
        $immigrationRecord->setNumber('RTF33323411');
        $immigrationRecord->setType(2);
        $immigrationRecord->setIssuedDate(new DateTime('2020-12-12'));
        $immigrationRecord->setExpiryDate(new DateTime('2021-12-12'));
        $immigrationRecord->setStatus('some status1');
        $immigrationRecord->setComment('test Comment1');
        $immigrationRecord->setReviewDate(new DateTime('2021-12-30'));
        $immigrationRecord->setCountryCode('LK');

        $employeeImmigrationRecordDao->expects($this->exactly(1))
            ->method('getEmployeeImmigrationRecord')
            ->with(1,1)
            ->willReturn($immigrationRecord);

        $employeeImmigrationRecordDao->expects($this->exactly(1))
            ->method('saveEmployeeImmigrationRecord')
            ->with($immigrationRecord)
            ->will($this->returnValue($immigrationRecord));

        $employeeImmigrationRecordService = $this->getMockBuilder(EmployeeImmigrationRecordService::class)
            ->onlyMethods(['getEmployeeImmigrationRecordDao'])
            ->getMock();

        $employeeImmigrationRecordService->expects($this->exactly(2))
            ->method('getEmployeeImmigrationRecordDao')
            ->willReturn($employeeImmigrationRecordDao);

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => $employeeImmigrationRecordService,
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );

        /** @var MockObject&EmployeeImmigrationRecordAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeImmigrationRecordAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                    CommonParams::PARAMETER_ID => 1
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    EmployeeImmigrationRecordAPI::PARAMETER_NUMBER => "RTF33323415",
                    EmployeeImmigrationRecordAPI::PARAMETER_ISSUE_DATE => "2020-12-13",
                    EmployeeImmigrationRecordAPI::PARAMETER_EXPIRY_DATE => "2021-12-13",
                    EmployeeImmigrationRecordAPI::PARAMETER_TYPE => 1,
                    EmployeeImmigrationRecordAPI::PARAMETER_STATUS => 'some status',
                    EmployeeImmigrationRecordAPI::PARAMETER_REVIEW_DATE => '2021-12-31',
                    EmployeeImmigrationRecordAPI::PARAMETER_COUNTRY_CODE => 'LK',
                    EmployeeImmigrationRecordAPI::PARAMETER_COMMENT => 'test Comment',
                ]
            ]
        )->onlyMethods(['getEmployeeImmigrationRecordService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getEmployeeImmigrationRecordService')
            ->will($this->returnValue($employeeImmigrationRecordService));

        $result = $api->update();
        $this->assertEquals(
            [
                'id' => '1',
                "number" => "RTF33323415",
                "issuedDate" => "2020-12-13",
                "expiryDate" => "2021-12-13",
                "type" => 1,
                "status" => "some status",
                "reviewDate" => "2021-12-31",
                "countryCode" => "LK",
                "comment" => "test Comment",
            ],
            $result->normalize()
        );
        $this->assertEquals(
            [
                "empNumber" => 1,
            ],
            $result->getMeta()->all()
        );
    }

    public function testGetValidationRuleForUpdate(): void
    {
        $api = new EmployeeImmigrationRecordAPI($this->getRequest());
        $rules = $api->getValidationRuleForUpdate();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => 1,
                    CommonParams::PARAMETER_ID => 1,
                    EmployeeImmigrationRecordAPI::PARAMETER_NUMBER => "RTF33323415",
                    EmployeeImmigrationRecordAPI::PARAMETER_ISSUE_DATE => "2020-12-13",
                    EmployeeImmigrationRecordAPI::PARAMETER_EXPIRY_DATE => "2021-12-13",
                    EmployeeImmigrationRecordAPI::PARAMETER_TYPE => 1,
                    EmployeeImmigrationRecordAPI::PARAMETER_STATUS => 'some status',
                    EmployeeImmigrationRecordAPI::PARAMETER_REVIEW_DATE => '2021-12-31',
                    EmployeeImmigrationRecordAPI::PARAMETER_COUNTRY_CODE => 'LK',
                    EmployeeImmigrationRecordAPI::PARAMETER_COMMENT => 'test Comment',
                ],
                $rules
            )
        );
    }

    public function testCreate()
    {
        $empNumber = 1;
        $employeeImmigrationRecordDao = $this->getMockBuilder(EmployeeImmigrationRecordDao::class)
            ->onlyMethods(['saveEmployeeImmigrationRecord','getEmployeeImmigrationRecord'])
            ->getMock();

        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $immigrationRecord = new EmployeeImmigrationRecord();
        $immigrationRecord->setEmployee($employee);
        $immigrationRecord->setRecordId(1);
        $immigrationRecord->setNumber('RTF33323411');
        $immigrationRecord->setType(2);
        $immigrationRecord->setIssuedDate(new DateTime('2020-12-12'));
        $immigrationRecord->setExpiryDate(new DateTime('2021-12-12'));
        $immigrationRecord->setStatus('some status1');
        $immigrationRecord->setComment('test Comment1');
        $immigrationRecord->setReviewDate(new DateTime('2021-12-30'));
        $immigrationRecord->setCountryCode('LK');

        $employeeImmigrationRecordDao->expects($this->exactly(1))
            ->method('getEmployeeImmigrationRecord')
            ->with(1, 1)
            ->willReturn($immigrationRecord);

        $employeeImmigrationRecordDao->expects($this->exactly(1))
            ->method('saveEmployeeImmigrationRecord')
            ->with($immigrationRecord)
            ->will($this->returnValue($immigrationRecord));

        $employeeImmigrationRecordService = $this->getMockBuilder(EmployeeImmigrationRecordService::class)
            ->onlyMethods(['getEmployeeImmigrationRecordDao'])
            ->getMock();

        $employeeImmigrationRecordService->expects($this->exactly(2))
            ->method('getEmployeeImmigrationRecordDao')
            ->willReturn($employeeImmigrationRecordDao);

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => $employeeImmigrationRecordService,
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );



        /** @var MockObject&EmployeeImmigrationRecordAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeImmigrationRecordAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                    CommonParams::PARAMETER_ID => 1
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    EmployeeImmigrationRecordAPI::PARAMETER_NUMBER => "RTF33323415",
                    EmployeeImmigrationRecordAPI::PARAMETER_ISSUE_DATE => "2020-12-13",
                    EmployeeImmigrationRecordAPI::PARAMETER_EXPIRY_DATE => "2021-12-13",
                    EmployeeImmigrationRecordAPI::PARAMETER_TYPE => 1,
                    EmployeeImmigrationRecordAPI::PARAMETER_STATUS => 'some status',
                    EmployeeImmigrationRecordAPI::PARAMETER_REVIEW_DATE => '2021-12-31',
                    EmployeeImmigrationRecordAPI::PARAMETER_COUNTRY_CODE => 'LK',
                    EmployeeImmigrationRecordAPI::PARAMETER_COMMENT => 'test Comment',
                ]
            ]
        )->onlyMethods(['getEmployeeImmigrationRecordService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getEmployeeImmigrationRecordService')
            ->will($this->returnValue($employeeImmigrationRecordService));

        $result = $api->create();
        $this->assertEquals(
            [
                'id' => '1',
                "number" => "RTF33323415",
                "issuedDate" => "2020-12-13",
                "expiryDate" => "2021-12-13",
                "type" => 1,
                "status" => "some status",
                "reviewDate" => "2021-12-31",
                "countryCode" => "LK",
                "comment" => "test Comment",
            ],
            $result->normalize()
        );
        $this->assertEquals(
            [
                "empNumber" => 1,
            ],
            $result->getMeta()->all()
        );
    }

    public function testGetValidationRuleForCreate(): void
    {
        $api = new EmployeeImmigrationRecordAPI($this->getRequest());
        $rules = $api->getValidationRuleForCreate();
        $this->assertTrue(
            $this->validate(
                [
                    EmployeeImmigrationRecordAPI::PARAMETER_NAME => 'Dismissed',
                ],
                $rules
            )
        );
    }

    public function testGetAll()
    {
        $empNumber = 1;
        $employeeImmigrationRecordDao = $this->getMockBuilder(EmployeeImmigrationRecordDao::class)
            ->onlyMethods(['getEmployeeImmigrationRecordList', 'getSearchEmployeeImmigrationRecordsCount'])
            ->getMock();

        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $immigrationRecord1 = new EmployeeImmigrationRecord();
        $immigrationRecord1->setEmployee($employee);
        $immigrationRecord1->setRecordId(1);
        $immigrationRecord1->setNumber('RTF33323411');
        $immigrationRecord1->setType(1);
        $immigrationRecord1->setIssuedDate(new DateTime('2020-12-11'));
        $immigrationRecord1->setExpiryDate(new DateTime('2021-12-11'));
        $immigrationRecord1->setStatus('some status1');
        $immigrationRecord1->setComment('test Comment1');
        $immigrationRecord1->setReviewDate(new DateTime('2021-12-30'));
        $immigrationRecord1->setCountryCode('LK');

        $immigrationRecord2 = new EmployeeImmigrationRecord();
        $immigrationRecord2->setEmployee($employee);
        $immigrationRecord2->setRecordId(2);
        $immigrationRecord2->setNumber('RTF33323412');
        $immigrationRecord2->setType(1);
        $immigrationRecord2->setIssuedDate(new DateTime('2020-12-12'));
        $immigrationRecord2->setExpiryDate(new DateTime('2021-12-12'));
        $immigrationRecord2->setStatus('some status2');
        $immigrationRecord2->setComment('test Comment2');
        $immigrationRecord2->setReviewDate(new DateTime('2021-12-31'));
        $immigrationRecord2->setCountryCode('LK');

        $employeeImmigrationRecordDao->expects($this->exactly(1))
            ->method('getEmployeeImmigrationRecordList')
            ->willReturn([$immigrationRecord1, $immigrationRecord2]);

        $employeeImmigrationRecordDao->expects($this->exactly(1))
            ->method('getSearchEmployeeImmigrationRecordsCount')
            ->willReturn(2);

        $employeeImmigrationRecordService = $this->getMockBuilder(EmployeeImmigrationRecordService::class)
            ->onlyMethods(['getEmployeeImmigrationRecordDao'])
            ->getMock();

        $employeeImmigrationRecordService->expects($this->exactly(2))
            ->method('getEmployeeImmigrationRecordDao')
            ->willReturn($employeeImmigrationRecordDao);

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => $employeeImmigrationRecordService,
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );

        /** @var MockObject&EmployeeImmigrationRecordAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeImmigrationRecordAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    EmployeeImmigrationRecordAPI::PARAMETER_COUNTRY_CODE => 'LK',
                ]

            ]
        )->onlyMethods(['getEmployeeImmigrationRecordService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getEmployeeImmigrationRecordService')
            ->will($this->returnValue($employeeImmigrationRecordService));

        $result = $api->getAll();
        $this->assertEquals(
            [
                [
                    'id' => '1',
                    "number" => "RTF33323411",
                    "issuedDate" => "2020-12-11",
                    "expiryDate" => "2021-12-11",
                    "type" => 1,
                    "status" => "some status1",
                    "reviewDate" => "2021-12-30",
                    "countryCode" => "LK",
                    "comment" => "test Comment1",
                ],
                [
                    'id' => '2',
                    "number" => "RTF33323412",
                    "issuedDate" => "2020-12-12",
                    "expiryDate" => "2021-12-12",
                    "type" => 2,
                    "status" => "some status2",
                    "reviewDate" => "2021-12-31",
                    "countryCode" => "LK",
                    "comment" => "test Comment2",
                ]
            ],
            $result->normalize()
        );
        $this->assertEquals(
            [
                "empNumber" => 1,
                "total" => 2
            ],
            $result->getMeta()->all()
        );
    }

    public function testGetValidationRuleForGetAll(): void
    {
        $api = new EmployeeImmigrationRecordAPI($this->getRequest());
        $rules = $api->getValidationRuleForGetAll();
        $this->assertTrue(
            $this->validate(
                [],
                $rules
            )
        );
    }
}
