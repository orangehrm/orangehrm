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

use OrangeHRM\Admin\Api\Model\CurrencyTypeModel;
use OrangeHRM\Admin\Dto\AllowedPayGradeCurrencySearchFilterParams;
use OrangeHRM\Admin\Dto\PayGradeCurrencySearchFilterParams;
use OrangeHRM\Admin\Traits\Service\PayGradeServiceTrait;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;

class PayGradeAllowedCurrencyAPI extends Endpoint implements CollectionEndpoint
{
    use PayGradeServiceTrait;

    /**
     * @OA\Get(
     *     path="/api/v2/admin/pay-grades/{payGradeId}/currencies/allowed",
     *     tags={"Admin/Pay Grade Currency"},
     *     summary="List Allowed Currencies for Pay Grade",
     *     operationId="list-allowed-currencies-for-pay-grade",
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
     *                 @OA\Items(ref="#/components/schemas/Admin-CurrencyTypeModel")
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
    public function getAll(): EndpointResult
    {
        $payGradeId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, PayGradeCurrencySearchFilterParams::PARAMETER_PAY_GRADE_ID);
        $payGradeCurrencySearchFilterParams = new AllowedPayGradeCurrencySearchFilterParams();
        $this->setSortingAndPaginationParams($payGradeCurrencySearchFilterParams);
        $payGradeCurrencySearchFilterParams->setPayGradeId($payGradeId);
        $allowedCurrencies = $this->getPayGradeService()->getAllowedPayCurrencies($payGradeCurrencySearchFilterParams);
        $count = $this->getPayGradeService()->getAllowedPayCurrenciesCount($payGradeCurrencySearchFilterParams);
        return new EndpointCollectionResult(
            CurrencyTypeModel::class,
            $allowedCurrencies,
            new ParameterBag([
                PayGradeCurrencySearchFilterParams::PARAMETER_PAY_GRADE_ID => $payGradeId,
                CommonParams::PARAMETER_TOTAL => $count,
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
                PayGradeCurrencySearchFilterParams::PARAMETER_PAY_GRADE_ID,
                new Rule(Rules::POSITIVE)
            ),
            ...$this->getSortingAndPaginationParamsRules(AllowedPayGradeCurrencySearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    public function create(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    public function delete(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }
}
