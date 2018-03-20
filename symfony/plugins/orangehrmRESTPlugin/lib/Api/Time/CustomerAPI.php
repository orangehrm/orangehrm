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

namespace Orangehrm\Rest\Api\Time;

use Orangehrm\Rest\Api\EndPoint;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Api\Time\Entity\Customer;
use Orangehrm\Rest\Http\Response;

class CustomerAPI extends EndPoint
{
    const PARAMETER_NAME = "name";
    const PARAMETER_DESCRIPTION = "description";
    const PARAMETER_CUSTOMER_ID = "customerId";

    private $customerService;

    /**
     *
     * @return customerService
     */
    public function getCustomerService()
    {
        if (is_null($this->customerService)) {
            $this->customerService = new \CustomerService();
        }
        return $this->customerService;
    }

    /**
     * Set customer service
     *
     * @param $customerService
     */
    public function setCustomerService($customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * Get customers
     *
     * @return Response
     * @throws RecordNotFoundException
     */
    public function getCustomers()
    {
        $customers = $this->getCustomerService()->getCustomerList();
        $responseArray = null;
        foreach ($customers as $customer) {

            $customerEntity = new Customer();
            $customerEntity->build($customer);
            $responseArray[] = $customerEntity->toArray();
        }
        if (count(responseArray) > 0) {
            return new Response($responseArray, array());
        } else {
            throw new RecordNotFoundException('No Customers Found');
        }

    }

    /**
     * Save customer
     *
     * @return Response
     * @throws InvalidParamException
     */
    public function saveCustomer()
    {

        $filters = $this->filterParameters();
        $customerCount = $this->getCustomerService()->getCustomerByName($filters[self::PARAMETER_NAME]);

        if ($customerCount == 0) {
            $customer = new \Customer();
            $customer->setName($filters[self::PARAMETER_NAME]);
            $customer->setDescription($filters[self::PARAMETER_DESCRIPTION]);
            $customer->save();
            return new Response(array('success' => 'Successfully Saved', 'customerId' => $customer->getCustomerId()));
        } else {
            throw new InvalidParamException('Customer Already Exists');
        }
    }

    /**
     * Update customer
     *
     * @return Response
     * @throws InvalidParamException
     * @throws RecordNotFoundException
     */
    public function updateCustomer()
    {
        $filters = $this->filterParameters();
        $customerId = $filters[self::PARAMETER_CUSTOMER_ID];
        if (empty($customerId)) {
            throw new InvalidParamException("Customer Id Is Empty");
        }
        $customer = $this->getCustomerService()->getActiveCustomerById($filters[self::PARAMETER_CUSTOMER_ID]);

        if ($customer instanceof \Customer) {

            if ($this->checkCustomerNameForUpdate($filters[self::PARAMETER_NAME])) {

                if ($customer->getName() == $filters[self::PARAMETER_NAME]) {

                    $this->updateCustomerFields($customer,$filters);
                    return new Response(array('success' => 'Successfully Updated'));
                } else {
                    throw new InvalidParamException('Customer Already Exists');

                }
            } else {
                $this->updateCustomerFields($customer,$filters);
                return new Response(array('success' => 'Successfully Updated'));
            }


        } else {
            throw new RecordNotFoundException('Customer Not Found');
        }
    }

    /**
     * Delete customer
     *
     * @return Response
     * @throws InvalidParamException
     * @throws RecordNotFoundException
     */
    public function deleteCustomer()
    {
        $filters = $this->filterDeleteParameters();
        $customerId = $filters[self::PARAMETER_CUSTOMER_ID];

        if (empty($customerId)) {
            throw new InvalidParamException("Customer Id Is Empty");
        }
        $hasTimeSheets = $this->getCustomerService()->hasCustomerGotTimesheetItems($customerId);

        if (!$hasTimeSheets) {

            $customer = $this->getCustomerService()->getCustomerById($filters[self::PARAMETER_CUSTOMER_ID]);

            if ($customer instanceof \Customer && $customer->getIsDeleted() == 0) {

                $this->getCustomerService()->deleteCustomer($customerId);
                return new Response(array('success' => 'Successfully Deleted'));

            } else {
                throw new RecordNotFoundException('Customer Not Found');
            }

        } else {
            throw new InvalidParamException("Given Customer Id Cannot Be Deleted");
        }

    }

    /**
     * Filter parameters
     *
     * @return array
     * @throws InvalidParamException
     */
    protected function filterParameters()
    {
        $filters[] = array();

        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_NAME))) {
            $filters[self::PARAMETER_NAME] = $this->getRequestParams()->getPostParam(self::PARAMETER_NAME);
        } else {
            throw new InvalidParamException('Name Value Is Not Set');
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_DESCRIPTION))) {
            $filters[self::PARAMETER_DESCRIPTION] = $this->getRequestParams()->getPostParam(self::PARAMETER_DESCRIPTION);
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_CUSTOMER_ID))) {
            $filters[self::PARAMETER_CUSTOMER_ID] = $this->getRequestParams()->getPostParam(self::PARAMETER_CUSTOMER_ID);
        }
        return $filters;

    }

    protected function filterDeleteParameters()
    {
        $filters[] = array();

        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_CUSTOMER_ID))) {
            $filters[self::PARAMETER_CUSTOMER_ID] = $this->getRequestParams()->getPostParam(self::PARAMETER_CUSTOMER_ID);
        }
        return $filters;

    }

    public function getPostValidationRules()
    {
        return array(
            self::PARAMETER_NAME => array('StringType' => true, 'NotEmpty' => true, 'Length' => array(1, 50)),
            self::PARAMETER_DESCRIPTION => array('StringType' => true, 'NotEmpty' => true, 'Length' => array(1, 200)),
        );
    }

    public function putValidationRules()
    {
        return array(
            self::PARAMETER_CUSTOMER_ID => array('IntVal' => true, 'NotEmpty' => true),
            self::PARAMETER_NAME => array('StringType' => true, 'NotEmpty' => true, 'Length' => array(1, 50)),
            self::PARAMETER_DESCRIPTION => array('StringType' => true, 'NotEmpty' => true, 'Length' => array(1, 200)),
        );
    }

    public function deleteValidationRules()
    {
        return array(
            self::PARAMETER_CUSTOMER_ID => array('IntVal' => true, 'NotEmpty' => true),

        );
    }

    /**
     * Check customer name
     *
     * @param $name
     * @return bool
     */
    public function checkCustomerNameForUpdate($name)
    {

        $customerCount = $this->getCustomerService()->getCustomerByName($name);
        if ($customerCount != 0) {
            return true;
        } else {
            return false;
        }
    }

    public function updateCustomerFields(\Customer $customer,$filters){

        if(!empty($filters[self::PARAMETER_NAME])){
            $customer->setName($filters[self::PARAMETER_NAME]);
        }
        if(!empty($filters[self::PARAMETER_DESCRIPTION])){
            $customer->setDescription($filters[self::PARAMETER_DESCRIPTION]);
        }

        $customer->save();
    }



}

