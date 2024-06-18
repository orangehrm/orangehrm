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
use OrangeHRM\Core\Api\V2\Validator\Rules\EntityUniquePropertyOption;
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
     * @OA\Get(
     *     path="/api/v2/time/customers",
     *     tags={"Time/Customers"},
     *     summary="List All Customers",
     *     operationId="list-all-customers",
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sortField",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=CustomerSearchFilterParams::ALLOWED_SORT_FIELDS)
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
     *                 ref="#/components/schemas/Time-CustomerModel"
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
     * @OA\Post(
     *     path="/api/v2/time/customers",
     *     tags={"Time/Customers"},
     *     summary="Create a Customer",
     *     operationId="create-a-customer",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 maxLength=OrangeHRM\Time\Api\CustomerAPI::PARAM_RULE_NAME_MAX_LENGTH
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 maxLength=OrangeHRM\Time\Api\CustomerAPI::PARAM_RULE_DESCRIPTION_MAX_LENGTH
     *             ),
     *             required={"name"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Time-CustomerModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     * )
     *
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
            ...$this->getCommonBodyValidationRules($this->getNameCommonUniqueOption()),
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/v2/time/customers",
     *     tags={"Time/Customers"},
     *     summary="Delete Customers",
     *     operationId="delete-customers",
     *     @OA\RequestBody(ref="#/components/requestBodies/DeleteRequestBody"),
     *     @OA\Response(response="200", ref="#/components/responses/DeleteResponse"),
     *     @OA\Response(
     *         response="400",
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="object",
     *                 @OA\Property(property="status", type="string", default="400"),
     *                 @OA\Property(property="message", type="string", default="Not Allowed to Delete Customer(s) Which Have Time Logged Against")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        try {
            $ids = $this->getCustomerService()->getCustomerDao()->getExistingCustomerIds(
                $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS)
            );
            $this->throwRecordNotFoundExceptionIfEmptyIds($ids);
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
     * @OA\Get(
     *     path="/api/v2/time/customers/{id}",
     *     tags={"Time/Customers"},
     *     summary="Get a Customer",
     *     operationId="get-a-customer",
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
     *                 ref="#/components/schemas/Time-CustomerModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         ),
     *         @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     *     )
     * )
     *
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
     * @OA\Put(
     *     path="/api/v2/time/customers/{id}",
     *     tags={"Time/Customers"},
     *     summary="Update a Customer",
     *     operationId="update-a-customer",
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 maxLength=OrangeHRM\Time\Api\CustomerAPI::PARAM_RULE_NAME_MAX_LENGTH
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 maxLength=OrangeHRM\Time\Api\CustomerAPI::PARAM_RULE_DESCRIPTION_MAX_LENGTH
     *             ),
     *             required={"name"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Time-CustomerModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
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
        $uniqueOption = $this->getNameCommonUniqueOption();
        $uniqueOption->setIgnoreId($this->getAttributeId());

        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            ),
            ...$this->getCommonBodyValidationRules($uniqueOption),
        );
    }

    /**
     * @param EntityUniquePropertyOption|null $uniqueOption
     * @return ParamRule[]
     */
    protected function getCommonBodyValidationRules(?EntityUniquePropertyOption $uniqueOption = null): array
    {
        return [
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_NAME,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_NAME_MAX_LENGTH]),
                    new Rule(Rules::ENTITY_UNIQUE_PROPERTY, [Customer::class, 'name', $uniqueOption])
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
     * @return EntityUniquePropertyOption
     */
    private function getNameCommonUniqueOption(): EntityUniquePropertyOption
    {
        $uniqueOption = new EntityUniquePropertyOption();
        $uniqueOption->setIgnoreValues([
            'deleted' => true
        ]);
        return $uniqueOption;
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
