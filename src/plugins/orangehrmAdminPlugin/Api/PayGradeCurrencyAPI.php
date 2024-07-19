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

use OrangeHRM\Admin\Api\Model\PayGradeCurrencyModel;
use OrangeHRM\Admin\Dto\PayGradeCurrencySearchFilterParams;
use OrangeHRM\Admin\Traits\Service\PayGradeServiceTrait;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\Exception\RecordNotFoundException;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\CurrencyType;
use OrangeHRM\Entity\PayGrade;
use OrangeHRM\Entity\PayGradeCurrency;

class PayGradeCurrencyAPI extends Endpoint implements CrudEndpoint
{
    use PayGradeServiceTrait;
    use EntityManagerHelperTrait;

    public const PARAMETER_PAY_GRADE_ID = 'payGradeId';
    public const PARAMETER_CURRENCY_ID = 'currencyId';
    public const PARAMETER_MIN_SALARY = 'minSalary';
    public const PARAMETER_MAX_SALARY = 'maxSalary';
    public const PARAM_RULE_SALARY_MAX_VALUE = 1000000000; // 1 billion

    /**
     * @OA\Get(
     *     path="/api/v2/admin/pay-grades/{payGradeId}/currencies/{id}",
     *     tags={"Admin/Pay Grade Currency"},
     *     summary="Get a Pay Grade Currency",
     *     operationId="get-a-pay-grade-currency",
     *     @OA\PathParameter(
     *         name="payGradeId",
     *         @OA\Schema(type="integer")
     *     ),
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
     *                 ref="#/components/schemas/Admin-PayGradeCurrencyModel"
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
        $payGradeId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_PAY_GRADE_ID
        );
        $currencyId = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_ID
        );
        $payGradeCurrency = $this->getPayGradeService()->getCurrencyByCurrencyIdAndPayGradeId($currencyId, $payGradeId);
        $this->throwRecordNotFoundExceptionIfNotExist($payGradeCurrency, PayGradeCurrency::class);
        return new EndpointResourceResult(PayGradeCurrencyModel::class, $payGradeCurrency);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_PAY_GRADE_ID,
                    new Rule(Rules::POSITIVE)
                )
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    CommonParams::PARAMETER_ID,
                    new Rule(Rules::STRING_TYPE)
                )
            )
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v2/admin/pay-grades/{payGradeId}/currencies",
     *     tags={"Admin/Pay Grade Currency"},
     *     summary="List All Pay Grade Currencies",
     *     operationId="list-all-pay-grade-currencies",
     *     @OA\PathParameter(
     *         name="payGradeId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="sortField",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=PayGradeCurrencySearchFilterParams::ALLOWED_SORT_FIELDS)
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
     *                 @OA\Items(ref="#/components/schemas/Admin-PayGradeCurrencyModel")
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
        $payGradeId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_PAY_GRADE_ID
        );
        $payGradeCurrencySearchFilterParams = new PayGradeCurrencySearchFilterParams();
        $payGradeCurrencySearchFilterParams->setPayGradeId($payGradeId);
        $this->setSortingAndPaginationParams($payGradeCurrencySearchFilterParams);
        $payGradeCurrencies = $this->getPayGradeService()->getPayGradeCurrencyList($payGradeCurrencySearchFilterParams);
        $count = $this->getPayGradeService()->getPayGradeCurrencyListCount($payGradeCurrencySearchFilterParams);

        return new EndpointCollectionResult(
            PayGradeCurrencyModel::class,
            $payGradeCurrencies,
            new ParameterBag([
                self::PARAMETER_PAY_GRADE_ID => $payGradeId,
                CommonParams::PARAMETER_TOTAL => $count
            ])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_PAY_GRADE_ID,
                new Rule(Rules::POSITIVE)
            ),
            ...$this->getSortingAndPaginationParamsRules(PayGradeCurrencySearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v2/admin/pay-grades/{payGradeId}/currencies",
     *     tags={"Admin/Pay Grade Currency"},
     *     summary="Create a Pay Grade Currency",
     *     operationId="create-a-pay-grade-currency",
     *     @OA\PathParameter(
     *         name="payGradeId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="currencyId", type="string"),
     *             @OA\Property(property="maxSalary", type="integer"),
     *             @OA\Property(property="minSalary", type="integer"),
     *             required={"currencyId", "maxSalary", "minSalary"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Admin-PayGradeCurrencyModel"
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
        $payGradeCurrency = $this->savePayGradeCurrency();
        return new EndpointResourceResult(
            PayGradeCurrencyModel::class,
            $payGradeCurrency,
            new ParameterBag(
                [
                    self::PARAMETER_PAY_GRADE_ID => $payGradeCurrency->getPayGrade()->getId(),
                ]
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_CURRENCY_ID,
                    new Rule(Rules::STRING_TYPE)
                ),
            ),
            ...$this->getBodyValidationRules(),
        );
    }

    /**
     * @OA\Put(
     *     path="/api/v2/admin/pay-grades/{payGradeId}/currencies/{id}",
     *     tags={"Admin/Pay Grade Currency"},
     *     summary="Update a Pay Grade Currency",
     *     operationId="update-a-pay-grade-currency",
     *     @OA\PathParameter(
     *         name="payGradeId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="maxSalary", type="integer"),
     *             @OA\Property(property="minSalary", type="integer"),
     *             required={"maxSalary", "minSalary"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Admin-PayGradeCurrencyModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     *
     * @inheritDoc
     */
    public function update(): EndpointResourceResult
    {
        $payGradeCurrency = $this->savePayGradeCurrency();
        return new EndpointResourceResult(
            PayGradeCurrencyModel::class,
            $payGradeCurrency,
            new ParameterBag([
                self::PARAMETER_PAY_GRADE_ID => $payGradeCurrency->getPayGrade()->getId()
            ])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    CommonParams::PARAMETER_ID,
                    new Rule(Rules::STRING_TYPE)
                )
            ),
            ...$this->getBodyValidationRules()
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/v2/admin/pay-grades/{payGradeId}/currencies",
     *     tags={"Admin/Pay Grade Currency"},
     *     summary="Delete Pay Grade Currencies",
     *     operationId="delete-pay-grade-currencies",
     *     @OA\PathParameter(
     *         name="payGradeId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(ref="#/components/requestBodies/DeleteRequestBody"),
     *     @OA\Response(response="200", ref="#/components/responses/DeleteResponse"),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function delete(): EndpointResourceResult
    {
        $payGradeId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, self::PARAMETER_PAY_GRADE_ID);
        $ids = $this->getPayGradeService()->getPayGradeDao()->getExistingCurrencyIdsForPayGradeId(
            $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS),
            $payGradeId
        );
        $this->throwRecordNotFoundExceptionIfEmptyIds($ids);
        $this->getPayGradeService()->deletePayGradeCurrency($payGradeId, $ids);
        return new EndpointResourceResult(
            ArrayModel::class,
            $ids,
            new ParameterBag(
                [
                    self::PARAMETER_PAY_GRADE_ID => $payGradeId,
                ]
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_PAY_GRADE_ID,
                new Rule(Rules::POSITIVE)
            ),
            new ParamRule(
                CommonParams::PARAMETER_IDS,
                new Rule(Rules::ARRAY_TYPE)
            )
        );
    }

    protected function getBodyValidationRules(): array
    {
        return [
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_PAY_GRADE_ID,
                    new Rule(Rules::POSITIVE)
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_MIN_SALARY,
                    new Rule(Rules::NUMBER),
                    new Rule(Rules::LESS_THAN, [self::PARAM_RULE_SALARY_MAX_VALUE]),
                    new Rule(
                        Rules::ONE_OF,
                        [
                            new Rule(Rules::DECIMAL, [0]),
                            new Rule(Rules::DECIMAL, [1]),
                            new Rule(Rules::DECIMAL, [2]),
                        ]
                    )
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_MAX_SALARY,
                    new Rule(Rules::NUMBER),
                    new Rule(Rules::LESS_THAN, [self::PARAM_RULE_SALARY_MAX_VALUE]),
                    new Rule(
                        Rules::ONE_OF,
                        [
                            new Rule(Rules::DECIMAL, [0]),
                            new Rule(Rules::DECIMAL, [1]),
                            new Rule(Rules::DECIMAL, [2]),
                        ]
                    )
                ),
            ),
        ];
    }

    /**
     * @return PayGradeCurrency
     * @throws RecordNotFoundException
     */
    protected function savePayGradeCurrency(): PayGradeCurrency
    {
        $payGradeId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_PAY_GRADE_ID
        )
        ;
        $currencyId = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_CURRENCY_ID
        );
        $minSalary = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_MIN_SALARY
        );
        $maxSalary = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_MAX_SALARY
        );

        if (!is_null($minSalary) && $minSalary !== '0' && $maxSalary <= $minSalary) {
            throw $this->getBadRequestException("Min salary should be less than max salary");
        }

        $id = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_ID
        );
        if (is_null($currencyId)) {
            $currencyId = $id;
        }
        $payGradeCurrency = $this->getPayGradeService()->getCurrencyByCurrencyIdAndPayGradeId($currencyId, $payGradeId);
        $currencyType = $this->getRepository(CurrencyType::class)->find($currencyId);
        $payGrade = $this->getRepository(PayGrade::class)->find($payGradeId);
        $this->throwRecordNotFoundExceptionIfNotExist($payGrade, PayGrade::class);
        $this->throwRecordNotFoundExceptionIfNotExist($currencyType, CurrencyType::class);
        if (!$payGradeCurrency instanceof PayGradeCurrency) {
            $payGradeCurrency = new PayGradeCurrency();
            $payGradeCurrency->setPayGrade($payGrade);
            $payGradeCurrency->setCurrencyType($currencyType);
        }
        $payGradeCurrency->setMinSalary($minSalary);
        $payGradeCurrency->setMaxSalary($maxSalary);
        return $this->getPayGradeService()->savePayGradeCurrency($payGradeCurrency);
    }
}
