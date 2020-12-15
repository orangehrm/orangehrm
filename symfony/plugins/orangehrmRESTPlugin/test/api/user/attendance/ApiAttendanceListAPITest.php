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
use Orangehrm\Rest\Api\Exception\InvalidParamException;
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
            ->setMethods(['getParameters', 'getAccessibleEmployeeIds'])
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
        $employee = new Employee();
        $employee->setEmpNumber(1);
        $employee->setFirstName("Test");
        $employee->setLastName("Employee");

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
                    'duration' => '9:03'
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
                    'duration' => '9:03'
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
     * @param $fromDate
     * @param $toDate
     * @throws DaoException
     * @throws Doctrine_Connection_Exception
     * @throws Doctrine_Record_Exception
     * @throws InvalidParamException
     * @throws RecordNotFoundException
     */
    public function testGetParameters($id, $returnParamCallback, $empIds, $expectedParams, $exact)
    {
        $requestParams = $this->getMockBuilder('\Orangehrm\Rest\Http\RequestParams')
            ->disableOriginalConstructor()
            ->setMethods(['getQueryParam'])
            ->getMock();
        $requestParams->expects($this->exactly($exact))
            ->method('getQueryParam')
            ->will($this->returnCallback($returnParamCallback));

        $sfEvent = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);

        $attendanceListApi = $this->getMockBuilder('Orangehrm\Rest\Api\User\Attendance\AttendanceListAPI')
            ->setMethods(['getAccessibleEmployeeIds'])
            ->setConstructorArgs([$request])
            ->getMock();
        $attendanceListApi->setRequestParams($requestParams);

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
                AttendanceListAPI::PARAMETER_FROM_DATE => null,
                AttendanceListAPI::PARAMETER_TO_DATE => null,
                AttendanceListAPI::PARAMETER_PAST_EMPLOYEE => false,
                AttendanceListAPI::PARAMETER_EMP_NUMBER => null
            ],
            4
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
                AttendanceListAPI::PARAMETER_EMP_NUMBER => 1
            ],
            4
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
            1
        ];
    }
}
