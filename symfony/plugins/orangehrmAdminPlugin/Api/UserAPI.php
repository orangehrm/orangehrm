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
use OrangeHRM\Admin\Dto\UserSearchFilterParams;
use OrangeHRM\Admin\Service\UserService;
use OrangeHRM\Core\Api\CommonParams;
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
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\User;
use OrangeHRM\ORM\Doctrine;

class UserAPI extends Endpoint implements CrudEndpoint
{
    public const PARAMETER_USERNAME = 'username';
    public const PARAMETER_PASSWORD = 'password';
    public const PARAMETER_USER_ROLE_ID = 'userRoleId';
    public const PARAMETER_EMPLOYEE_NUMBER = 'empNumber';
    public const PARAMETER_STATUS = 'status';
    public const PARAMETER_CHANGE_PASSWORD = 'changePassword';

    public const FILTER_USERNAME = 'username';
    public const FILTER_USER_ROLE_ID = 'userRoleId';
    public const FILTER_EMPLOYEE_NUMBER = 'empNumber';
    public const FILTER_STATUS = 'status';

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
     */
    public function getOne(): EndpointGetOneResult
    {
        $userId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $user = $this->getSystemUserService()->getSystemUser($userId);
        return new EndpointGetOneResult(UserModel::class, $user);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_ID),
        );
    }

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointGetAllResult
    {
        // TODO:: Check data group permission
        $userSearchParamHolder = new UserSearchFilterParams();
        $this->setSortingAndPaginationParams($userSearchParamHolder);
        $userSearchParamHolder->setStatus(
            $this->getRequestParams()->getBooleanOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_STATUS
            )
        );
        $userSearchParamHolder->setUsername(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_USERNAME
            )
        );
        $userSearchParamHolder->setEmpNumber(
            $this->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_EMPLOYEE_NUMBER
            )
        );
        $userSearchParamHolder->setUserRoleId(
            $this->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_USER_ROLE_ID
            )
        );

        $users = $this->getSystemUserService()->searchSystemUsers($userSearchParamHolder);
        return new EndpointGetAllResult(
            UserModel::class,
            $users,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_TOTAL => $this->getSystemUserService()->getSearchSystemUsersCount(
                        $userSearchParamHolder
                    )
                ]
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(self::FILTER_USER_ROLE_ID),
            new ParamRule(self::FILTER_USERNAME),
            new ParamRule(self::FILTER_EMPLOYEE_NUMBER),
            new ParamRule(self::FILTER_STATUS),
            ...$this->getSortingAndPaginationParamsRules(UserSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @inheritDoc
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
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            ...$this->getCommonBodyValidationRules(),
        );
    }

    /**
     * @return ParamRule[]
     */
    private function getCommonBodyValidationRules(): array
    {
        return [
            new ParamRule(self::PARAMETER_USERNAME),
            new ParamRule(self::PARAMETER_PASSWORD),
            new ParamRule(self::PARAMETER_USER_ROLE_ID),
            new ParamRule(self::PARAMETER_EMPLOYEE_NUMBER),
            new ParamRule(self::PARAMETER_STATUS),
        ];
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointUpdateResult
    {
        $userId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
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
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            ),
            new ParamRule(self::PARAMETER_CHANGE_PASSWORD),
            ...$this->getCommonBodyValidationRules(),
        );
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointDeleteResult
    {
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        $this->getSystemUserService()->deleteSystemUsers($ids);
        return new EndpointDeleteResult(ArrayModel::class, $ids);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_IDS),
        );
    }
}
