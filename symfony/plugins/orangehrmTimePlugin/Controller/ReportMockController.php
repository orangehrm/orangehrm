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

namespace OrangeHRM\Time\Controller;

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
            case "time_employee_report":
                $response->setContent(
                    json_encode([
                        "data" => [
                            "headers" => [
                                [
                                    "name" => "Project Name",
                                    "prop" => "projectName",
                                    "pin" => null,
                                    "cellProperties" => null,
                                ],
                                [
                                    "name" => "Activity Name",
                                    "prop" => "activityName",
                                    "pin" => null,
                                    "cellProperties" => [
                                        "class" => [
                                            "col-alt" => true,
                                        ],
                                    ],
                                ],
                                [
                                    "name" => "Time (Hours)",
                                    "prop" => "time",
                                    "pin" => null,
                                    "cellProperties" => null,
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

            case "time_project_report":
                $response->setContent(
                    json_encode([
                        "data" => [
                            "headers" => [
                                [
                                    "name" => "Activity Name",
                                    "prop" => "activityName",
                                    "pin" => null,
                                    "cellProperties" => [
                                        "class" => [
                                            "cell-action" => true,
                                        ],
                                    ],
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
            case "time_employee_report":
                $response->setContent(
                    json_encode([
                        "data" => [
                            [
                                "projectName" => "Manhattan Project",
                                "activityName" => "Nuclear Fission",
                                "time" => "10:00",
                            ],
                            [
                                "projectName" => "Manhattan Project",
                                "activityName" => "Uranium enrichment",
                                "time" => "00:01",
                            ],
                            [
                                "projectName" => "Project Chicago",
                                "activityName" => "Design posters",
                                "time" => "01:00",
                            ],
                            [
                                "projectName" => "Manhattan Project",
                                "activityName" => "Ground state excitment",
                                "time" => "44:17",
                            ],
                            [
                                "projectName" => "OHRM5X",
                                "activityName" => "Develop report table UI",
                                "time" => "00:21",
                            ],
                        ],
                        "meta" => [
                            "total" => 5,
                            "sum" => [
                                "hours" => 2,
                                "minutes" => 30,
                                "label" => "02:59",
                            ],
                        ],
                    ])
                );
                break;

                case "time_project_report":
                    $response->setContent(
                        json_encode([
                            "data" => [
                                [
                                    "activityName" => "Nuclear Fission",
                                    "time" => "10:00",
                                    "_url" => [
                                        "activityName" => "/time/displayProjectActivityDetailsReport?empNumber=1&fromDate=2022-01-01&toDate=2022-12-31&activityId=1",
                                    ],
                                ],
                                [
                                    "activityName" => "Uranium enrichment",
                                    "time" => "00:01",
                                    "_url" => [
                                        "activityName" => "/time/displayProjectActivityDetailsReport?empNumber=1&fromDate=2022-01-01&toDate=2022-12-31&activityId=2",
                                    ],
                                ],
                                [
                                    "activityName" => "Design posters",
                                    "time" => "01:00",
                                    "_url" => [
                                        "activityName" => "/time/displayProjectActivityDetailsReport?empNumber=1&fromDate=2022-01-01&toDate=2022-12-31&activityId=3",
                                    ],
                                ],
                                [
                                    "activityName" => "Ground state excitment",
                                    "time" => "44:17",
                                    "_url" => [
                                        "activityName" => "/time/displayProjectActivityDetailsReport?empNumber=1&fromDate=2022-01-01&toDate=2022-12-31&activityId=4",
                                    ],
                                ],
                                [
                                    "activityName" => "Develop report table UI",
                                    "time" => "00:21",
                                    "_url" => [
                                        "activityName" => "/time/displayProjectActivityDetailsReport?empNumber=1&fromDate=2022-01-01&toDate=2022-12-31&activityId=5",
                                    ],
                                ],
                            ],
                            "meta" => [
                                "total" => 5,
                                "sum" => [
                                    "hours" => 2,
                                    "minutes" => 30,
                                    "label" => "02:59",
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
