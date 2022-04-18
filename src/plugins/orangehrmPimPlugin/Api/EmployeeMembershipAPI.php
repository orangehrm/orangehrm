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
use OrangeHRM\Entity\EmployeeMembership;
use OrangeHRM\Pim\Api\Model\EmployeeMembershipModel;
use OrangeHRM\Pim\Dto\EmployeeMembershipSearchFilterParams;
use OrangeHRM\Pim\Service\EmployeeMembershipService;

class EmployeeMembershipAPI extends Endpoint implements CrudEndpoint
{
    public const PARAMETER_MEMBERSHIP_ID = 'membershipId';
    public const PARAMETER_SUBSCRIPTION_FEE = 'subscriptionFee';
    public const PARAMETER_SUBSCRIPTION_PAID_BY = 'subscriptionPaidBy';
    public const PARAMETER_SUBSCRIPTION_CURRENCY = 'currencyTypeId';
    public const PARAMETER_SUBSCRIPTION_COMMENCE_DATE = 'subscriptionCommenceDate';
    public const PARAMETER_SUBSCRIPTION_RENEWAL_DATE = 'subscriptionRenewalDate';

    /**
     * @var null|EmployeeMembershipService
     */
    protected ?EmployeeMembershipService $employeeMembershipService = null;

    /**
     * @return EmployeeMembershipService
     */
    public function getEmployeeMembershipService(): EmployeeMembershipService
    {
        if (is_null($this->employeeMembershipService)) {
            $this->employeeMembershipService = new EmployeeMembershipService();
        }
        return $this->employeeMembershipService;
    }

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResourceResult
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $id = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_ID
        );
        $employeeMembership = $this->getEmployeeMembershipService()
            ->getEmployeeMembershipDao()
            ->getEmployeeMembershipById($empNumber, $id);
        $this->throwRecordNotFoundExceptionIfNotExist($employeeMembership, EmployeeMembership::class);

        return new EndpointResourceResult(
            EmployeeMembershipModel::class,
            $employeeMembership,
            new ParameterBag([CommonParams::PARAMETER_EMP_NUMBER => $empNumber])
        );
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
            ),
        );
    }

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointCollectionResult
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $employeeMembershipSearchParams = new EmployeeMembershipSearchFilterParams();
        $this->setSortingAndPaginationParams($employeeMembershipSearchParams);
        $employeeMembershipSearchParams->setEmpNumber($empNumber);

        $employeeMemberships = $this->getEmployeeMembershipService()->getEmployeeMembershipDao()->searchEmployeeMembership(
            $employeeMembershipSearchParams
        );

        return new EndpointCollectionResult(
            EmployeeMembershipModel::class,
            $employeeMemberships,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                    CommonParams::PARAMETER_TOTAL => $this->getEmployeeMembershipService()->getEmployeeMembershipDao()->getSearchEmployeeMembershipsCount(
                        $employeeMembershipSearchParams
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
            new ParamRule(
                CommonParams::PARAMETER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            ),
            ...$this->getSortingAndPaginationParamsRules(EmployeeMembershipSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @inheritDoc
     */
    public function create(): EndpointResourceResult
    {
        $employeeMembership = $this->saveEmployeeMembership();
        return new EndpointResourceResult(
            EmployeeMembershipModel::class,
            $employeeMembership,
            new ParameterBag([CommonParams::PARAMETER_EMP_NUMBER => $employeeMembership->getEmployee()->getEmpNumber()])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            ),
            ...$this->getCommonBodyValidationRules(),
        );
    }

    /**
     * @return ParamRule[]
     */
    private function getCommonBodyValidationRules(): array
    {
        return [
            new ParamRule(self::PARAMETER_MEMBERSHIP_ID, new Rule(Rules::REQUIRED), new Rule(Rules::POSITIVE)),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_SUBSCRIPTION_FEE,
                    new Rule(Rules::NOT_EMPTY),
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::BETWEEN, [0, 1000000000]),
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_SUBSCRIPTION_PAID_BY,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::IN, [[EmployeeMembership::COMPANY,EmployeeMembership::INDIVIDUAL]]),
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_SUBSCRIPTION_CURRENCY,
                    new Rule(Rules::CURRENCY),
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_SUBSCRIPTION_COMMENCE_DATE,
                    new Rule(Rules::API_DATE),
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_SUBSCRIPTION_RENEWAL_DATE,
                    new Rule(Rules::API_DATE),
                ),
            ),
        ];
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointResourceResult
    {
        $employeeMembership = $this->saveEmployeeMembership();
        return new EndpointResourceResult(
            EmployeeMembershipModel::class,
            $employeeMembership,
            new ParameterBag([CommonParams::PARAMETER_EMP_NUMBER => $employeeMembership->getEmployee()->getEmpNumber()])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            ),
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            ),
            ...$this->getCommonBodyValidationRules(),
        );
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointResourceResult
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        $this->getEmployeeMembershipService()->getEmployeeMembershipDao()->deleteEmployeeMemberships($empNumber, $ids);
        return new EndpointResourceResult(
            ArrayModel::class,
            $ids,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
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
                CommonParams::PARAMETER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            ),
            new ParamRule(
                CommonParams::PARAMETER_IDS,
                new Rule(Rules::ARRAY_TYPE)
            ),
        );
    }

    /**
     * @return EmployeeMembership
     */
    private function saveEmployeeMembership(): EmployeeMembership
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $membershipId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_MEMBERSHIP_ID
        );
        $paidBy = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_SUBSCRIPTION_PAID_BY
        );
        $currency = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_SUBSCRIPTION_CURRENCY
        );

        $fee = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_SUBSCRIPTION_FEE
        );
        $commenceDate = $this->getRequestParams()->getDateTimeOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_SUBSCRIPTION_COMMENCE_DATE
        );
        $renewalDate = $this->getRequestParams()->getDateTimeOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_SUBSCRIPTION_RENEWAL_DATE
        );
        if ($id) {
            $employeeMembership = $this->getEmployeeMembershipService()->getEmployeeMembershipDao()->getEmployeeMembershipById(
                $empNumber,
                $id
            );
            $this->throwRecordNotFoundExceptionIfNotExist($employeeMembership, EmployeeMembership::class);
        } else {
            $employeeMembership = new EmployeeMembership();
            $employeeMembership->getDecorator()->setEmployeeByEmpNumber($empNumber);
        }

        $employeeMembership->getDecorator()->setMembershipByMembershipId($membershipId);
        $employeeMembership->setSubscriptionPaidBy($paidBy);
        $employeeMembership->setSubscriptionCurrency($currency);
        $employeeMembership->setSubscriptionFee($fee);
        $employeeMembership->setSubscriptionCommenceDate($commenceDate);
        $employeeMembership->setSubscriptionRenewalDate($renewalDate);

        return $this->getEmployeeMembershipService()
            ->getEmployeeMembershipDao()
            ->saveEmployeeMembership($employeeMembership);
    }
}
