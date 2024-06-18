<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Admin\Api;

use OrangeHRM\Admin\Api\Model\UserModel;
use OrangeHRM\Admin\Dto\UserSearchFilterParams;
use OrangeHRM\Admin\Service\UserService;
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
use OrangeHRM\OpenidAuthentication\Traits\Service\SocialMediaAuthenticationServiceTrait;

class UserAPI extends Endpoint implements CrudEndpoint
{
    use UserServiceTrait;
    use DateTimeHelperTrait;
    use AuthUserTrait;
    use SocialMediaAuthenticationServiceTrait;

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

    public const PARAM_RULE_USERNAME_MIN_LENGTH = UserService::USERNAME_MIN_LENGTH;
    public const PARAM_RULE_USERNAME_MAX_LENGTH = UserService::USERNAME_MAX_LENGTH;
    public const PARAM_RULE_PASSWORD_MAX_LENGTH = 64;

    /**
     * @OA\Get(
     *     path="/api/v2/admin/users/{id}",
     *     tags={"Admin/Users"},
     *     summary="Get a User",
     *     operationId="get-a-user",
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Admin-UserModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
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
            new ParamRule(CommonParams::PARAMETER_ID, new Rule(Rules::POSITIVE)),
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v2/admin/users",
     *     tags={"Admin/Users"},
     *     summary="List All Users",
     *     operationId="list-all-users",
     *     @OA\Parameter(
     *         name="username",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="userRoleId",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="empNumber",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="sortField",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=UserSearchFilterParams::ALLOWED_SORT_FIELDS)
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/sortOrder"),
     *     @OA\Parameter(ref="#/components/parameters/limit"),
     *     @OA\Parameter(ref="#/components/parameters/offset"),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Admin-UserModel")
     *             ),
     *             @OA\Property(property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     )
     * )
     *
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
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(self::FILTER_USER_ROLE_ID, new Rule(Rules::POSITIVE))
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(self::FILTER_USERNAME, new Rule(Rules::STRING_TYPE))
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(self::FILTER_EMPLOYEE_NUMBER, new Rule(Rules::POSITIVE))
            ),
            new ParamRule(self::FILTER_STATUS, new Rule(Rules::BOOL_VAL)),
            ...$this->getSortingAndPaginationParamsRules(UserSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v2/admin/users",
     *     tags={"Admin/Users"},
     *     summary="Create a User",
     *     operationId="create-a-user",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="username",
     *                 type="string",
     *                 minLength=OrangeHRM\Admin\Api\UserAPI::PARAM_RULE_USERNAME_MIN_LENGTH,
     *                 maxLength=OrangeHRM\Admin\Api\UserAPI::PARAM_RULE_USERNAME_MAX_LENGTH
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 maxLength=OrangeHRM\Admin\Api\UserAPI::PARAM_RULE_PASSWORD_MAX_LENGTH
     *             ),
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="userRoleId", type="integer"),
     *             @OA\Property(property="empNumber", type="integer"),
     *             required={"username", "password", "status", "userRoleId", "empNumber"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Admin-UserModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     *
     * @inheritDoc
     */
    public function create(): EndpointResourceResult
    {
        $user = new User();
        $this->setUserParams($user);
        $user->setDateEntered($this->getDateTimeHelper()->getNow());
        $user->setCreatedBy($this->getAuthUser()->getUserId());

        $user = $this->getUserService()->saveSystemUser($user);
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
            $user->getDecorator()->setNonHashedPassword($password);
        }
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            ...$this->getCommonBodyValidationRules($this->getUserCommonUniqueOption()),
        );
    }

    /**
     * @return ParamRule[]
     */
    private function getCommonBodyValidationRules(
        ?EntityUniquePropertyOption $uniqueOption = null,
        bool $passwordConstructorOption = true
    ): array {
        return [
            new ParamRule(
                self::PARAMETER_USERNAME,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [self::PARAM_RULE_USERNAME_MIN_LENGTH, self::PARAM_RULE_USERNAME_MAX_LENGTH]),
                new Rule(Rules::ENTITY_UNIQUE_PROPERTY, [User::class, 'userName', $uniqueOption])
            ),
            new ParamRule(
                self::PARAMETER_PASSWORD,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_PASSWORD_MAX_LENGTH]),
                new Rule(
                    Rules::PASSWORD,
                    [!$this->getSocialMediaAuthenticationService()->isSocialMediaAuthEnable() && $passwordConstructorOption]
                )
            ),
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
     * @return EntityUniquePropertyOption
     */
    private function getUserCommonUniqueOption(): EntityUniquePropertyOption
    {
        $uniqueOption = new EntityUniquePropertyOption();
        $uniqueOption->setIgnoreValues(['deleted' => true]);
        return $uniqueOption;
    }

    /**
     * @OA\Put(
     *     path="/api/v2/admin/users/{id}",
     *     tags={"Admin/Users"},
     *     summary="Update a User",
     *     operationId="update-a-user",
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="username",
     *                 type="string",
     *                 minLength=OrangeHRM\Admin\Api\UserAPI::PARAM_RULE_USERNAME_MIN_LENGTH,
     *                 maxLength=OrangeHRM\Admin\Api\UserAPI::PARAM_RULE_USERNAME_MAX_LENGTH
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 maxLength=OrangeHRM\Admin\Api\UserAPI::PARAM_RULE_PASSWORD_MAX_LENGTH
     *             ),
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="userRoleId", type="integer"),
     *             @OA\Property(property="empNumber", type="integer"),
     *             required={"username", "password", "status", "userRoleId", "empNumber"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Admin-UserModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
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
        $user = $this->getUserService()->saveSystemUser($user);
        return new EndpointResourceResult(UserModel::class, $user);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        $uniqueOption = $this->getUserCommonUniqueOption();
        $uniqueOption->setIgnoreId($this->getAttributeId());

        $passwordOption = $this->getRequestParams()->getBoolean(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_CHANGE_PASSWORD
        );

        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            ),
            new ParamRule(
                self::PARAMETER_CHANGE_PASSWORD,
                new Rule(Rules::BOOL_TYPE)
            ),
            ...$this->getCommonBodyValidationRules(
                $uniqueOption,
                $passwordOption
            ),
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/v2/admin/users",
     *     tags={"Admin/Users"},
     *     summary="Delete Users",
     *     operationId="delete-users",
     *     @OA\RequestBody(ref="#/components/requestBodies/DeleteRequestBody"),
     *     @OA\Response(response="200", ref="#/components/responses/DeleteResponse"),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function delete(): EndpointResourceResult
    {
        $ids = $this->getUserService()->geUserDao()->getExistingSystemUserIds(
            $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS)
        );
        $this->throwRecordNotFoundExceptionIfEmptyIds($ids);
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
