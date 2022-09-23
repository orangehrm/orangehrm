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

use OrangeHRM\Core\Controller\AbstractController;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\Response;

class BuzzMockAPIController extends AbstractController
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
                            'empNumber' => 29,
                            'lastName' => 'user02',
                            'firstName' => 'test',
                            'deleted' => false,
                        ],
                        'jobTitle' => 'Senior Sales Executive',
                        'joinedDate' => '2019-10-14',
                    ],
                    [
                        'employee' => [
                            'empNumber' => 15,
                            'lastName' => 'user03',
                            'firstName' => 'test',
                            'deleted' => false,
                        ],
                        'jobTitle' => 'Senior Software Engineer',
                        'joinedDate' => '2016-10-17',
                    ],
                    [
                        'employee' => [
                            'empNumber' => 1,
                            'lastName' => 'user01',
                            'firstName' => 'test',
                            'deleted' => false,
                        ],
                        'jobTitle' => 'Senior Project Lead',
                        'joinedDate' => '2020-10-15',
                    ],
                ],
                "meta" => [
                    "count" => 3,
                ]
            ])
        );
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode(Response::HTTP_OK);
        return $response->send();
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function getPosts(Request $request): Response
    {
        $posts = [];
        $response = new Response();
        $mockData = array_values(
            explode(
                " ",
                "Lorem ipsum dolor sit amet consectetur adipisicing elit. Ipsa soluta ullam facilis velit. Voluptatem quisquam unde itaque ipsum natus dolore vero eaque delectus eos. Neque hic totam doloremque itaque nihil"
            )
        );

        for ($postCount = 0; $postCount < 50; $postCount++) {
            array_push($posts, [
                'id' => $postCount + 1,
                'type' => 'text', // text | photo | video
                'text' => implode(" ", array_slice([...$mockData], rand(0, 10), rand(1, 25))),
                'employee' => [
                    'empNumber' => $postCount,
                    'employeeId' => '00' . $postCount,
                    'firstName' => $mockData[rand(0, 25)],
                    'lastName' => $mockData[rand(0, 25)],
                    'middleName' => $mockData[rand(0, 25)],
                    'terminationId' => null,
                ],
                'stats' => [
                    'noOfLikes' => rand(0, 100),
                    'noOfComments' => rand(0, 100),
                    'noOfShares' => rand(0, 100),
                ],
                'createdTime' => rand(1262055681, time()),
                'updatedtime' => rand(1262055681, time()),
            ]);
        }

        $response->setContent(
            json_encode([
                "data" => array_slice($posts, 0, $request->query->getInt('limit')),
                "meta" => [
                    "total" => count($posts),
                ]
            ])
        );
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode(Response::HTTP_OK);
        return $response->send();
    }
}
