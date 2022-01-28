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

namespace OrangeHRM\Attendance\Controller;

use OrangeHRM\Core\Controller\AbstractController;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\Response;
use OrangeHRM\Pim\Dto\EmployeeSearchFilterParams;
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;
use OrangeHRM\Core\Traits\Service\NormalizerServiceTrait;
use OrangeHRM\Pim\Service\Model\EmployeeModel;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;

class AttendanceMockController extends AbstractController
{
    use AuthUserTrait;
    use EmployeeServiceTrait;
    use NormalizerServiceTrait;

    /**
     * @return Response
     */
    public function getAttendanceConfig(): Response
    {
        $response = new Response();
        $response->setContent(
            json_encode([
                "data" => [
                    "userCanChangeCurrentTime" => false,
                    "userCanModifyAttendance" => false,
                    "supervisorCanModifyAttendance" => false,
                ],
                "meta" => []
            ])
        );

        $response->setStatusCode(Response::HTTP_OK);
        return $response->send();
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function setAttendanceConfig(Request $request): Response
    {
        $response = new Response();
        $response->setContent(
            json_encode([
                "data" => [
                    "userCanChangeCurrentTime" => $request->request->getBoolean('userCanChangeCurrentTime'),
                    "userCanModifyAttendance" => $request->request->getBoolean('userCanModifyAttendance'),
                    "supervisorCanModifyAttendance" => $request->request->getBoolean('supervisorCanModifyAttendance'),
                ],
                "meta" => []
            ])
        );

        $response->setStatusCode(Response::HTTP_OK);
        return $response->send();
    }

    /**
     * @return Response
     */
    public function getCurrentDateTime(): Response
    {
        $response = new Response();
        $response->setContent(
            json_encode([
                "data" => [
                    "date" => date("Y-m-d"),
                    "time" => date("H:i"),
                    "timestamp" => microtime(true) * 1000, // Unix timestamp in miliseconds [UTC by deafult]
                ],
                "meta" => []
            ])
        );

        $response->setStatusCode(Response::HTTP_OK);
        return $response->send();
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function validateDateTime(Request $request): Response
    {
        $response = new Response();
        // $date = $request->query->get("date");
        // $time = $request->query->get("time");
        $response->setContent(
            json_encode([
                "data" => true,
                "meta" => []
            ])
        );

        $response->setStatusCode(Response::HTTP_OK);
        return $response->send();
    }

    /**
     * @return Response
     */
    public function sendAttendanceResponse(): Response
    {
        $response = new Response();
        $response->setContent(
            json_encode([
                "data" => [
                    "date" => date("Y-m-d"),
                    "time" => date("H:i"),
                    "timezoneOffset" => date("Z") / 3600,
                ],
                "meta" => []
            ])
        );

        $response->setStatusCode(Response::HTTP_OK);
        return $response->send();
    }

    /**
     * @return Response
     */
    public function getTimezones(): Response
    {
        $response = new Response();
        $response->setContent(
            json_encode([
                "data" => [
                    0 => [
                        'id' => 1,
                        // TODO: Add GMT Label to show output in UI as (GMT -3.30) Pacific/Midway
                        'name' => 'Pacific/Midway',
                        'offset' => -330,
                    ],
                    1 => [
                        'id' => 2,
                        'name' => 'America/Adaky',
                        'offset' => -130,
                    ],
                    2 => [
                        'id' => 3,
                        'name' => 'Europe/Lisbon',
                        'offset' => -300,
                    ],
                    3 => [
                        'id' => 4,
                        'name' => 'Africa/Algiers',
                        'offset' => -200,
                    ],
                    4 => [
                        'id' => 5,
                        'name' => 'Europe/Brussels',
                        'offset' => -150,
                    ],
                    5 => [
                        'id' => 6,
                        'name' => 'Europe/Minsk',
                        'offset' => -80,
                    ],

                ],
                "meta" => []
            ])
        );

        $response->setStatusCode(Response::HTTP_OK);
        return $response->send();
    }

    /**
     * @return Response
     */
    public function sendEmployeeAttendanceRecordsResponse(): Response
    {
        $response = new Response();
        $id = 1;
        $employees = $this->getEmployeeService()->getEmployeeList(new EmployeeSearchFilterParams());

        $response->setContent(
            json_encode([
                "data" => array_merge(...array_map(function ($employee) use (&$id) {
                    // TODO: Need to show past employees by default
                    $employee = $this->getNormalizerService()->normalize(EmployeeModel::class, $employee);
                    return array_map(function () use (&$employee, &$id) {
                        return [
                            "id" => $id++,
                            "employee" => $employee,
                            "total" => sprintf('%0.2f', rand(0, 12)),
                            "duration" => sprintf('%0.2f', rand(0, 12)),
                            "records" => [
                                "in" => [
                                    "date" => date("Y-m-d"),
                                    "time" => date("H:i"),
                                    "note" => "Arrived at work",
                                    "timezone" => "GMT 5.50",
                                    "timezoneOffset" => date("Z") / 3600,
                                ],
                                "out" => [
                                    "date" => date("Y-m-d"),
                                    "time" => date("H:i"),
                                    "note" => "Left work",
                                    "timezone" => "GMT 5.50",
                                    "timezoneOffset" => date("Z") / 3600,
                                ]
                            ]
                        ];
                    }, array_fill(0, 5, null));
                }, $employees)),
                "meta" => [
                    "total" => $id - 1
                ]
            ])
        );

        $response->setStatusCode(Response::HTTP_OK);
        return $response->send();
    }

    public function sendMyAttendanceRecordsResponse(): Response
    {
        $response = new Response();
        $id = 1;
        $employee = $this->getEmployeeService()->getEmployeeAsArray($this->getAuthUser()->getEmpNumber());
        $response->setContent(
            json_encode([
                "data" => array_map(function () use (&$employee, &$id) {
                    return [
                        "id" => $id++,
                        "employee" => $employee,
                        "total" => sprintf('%0.2f', rand(0, 12)),
                        "duration" => sprintf('%0.2f', rand(0, 12)),
                        "records" => [
                            "in" => [
                                "date" => date("Y-m-d"),
                                "time" => date("H:i"),
                                "note" => "Arrived at work",
                                "timezone" => "GMT 5.50",
                                "timezoneOffset" => date("Z") / 3600,
                            ],
                            "out" => [
                                "date" => date("Y-m-d"),
                                "time" => date("H:i"),
                                "note" => "Left work",
                                "timezone" => "GMT 5.50",
                                "timezoneOffset" => date("Z") / 3600,
                            ]
                        ]
                    ];
                }, array_fill(0, 5, null)),
                "meta" => [
                    "total" => $id - 1
                ]
            ])
        );

        $response->setStatusCode(Response::HTTP_OK);
        return $response->send();
    }

    /**
     * @return Response
     */
    public function deleteEmployeeAttendanceRecords(): Response
    {
        $response = new Response();
        $response->setContent(
            json_encode([
                "data" => [],
                "meta" => []
            ])
        );

        $response->setStatusCode(Response::HTTP_OK);
        return $response->send();
    }
}
