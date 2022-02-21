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

class ReportMockController extends AbstractController
{
    /**
     * @param Request $request
     * @return Response
     */
    public function getReportHeaders(Request $request): Response
    {
        $response = new Response();
        $reportName = $request->query->get("name");

        switch ($reportName) {
            case "attendance_summary_report":
                $response->setContent(
                    json_encode([
                        "data" => [
                            "headers" => [
                                [
                                    "name" => "Employee Name",
                                    "prop" => "employeeName",
                                    "pin" => null,
                                    "cellProperties" => null,
                                ],
                                [
                                    "name" => "Time (Hours)",
                                    "prop" => "time",
                                    "pin" => null,
                                    "cellProperties" => [
                                        "class" => [
                                            "col-alt" => true,
                                        ],
                                    ],
                                ],
                            ],
                            "filters" => [],
                        ],
                        "meta" => [
                            "headers" => null,
                            "filters" => null,
                        ]
                    ])
                );
                break;

            default:
                $response->setContent(json_encode([]));
                break;
        }

        if (!empty($response->getContent())) {
            $response->setStatusCode(Response::HTTP_OK);
            return $response->send();
        }

        return $this->handleBadRequest();
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function getReportData(Request $request): Response
    {
        $response = new Response();
        $reportName = $request->query->get("name");

        switch ($reportName) {
            case "attendance_summary_report":
                $response->setContent(
                    json_encode([
                        "data" => [
                            [
                                "employeeName" => "Levi Ackerman",
                                "time" => "10.00",
                            ],
                            [
                                "employeeName" => "Kieth Shadis",
                                "time" => "00.01",
                            ],
                            [
                                "employeeName" => "Kenny Ackerman",
                                "time" => "01.00",
                            ],
                            [
                                "employeeName" => "Niel Stampede",
                                "time" => "44.17",
                            ],
                            [
                                "employeeName" => "Pixis Corner",
                                "time" => "00.21",
                            ],
                        ],
                        "meta" => [
                            "total" => 5,
                            "sum" => [
                                "hours" => 2,
                                "minutes" => 30,
                                "label" => "2.50",
                            ],
                        ],
                    ])
                );
                break;

            default:
                $response->setContent(json_encode([]));
                break;
        }

        if (!empty($response->getContent())) {
            $response->setStatusCode(Response::HTTP_OK);
            return $response->send();
        }

        return $this->handleBadRequest();
    }
}
