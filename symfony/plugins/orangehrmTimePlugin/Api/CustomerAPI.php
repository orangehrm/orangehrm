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

namespace OrangeHRM\Time\Api;

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
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\Customer;
use OrangeHRM\Time\Api\Model\CustomerModel;
use OrangeHRM\Time\Dto\CustomerSearchFilterParams;
use OrangeHRM\Time\Exception\CustomerServiceException;
use OrangeHRM\Time\Traits\Service\CustomerServiceTrait;

class CustomerAPI extends Endpoint implements CrudEndpoint
{
    use CustomerServiceTrait;
    use UserRoleManagerTrait;

    public const PARAMETER_NAME = 'name';
    public const PARAMETER_DESCRIPTION = 'description';
    public const PARAM_RULE_NAME_MAX_LENGTH = 50;
    public const PARAM_RULE_DESCRIPTION_MAX_LENGTH = 255;

    public const FILTER_NAME = 'name';

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $customerSearchParamHolder = new CustomerSearchFilterParams();
        $this->setSortingAndPaginationParams($customerSearchParamHolder);
        $accessibleCustomerIds = $this->getUserRoleManager()->getAccessibleEntityIds(Customer::class);
        $customerSearchParamHolder->setCustomerIds($accessibleCustomerIds);
        $customerSearchParamHolder->setName(
            $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_QUERY, self::FILTER_NAME)
        );
        $customers = $this->getCustomerService()->searchCustomers($customerSearchParamHolder);
        $count = $this->getCustomerService()->getCustomersCount($customerSearchParamHolder);

        return new EndpointCollectionResult(
            CustomerModel::class,
            $customers,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(self::FILTER_NAME),
            ...$this->getSortingAndPaginationParamsRules(CustomerSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        $customer = new Customer();
        $this->setParamsToCustomer($customer);
        $this->getCustomerService()
            ->getCustomerDao()
            ->saveCustomer($customer);

        return new EndpointResourceResult(CustomerModel::class, $customer);
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
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        try {
            $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
            $this->getCustomerService()->getCustomerDao()->deleteCustomer($ids);
            return new EndpointResourceResult(ArrayModel::class, $ids);
        } catch (CustomerServiceException $customerServiceException) {
            throw $this->getBadRequestException($customerServiceException->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_IDS,
                new Rule(Rules::ARRAY_TYPE),
                new Rule(
                    Rules::EACH,
                    [new Rules\Composite\AllOf(new Rule(Rules::POSITIVE))]
                )
            ),
        );
    }

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $customerId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_ID
        );
        $customer = $this->getCustomerService()->getCustomer($customerId);
        $this->throwRecordNotFoundExceptionIfNotExist($customer, Customer::class);

        return new EndpointResourceResult(CustomerModel::class, $customer);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE),
                new Rule(Rules::IN_ACCESSIBLE_ENTITY_ID, [Customer::class])
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        $customerId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_ID
        );
        $customer = $this->getCustomerService()->getCustomer($customerId);
        $this->throwRecordNotFoundExceptionIfNotExist($customer, Customer::class);
        $this->setParamsToCustomer($customer);
        $this->getCustomerService()
            ->getCustomerDao()
            ->saveCustomer($customer);

        return new EndpointResourceResult(CustomerModel::class, $customer);
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
            ...$this->getCommonBodyValidationRules(),
        );
    }

    /**
     * @return ParamRule[]
     */
    protected function getCommonBodyValidationRules(): array
    {
        return [
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_NAME,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_NAME_MAX_LENGTH])
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_DESCRIPTION,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_DESCRIPTION_MAX_LENGTH])
                ),
                true
            ),
        ];
    }

    /**
     * @param Customer $customer
     */
    private function setParamsToCustomer(Customer $customer): void
    {
        $customerName = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_NAME);
        $customerDescription = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_DESCRIPTION
        );
        $customer->setName($customerName);
        $customer->setDescription($customerDescription);
        $customer->setDeleted(false);
    }
}
