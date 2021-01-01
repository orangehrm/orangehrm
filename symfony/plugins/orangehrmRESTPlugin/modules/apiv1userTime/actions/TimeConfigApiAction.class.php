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

use Orangehrm\Rest\Api\Exception\NotImplementedException;
use Orangehrm\Rest\Api\User\Time\TimeConfigAPI;
use Orangehrm\Rest\Http\Request;

class TimeConfigApiAction extends BaseUserApiAction
{
    /**
     * @var null|TimeConfigAPI
     */
    private $timeConfigAPI = null;

    /**
     * @param Request $request
     */
    protected function init(Request $request)
    {
        $this->timeConfigAPI = new TimeConfigAPI($request);
    }

    /**
     * @OA\Get(
     *     path="/time/config",
     *     summary="Get Time Configurations",
     *     tags={"Time","User"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/TimeConfig"),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="No Bound User",
     *         @OA\JsonContent(ref="#/components/schemas/NoBoundUserError"),
     *     ),
     * )
     * @OA\Schema(
     *     schema="TimeConfig",
     *     type="object",
     *     example={"data":{"startDate":3},"rels":{}}
     * )
     */
    protected function handleGetRequest(Request $request)
    {
        $this->setUserToContext();
        return $this->timeConfigAPI->getTimeConfigs();
    }

    /**
     * @param Request $request
     * @throws NotImplementedException
     */
    protected function handlePostRequest(Request $request)
    {
        throw new NotImplementedException();
    }
}
