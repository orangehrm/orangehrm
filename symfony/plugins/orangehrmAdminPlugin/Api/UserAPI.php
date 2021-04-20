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

namespace OrangeHRM\Admin\Api;

use OrangeHRM\Admin\Api\Model\UserModel;
use OrangeHRM\Admin\Service\UserService;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Serializer\EndpointCreateResult;
use OrangeHRM\Core\Api\V2\Serializer\EndpointDeleteResult;
use OrangeHRM\Core\Api\V2\Serializer\EndpointGetAllResult;
use OrangeHRM\Core\Api\V2\Serializer\EndpointGetOneResult;
use OrangeHRM\Core\Api\V2\Serializer\EndpointUpdateResult;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\User;
use OrangeHRM\ORM\Doctrine;

class UserAPI extends Endpoint implements CrudEndpoint
{
    public const PARAMETER_ID = 'id';
    public const PARAMETER_IDS = 'ids';
    public const PARAMETER_USERNAME = 'username';
    public const PARAMETER_PASSWORD = 'password';
    public const PARAMETER_USER_ROLE_ID = 'userRoleId';
    public const PARAMETER_EMPLOYEE_NUMBER = 'empNumber';
    public const PARAMETER_STATUS = 'status';
    public const PARAMETER_CHANGE_PASSWORD = 'changePassword';

    public const PARAMETER_SORT_FIELD = 'sortField';
    public const PARAMETER_SORT_ORDER = 'sortOrder';
    public const PARAMETER_OFFSET = 'offset';
    public const PARAMETER_LIMIT = 'limit';

    /**
     * @var null|UserService
     */
    protected ?UserService $systemUserService = null;

    /**
     * @return UserService|null
     */
    public function getSystemUserService(): ?UserService
    {
        if (is_null($this->systemUserService)) {
            $this->systemUserService = new UserService();
        }
        return $this->systemUserService;
    }

    /**
     * @param UserService|null $systemUserService
     */
    public function setSystemUserService(?UserService $systemUserService): void
    {
        $this->systemUserService = $systemUserService;
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function getOne(): EndpointGetOneResult
    {
        $userId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, self::PARAMETER_ID);
        $user = $this->getSystemUserService()->getSystemUser($userId);
        return new EndpointGetOneResult(UserModel::class, $user);
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function getAll(): EndpointGetAllResult
    {
        $searchClues['offset'] = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_QUERY,
            self::PARAMETER_OFFSET
        );
        $searchClues['limit'] = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_QUERY,
            self::PARAMETER_LIMIT
        );
        $searchClues['sortField'] = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_QUERY,
            self::PARAMETER_SORT_FIELD
        );
        $searchClues['sortOrder'] = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_QUERY,
            self::PARAMETER_SORT_ORDER
        );

        $users = $this->getSystemUserService()->searchSystemUsers($searchClues);
        return new EndpointGetAllResult(
            UserModel::class,
            $users,
            new ParameterBag(
                [
                    'total' => $this->getSystemUserService()->getSearchSystemUsersCount(
                        $searchClues
                    )
                ]
            )
        );
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function create(): EndpointCreateResult
    {
        $username = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_USERNAME);
        $password = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_PASSWORD);
        $userRoleId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_USER_ROLE_ID);
        $empNumber = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_EMPLOYEE_NUMBER);
        $status = $this->getRequestParams()->getBoolean(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_STATUS);

        $employee = Doctrine::getEntityManager()->getReference(Employee::class, $empNumber);

        $userRole = $this->getSystemUserService()->getUserRoleById($userRoleId);
        $systemUser = new User();
        $systemUser->setUserName($username);
        $systemUser->setUserPassword($password);
        $systemUser->setStatus($status);
        $systemUser->setUserRole($userRole);
        $systemUser->setEmployee($employee);
        $systemUser = $this->getSystemUserService()->saveSystemUser($systemUser, true);
        return new EndpointCreateResult(UserModel::class, $systemUser);
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function update(): EndpointUpdateResult
    {
        $userId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, self::PARAMETER_ID);
        $username = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_USERNAME);
        $userRoleId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_USER_ROLE_ID);
        $empNumber = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_EMPLOYEE_NUMBER);
        $status = $this->getRequestParams()->getBoolean(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_STATUS);
        $changePassword = $this->getRequestParams()->getBoolean(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_CHANGE_PASSWORD
        );

        $employee = Doctrine::getEntityManager()->getReference(Employee::class, $empNumber);

        $userRole = $this->getSystemUserService()->getUserRoleById($userRoleId);

        $systemUser = $this->getSystemUserService()->getSystemUser($userId);
        $systemUser->setUserName($username);
        $systemUser->setStatus($status);
        $systemUser->setUserRole($userRole);
        $systemUser->setEmployee($employee);

        if ($changePassword) {
            $password = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_PASSWORD);
            $systemUser->setUserPassword($password);
        }
        $systemUser = $this->getSystemUserService()->saveSystemUser($systemUser, $changePassword);
        return new EndpointUpdateResult(UserModel::class, $systemUser);
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function delete(): EndpointDeleteResult
    {
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_IDS);
        $this->getSystemUserService()->deleteSystemUsers($ids);
        return new EndpointDeleteResult(ArrayModel::class, $ids);
    }
}
