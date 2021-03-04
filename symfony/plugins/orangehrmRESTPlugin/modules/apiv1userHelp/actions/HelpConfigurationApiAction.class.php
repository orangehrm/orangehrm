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

use Orangehrm\Rest\Http\Request;
use Orangehrm\Rest\Api\User\Help\HelpConfigurationAPI;
use Orangehrm\Rest\Api\Exception\NotImplementedException;

class HelpConfigurationApiAction extends BaseUserApiAction
{

    private $helpApi = null;


    /**
     * @return HelpConfigurationAPI
     */
    public function getHelpApi($request){
        if(!$this->helpApi){
            $this->helpApi = new HelpConfigurationAPI($request);
        }
        return $this->helpApi;
    }

    /**
     * @param $punchInApi
     * @return $this
     */
    public function setHelpApi($helpApi){
        $this->helpApi = $helpApi;
        return $this;
    }

    /**
     * @param Request $request
     */
    protected function init(Request $request){
        $this->helpApi = new HelpConfigurationAPI($request);
        $this->getValidationRule = $this->helpApi->getValidationRules();
    }


    /**
     * @OA\Get(
     *     path="/help/config",
     *     summary="Get Help Configuration",
     *     tags={"Help","User"},
     *     @OA\Parameter(
     *         name="query",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="Search by article name mode category name.",
     *     ),
     *     @OA\Parameter(
     *         name="mode",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="If mode is 'category', will result in matching categories, otherwise matching articles.",
     *     ),
     *     @OA\Parameter(
     *         name="labels",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string[]"),
     *         description="Get the articles by article labels ( eg :- ['add_employee' , 'apply_leave'] )",
     *     ),
     *     @OA\Parameter(
     *         name="categoryIds",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string[]"),
     *         description="Articles in specified categories ( eg :- ['123456' , '654321'] )",
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/HelpConfiguration"),
     *     ),
     * )
     * @OA\Schema(
     *     schema="HelpConfiguration",
     *     type="object",
     *     example = { "data": { "defaultRedirectUrl": "https://opensourcehelp.orangehrm.com/hc/en-us", "redirectUrls": {{"name": "Create master data for employee job information", "url": "https://opensourcehelp.orangehrm.com/hc/en-us/articles/360018594080-Create-master-data-for-employee-job-information"}, {"name": "How to Add a User Account", "url": "https://opensourcehelp.orangehrm.com/hc/en-us/articles/360018588480-How-to-Add-a-User-Account"}, {"name": "How to Approve Leave by Admin or Supervisor", "url": "https://opensourcehelp.orangehrm.com/hc/en-us/articles/360018659479-How-to-Approve-Leave-by-Admin-or-Supervisor"} } }, "rels": {} }
     * )
     */
    protected function handleGetRequest(Request $request)
    {
        return $this->getHelpApi($request)->getHelpConfiguration();
    }

    /**
     * @param \Orangehrm\Rest\Http\Request $request
     * @return \Orangehrm\Rest\Http\Response
     */
    protected function handlePostRequest(Request $request)
    {
        throw new NotImplementedException();
    }
}
