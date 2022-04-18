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

namespace OrangeHRM\Pim\Api;

use OrangeHRM\Admin\Dto\EmployeeSalarySearchFilterParams;
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
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Entity\EmpDirectDebit;
use OrangeHRM\Entity\EmployeeSalary;
use OrangeHRM\Framework\Services;
use OrangeHRM\Pim\Api\Model\EmployeeSalaryModel;
use OrangeHRM\Pim\Service\EmployeeSalaryService;

class EmployeeSalaryComponentAPI extends Endpoint implements CrudEndpoint
{
    use ServiceContainerTrait;

    public const PARAMETER_PAY_GRADE_ID = 'payGradeId';
    public const PARAMETER_SALARY_COMPONENT = 'salaryComponent';
    public const PARAMETER_PAY_FREQUENCY_ID = 'payFrequencyId';
    public const PARAMETER_CURRENCY_ID = 'currencyId';
    public const PARAMETER_SALARY_AMOUNT = 'salaryAmount';
    public const PARAMETER_COMMENT = 'comment';
    public const PARAMETER_ADD_DIRECT_DEPOSIT = 'addDirectDeposit';
    public const PARAMETER_DIRECT_DEPOSIT_ACCOUNT = 'directDepositAccount';
    public const PARAMETER_DIRECT_DEPOSIT_ACCOUNT_TYPE = 'directDepositAccountType';
    public const PARAMETER_DIRECT_DEPOSIT_AMOUNT = 'directDepositAmount';
    public const PARAMETER_DIRECT_DEPOSIT_ROUTING_NUMBER = 'directDepositRoutingNumber';

    public const PARAM_RULE_SALARY_COMPONENT_MAX_LENGTH = 100;
    public const PARAM_RULE_SALARY_AMOUNT_MAX_LENGTH = 100;
    public const PARAM_RULE_COMMENT_MAX_LENGTH = 250;
    public const PARAM_RULE_DIRECT_DEPOSIT_ACCOUNT_MAX_LENGTH = 100;
    public const PARAM_RULE_DIRECT_DEPOSIT_ACCOUNT_TYPE_MAX_LENGTH = 20;
    public const PARAM_RULE_DIRECT_DEPOSIT_ROUTING_NUMBER_MAX_LENGTH = 9;
    public const PARAM_RULE_DIRECT_DEPOSIT_AMOUNT_MIN = 0;
    public const PARAM_RULE_DIRECT_DEPOSIT_AMOUNT_MAX = 999999999.99;

    /**
     * @var EmployeeSalaryService|null
     */
    protected ?EmployeeSalaryService $employeeSalaryService = null;

    /**
     * @return EmployeeSalaryService
     */
    public function getEmployeeSalaryService(): EmployeeSalaryService
    {
        if (!$this->employeeSalaryService instanceof EmployeeSalaryService) {
            $this->employeeSalaryService = new EmployeeSalaryService();
        }
        return $this->employeeSalaryService;
    }

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
        list($empNumber, $id) = $this->getUrlAttributes();
        $employeeSalary = $this->getEmployeeSalaryService()->getEmployeeSalaryDao()->getEmployeeSalary($empNumber, $id);
        $this->throwRecordNotFoundExceptionIfNotExist($employeeSalary, EmployeeSalary::class);

        return new EndpointResourceResult(
            EmployeeSalaryModel::class,
            $employeeSalary,
            new ParameterBag([CommonParams::PARAMETER_EMP_NUMBER => $empNumber])
        );
    }

    /**
     * @return array
     */
    private function getUrlAttributes(): array
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $id = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_ID
        );
        return [$empNumber, $id];
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            ),
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointCollectionResult
    {
        list($empNumber) = $this->getUrlAttributes();
        $employeeSalarySearchFilterParams = new EmployeeSalarySearchFilterParams();
        $this->setSortingAndPaginationParams($employeeSalarySearchFilterParams);
        $employeeSalarySearchFilterParams->setEmpNumber($empNumber);
        $employeeSalaries = $this->getEmployeeSalaryService()->getEmployeeSalaryDao()->getEmployeeSalaries(
            $employeeSalarySearchFilterParams
        );
        return new EndpointCollectionResult(
            EmployeeSalaryModel::class,
            $employeeSalaries,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                    CommonParams::PARAMETER_TOTAL => $this->getEmployeeSalaryService()
                        ->getEmployeeSalaryDao()
                        ->getEmployeeSalariesCount($employeeSalarySearchFilterParams)
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
            new ParamRule(
                CommonParams::PARAMETER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            ),
            ...$this->getSortingAndPaginationParamsRules(EmployeeSalarySearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @inheritDoc
     */
    public function create(): EndpointResourceResult
    {
        list($empNumber) = $this->getUrlAttributes();
        $employeeSalary = new EmployeeSalary();
        $employeeSalary->getDecorator()->setEmployeeByEmpNumber($empNumber);
        $this->setEmployeeSalary($employeeSalary);
        $this->setEmployeeDirectDebitToEmployeeSalary($employeeSalary);

        $this->getEmployeeSalaryService()
            ->getEmployeeSalaryDao()
            ->saveEmployeeSalary($employeeSalary);

        return new EndpointResourceResult(
            EmployeeSalaryModel::class,
            $employeeSalary,
            new ParameterBag([CommonParams::PARAMETER_EMP_NUMBER => $empNumber])
        );
    }

    /**
     * @param EmployeeSalary $employeeSalary
     */
    protected function setEmployeeSalary(EmployeeSalary $employeeSalary): void
    {
        $payGradeId = $this->getRequestParams()->getIntOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_PAY_GRADE_ID
        );
        $employeeSalary->getDecorator()->setPayGradeById($payGradeId);
        $employeeSalary->setSalaryName(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_SALARY_COMPONENT
            )
        );
        $employeeSalary->getDecorator()->setPayPeriodById(
            $this->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_PAY_FREQUENCY_ID
            )
        );

        $currencyTypeId = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_CURRENCY_ID
        );
        $employeeSalary->getDecorator()->setCurrencyTypeById($currencyTypeId);

        $salaryAmount = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_SALARY_AMOUNT
        );

        if (!is_null($payGradeId)) {
            if (!$this->getPayGradeService()
                ->isValidSalaryAmountForPayGradeCurrency($salaryAmount, $currencyTypeId, $payGradeId)
            ) {
                throw $this->getBadRequestException('Salary should be within min and max');
            }
        }

        $employeeSalary->setAmount($salaryAmount);
        $employeeSalary->setComment(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_COMMENT
            )
        );
    }

    /**
     * @param EmployeeSalary $employeeSalary
     */
    protected function setEmployeeDirectDebitToEmployeeSalary(EmployeeSalary $employeeSalary): void
    {
        $addDirectDeposit = $this->getRequestParams()->getBoolean(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_ADD_DIRECT_DEPOSIT
        );

        $directDebit = $employeeSalary->getDirectDebit();
        if ($addDirectDeposit) {
            if (!$directDebit instanceof EmpDirectDebit) {
                $directDebit = new EmpDirectDebit();
            }
            $directDebit->setAccount(
                $this->getRequestParams()->getString(
                    RequestParams::PARAM_TYPE_BODY,
                    self::PARAMETER_DIRECT_DEPOSIT_ACCOUNT
                )
            );
            $directDebit->setAccountType(
                $this->getRequestParams()->getString(
                    RequestParams::PARAM_TYPE_BODY,
                    self::PARAMETER_DIRECT_DEPOSIT_ACCOUNT_TYPE
                )
            );
            $directDebit->setRoutingNumber(
                $this->getRequestParams()->getInt(
                    RequestParams::PARAM_TYPE_BODY,
                    self::PARAMETER_DIRECT_DEPOSIT_ROUTING_NUMBER
                )
            );
            $directDebit->setAmount(
                $this->getRequestParams()->getString(
                    RequestParams::PARAM_TYPE_BODY,
                    self::PARAMETER_DIRECT_DEPOSIT_AMOUNT
                )
            );
            $directDebit->setSalary($employeeSalary);
            $employeeSalary->setDirectDebit($directDebit);
        } elseif ($directDebit instanceof EmpDirectDebit) {
            $employeeSalary->setDirectDebit(null);
            $this->getEmployeeSalaryService()
                ->getEmployeeSalaryDao()
                ->deleteEmployeeDirectDebit($directDebit);
        }
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return $this->getCommonBodyValidationRules();
    }

    private function getCommonBodyValidationRules(): ParamRuleCollection
    {
        $addDirectDeposit = $this->getRequestParams()->getBoolean(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_ADD_DIRECT_DEPOSIT
        );

        $paramRules = new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_PAY_GRADE_ID,
                    new Rule(Rules::POSITIVE)
                )
            ),
            new ParamRule(
                self::PARAMETER_SALARY_COMPONENT,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_SALARY_COMPONENT_MAX_LENGTH])
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_PAY_FREQUENCY_ID,
                    new Rule(Rules::POSITIVE)
                )
            ),
            new ParamRule(
                self::PARAMETER_CURRENCY_ID,
                new Rule(Rules::CURRENCY)
            ),
            new ParamRule(
                self::PARAMETER_SALARY_AMOUNT,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_SALARY_AMOUNT_MAX_LENGTH])
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_COMMENT,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_COMMENT_MAX_LENGTH])
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_ADD_DIRECT_DEPOSIT,
                    new Rule(Rules::BOOL_TYPE)
                )
            ),
        );

        if ($addDirectDeposit) {
            $paramRules->addParamValidation(
                new ParamRule(
                    self::PARAMETER_DIRECT_DEPOSIT_ACCOUNT,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_DIRECT_DEPOSIT_ACCOUNT_MAX_LENGTH])
                )
            );
            $paramRules->addParamValidation(
                new ParamRule(
                    self::PARAMETER_DIRECT_DEPOSIT_ACCOUNT_TYPE,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_DIRECT_DEPOSIT_ACCOUNT_TYPE_MAX_LENGTH])
                )
            );
            $paramRules->addParamValidation(
                new ParamRule(
                    self::PARAMETER_DIRECT_DEPOSIT_ROUTING_NUMBER,
                    new Rule(Rules::NUMBER),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_DIRECT_DEPOSIT_ROUTING_NUMBER_MAX_LENGTH])
                )
            );
            $paramRules->addParamValidation(
                new ParamRule(
                    self::PARAMETER_DIRECT_DEPOSIT_AMOUNT,
                    new Rule(
                        Rules::BETWEEN,
                        [
                            self::PARAM_RULE_DIRECT_DEPOSIT_AMOUNT_MIN,
                            self::PARAM_RULE_DIRECT_DEPOSIT_AMOUNT_MAX
                        ]
                    )
                )
            );
        }
        return $paramRules;
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointResourceResult
    {
        list($empNumber, $id) = $this->getUrlAttributes();
        $employeeSalary = $this->getEmployeeSalaryService()
            ->getEmployeeSalaryDao()
            ->getEmployeeSalary($empNumber, $id);
        $this->throwRecordNotFoundExceptionIfNotExist($employeeSalary, EmployeeSalary::class);

        $this->setEmployeeSalary($employeeSalary);
        $this->setEmployeeDirectDebitToEmployeeSalary($employeeSalary);

        $this->getEmployeeSalaryService()
            ->getEmployeeSalaryDao()
            ->saveEmployeeSalary($employeeSalary);

        return new EndpointResourceResult(
            EmployeeSalaryModel::class,
            $employeeSalary,
            new ParameterBag([CommonParams::PARAMETER_EMP_NUMBER => $empNumber])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        $paramRules = $this->getCommonBodyValidationRules();
        $paramRules->addParamValidation(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            )
        );
        return $paramRules;
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointResourceResult
    {
        list($empNumber) = $this->getUrlAttributes();
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        $this->getEmployeeSalaryService()->getEmployeeSalaryDao()->deleteEmployeeSalaries($empNumber, $ids);
        return new EndpointResourceResult(
            ArrayModel::class,
            $ids,
            new ParameterBag([CommonParams::PARAMETER_EMP_NUMBER => $empNumber])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            ),
            new ParamRule(CommonParams::PARAMETER_IDS),
        );
    }
}
