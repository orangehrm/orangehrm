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

namespace Orangehrm\Rest\Api\Admin;

use Orangehrm\Rest\Api\Admin\Entity\SystemUser;
use Orangehrm\Rest\Api\EndPoint;
use Orangehrm\Rest\Http\Response;

class SystemUserAPI extends EndPoint
{

    const PARAMETER_USER_NAME = 'userName';
    const PARAMETER_USER_TYPE = 'userType';
    const PARAMETER_EMPLOYEE_ID = 'employeeId';
    const PARAMETER_OFFSET = 'offset';
    const PARAMETER_LIMIT = 'limit';


    private $systemUserService;

    public function getSystemUserService()
    {
        if (is_null($this->systemUserService)) {
            $this->systemUserService = new \SystemUserService();
        }
        return $this->systemUserService;
    }

    /**
     * @param mixed $systemUserService
     */
    public function setSystemUserService($systemUserService)
    {
        $this->systemUserService = $systemUserService;
    }


    public function getSystemUsers()
    {
        $parameterObject = $this->getSearchParameters();
        $systemUserList = $this->getSystemUserService()->searchSystemUsers($parameterObject);
        $systemUserListCount = $this->getSystemUserService()->getSearchSystemUsersCount($parameterObject);
        $responseList = null;
        if (!$systemUserListCount == 0) {
            foreach ($systemUserList as $user) {
                $systemUser = new SystemUser();
                $systemUser->buildUser($user);
                $responseList[] = $systemUser->toArray();
            }
        }
        return new Response($responseList);
    }

    private function getSearchParameters()
    {

        $searchParameters = array(

            'userName' => $this->getRequestParams()->getUrlParam(self::PARAMETER_USER_NAME),
            'userType' => $this->getRequestParams()->getUrlParam(self::PARAMETER_USER_TYPE),
            'employeeId' => $this->getRequestParams()->getUrlParam(self::PARAMETER_EMPLOYEE_ID),
            'offset' => $this->getRequestParams()->getUrlParam(self::PARAMETER_OFFSET),
            'limit' => $this->getRequestParams()->getUrlParam(self::PARAMETER_LIMIT),
            'use_ids' => array(1, 2)
        );


        return $searchParameters;
    }

}
