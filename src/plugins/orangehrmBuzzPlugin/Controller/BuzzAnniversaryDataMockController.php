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

namespace OrangeHRM\Buzz\Controller;

use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\Response;
use OrangeHRM\Core\Controller\AbstractController;
use OrangeHRM\Pim\Service\EmployeeService;

class BuzzAnniversaryDataMockController extends AbstractController
{


    use ConfigServiceTrait;

    /**
     * @param Request $request
     * @return Response
     */
    public function getUpcomingAnniversaries(Request $request): Response
    {
        $response = new Response();
        $response->setContent(
            json_encode([
                "data" => [
                    [
                        'employee' => [
                            'empNumber'=> 29,
                            'lastName' => 'user02',
                            'firstName'=> 'test',
                            'deleted'=> false,
                        ],
                        'jobTitle' => 'Senior Sales Executive',
                        'joinedDate' => '2019-10-14',
                    ],
                    [
                        'employee' => [
                            'empNumber'=> 15,
                            'lastName' => 'user03',
                            'firstName'=> 'test',
                            'deleted'=> false,
                        ],
                        'jobTitle' => 'Senior Software Engineer',
                        'joinedDate' => '2016-10-17',
                    ],
                    [
                        'employee' => [
                            'empNumber'=> 1,
                            'lastName' => 'user04',
                            'firstName'=> 'test',
                            'deleted'=> false,
                        ],
                        'jobTitle' => 'Senior Project Lead',
                        'joinedDate' => '2020-10-15',
                    ],
                    [
                        'employee' => [
                            'empNumber'=> 7,
                            'lastName' => 'user05',
                            'firstName'=> 'test',
                            'deleted'=> false,
                        ],
                        'jobTitle' => 'Senior Project Lead',
                        'joinedDate' => '2020-10-15',
                    ],
                    [
                        'employee' => [
                            'empNumber'=> 9,
                            'lastName' => 'user06',
                            'firstName'=> 'test',
                            'deleted'=> false,
                        ],
                        'jobTitle' => 'Senior Project Lead',
                        'joinedDate' => '2020-10-15',
                    ],
                    [
                        'employee' => [
                            'empNumber'=> 19,
                            'lastName' => 'user07',
                            'firstName'=> 'test',
                            'deleted'=> false,
                        ],
                        'jobTitle' => 'Senior Project Lead',
                        'joinedDate' => '2020-10-15',
                    ],
                    [
                        'employee' => [
                            'empNumber'=> 11,
                            'lastName' => 'user08',
                            'firstName'=> 'test',
                            'deleted'=> false,
                        ],
                        'jobTitle' => 'Senior Project Lead',
                        'joinedDate' => '2021-10-15',
                    ],
                    [
                        'employee' => [
                            'empNumber'=> 10,
                            'lastName' => 'user09',
                            'firstName'=> 'test',
                            'deleted'=> false,
                        ],
                        'jobTitle' => 'Senior Project Lead',
                        'joinedDate' => '2020-10-15',
                    ],
                ],
                "meta" => [
                    "count" => 8,
                ]
            ])
        );
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode(Response::HTTP_OK);
        return $response->send();
    }
}