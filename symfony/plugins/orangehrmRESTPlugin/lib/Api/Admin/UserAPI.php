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

use Orangehrm\Rest\Api\Admin\Entity\User;
use Orangehrm\Rest\Api\EndPoint;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Http\Response;

class UserAPI extends EndPoint
{
    const PARAMETER_USER_NAME = 'userName';
    const PARAMETER_USER_TYPE = 'userRole';
    const PARAMETER_EMPLOYEE_ID = 'employeeId';
    const PARAMETER_OFFSET = 'offset';
    const PARAMETER_LIMIT = 'limit';

    private $systemUserService;

    /**
     * Get system user service
     *
     * @return \SystemUserService
     */
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

    /**
     * Get System Users
     *
     * @return Response
     * @throws RecordNotFoundException
     */
    public function getSystemUsers()
    {
        $parameterObject = $this->getSearchParameters();
        $systemUserList = $this->getSystemUserService()->searchSystemUsers($parameterObject);

        $responseList = null;
        if (!count($systemUserList) == 0) {
            foreach ($systemUserList as $user) {
                $systemUser = new User();
                $systemUser->buildUser($user);
                $responseList[] = $systemUser->toArray();
            }
            return new Response($responseList);
        }else {
            throw new RecordNotFoundException('No Users Found');
        }

    }

    /**
     * Creating search parameters
     *
     * @return array
     */
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

    /**
     * Return API User entity or null if user not exits
     * @param $userId
     * @return User|null
     * @throws \ServiceException
     */
    public function getSystemUserById($userId)
    {
        $systemUser = $this->getSystemUserService()->getSystemUser($userId);
        if ($systemUser instanceof \SystemUser){
            $user = new User();
            $user->buildUser($systemUser);
            return $user;
        }
        return null;
    }
}
