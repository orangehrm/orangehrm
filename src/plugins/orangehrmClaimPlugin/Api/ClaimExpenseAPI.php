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

namespace OrangeHRM\Claim\Api;

use OrangeHRM\Claim\Api\Model\ClaimExpenseModel;
use OrangeHRM\Claim\Dto\ClaimExpenseSearchFilterParams;
use OrangeHRM\Claim\Traits\Service\ClaimServiceTrait;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\BadRequestException;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\ClaimExpense;
use OrangeHRM\Entity\ClaimRequest;

class ClaimExpenseAPI extends Endpoint implements CrudEndpoint
{
    use EntityManagerHelperTrait;
    use ClaimServiceTrait;
    use DateTimeHelperTrait;
    use AuthUserTrait;

    public const PARAMETER_EXPENSE_TYPE_ID = 'expenseTypeId';
    public const PARAMETER_AMOUNT = 'amount';
    public const PARAMETER_NOTE = 'note';
    public const PARAMETER_DATE = 'date';
    public const PARAMETER_REQUEST_ID = 'requestId';
    public const NOTE_MAX_LENGTH = 1000;

    /**
     * @OA\Get(
     *     path="/api/v2/claim/expenses",
     *     tags={"Claim/Expenses"},
     *     @OA\Parameter(
     *         name="claimRequestId",
     *         in="query",
     *         required=false,
     *     )
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Property(ref="#/components/schemas/Claim-ClaimExpenseModel")
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     )
     * )
     *
     */
    public function getAll(): EndpointResult
    {
        $claimExpenseSearchFilterParams = new ClaimExpenseSearchFilterParams();
        $claimExpenseSearchFilterParams->setClaimRequestId($this->getRequestParams()->getIntOrNull(RequestParams::PARAM_TYPE_QUERY, self::PARAMETER_REQUEST_ID));
        $claimExpenses = $this->getClaimService()->getClaimDao()->getClaimExpenseList($claimExpenseSearchFilterParams);
        $count = $this->getClaimService()->getClaimDao()->getClaimExpenseCount($claimExpenseSearchFilterParams);
        return new EndpointCollectionResult(ClaimExpenseModel::class, $claimExpenses, new ParameterBag([CommonParams::PARAMETER_TOTAL => $count]));
    }

    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_REQUEST_ID,
                    new Rule(Rules::POSITIVE)
                )
            ),
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v2/claim/expenses",
     *     tags={"Claim/Expenses"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="expenseTypeId", type="integer"),
     *             @OA\Property(property="amount", type="float"),
     *             @OA\Property(property="requestId", type="integer"),
     *             @OA\Property(property="note", type="string"),
     *             @OA\Property(property="date", type="datetime"),
     *             required={"name", "expenseTypeId", "amount", "requestId", "date"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Claim-ClaimExpenseModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        $claimExpense = new ClaimExpense();
        $claimExpense->getDecorator()->setClaimRequestByRequestId($this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_REQUEST_ID));
        $this->setClaimExpense($claimExpense);
        return new EndpointResourceResult(ClaimExpenseModel::class, $claimExpense);
    }

    public function setClaimExpense(ClaimExpense $claimExpense): void
    {
        $claimExpense->getDecorator()->setExpenseTypeByExpenseTypeId($this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_EXPENSE_TYPE_ID));
        $claimExpense->setDate($this->getRequestParams()->getDateTime(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_DATE));
        $claimExpense->setAmount($this->getRequestParams()->getFloat(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_AMOUNT));
        $claimExpense->setNote($this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_NOTE));
        $this->getClaimService()->getClaimDao()->saveClaimExpense($claimExpense);
    }

    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_EXPENSE_TYPE_ID,
                    new Rule(Rules::INT_TYPE)
                ),
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_AMOUNT,
                    new Rule(Rules::FLOAT_TYPE)
                ),
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_REQUEST_ID,
                    new Rule(Rules::INT_TYPE)
                ),
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_DATE,
                    new Rule(Rules::DATE_TIME)
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_NOTE,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::NOTE_MAX_LENGTH])
                ),
            ),
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/v2/claim/expenses",
     *     tags={"Claim/Expenses"},
     *     @OA\RequestBody(ref="#/components/requestBodies/DeleteRequestBody"),
     *     @OA\Response(response="200", ref="#/components/responses/DeleteResponse")
     * )
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        $this->getClaimService()->getClaimDao()->deleteClaimExpense($ids);
        return new EndpointResourceResult(ArrayModel::class, $ids);
    }

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
     *     path="/api/v2/claim/expenses/{id}",
     *     tags={"Claim/Expenses"},
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
     *                 ref="#/components/schemas/Claim-ClaimExpenseModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $claimExpenseId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $claimExpense = $this->getClaimService()->getClaimDao()->getClaimExpenseById($claimExpenseId);
        $this->throwRecordNotFoundExceptionIfNotExist($claimExpense, ClaimExpense::class);
        return new EndpointResourceResult(ClaimExpenseModel::class, $claimExpense);
    }

    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    CommonParams::PARAMETER_ID,
                    new Rule(Rules::POSITIVE)
                )
            ),
        );
    }

    /**
     * @OA\Put(
     *     path="/api/v2/claim/expenses/{id}",
     *     tags={"Claim/Expenses"},
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="expense_type_id", type="integer"),
     *             @OA\Property(property="date", type="string"),
     *             @OA\Property(property="amount", type="float"),
     *             @OA\Property(property="note", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Claim-ClaimExpenseModel"
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
        $claimExpenseId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $claimExpense = $this->getClaimService()->getClaimDao()->getClaimExpenseById($claimExpenseId);
        $this->throwRecordNotFoundExceptionIfNotExist($claimExpense, ClaimExpense::class);
        $status = $this->getClaimService()->getClaimDao()->getClaimRequestById($claimExpense->getClaimRequest()->getId())->getStatus();
        if (!$status == ClaimRequest::REQUEST_STATUS_INITIATED) {
            throw new BadRequestException('Claim request is already approved');
        }
        $this->setClaimExpense($claimExpense);
        return new EndpointResourceResult(ClaimExpenseModel::class, $claimExpense);
    }

    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            ),
            new ParamRule(
                self::PARAMETER_EXPENSE_TYPE_ID,
                new Rule(Rules::POSITIVE)
            ),
            new ParamRule(
                self::PARAMETER_NOTE,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, self::NOTE_MAX_LENGTH]),
            ),
            new ParamRule(
                self::PARAMETER_AMOUNT,
                new Rule(Rules::FLOAT_TYPE)
            ),
            new ParamRule(
                self::PARAMETER_DATE,
                new Rule(Rules::DATE_TIME)
            ),
        );
    }
}
