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
use Generator;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Authorization\Manager\BasicUserRoleManager;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Entity\Decorator\EmpContractDecorator;
use OrangeHRM\Entity\EmpContract;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmployeeAttachment;
use OrangeHRM\Entity\User;
use OrangeHRM\Framework\Services;
use OrangeHRM\Pim\Api\EmploymentContractAPI;
use OrangeHRM\Pim\Dao\EmploymentContractDao;
use OrangeHRM\Pim\Service\EmploymentContractService;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\MockObject;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group APIv2
 */
class EmploymentContractAPITest extends EndpointTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([EmpContract::class, EmployeeAttachment::class]);
    }

    public function testGetEmploymentContractService(): void
    {
        $api = new EmploymentContractAPI($this->getRequest());
        $this->assertTrue($api->getEmploymentContractService() instanceof EmploymentContractService);
    }

    public function testDelete(): void
    {
        $api = new EmploymentContractAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->delete();
    }

    public function testGetValidationRuleForDelete(): void
    {
        $api = new EmploymentContractAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForDelete();
    }

    public function testGetOne(): void
    {
        $empNumber = 1;
        $employmentContractDao = $this->getMockBuilder(EmploymentContractDao::class)
            ->onlyMethods(['getEmploymentContractByEmpNumber'])
            ->getMock();

        $employee = new Employee();
        $employee->setEmpNumber($empNumber);
        $empContract = new EmpContract();
        $empContract->setEmployee($employee);
        $empContract->setContractId('1');
        $empContract->setStartDate(new DateTime('2020-05-23'));
        $empContract->setEndDate(new DateTime('2021-05-23'));

        $map = [
            [$empNumber, $empContract],
            [2, null],
        ];
        $employmentContractDao->expects($this->exactly(2))
            ->method('getEmploymentContractByEmpNumber')
            ->will($this->returnValueMap($map));

        $employmentContractService = $this->getMockBuilder(EmploymentContractService::class)
            ->onlyMethods(['getEmploymentContractDao'])
            ->getMock();

        $employmentContractService->expects($this->exactly(2))
            ->method('getEmploymentContractDao')
            ->willReturn($employmentContractDao);

        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService()]);
        /** @var MockObject&EmploymentContractAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmploymentContractAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [CommonParams::PARAMETER_EMP_NUMBER => $empNumber]
            ]
        )->onlyMethods(['getEmploymentContractService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getEmploymentContractService')
            ->will($this->returnValue($employmentContractService));

        $result = $api->getOne();
        $this->assertEquals(
            [
                'startDate' => '2020-05-23',
                'endDate' => '2021-05-23',
                'contractAttachment' => [
                    'id' => null,
                    'filename' => null,
                    'size' => null,
                    'fileType' => null,
                    'attachedBy' => null,
                    'attachedByName' => null,
                    'attachedTime' => null,
                    'attachedDate' => null,
                ]
            ],
            $result->normalize()
        );
        $this->assertEquals(
            ['empNumber' => $empNumber],
            $result->getMeta()->all()
        );

        /** @var MockObject&EmploymentContractAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmploymentContractAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [CommonParams::PARAMETER_EMP_NUMBER => 2]
            ]
        )->onlyMethods(['getEmploymentContractService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getEmploymentContractService')
            ->will($this->returnValue($employmentContractService));

        $result = $api->getOne();
        $this->assertEquals(
            [
                'startDate' => null,
                'endDate' => null,
                'contractAttachment' => [
                    'id' => null,
                    'filename' => null,
                    'size' => null,
                    'fileType' => null,
                    'attachedBy' => null,
                    'attachedByName' => null,
                    'attachedTime' => null,
                    'attachedDate' => null,
                ]
            ],
            $result->normalize()
        );
        $this->assertEquals(
            ['empNumber' => 2],
            $result->getMeta()->all()
        );
    }

    public function testGetValidationRuleForGetOne(): void
    {
        $userRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAccessibleEntityIds'])
            ->getMock();
        $userRoleManager->expects($this->exactly(0))
            ->method('getAccessibleEntityIds')
            ->willReturn([1, 2]);

        $authUser = $this->getMockBuilder(\OrangeHRM\Authentication\Auth\User::class)
            ->onlyMethods(['getEmpNumber'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->expects($this->once())
            ->method('getEmpNumber')
            ->willReturn(1);
        $this->createKernelWithMockServices(
            [
                Services::USER_ROLE_MANAGER => $userRoleManager,
                Services::AUTH_USER => $authUser
            ]
        );
        $api = new EmploymentContractAPI($this->getRequest());
        $rules = $api->getValidationRuleForGetOne();
        $this->assertTrue(
            $this->validate(
                [CommonParams::PARAMETER_EMP_NUMBER => 1],
                $rules
            )
        );
    }

    /**
     * @dataProvider updateOnlyDatesDataProvider
     * @param $empNumber
     * @param $startDate
     * @param $endDate
     */
    public function testUpdateOnlyDates($empNumber, $startDate, $endDate): void
    {
        $employmentContractDao = $this->getMockBuilder(EmploymentContractDao::class)
            ->onlyMethods(['getEmploymentContractByEmpNumber', 'saveEmploymentContract'])
            ->getMock();

        $employee = new Employee();
        $employee->setEmpNumber(1);
        $empContract = new EmpContract();
        $empContract->setEmployee($employee);
        $empContract->setContractId('1');
        $empContract->setStartDate(new DateTime('2020-05-23'));
        $empContract->setEndDate(new DateTime('2021-05-23'));

        $map = [
            [1, $empContract],
            [2, null],
        ];
        $employmentContractDao->expects($this->once())
            ->method('getEmploymentContractByEmpNumber')
            ->will($this->returnValueMap($map));
        $employmentContractDao->expects($this->once())
            ->method('saveEmploymentContract')
            ->will(
                $this->returnCallback(
                    function ($empContract) {
                        return $empContract;
                    }
                )
            );

        $employmentContractService = $this->getMockBuilder(EmploymentContractService::class)
            ->onlyMethods(
                [
                    'getEmploymentContractDao',
                    'getContractAttachment',
                    'saveContractAttachment',
                    'deleteContractAttachment'
                ]
            )
            ->getMock();

        $employmentContractService->expects($this->exactly(2))
            ->method('getEmploymentContractDao')
            ->willReturn($employmentContractDao);

        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService()]);

        /** @var MockObject&EmploymentContractAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmploymentContractAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [CommonParams::PARAMETER_EMP_NUMBER => $empNumber],
                RequestParams::PARAM_TYPE_BODY => [
                    EmploymentContractAPI::PARAMETER_START_DATE => $startDate,
                    EmploymentContractAPI::PARAMETER_END_DATE => $endDate,
                ]
            ]
        )->onlyMethods(['getEmploymentContractService'])
            ->getMock();
        $api->expects($this->exactly(3))
            ->method('getEmploymentContractService')
            ->will($this->returnValue($employmentContractService));

        $result = $api->update();
        $this->assertEquals(
            [
                'startDate' => $startDate,
                'endDate' => $endDate,
                'contractAttachment' => [
                    'id' => null,
                    'filename' => null,
                    'size' => null,
                    'fileType' => null,
                    'attachedBy' => null,
                    'attachedByName' => null,
                    'attachedTime' => null,
                    'attachedDate' => null,
                ]
            ],
            $result->normalize()
        );
        $this->assertEquals(
            ['empNumber' => $empNumber],
            $result->getMeta()->all()
        );
    }

    /**
     * @return Generator
     */
    public function updateOnlyDatesDataProvider(): Generator
    {
        yield [1, '2021-05-23', '2022-05-23'];
        yield [2, '2021-05-23', null];
        yield [2, null, '2022-05-23'];
    }

    public function testUpdateNewContractAttachment(): void
    {
        $this->setDateTimeHelper();
        $empNumber = 1;
        $employmentContractDao = $this->getMockBuilder(EmploymentContractDao::class)
            ->onlyMethods(['getEmploymentContractByEmpNumber', 'saveEmploymentContract'])
            ->getMock();
        $employmentContractDao->expects($this->once())
            ->method('getEmploymentContractByEmpNumber')
            ->willReturn(null);

        $empContract = $this->getMockBuilder(EmpContract::class)
            ->onlyMethods(['getDecorator'])
            ->getMock();

        $attachTime = new DateTime();
        $empAttachment = new EmployeeAttachment();
        $empAttachment->setAttachment('test');
        $empAttachment->setAttachId(1);
        $empAttachment->setFilename('attachment.txt');
        $empAttachment->setFileType('text/plain');
        $empAttachment->setSize(6);
        $empAttachment->setAttachedTime($attachTime);
        $empContractDecorator = $this->getMockBuilder(EmpContractDecorator::class)
            ->onlyMethods(['getContractAttachment'])
            ->setConstructorArgs([$empContract])
            ->getMock();
        $empContractDecorator->expects($this->exactly(8))
            ->method('getContractAttachment')
            ->willReturn($empAttachment);
        $empContract->expects($this->exactly(10))
            ->method('getDecorator')
            ->willReturn($empContractDecorator);
        $employmentContractDao->expects($this->once())
            ->method('saveEmploymentContract')
            ->willReturn($empContract);

        $employmentContractService = $this->getMockBuilder(EmploymentContractService::class)
            ->onlyMethods(
                [
                    'getEmploymentContractDao',
                    'getContractAttachment',
                    'saveContractAttachment',
                    'deleteContractAttachment'
                ]
            )->getMock();

        $employmentContractService->expects($this->exactly(2))
            ->method('getEmploymentContractDao')
            ->willReturn($employmentContractDao);

        $contractAttachmentMap = [
            [1, null],
            [2, null],
        ];
        $employmentContractService->expects($this->once())
            ->method('getContractAttachment')
            ->will($this->returnValueMap($contractAttachmentMap));
        $employmentContractService->expects($this->once())
            ->method('saveContractAttachment');

        $user = new User();
        $user->setId(1);
        $user->setUserName('Admin');
        $userRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUser'])
            ->getMock();
        $userRoleManager->expects($this->exactly(2))
            ->method('getUser')
            ->will($this->returnValue($user));

        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService()]);

        /** @var MockObject&EmploymentContractAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmploymentContractAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [CommonParams::PARAMETER_EMP_NUMBER => $empNumber],
                RequestParams::PARAM_TYPE_BODY => [
                    EmploymentContractAPI::PARAMETER_CONTRACT_ATTACHMENT => [
                        'name' => 'attachment.txt',
                        'type' => 'text/plain',
                        'base64' => 'dGVzdA0K',
                        'size' => '6'
                    ]
                ]
            ]
        )->onlyMethods(['getEmploymentContractService', 'getUserRoleManager'])
            ->getMock();
        $api->expects($this->exactly(4))
            ->method('getEmploymentContractService')
            ->will($this->returnValue($employmentContractService));
        $api->expects($this->exactly(2))
            ->method('getUserRoleManager')
            ->will($this->returnValue($userRoleManager));

        $result = $api->update();
        $this->assertEquals(
            [
                'startDate' => null,
                'endDate' => null,
                'contractAttachment' => [
                    'id' => 1,
                    'filename' => 'attachment.txt',
                    'size' => 6,
                    'fileType' => 'text/plain',
                    'attachedBy' => null,
                    'attachedByName' => null,
                    'attachedTime' => $attachTime->format('H:i'),
                    'attachedDate' => $attachTime->format('Y-m-d')
                ]
            ],
            $result->normalize()
        );
        $this->assertEquals(
            ['empNumber' => $empNumber],
            $result->getMeta()->all()
        );
    }

    public function testUpdateNewContractAttachmentBadRequest(): void
    {
        $empNumber = 1;
        $employmentContractService = $this->getMockBuilder(EmploymentContractService::class)
            ->onlyMethods(
                [
                    'getEmploymentContractDao',
                    'getContractAttachment',
                    'saveContractAttachment',
                    'deleteContractAttachment'
                ]
            )->getMock();

        $contractAttachmentMap = [
            [1, null],
            [2, null],
        ];
        $employmentContractService->expects($this->once())
            ->method('getContractAttachment')
            ->will($this->returnValueMap($contractAttachmentMap));

        /** @var MockObject&EmploymentContractAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmploymentContractAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [CommonParams::PARAMETER_EMP_NUMBER => $empNumber],
                RequestParams::PARAM_TYPE_BODY => [
                    EmploymentContractAPI::PARAMETER_CURRENT_CONTRACT_ATTACHMENT => EmploymentContractAPI::CONTRACT_ATTACHMENT_KEEP_CURRENT
                ]
            ]
        )->onlyMethods(['getEmploymentContractService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getEmploymentContractService')
            ->will($this->returnValue($employmentContractService));

        $this->expectBadRequestException();
        $api->update();
    }

    public function testUpdateDeleteContractAttachment(): void
    {
        $this->setDateTimeHelper();
        $empNumber = 1;
        $employmentContractDao = $this->getMockBuilder(EmploymentContractDao::class)
            ->onlyMethods(['getEmploymentContractByEmpNumber', 'saveEmploymentContract'])
            ->getMock();
        $employmentContractDao->expects($this->once())
            ->method('getEmploymentContractByEmpNumber')
            ->willReturn(null);

        $empContract = $this->getMockBuilder(EmpContract::class)
            ->onlyMethods(['getDecorator'])
            ->getMock();

        $empContractDecorator = $this->getMockBuilder(EmpContractDecorator::class)
            ->onlyMethods(['getContractAttachment'])
            ->setConstructorArgs([$empContract])
            ->getMock();
        $empContractDecorator->expects($this->exactly(8))
            ->method('getContractAttachment')
            ->willReturn(null);
        $empContract->expects($this->exactly(10))
            ->method('getDecorator')
            ->willReturn($empContractDecorator);
        $employmentContractDao->expects($this->once())
            ->method('saveEmploymentContract')
            ->willReturn($empContract);

        $employmentContractService = $this->getMockBuilder(EmploymentContractService::class)
            ->onlyMethods(
                [
                    'getEmploymentContractDao',
                    'getContractAttachment',
                    'saveContractAttachment',
                    'deleteContractAttachment'
                ]
            )->getMock();

        $employmentContractService->expects($this->exactly(2))
            ->method('getEmploymentContractDao')
            ->willReturn($employmentContractDao);

        $employee = new Employee();
        $employee->setEmpNumber(1);
        $contractAttachment = new EmployeeAttachment();
        $contractAttachment->setEmployee($employee);
        $contractAttachment->setFilename('attachment.txt');
        $contractAttachment->setAttachment('text');
        $contractAttachment->setFileType('text/plain');
        $contractAttachment->setSize(1024);

        $contractAttachmentMap = [
            [1, $contractAttachment],
            [2, null],
        ];
        $employmentContractService->expects($this->once())
            ->method('getContractAttachment')
            ->will($this->returnValueMap($contractAttachmentMap));
        $employmentContractService->expects($this->once())
            ->method('deleteContractAttachment');

        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService()]);

        /** @var MockObject&EmploymentContractAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmploymentContractAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [CommonParams::PARAMETER_EMP_NUMBER => $empNumber],
                RequestParams::PARAM_TYPE_BODY => [
                    EmploymentContractAPI::PARAMETER_CURRENT_CONTRACT_ATTACHMENT => EmploymentContractAPI::CONTRACT_ATTACHMENT_DELETE_CURRENT
                ]
            ]
        )->onlyMethods(['getEmploymentContractService'])
            ->getMock();
        $api->expects($this->exactly(4))
            ->method('getEmploymentContractService')
            ->will($this->returnValue($employmentContractService));

        $result = $api->update();
        $this->assertEquals(
            [
                'startDate' => null,
                'endDate' => null,
                'contractAttachment' => [
                    'id' => null,
                    'filename' => null,
                    'size' => null,
                    'fileType' => null,
                    'attachedBy' => null,
                    'attachedByName' => null,
                    'attachedTime' => null,
                    'attachedDate' => null,
                ]
            ],
            $result->normalize()
        );
        $this->assertEquals(
            ['empNumber' => $empNumber],
            $result->getMeta()->all()
        );
    }

    public function testUpdateReplaceContractAttachment(): void
    {
        $this->setDateTimeHelper();
        $empNumber = 1;
        $employmentContractDao = $this->getMockBuilder(EmploymentContractDao::class)
            ->onlyMethods(['getEmploymentContractByEmpNumber', 'saveEmploymentContract'])
            ->getMock();
        $employmentContractDao->expects($this->once())
            ->method('getEmploymentContractByEmpNumber')
            ->willReturn(null);

        $empContract = $this->getMockBuilder(EmpContract::class)
            ->onlyMethods(['getDecorator'])
            ->getMock();

        $attachTime = new DateTime();
        $empAttachment = new EmployeeAttachment();
        $empAttachment->setAttachment('test');
        $empAttachment->setAttachId(1);
        $empAttachment->setFilename('attachment.txt');
        $empAttachment->setFileType('text/plain');
        $empAttachment->setSize(6);
        $empAttachment->setAttachedTime($attachTime);
        $empContractDecorator = $this->getMockBuilder(EmpContractDecorator::class)
            ->onlyMethods(['getContractAttachment'])
            ->setConstructorArgs([$empContract])
            ->getMock();
        $empContractDecorator->expects($this->exactly(8))
            ->method('getContractAttachment')
            ->willReturn($empAttachment);
        $empContract->expects($this->exactly(10))
            ->method('getDecorator')
            ->willReturn($empContractDecorator);
        $employmentContractDao->expects($this->once())
            ->method('saveEmploymentContract')
            ->willReturn($empContract);

        $employmentContractService = $this->getMockBuilder(EmploymentContractService::class)
            ->onlyMethods(
                [
                    'getEmploymentContractDao',
                    'getContractAttachment',
                    'saveContractAttachment',
                    'deleteContractAttachment'
                ]
            )->getMock();

        $employmentContractService->expects($this->exactly(2))
            ->method('getEmploymentContractDao')
            ->willReturn($employmentContractDao);

        $employee = new Employee();
        $employee->setEmpNumber(1);
        $contractAttachment = new EmployeeAttachment();
        $contractAttachment->setEmployee($employee);
        $contractAttachment->setFilename('attachment.txt');
        $contractAttachment->setAttachment('text');
        $contractAttachment->setFileType('text/plain');
        $contractAttachment->setSize(1024);

        $contractAttachmentMap = [
            [1, $contractAttachment],
            [2, null],
        ];
        $employmentContractService->expects($this->once())
            ->method('getContractAttachment')
            ->will($this->returnValueMap($contractAttachmentMap));
        $employmentContractService->expects($this->once())
            ->method('saveContractAttachment');

        $user = new User();
        $user->setId(1);
        $user->setUserName('Admin');
        $userRoleManager = $this->getMockBuilder(BasicUserRoleManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUser'])
            ->getMock();
        $userRoleManager->expects($this->exactly(2))
            ->method('getUser')
            ->will($this->returnValue($user));

        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService()]);

        /** @var MockObject&EmploymentContractAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmploymentContractAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [CommonParams::PARAMETER_EMP_NUMBER => $empNumber],
                RequestParams::PARAM_TYPE_BODY => [
                    EmploymentContractAPI::PARAMETER_CONTRACT_ATTACHMENT => [
                        'name' => 'attachment.txt',
                        'type' => 'text/plain',
                        'base64' => 'dGVzdA0K',
                        'size' => '6'
                    ],
                    EmploymentContractAPI::PARAMETER_CURRENT_CONTRACT_ATTACHMENT => EmploymentContractAPI::CONTRACT_ATTACHMENT_REPLACE_CURRENT,
                ]
            ]
        )->onlyMethods(['getEmploymentContractService', 'getUserRoleManager'])
            ->getMock();
        $api->expects($this->exactly(4))
            ->method('getEmploymentContractService')
            ->will($this->returnValue($employmentContractService));
        $api->expects($this->exactly(2))
            ->method('getUserRoleManager')
            ->will($this->returnValue($userRoleManager));

        $result = $api->update();
        $this->assertEquals(
            [
                'startDate' => null,
                'endDate' => null,
                'contractAttachment' => [
                    'id' => 1,
                    'filename' => 'attachment.txt',
                    'size' => 6,
                    'fileType' => 'text/plain',
                    'attachedBy' => null,
                    'attachedByName' => null,
                    'attachedTime' => $attachTime->format('H:i'),
                    'attachedDate' => $attachTime->format('Y-m-d')
                ]
            ],
            $result->normalize()
        );
        $this->assertEquals(
            ['empNumber' => $empNumber],
            $result->getMeta()->all()
        );
    }

    public function testGetValidationRuleForUpdate(): void
    {
        $configService = $this->getMockBuilder(ConfigService::class)
            ->onlyMethods(['getAllowedFileTypes', 'getMaxAttachmentSize'])
            ->getMock();
        $this->createKernelWithMockServices(
            [
                Services::CONFIG_SERVICE => $configService
            ]
        );

        $api = new EmploymentContractAPI($this->getRequest());
        $rules = $api->getValidationRuleForUpdate();
        $rules->removeParamValidation(CommonParams::PARAMETER_EMP_NUMBER);
        $this->assertTrue(
            $this->validate(
                [],
                $rules
            )
        );
    }

    private function setDateTimeHelper(): void
    {
        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->method('getNow')
            ->willReturn(new DateTime('2021-10-04'));
        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => $dateTimeHelper]);
    }
}
