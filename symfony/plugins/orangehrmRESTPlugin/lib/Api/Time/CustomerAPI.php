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
use Orangehrm\Rest\Api\Exception\BadRequestException;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Api\Pim\Entity\Employee;
use Orangehrm\Rest\Api\Pim\Entity\EmployeeDependent;
use Orangehrm\Rest\Http\Response;

class CustomerAPI extends EndPoint
{
    const PARAMETER_NAME = "name";
    const PARAMETER_DESCRIPTION = "description";

    private $customerService;

    /**
     *
     * @return ProjectService
     */
    public function getCustomerService() {
        if (is_null($this->customerService)) {
            $this->customerService = new \CustomerService();
        }
        return $this->customerService;
    }

    /**
     * get Customers
     *
     * @return Response
     */
    public function getCustomers()
    {
        $customers = $this->getCustomerService()->getCustomerList();
        foreach ($customers as $customer) {

            $responseArray[] = $customer->toArray();
        }
        if(count(responseArray) >0){
            return new Response($responseArray, array());
        } else {
            throw new RecordNotFoundException('No Customers Found');
        }

    }

    /**
     * Save Customer
     *
     * @return Response
     * @throws InvalidParamException
     */
    public function saveCustomer()
    {
        $filters = $this->filterParameters();
        $customerCount = $this->getCustomerService()->getCustomerByName($filters[self::PARAMETER_NAME]);

        if($customerCount == 0) {
            $customer = new \Customer();
            $customer->setName($filters[self::PARAMETER_NAME]);
            $customer->setDescription($filters[self::PARAMETER_DESCRIPTION]);
            $customer->save();
            return new Response(array('success' => 'Successfully Saved'));
        } else {
            throw new InvalidParamException('Customer Already Exists');
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
            throw new InvalidParamException('Name Value is Not Set');
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_DESCRIPTION))) {
            $filters[self::PARAMETER_DESCRIPTION] = $this->getRequestParams()->getPostParam(self::PARAMETER_DESCRIPTION);
        }

        return $filters;

    }

    public function getPostValidationRules()
    {
        return array(
            self::PARAMETER_NAME => array('StringType' => true, 'NotEmpty' => true,'Length' => array(1,50)),
            self::PARAMETER_DESCRIPTION => array('StringType' => true, 'NotEmpty' => true,'Length' => array(1,200)),
        );
    }

}


