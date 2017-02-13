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
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Api\Exception\BadRequestException;
use Orangehrm\Rest\Api\Pim\Entity\EmployeeJobDetail;
use Orangehrm\Rest\Http\Response;

class EmployeeJobDetailAPI extends EndPoint
{

    const PARAMETER_ID = "id";
    const PARAMETER_TITILE = "title";
    const PARAMETER_CATEGORY = "category";
    const PARAMETER_JOINED_DATE = "joinedDate";
    const PARAMETER_START_DATE = "startDate";
    const PARAMETER_END_DATE = "endDate";

    protected $employeeService;
    protected $jobTitleService;
    protected $categoryService;
    protected $filters;

    /**
     * @return mixed
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param mixed $filters
     */
    public function setFilters($filters)
    {
        $this->filters = $filters;
    }


    /**
     * @return \EmployeeService|null
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
     * @return mixed
     */
    public function getJobTitleService()
    {
        if ($this->jobTitleService != null) {
            return $this->jobTitleService;
        } else {
            return new \JobTitleService();
        }
    }

    /**
     * @param mixed $jobTitleService
     */
    public function setJobTitleService($jobTitleService)
    {
        $this->jobTitleService = $jobTitleService;
    }

    /**
     * @return mixed
     */
    public function getCategoryService()
    {
        if ($this->categoryService != null) {
            return $this->categoryService;
        } else {
            return new \JobCategoryService();
        }
    }

    /**
     * @param mixed $categoryService
     */
    public function setCategoryService($categoryService)
    {
        $this->categoryService = $categoryService;
    }


    /**
     * Get employee job details
     *
     * @return Response
     * @throws InvalidParamException
     * @throws RecordNotFoundException
     */
    public function getEmployeeJobDetails()
    {

        $responseArray = null;
        $empId = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);

        if (!is_numeric($empId)) {
            throw new InvalidParamException("Invalid Parameter");

        }

        $employee = $this->getEmployeeService()->getEmployee($empId);

        if (empty($employee)) {
            throw new RecordNotFoundException("Employee Not Found");

        }

        $employeeJobDetails = new EmployeeJobDetail();
        $employeeJobDetails->build($employee);
        return new Response($employeeJobDetails->toArray(), array());
    }

    /**
     * Save employee job details
     *
     * @return Response
     */
    public function saveEmployeeJobDetails()
    {

        $relationsArray = array();
        $returned = null;
        $this->filters = $this->filterParameters();
        if ($this->validateInputs($filters)) {

            $empId = $this->getRequestParams()->getUrlParam(self::PARAMETER_ID);
            $employee = $this->getEmployeeService()->getEmployee($empId);
            $this->buildEmployeeJobDetails($employee, $filters);
            $returnedEmployee = $this->getEmployeeService()->saveEmployee($employee);

            if ($returnedEmployee instanceof \Employee) {
                return new Response(array('success' => 'successfully saved'), $relationsArray);
            } else {
                throw new BadRequestException("saving failed");
            }
        } else {
            throw new BadRequestException("saving failed");
        }


    }

    /**
     * Build employee job details
     *
     * @param \Employee $employee
     *
     */
    private function buildEmployeeJobDetails(\Employee $employee, $filters)
    {
        $empContract = new \EmpContract();
        $empContract->emp_number = $employee->empNumber;
        $empContract->contract_id = 1;
        $employee->job_title_code = $filters[self::PARAMETER_TITILE];
        $employee->eeo_cat_code = $filters[self::PARAMETER_CATEGORY];
        $employee->setJoinedDate($filters[self::PARAMETER_JOINED_DATE]);

        $empContract->start_date = $filters[self::PARAMETER_START_DATE];;
        $empContract->end_date = $filters[self::PARAMETER_END_DATE];

        $employee->contracts[0] = $empContract;
    }

    /**
     * Filter post parameters to validate
     *
     * @return array
     */
    protected function filterParameters()
    {

        $filters[] = array();

        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_CATEGORY))) {
            $filters[self::PARAMETER_CATEGORY] = ($this->getRequestParams()->getPostParam(self::PARAMETER_CATEGORY));
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_JOINED_DATE))) {
            $filters[self::PARAMETER_JOINED_DATE] = ($this->getRequestParams()->getPostParam(self::PARAMETER_JOINED_DATE));
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_START_DATE))) {
            $filters[self::PARAMETER_START_DATE] = ($this->getRequestParams()->getPostParam(self::PARAMETER_START_DATE));
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_END_DATE))) {
            $filters[self::PARAMETER_END_DATE] = ($this->getRequestParams()->getPostParam(self::PARAMETER_END_DATE));
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_TITILE))) {
            $filters[self::PARAMETER_TITILE] = ($this->getRequestParams()->getPostParam(self::PARAMETER_TITILE));
        }
        return $filters;

    }

    /**
     * validate input parameters
     *
     * @param $filters
     * @return bool
     */
    protected function validateInputs($filters)
    {
        $valid = true;

        $format = "Y-m-d";
       // var_dump($valid);die();


        if (!$this->validateCategory($filters)){
            $valid = false;  var_dump("111");die();

        }
        if (!$this->validateTitle($filters)){
            $valid = false;  var_dump("222");die();

        }
        if (!date($format,
                strtotime($filters[self::PARAMETER_JOINED_DATE])) == date($filters[self::PARAMETER_JOINED_DATE])
        ) {
            $valid = false;  var_dump("333");die();
        }
        if (!date($format,
                strtotime($filters[self::PARAMETER_START_DATE])) == date($filters[self::PARAMETER_START_DATE])
        ) {
            $valid = false;  var_dump("444");die();
        }
        if (!date($format, strtotime($filters[self::PARAMETER_END_DATE])) == date($filters[self::PARAMETER_END_DATE])) {
            $valid = false;  var_dump("555");die();
        }

        return $valid;
    }

    public function validateTitle($filters)
    {
        $jobTitleList = $this->getJobTitleService()->getJobTitleList();
        foreach ($jobTitleList as $title) {

            if ($title->getJobTitleName() === $filters[self::PARAMETER_TITILE]) {
                $this->filters[self::PARAMETER_TITILE] = $title->getId();
                return true;
            }
        }
        return false;
    }

    public function validateCategory($filters)
    {
        $jobCategoryList = $this->getCategoryService()->getJobCategoryList();

        foreach ($jobCategoryList as $category) {

            if ($category->getName() === $filters[self::PARAMETER_CATEGORY]) {
                $this->$filters[self::PARAMETER_CATEGORY] = $category->getId();
                return true;
            }
        }
        return false;
    }
}
