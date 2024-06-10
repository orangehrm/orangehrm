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

namespace OrangeHRM\Claim\Api;

use OpenApi\Annotations as OA;
use OrangeHRM\Claim\Api\Model\ClaimExpenseTypeModel;
use OrangeHRM\Claim\Dto\ClaimExpenseTypeSearchFilterParams;
use OrangeHRM\Claim\Traits\Service\ClaimServiceTrait;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Api\V2\Validator\Rules\EntityUniquePropertyOption;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Entity\ExpenseType;

class ClaimExpenseTypeAPI extends Endpoint implements CrudEndpoint
{
    use ClaimServiceTrait;
    use AuthUserTrait;

    public const PARAMETER_NAME = 'name';
    public const PARAMETER_DESCRIPTION = 'description';
    public const PARAMETER_EXPENSE_TYPE_ID = 'expenseTypeId';
    public const PARAMETER_STATUS = 'status';
    public const DESCRIPTION_MAX_LENGTH = 1000;
    public const NAME_MAX_LENGTH = 100;
    public const PARAMETER_CAN_EXPENSE_TYPE_EDIT = 'canEdit';

    /**
     * @OA\Get(
     *     path="/api/v2/claim/expenses/types",
     *     tags={"Claim/Expense Types"},
     *     summary="List All Expense Types",
     *     operationId="list-all-expense-types",
     *     @OA\Parameter(
     *         name="sortField",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=ClaimExpenseTypeSearchFilterParams::ALLOWED_SORT_FIELDS)
     *     ),
     *     @OA\Parameter(
     *         name="expenseTypeId",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="boolean")
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
     *                 @OA\Items(ref="#/components/schemas/Claim-ExpenseTypeModel")
     *             ),
     *             @OA\Property(property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     )
     * )
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $claimExpenseTypeSearchFilterParams = new ClaimExpenseTypeSearchFilterParams();
        $this->setSortingAndPaginationParams($claimExpenseTypeSearchFilterParams);
        $claimExpenseTypeSearchFilterParams->setName(
            $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_QUERY, self::PARAMETER_NAME)
        );
        $claimExpenseTypeSearchFilterParams->setStatus(
            $this->getRequestParams()->getBooleanOrNull(RequestParams::PARAM_TYPE_QUERY, self::PARAMETER_STATUS)
        );
        $claimExpenseTypeSearchFilterParams->setId(
            $this->getRequestParams()->getIntOrNull(RequestParams::PARAM_TYPE_QUERY, self::PARAMETER_EXPENSE_TYPE_ID)
        );
        $claimExpenseTypes = $this->getClaimService()->getClaimDao()->getExpenseTypeList(
            $claimExpenseTypeSearchFilterParams
        );
        $count = $this->getClaimService()
            ->getClaimDao()
            ->getClaimExpenseTypeCount($claimExpenseTypeSearchFilterParams);
        return new EndpointCollectionResult(
            ClaimExpenseTypeModel::class,
            $claimExpenseTypes,
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
                new ParamRule(self::PARAMETER_NAME, new Rule(Rules::STRING_TYPE), new Rule(Rules::LENGTH, [null, self::NAME_MAX_LENGTH]))
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_STATUS,
                    new Rule(Rules::STRING_TYPE)
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_EXPENSE_TYPE_ID,
                    new Rule(Rules::POSITIVE)
                )
            ),
            ...$this->getSortingAndPaginationParamsRules(ClaimExpenseTypeSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v2/claim/expenses/types",
     *     tags={"Claim/Expense Types"},
     *     summary="Create an Expense Type",
     *     operationId="create-an-expense-type",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="status", type="boolean"),
     *             required={"name"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Claim-ExpenseTypeModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     * @inheritDoc
     */
    public function create(): EndpointResourceResult
    {
        $expenseType = new ExpenseType();
        $expenseType->setName(
            $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_NAME)
        );
        $this->setExpenseType($expenseType);

        $userId = $this->getAuthUser()->getUserId();
        $expenseType->getDecorator()->setUserByUserId($userId);

        $this->getClaimService()->getClaimDao()->saveExpenseType($expenseType);
        return new EndpointResourceResult(ClaimExpenseTypeModel::class, $expenseType);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->requiredParamRule(
                $this->getNameRule($this->getClaimExpenseTypeCommonUniqueOption()),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_DESCRIPTION,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::DESCRIPTION_MAX_LENGTH])
                ),
                true
            ),
            new ParamRule(
                self::PARAMETER_STATUS,
                new Rule(Rules::BOOL_VAL)
            ),
        );
    }

    /**
     * @param EntityUniquePropertyOption|null $uniqueOption
     * @return ParamRule
     */
    protected function getNameRule(?EntityUniquePropertyOption $uniqueOption = null): ParamRule
    {
        return new ParamRule(
            self::PARAMETER_NAME,
            new Rule(Rules::STRING_TYPE),
            new Rule(Rules::LENGTH, [null, self::NAME_MAX_LENGTH]),
            new Rule(Rules::ENTITY_UNIQUE_PROPERTY, [ExpenseType::class, 'name', $uniqueOption])
        );
    }

    /**
     * @return EntityUniquePropertyOption
     */
    private function getClaimExpenseTypeCommonUniqueOption(): EntityUniquePropertyOption
    {
        $uniqueOption = new EntityUniquePropertyOption();
        $uniqueOption->setIgnoreValues(['isDeleted' => true]);
        return $uniqueOption;
    }

    /**
     * @param ExpenseType $expenseType
     */
    private function setExpenseType(ExpenseType $expenseType): void
    {
        $expenseType->setDescription(
            $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_DESCRIPTION)
        );
        $expenseType->setStatus(
            $this->getRequestParams()->getBoolean(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_STATUS, true)
        );

        $expenseType->getDecorator()->setUserByUserId($this->getAuthUser()->getUserId());
    }

    /**
     * @OA\Delete(
     *     path="/api/v2/claim/expenses/types",
     *     tags={"Claim/Expense Types"},
     *     summary="Delete Expense Types",
     *     operationId="delete-expense-types",
     *     @OA\RequestBody(ref="#/components/requestBodies/DeleteRequestBody"),
     *     @OA\Response(response="200", ref="#/components/responses/DeleteResponse"),
     *     @OA\Response(response="404", ref="#/components/responses/ForbiddenResponse")
     * )
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        $ids = $this->getClaimService()->getClaimDao()->getExistingExpenseTypeIds(
            $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS)
        );
        $this->throwRecordNotFoundExceptionIfEmptyIds($ids);
        $this->getClaimService()->getClaimDao()->deleteExpenseTypes($ids);
        return new EndpointResourceResult(ArrayModel::class, $ids);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_IDS,
                new Rule(Rules::INT_ARRAY)
            ),
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v2/claim/expenses/types/{id}",
     *     tags={"Claim/Expense Types"},
     *     summary="Get an Expense Type",
     *     operationId="get-an-expense-type",
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
     *                 ref="#/components/schemas/Claim-ExpenseTypeModel"
     *             ),
     *             @OA\Property(property="meta",
     *                 type="object",
     *                 @OA\Property(property="canEdit", type="boolean")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $expenseType = $this->getClaimService()->getClaimDao()->getExpenseTypeById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($expenseType, ExpenseType::class);
        $isUsed = $this->getClaimService()->getClaimDao()->isExpenseTypeUsed($id);
        return new EndpointResourceResult(
            ClaimExpenseTypeModel::class,
            $expenseType,
            new ParameterBag([self::PARAMETER_CAN_EXPENSE_TYPE_EDIT => !$isUsed])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            ),
        );
    }

    /**
     * @OA\Put(
     *     path="/api/v2/claim/expenses/types/{id}",
     *     tags={"Claim/Expense Types"},
     *     summary="Update an Expense Type",
     *     operationId="update-an-expense-type",
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="name",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Claim-ExpenseTypeModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $expenseType = $this->getClaimService()->getClaimDao()->getExpenseTypeById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($expenseType, ExpenseType::class);
        $canNameEdit = !$this->getClaimService()->getClaimDao()->isExpenseTypeUsed($id);
        $name = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_NAME);
        if (!$canNameEdit && $name !== null) {
            throw $this->getInvalidParamException(self::PARAMETER_NAME);
        }
        if ($canNameEdit && $name !== null) {
            $expenseType->setName($name);
        }
        $this->setExpenseType($expenseType);
        $this->getClaimService()->getClaimDao()->saveExpenseType($expenseType);
        return new EndpointResourceResult(ClaimExpenseTypeModel::class, $expenseType);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        $uniqueOption = $this->getClaimExpenseTypeCommonUniqueOption();
        $uniqueOption->setIgnoreId($this->getAttributeId());

        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_DESCRIPTION,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::DESCRIPTION_MAX_LENGTH]),
                ),
                true
            ),
            new ParamRule(
                self::PARAMETER_STATUS,
                new Rule(Rules::BOOL_VAL)
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                $this->getNameRule($uniqueOption),
            ),
        );
    }
}
