<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http=>//www.orangehrm.com
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

use Orangehrm\Rest\Api\Exception\BadRequestException;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Api\User\Attendance\AttendanceListAPI;
use Orangehrm\Rest\Http\Request;
use Orangehrm\Rest\Http\Response;

/**
 * @group API
 */
class ApiAttendanceListAPITest extends PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderGetAttendanceList
     * @param $id
     * @param $params
     * @param $empIds
     * @param $responseArray
     * @param $expects
     * @param $records
     * @throws BadRequestException
     * @throws DaoException
     */
    public function testGetAttendanceList($id, $params, $empIds, $responseArray, $expects, $records)
    {
        $sfEvent = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);

        $attendanceListApi = $this->getMockBuilder('Orangehrm\Rest\Api\User\Attendance\AttendanceListAPI')
            ->setMethods(['getParameters', 'getAccessibleEmployeeIds','getUserAttribute'])
            ->setConstructorArgs([$request])
            ->getMock();
        $attendanceListApi->expects($this->once())
            ->method('getParameters')
            ->will(
                $this->returnValue($params)
            );
        $attendanceListApi->expects($this->exactly($expects))
            ->method('getAccessibleEmployeeIds')
            ->withAnyParameters()
            ->will($this->returnValue($empIds));
        $attendanceListApi->expects($this->once())
            ->method('getUserAttribute')
            ->withAnyParameters()
            ->will($this->returnValue(1));

        $attendanceService = $this->getMockBuilder('AttendanceService')->getMock();
        $attendanceService->expects($this->once())
            ->method('getAttendanceRecordsByEmpNumbers')
            ->withAnyParameters()
            ->will($this->returnValue($records));

        $attendanceListApi->setAttendanceService($attendanceService);

        if ($id == 3) {
            $this->expectException(RecordNotFoundException::class);
        }
        $response = $attendanceListApi->getAttendanceList();

        $this->assertEquals(
            new Response($responseArray),
            $response
        );
    }

    /**
     * @return Generator
     */
    public function dataProviderGetAttendanceList()
    {
        $jobTitle = new JobTitle();
        $jobTitle->setJobTitleName('SE');
        $unit = new Subunit();
        $unit->setName('Subunit');
        $status = new EmploymentStatus();
        $status->setName('Status');

        $employee = new Employee();
        $employee->setEmpNumber('1');
        $employee->setFirstName("Test");
        $employee->setLastName("Employee");
        $employee->setEmployeeId('0001');
        $employee->setJobTitle($jobTitle);
        $employee->setSubDivision($unit);
        $employee->setEmployeeStatus($status);

        $record = new AttendanceRecord();
        $record->setEmployee($employee);
        $record->setEmployeeId(1);
        $record->setPunchInUtcTime('2020-12-09 08:40:00');
        $record->setPunchOutUtcTime('2020-12-09 17:43:00');

        $attendanceRecordCollection = new Doctrine_Collection('AttendanceRecord');
        $attendanceRecordCollection[] = $record;

        yield [
            1,
            [
                AttendanceListAPI::PARAMETER_FROM_DATE => null,
                AttendanceListAPI::PARAMETER_TO_DATE => null,
                AttendanceListAPI::PARAMETER_PAST_EMPLOYEE => false
            ],
            [1, 2, 5],
            [
                [
                    'employeeId' => 1,
                    'employeeName' => 'Test Employee',
                    'duration' => '9:03',
                    'code' => '0001',
                    'jobTitle' => 'SE',
                    'unit' => 'Subunit',
                    'status' => 'Status',
                ]
            ],
            1,
            $attendanceRecordCollection
        ];
        yield [
            2,
            [
                AttendanceListAPI::PARAMETER_FROM_DATE => null,
                AttendanceListAPI::PARAMETER_TO_DATE => null,
                AttendanceListAPI::PARAMETER_PAST_EMPLOYEE => false,
                AttendanceListAPI::PARAMETER_EMP_NUMBER => 5
            ],
            [1, 2, 5],
            [
                [
                    'employeeId' => 1,
                    'employeeName' => 'Test Employee',
                    'duration' => '9:03',
                    'code' => '0001',
                    'jobTitle' => 'SE',
                    'unit' => 'Subunit',
                    'status' => 'Status',
                ]
            ],
            0,
            $attendanceRecordCollection
        ];
        yield [
            3,
            [
                AttendanceListAPI::PARAMETER_FROM_DATE => null,
                AttendanceListAPI::PARAMETER_TO_DATE => null,
                AttendanceListAPI::PARAMETER_PAST_EMPLOYEE => false,
                AttendanceListAPI::PARAMETER_EMP_NUMBER => 5
            ],
            [1, 2, 5],
            [],
            0,
            []
        ];
    }

    /**
     * @dataProvider requestParamProvider
     * @param $id
     * @param $returnParamCallback
     * @param $empIds
     * @param $expectedParams
     * @param $exactGetQueryParam
     * @param $exactGetDefinedTimesheetPeriod
     * @throws BadRequestException
     * @throws DaoException
     */
    public function testGetParameters(
        $id,
        $returnParamCallback,
        $empIds,
        $expectedParams,
        $exactGetQueryParam,
        $exactGetDefinedTimesheetPeriod
    ) {
        $requestParams = $this->getMockBuilder('\Orangehrm\Rest\Http\RequestParams')
            ->disableOriginalConstructor()
            ->setMethods(['getQueryParam'])
            ->getMock();
        $requestParams->expects($this->exactly($exactGetQueryParam))
            ->method('getQueryParam')
            ->will($this->returnCallback($returnParamCallback));

        $timesheetPeriodService = $this->getMockBuilder('\TimesheetPeriodService')
            ->disableOriginalConstructor()
            ->setMethods(['getDefinedTimesheetPeriod'])
            ->getMock();
        $timesheetPeriodService->expects($this->exactly($exactGetDefinedTimesheetPeriod))
            ->method('getDefinedTimesheetPeriod')
            ->will(
                $this->returnValue(
                    [
                        '2020-12-21 00:00',
                        '2020-12-22 00:00',
                        '2020-12-23 00:00',
                        '2020-12-24 00:00',
                        '2020-12-25 00:00',
                        '2020-12-26 00:00',
                        '2020-12-27 00:00'
                    ]
                )
            );

        $sfEvent = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);

        $attendanceListApi = $this->getMockBuilder('Orangehrm\Rest\Api\User\Attendance\AttendanceListAPI')
            ->setMethods(['getAccessibleEmployeeIds'])
            ->setConstructorArgs([$request])
            ->getMock();
        $attendanceListApi->setRequestParams($requestParams);
        $attendanceListApi->setTimesheetPeriodService($timesheetPeriodService);

        $attendanceListApi->expects($this->once())
            ->method('getAccessibleEmployeeIds')
            ->withAnyParameters()
            ->will($this->returnValue($empIds));

        if ($id == 3) {
            $this->expectException(BadRequestException::class);
        }
        $params = $attendanceListApi->getParameters();

        $this->assertEquals($expectedParams, $params);
    }

    /**
     * @return Generator
     */
    public function requestParamProvider()
    {
        yield [
            1,
            function ($param) {
                return null;
            },
            [1, 2, 10],
            [
                AttendanceListAPI::PARAMETER_FROM_DATE => '2020-12-21 00:00',
                AttendanceListAPI::PARAMETER_TO_DATE => '2020-12-27 00:00',
                AttendanceListAPI::PARAMETER_PAST_EMPLOYEE => false,
                AttendanceListAPI::PARAMETER_EMP_NUMBER => null,
                AttendanceListAPI::PARAMETER_ALL => null,
                AttendanceListAPI::PARAMETER_INCLUDE_SELF => false,
            ],
            6,
            1
        ];
        yield [
            2,
            function ($param) {
                if ($param == AttendanceListAPI::PARAMETER_FROM_DATE) {
                    return '2020-10-10';
                } elseif ($param == AttendanceListAPI::PARAMETER_TO_DATE) {
                    return '2020-10-11';
                } elseif ($param == AttendanceListAPI::PARAMETER_PAST_EMPLOYEE) {
                    return true;
                } elseif ($param == AttendanceListAPI::PARAMETER_EMP_NUMBER) {
                    return 1;
                }
                return null;
            },
            [1, 2, 10],
            [
                AttendanceListAPI::PARAMETER_FROM_DATE => '2020-10-10',
                AttendanceListAPI::PARAMETER_TO_DATE => '2020-10-11',
                AttendanceListAPI::PARAMETER_PAST_EMPLOYEE => true,
                AttendanceListAPI::PARAMETER_EMP_NUMBER => 1,
                AttendanceListAPI::PARAMETER_ALL => false,
                AttendanceListAPI::PARAMETER_INCLUDE_SELF => false,
            ],
            6,
            0
        ];
        yield [
            3,
            function ($param) {
                if ($param == AttendanceListAPI::PARAMETER_EMP_NUMBER) {
                    return 1;
                }
                return null;
            },
            [2, 10],
            [],
            1,
            0
        ];
    }
}
