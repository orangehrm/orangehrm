<?php

namespace OrangeHRM\Time\Api;

use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Entity\Customer;
use Exception;
use OrangeHRM\Time\Api\Model\CustomerModel;
use OrangeHRM\Time\Service\CustomerService;

class CustomerAPI extends EndPoint implements CrudEndpoint
{

    public const PARAMETER_NAME = 'name';
    public const PARAMETER_DESCRIPTION = 'description';
    public const PARAM_RULE_NAME_MAX_LENGTH = 100;


    /**
     * @var CustomerService|null
     */

    protected ?CustomerService $customerService = null;


    public function getAll(): EndpointResult
    {
        // TODO: Implement getAll() method.
    }

    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        // TODO: Implement getValidationRuleForGetAll() method.
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
            new ParamRule(self::PARAMETER_NAME),
            new ParamRule(self::PARAMETER_DESCRIPTION)
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