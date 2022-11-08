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
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\Response;

class BuzzMockAPIController extends AbstractController
{
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
            $postType = ['text', 'photo', 'video'][rand(0, 2)];
            array_push($posts, [
                'id' => $postCount + 1,
                'type' => $postType,
                'like' => rand(0, 1) === 1,
                'text' => $postCount > 0 ? implode(" ", array_slice([...$mockData], rand(0, 10), rand(1, 25))) : "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque vitae turpis faucibus, suscipit nibh vitae, venenatis tortor. Praesent blandit purus mi, vitae fringilla diam fermentum et. Duis blandit condimentum dui eu feugiat. Pellentesque eleifend, eros vestibulum aliquet lacinia, felis nulla consectetur justo, a ultrices tortor mi vitae mauris. Sed hendrerit augue tellus, sit amet consequat tellus tempus non. Proin mollis quam cursus, consectetur ante in, cursus orci. Donec mollis tellus a odio maximus convallis. Donec leo nunc, euismod sit amet finibus sed, consectetur sit amet erat. Etiam accumsan viverra porttitor. Quisque sit amet faucibus est. Donec interdum id metus in molestie. Proin maximus, est ac auctor fringilla, diam lorem aliquam urna, eu pellentesque sapien enim eget nulla. Proin a sollicitudin dui, eu pellentesque mauris. Curabitur scelerisque vulputate tortor, bibendum tempor sapien dapibus vel. Praesent faucibus nisi purus, sit amet laoreet eros ornare nec. Nullam pulvinar pretium metus a rhoncus. Nam dignissim vestibulum velit, et convallis odio egestas sagittis. Ut scelerisque congue blandit. In imperdiet lacinia libero, dictum venenatis est finibus a. Suspendisse consequat felis tortor, ornare congue orci tincidunt a. Pellentesque ut sagittis neque. Sed id faucibus ante. Duis porta elit vel est malesuada, vel pretium lectus tempor. Nulla lacinia, nisi et blandit ultrices, nulla ante ultrices lorem, id mollis augue justo eu augue. Donec nec justo id nisl sollicitudin sagittis. Proin rutrum dignissim orci, sit amet dignissim leo molestie consectetur. Praesent euismod convallis velit, non fermentum nisl commodo nec. Vivamus posuere, tortor vulputate dapibus tempus, erat nisi interdum sem, vitae vehicula est elit ac lacus. Sed hendrerit tortor ut laoreet interdum. Etiam tincidunt, tellus in gravida convallis, leo ante auctor quam, eget commodo nisi ex lobortis sapien. Suspendisse ac nunc elementum, eleifend tortor non, tincidunt augue. In viverra, felis id convallis commodo, libero elit pharetra massa, eget vehicula justo eros ac nibh. Ut aliquam tincidunt quam eget efficitur. Morbi pretium ante vitae odio posuere placerat. Aliquam blandit ullamcorper elit, id vestibulum justo bibendum id. In purus turpis, accumsan eget lacus bibendum, viverra congue velit. Nulla pretium lectus sed elit vehicula, a sollicitudin libero placerat. Morbi feugiat quis nunc sit amet sagittis. Duis convallis suscipit magna, ut fermentum eros interdum ut. Sed non varius est. Donec feugiat feugiat sodales. Donec congue tortor feugiat malesuada convallis. Suspendisse finibus odio vel egestas fermentum. Donec et nulla dui. Vestibulum quis tristique neque, non dapibus nulla. Sed vitae massa ac metus volutpat dapibus. Quisque ornare, orci id blandit rhoncus, nisl felis tincidunt leo, vitae pellentesque quam massa a est. Pellentesque nunc massa, sollicitudin sed tellus in, tristique mollis metus. Maecenas fringilla magna gravida efficitur aliquet. Aenean vitae ligula ut magna pharetra cursus. Pellentesque rutrum convallis ligula blandit convallis. Etiam odio eros, iaculis a elementum eget, viverra id mauris. In vulputate orci non urna iaculis lacinia. Suspendisse eleifend, nisi non suscipit egestas, turpis ligula hendrerit neque, in posuere tortor libero non ligula. Vivamus nec nulla sem. Fusce eget magna consectetur, lobortis orci nec, hendrerit ipsum. In euismod auctor ex, auctor rhoncus nibh fermentum in. Phasellus tincidunt faucibus eleifend. Nulla in diam in lorem rhoncus condimentum vitae ac velit. Nunc egestas elit et ante rhoncus tincidunt. Vestibulum maximus iaculis erat, eget rhoncus neque malesuada et.",
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
                'createdTime' => date('Y-m-d', rand(1262055681, time())),
                'updatedtime' => date('Y-m-d', rand(1262055681, time())),
                'parentPost' => null,
                'video' => $postType === 'video' ? 'https://www.youtube.com/watch?v=qMCMgedYqac' : null,
                'photo' => $postType === 'photo' ? array_fill(0, rand(0, 4), [
                    "name" => "test",
                    "size" => 193324,
                    "type" => "image/png",
                    "base64" => "iVBORw0KGgoAAAANSUhEUgAAAAgAAAAIAQMAAAD+wSzIAAAABlBMVEX///+/v7+jQ3Y5AAAADklEQVQI12P4AIX8EAgALgAD/aNpbtEAAAAASUVORK5CYII"
                ]) : null,
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

    /**
     * @param Request $request
     * @return Response
     */
    public function updatePost(Request $request): Response
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode(Response::HTTP_OK);
        return $response->send();
    }
}
