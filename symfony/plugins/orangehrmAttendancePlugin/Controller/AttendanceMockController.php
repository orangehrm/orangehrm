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

class AttendanceMockController extends AbstractController
{
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
}
