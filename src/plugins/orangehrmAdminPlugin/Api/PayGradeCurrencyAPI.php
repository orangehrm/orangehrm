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

use OrangeHRM\Admin\Api\Model\PayGradeCurrencyModel;
use OrangeHRM\Admin\Dto\PayGradeCurrencySearchFilterParams;
use OrangeHRM\Admin\Service\PayGradeService;
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
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Entity\CurrencyType;
use OrangeHRM\Entity\PayGrade;
use OrangeHRM\Entity\PayGradeCurrency;
use OrangeHRM\Framework\Services;

class PayGradeCurrencyAPI extends Endpoint implements CrudEndpoint
{
    use ServiceContainerTrait;
    use EntityManagerHelperTrait;

    public const PARAMETER_PAY_GRADE_ID = 'payGradeId';
    public const PARAMETER_CURRENCY_ID = 'currencyId';
    public const PARAMETER_MIN_SALARY = 'minSalary';
    public const PARAMETER_MAX_SALARY = 'maxSalary';

    /**
     * @return PayGradeService
     */
    public function getPayGradeService(): PayGradeService
    {
        return $this->getContainer()->get(Services::PAY_GRADE_SERVICE);
    }

    /**
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
            new ParamRule(self::PARAMETER_PAY_GRADE_ID, new Rule(Rules::POSITIVE)),
            new ParamRule(CommonParams::PARAMETER_ID, new Rule(Rules::REQUIRED))
        );
    }

    /**
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
                CommonParams::PARAMETER_TOTAL=> $count
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
            new ParamRule(self::PARAMETER_PAY_GRADE_ID, new Rule(Rules::REQUIRED)),
            new ParamRule(self::PARAMETER_CURRENCY_ID, new Rule(Rules::REQUIRED)),
            ...$this->getBodyValidationRules(),
        );
    }

    /**
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
            new ParamRule(self::PARAMETER_PAY_GRADE_ID, new Rule(Rules::REQUIRED)),
            new ParamRule(CommonParams::PARAMETER_ID, new Rule(Rules::REQUIRED)),
            ...$this->getBodyValidationRules()
        );
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointResourceResult
    {
        $payGradeId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, self::PARAMETER_PAY_GRADE_ID);
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
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
            new ParamRule(self::PARAMETER_PAY_GRADE_ID, new Rule(Rules::REQUIRED)),
            new ParamRule(CommonParams::PARAMETER_IDS, new Rule(Rules::ARRAY_TYPE)),
        );
    }

    protected function getBodyValidationRules(): array
    {
        return [
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_MIN_SALARY,
                    new Rule(Rules::NUMBER),
                    new Rule(Rules::LENGTH, [null, 9])
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_MAX_SALARY,
                    new Rule(Rules::NUMBER),
                    new Rule(Rules::LENGTH, [null, 9])
                ),
            ),
        ];
    }

    /**
     * @return PayGradeCurrency
     * @throws \OrangeHRM\Core\Exception\DaoException
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
        $payGradeCurrency = $this->getPayGradeService()->savePayGradeCurrency($payGradeCurrency);
        return  $payGradeCurrency;
    }
}
