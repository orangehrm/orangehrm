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
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Entity\Customer;
use Exception;
use OrangeHRM\Time\Api\Model\CustomerModel;
use OrangeHRM\Time\Dto\CustomerSearchFilterParams;
use OrangeHRM\Time\Service\CustomerService;

class CustomerAPI extends EndPoint implements CrudEndpoint
{
    public const PARAMETER_NAME = 'name';
    public const PARAMETER_DESCRIPTION = 'description';
    public const PARAM_RULE_NAME_MAX_LENGTH = 50;
    public const PARAM_RULE_DESCRIPTION_MAX_LENGTH = 255;

    public const FILTER_NAME = 'name';

    /**
     * @var CustomerService|null
     */

    protected ?CustomerService $customerService = null;

    public function getAll(): EndpointResult
    {

        $customerSearchParamHolder = new CustomerSearchFilterParams();
        $this->setSortingAndPaginationParams($customerSearchParamHolder);
        $customerSearchParamHolder->setName($this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_QUERY, self::FILTER_NAME));

        $customers = $this->getCustomerService()->searchCustomers($customerSearchParamHolder);
        $count = $this->getCustomerService()->getCustomersCount($customerSearchParamHolder);

        return new EndpointCollectionResult(
            CustomerModel::class, $customers,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
        );

    }

    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(self::FILTER_NAME),
            ...$this->getSortingAndPaginationParamsRules(CustomerSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function create(): EndpointResult
    {

        $customer = new Customer();
        $CustomerName = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_NAME);
        $customerDescription = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_DESCRIPTION);
        $customer->setName($CustomerName);
        $customer->setDescription($customerDescription);

        $this->getCustomerService()
            ->getCustomerDao()
            ->saveCustomer($customer);

        return new EndpointResourceResult(CustomerModel::class, $customer);

    }

    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(self::PARAMETER_NAME, new Rule(Rules::STRING_TYPE), new Rule(Rules::REQUIRED), new Rule(Rules::LENGTH, [null, self::PARAM_RULE_NAME_MAX_LENGTH])),
            new ParamRule(self::PARAMETER_DESCRIPTION, new Rule(Rules::STRING_TYPE), new Rule(Rules::LENGTH, [null, self::PARAM_RULE_DESCRIPTION_MAX_LENGTH]))
        );
    }

    public function delete(): EndpointResult
    {
        // TODO: Implement delete() method.
    }

    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        // TODO: Implement getValidationRuleForDelete() method.
    }

    public function getOne(): EndpointResult
    {
        // TODO: Implement getOne() method.
    }

    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        // TODO: Implement getValidationRuleForGetOne() method.
    }

    public function update(): EndpointResult
    {
        // TODO: Implement update() method.
    }

    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        // TODO: Implement getValidationRuleForUpdate() method.
    }

    /**
     * @return CustomerService
     */
    public function getCustomerService(): CustomerService
    {
        if (!$this->customerService instanceof CustomerService) {

            $this->customerService = new CustomerService();
        }

        return $this->customerService;
    }
}
