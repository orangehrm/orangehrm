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
use OrangeHRM\Admin\Traits\Service\UserServiceTrait;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Api\V2\Validator\Rules\EntityUniquePropertyOption;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\User;

class UserAPI extends Endpoint implements CrudEndpoint
{
    use UserServiceTrait;
    use DateTimeHelperTrait;
    use AuthUserTrait;

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

    public const PARAM_RULE_USERNAME_MIN_LENGTH = 5;
    public const PARAM_RULE_USERNAME_MAX_LENGTH = 40;
    public const PARAM_RULE_PASSWORD_MAX_LENGTH = 64;

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResourceResult
    {
        $userId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $user = $this->getUserService()->getSystemUser($userId);
        $this->throwRecordNotFoundExceptionIfNotExist($user, User::class);

        return new EndpointResourceResult(UserModel::class, $user);
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
    public function getAll(): EndpointCollectionResult
    {
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

        $users = $this->getUserService()->searchSystemUsers($userSearchParamHolder);
        $count = $this->getUserService()->getSearchSystemUsersCount($userSearchParamHolder);
        return new EndpointCollectionResult(
            UserModel::class,
            $users,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
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
    public function create(): EndpointResourceResult
    {
        $user = new User();
        $this->setUserParams($user);
        $user->setDateEntered($this->getDateTimeHelper()->getNow());
        $user->setCreatedBy($this->getAuthUser()->getUserId());

        $user = $this->getUserService()->saveSystemUser($user, true);
        return new EndpointResourceResult(UserModel::class, $user);
    }

    /**
     * @param User $user
     * @param bool $changePassword
     */
    public function setUserParams(User $user, bool $changePassword = true): void
    {
        $username = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_USERNAME);
        $userRoleId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_USER_ROLE_ID);
        $empNumber = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_EMPLOYEE_NUMBER);
        $status = $this->getRequestParams()->getBoolean(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_STATUS);

        $user->setUserName($username);
        $user->setStatus($status);
        $user->getDecorator()->setUserRoleById($userRoleId);
        $user->getDecorator()->setEmployeeByEmpNumber($empNumber);
        if ($changePassword) {
            $password = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_PASSWORD);
            $user->setUserPassword($password);
        }
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            ...$this->getUsernameAndPasswordRule(false),
            ...$this->getCommonBodyValidationRules(),
        );
    }

    /**
     * @return ParamRule[]
     */
    private function getCommonBodyValidationRules(): array
    {
        return [
            new ParamRule(
                self::PARAMETER_USER_ROLE_ID,
                new Rule(Rules::INT_TYPE)
            ),
            new ParamRule(
                CommonParams::PARAMETER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            ),
            new ParamRule(
                self::PARAMETER_STATUS,
                new Rule(Rules::BOOL_TYPE)
            ),
        ];
    }

    /**
     * @param bool $update
     * @return ParamRule[]
     */
    protected function getUsernameAndPasswordRule(bool $update): array
    {
        $uniquePropertyParams = [User::class, 'userName'];
        $passwordConstructor = [true];
        if ($update) {
            $entityProperties = new EntityUniquePropertyOption();
            $entityProperties->setIgnoreValues(
                ['getId' => $this->getRequestParams()->getInt(
                    RequestParams::PARAM_TYPE_ATTRIBUTE,
                    CommonParams::PARAMETER_ID
                )]
            );
            $uniquePropertyParams[] = $entityProperties;

            $passwordConstructor = [
                $this->getRequestParams()->getBoolean(
                    RequestParams::PARAM_TYPE_BODY,
                    self::PARAMETER_CHANGE_PASSWORD
                )
            ];
        }

        return [
            new ParamRule(
                self::PARAMETER_USERNAME,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [self::PARAM_RULE_USERNAME_MIN_LENGTH, self::PARAM_RULE_USERNAME_MAX_LENGTH]),
                new Rule(Rules::ENTITY_UNIQUE_PROPERTY, $uniquePropertyParams)
            ),
            new ParamRule(
                self::PARAMETER_PASSWORD,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_PASSWORD_MAX_LENGTH]),
                new Rule(Rules::PASSWORD, $passwordConstructor)
            )
        ];
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointResourceResult
    {
        $userId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $changePassword = $this->getRequestParams()->getBoolean(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_CHANGE_PASSWORD
        );

        $user = $this->getUserService()->getSystemUser($userId);
        $this->throwRecordNotFoundExceptionIfNotExist($user, User::class);

        $this->setUserParams($user, $changePassword);
        $user->setDateModified($this->getDateTimeHelper()->getNow());
        $user->setModifiedUserId($this->getAuthUser()->getUserId());
        $user = $this->getUserService()->saveSystemUser($user, $changePassword);
        return new EndpointResourceResult(UserModel::class, $user);
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
            new ParamRule(
                self::PARAMETER_CHANGE_PASSWORD,
                new Rule(Rules::BOOL_TYPE)
            ),
            ...$this->getUsernameAndPasswordRule(true),
            ...$this->getCommonBodyValidationRules(),
        );
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointResourceResult
    {
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        $this->getUserService()->deleteSystemUsers($ids);
        return new EndpointResourceResult(ArrayModel::class, $ids);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        $undeletableIds = $this->getUserService()->getUndeletableUserIds();
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_IDS,
                new Rule(
                    Rules::EACH,
                    [
                        new Rules\Composite\AllOf(
                            new Rule(Rules::POSITIVE),
                            new Rule(Rules::NOT_IN, [$undeletableIds])
                        )
                    ]
                )
            ),
        );
    }
}
