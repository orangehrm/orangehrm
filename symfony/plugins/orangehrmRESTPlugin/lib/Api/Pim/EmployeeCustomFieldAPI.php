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

namespace Orangehrm\Rest\Api\Pim;

use Orangehrm\Rest\Api\EndPoint;
use Orangehrm\Rest\Api\Exception\BadRequestException;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Api\Pim\Entity\EmployeeCustomField;
use Orangehrm\Rest\Http\Response;

class EmployeeCustomFieldAPI extends EndPoint
{
    /**
     * Employee event constants
     */
    const PARAMETER_ID = "id";
    const PARAMETER_VALUE = 'value';
    const PARAMETER_FIELD_ID = 'fieldId';

    protected $customFieldService;
    protected $employeeService;

    /**
     * Get CustomFieldsService
     * @returns \CustomFieldsService
     */
    public function getCustomFieldService()
    {
        if (is_null($this->customFieldService)) {
            $this->customFieldService = new \CustomFieldConfigurationService();
            $this->customFieldService->setCustomFieldsDao(new \CustomFieldConfigurationDao());
        }
        return $this->customFieldService;
    }

    /**
     * Set CustomFieldService
     */
    public function setCustomFieldService(\CustomFieldConfigurationService $customFieldsService)
    {
        $this->customFieldService = $customFieldsService;
    }

    /**
     * @return \EmployeeService
     */
    protected function getEmployeeService()
    {
        if ($this->employeeService != null) {
            return $this->employeeService;
        } else {
            return new \EmployeeService();
        }
    }

    public function setEmployeeService($employeeService)
    {
        $this->employeeService = $employeeService;
    }

    /**
     * Get employee custom field
     *
     * @return Response
     * @throws RecordNotFoundException
     */
    public function getCustomFields()
    {
        $customFieldList = $this->getCustomFieldService()->getCustomFieldList(null, 'name', 'ASC');
        $empId = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);
        $employee = $this->getEmployee($empId);
        $response = null;

        foreach ($customFieldList as $field) {

            $customField = new EmployeeCustomField();
            $customField->build($field, $employee);
            $response[] = $customField->toArray();

        }
        if (count($response) > 0) {
            return new Response($response);

        } else {
            throw new RecordNotFoundException('No Custom Fields Found');
        }


    }

    /**
     * Save Employee custom field
     *
     * @return Response
     * @throws BadRequestException
     * @throws InvalidParamException
     */
    public function saveEmployeeCustomField()
    {
        $filters = $this->getFilterParameters();
        $customField = $this->getCustomField($filters[self::PARAMETER_FIELD_ID]);
        $value = $filters[self::PARAMETER_VALUE];

        if (empty($value)) {
            throw new InvalidParamException('Field Value Must Not Be Empty');
        }

        $employee = $this->getEmployee($filters[self::PARAMETER_ID]);

        if ($customField->getType() == 1) {

            if ($this->checkFieldValueForDropDown($customField->getExtraData(), $value)) {
                $this->setEmployeeCustomField($employee, $filters[self::PARAMETER_FIELD_ID],$value);
            } else {

                throw new InvalidParamException('Custom Field Value Is Not Defined');
            }

        } else {

            $this->setEmployeeCustomField($employee, $filters[self::PARAMETER_FIELD_ID],
                $value);
        }
        $result = $this->getEmployeeService()->saveEmployee($employee);
        if ($result instanceof \Employee) {
            return new Response(array('success' => 'Successfully Saved'));
        } else {
            throw new BadRequestException();
        }
    }

    /**
     * Delete employee custom field
     *
     * @return Response
     * @throws BadRequestException
     */
    public function deleteEmployeeCustomField(){

        $filters = $this->getFilterParameters();
        $customField = $this->getCustomField($filters[self::PARAMETER_FIELD_ID]);
        $employee = $this->getEmployee($filters[self::PARAMETER_ID]);

        $this->setEmployeeCustomField($employee, $customField->getId(),null);

        $result = $this->getEmployeeService()->saveEmployee($employee);
        if ($result instanceof \Employee) {
            return new Response(array('success' => 'Successfully Deleted'));
        } else {
            throw new BadRequestException();
        }
    }
    /**
     * Filter Post parameters to validate
     *
     * @return array
     *
     */
    protected function getFilterParameters()
    {
        $filters[] = array();

        $filters[self::PARAMETER_VALUE] = $this->getPostParam(self::PARAMETER_VALUE, $this->getRequestParams());
        $filters[self::PARAMETER_FIELD_ID] = $this->getPostParam(self::PARAMETER_FIELD_ID, $this->getRequestParams());
        $filters[self::PARAMETER_ID] = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);

        return $filters;
    }

    /**
     * Getting post parameters
     *
     * @param $parameterName
     * @param $requestParams
     * @return param
     */
    protected function getPostParam($parameterName, $requestParams)
    {
        if (!empty($requestParams->getPostParam($parameterName))) {
            return $requestParams->getPostParam($parameterName);
        }
        return null;
    }

    protected function setEmployeeCustomField(\Employee $employee, $field, $value)
    {
        $function = "setCustom" . $field;
        call_user_func(array($employee, $function), $value);
    }

    /**
     * Validating whether a value is existing in drop down values
     *
     * @param $extraData
     * @param $value
     * @return bool
     */
    protected function checkFieldValueForDropDown($extraData, $value)
    {
        $values = explode(',', $extraData);
        if (in_array($value, $values)) {
            return true;
        }
        return false;
    }

    /**
     * Get and validate employee
     *
     * @param $empId
     * @return \Employee
     * @throws RecordNotFoundException
     */
    protected function getEmployee($empId)
    {
        $employee = $this->getEmployeeService()->getEmployee($empId);

        if (!$employee instanceof \Employee) {
            throw new RecordNotFoundException("Employee Not Found");
        }
        return $employee;
    }

    /**
     * Get and validate custom field
     *
     * @param $field
     * @return mixed
     * @throws RecordNotFoundException
     */
    protected function getCustomField($field)
    {
        $field = $this->getCustomFieldService()->getCustomField($field);
        if (!$field instanceof \CustomField) {
            throw new RecordNotFoundException("Custom Field Not Found");
        }
        return $field;
    }

}
