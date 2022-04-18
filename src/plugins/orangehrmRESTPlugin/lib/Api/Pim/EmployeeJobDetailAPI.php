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
    const PARAMETER_TITLE = "title";
    const PARAMETER_CATEGORY = "category";
    const PARAMETER_JOINED_DATE = "joinedDate";
    const PARAMETER_START_DATE = "startDate";
    const PARAMETER_END_DATE = "endDate";
    const PARAMETER_STATUS = "status";
    const PARAMETER_SUBUNIT = "subunit";
    const PARAMETER_LOCATION = "location";

    protected $employeeService;
    protected $jobTitleService;
    protected $categoryService;
    protected $filters;
    private $employeeEventService;

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
     * Get employee event service
     *
     * @return \EmployeeEventService
     */
    private function getEmployeeEventService() {

        if(is_null($this->employeeEventService)) {
            $this->employeeEventService = new \EmployeeEventService();
        }

        return $this->employeeEventService;
    }

    /**
     * @param mixed $employeeEventService
     */
    public function setEmployeeEventService($employeeEventService)
    {
        $this->employeeEventService = $employeeEventService;
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
     * @throws BadRequestException
     * @throws RecordNotFoundException
     */
    public function saveEmployeeJobDetails()
    {
        $relationsArray = array();
        $returned = null;
        $this->filters = $this->filterParameters();
        if (count($this->filters) > 1 && $this->validateInputs($this->filters)) {

            $empId = $this->filters[self::PARAMETER_ID];
            $employee = $this->getEmployeeService()->getEmployee($empId);
            if(empty($employee)){
                throw  new RecordNotFoundException('Employee Not Found');
            }
            $this->buildEmployeeJobDetails($employee, $this->filters);
            $returnedEmployee = $this->getEmployeeService()->saveEmployee($employee);

            if ($returnedEmployee instanceof \Employee) {
                $this->getEmployeeEventService()->saveEvent($returnedEmployee->getEmpNumber(),\PluginEmployeeEvent::EVENT_TYPE_JOB_DETAIL,\PluginEmployeeEvent::EVENT_UPDATE,'Updating Employee Job Details','API');
                return new Response(array('success' => 'Successfully Saved'), $relationsArray);
            } else {
                throw new BadRequestException("Saving Failed");
            }
        } else {
            throw new BadRequestException("Saving Failed");
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

        if (!empty($filters[self::PARAMETER_TITLE])) {
            $employee->job_title_code = $filters[self::PARAMETER_TITLE];
        }
        if (!empty($filters[self::PARAMETER_CATEGORY])) {
            $employee->eeo_cat_code = $filters[self::PARAMETER_CATEGORY];
        }
        if (!empty($filters[self::PARAMETER_JOINED_DATE])) {
            $employee->setJoinedDate($filters[self::PARAMETER_JOINED_DATE]);
        }
        if (!empty($filters[self::PARAMETER_START_DATE])) {
            $empContract->start_date = $filters[self::PARAMETER_START_DATE];
        }
        if (!empty($filters[self::PARAMETER_END_DATE])) {
            $empContract->end_date = $filters[self::PARAMETER_END_DATE];
        }
        if (!empty($filters[self::PARAMETER_LOCATION])) {
            $foundLocation = false;
            foreach ($employee->getLocations() as $empLocation) {
                if ($filters[self::PARAMETER_LOCATION] == $empLocation->id) {
                    $foundLocation = true;
                } else {
                    $employee->unlink('locations', $empLocation->id);
                }
            }
            if (!$foundLocation) {
                $employee->link('locations', $filters[self::PARAMETER_LOCATION]);
            }

        }
        if (!empty($filters[self::PARAMETER_STATUS])) {
            $employee->emp_status = $filters[self::PARAMETER_STATUS];
        }
        if (!empty($filters[self::PARAMETER_SUBUNIT])) {
            $employee->work_station = $filters[self::PARAMETER_SUBUNIT];
        }

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

        $filters[self::PARAMETER_CATEGORY] = ($this->getRequestParams()->getPostParam(self::PARAMETER_CATEGORY));
        $filters[self::PARAMETER_JOINED_DATE] = ($this->getRequestParams()->getPostParam(self::PARAMETER_JOINED_DATE));
        $filters[self::PARAMETER_START_DATE] = ($this->getRequestParams()->getPostParam(self::PARAMETER_START_DATE));
        $filters[self::PARAMETER_END_DATE] = ($this->getRequestParams()->getPostParam(self::PARAMETER_END_DATE));
        $filters[self::PARAMETER_TITLE] = ($this->getRequestParams()->getPostParam(self::PARAMETER_TITLE));
        $filters[self::PARAMETER_ID] = ($this->getRequestParams()->getUrlParam(self::PARAMETER_ID));
        $filters[self::PARAMETER_STATUS] = ($this->getRequestParams()->getPostParam(self::PARAMETER_STATUS));
        $filters[self::PARAMETER_LOCATION] = ($this->getRequestParams()->getPostParam(self::PARAMETER_LOCATION));
        $filters[self::PARAMETER_SUBUNIT] = ($this->getRequestParams()->getPostParam(self::PARAMETER_SUBUNIT));

        return $filters;

    }


    public function getValidationRules()
    {
        return array(
            self::PARAMETER_JOINED_DATE => array('Date' => array('Y-m-d')),
            self::PARAMETER_START_DATE => array('Date' => array('Y-m-d')),
            self::PARAMETER_END_DATE => array('Date' => array('Y-m-d')),
        );
    }

    /**
     * validate input parameters
     *
     * @param $filters
     * @return bool
     * @throws InvalidParamException
     */
    protected function validateInputs($filters)
    {
        $valid = true;

        $format = "Y-m-d";


        if (!empty($filters[self::PARAMETER_CATEGORY]) && !$this->validateCategory()) {
            $valid = false;
        }
        if (!empty($filters[self::PARAMETER_TITLE]) && !$this->validateTitle()) {
            $valid = false;
        }
        if (!empty($filters[self::PARAMETER_STATUS]) && !$this->validateEmployeeStatus()) {
            $valid = false;
        }
        if (!empty($filters[self::PARAMETER_LOCATION]) && !$this->validateEmployeeLocation()) {
            $valid = false;
        }
        if (!empty($filters[self::PARAMETER_SUBUNIT]) && !$this->validateSubunit()) {
            $valid = false;
        }
        if (!empty($filters[self::PARAMETER_START_DATE]) && !empty($filters[self::PARAMETER_END_DATE])) {
            if ((strtotime($filters[self::PARAMETER_START_DATE])) > (strtotime($filters[self::PARAMETER_END_DATE]))) {
                throw new InvalidParamException('End Date Should Be After Start Date');
            }
        }
        return $valid;
    }

    public function validateTitle()
    {
        $jobTitleList = $this->getJobTitleService()->getJobTitleList();
        foreach ($jobTitleList as $title) {

            if ($title->getId() === $this->filters[self::PARAMETER_TITLE]) {
                return true;
            }
        }
        throw new InvalidParamException('No Valid Title Found');
    }

    public function validateCategory()
    {
        $jobCategoryList = $this->getCategoryService()->getJobCategoryList();

        foreach ($jobCategoryList as $category) {

            if ($category->getId() === $this->filters[self::PARAMETER_CATEGORY]) {
                return true;
            }
        }
        throw new InvalidParamException('No Valid Category Found');
    }

    public function validateEmployeeStatus()
    {
        $empStatusService = new \EmploymentStatusService();

        $statuses = $empStatusService->getEmploymentStatusList();

        foreach ($statuses as $status) {
            if ($status->getId() == $this->filters[self::PARAMETER_STATUS]) {
                return true;
            }
        }

        throw new InvalidParamException('No Valid Status Found');
    }

    public function validateEmployeeLocation()
    {
        $locationService = new \LocationService();
        $locations = $locationService->getLocationList();

        foreach ($locations as $location) {
            if ($location->getId() == $this->filters[self::PARAMETER_LOCATION]) {
                return true;
            }
        }

        throw new InvalidParamException('No Valid Location Found');
    }

    public function validateSubunit()
    {
        $companyStructureService = new \CompanyStructureService();
        $treeObject = $companyStructureService  ->getSubunitTreeObject();

        $tree = $treeObject->fetchTree();

        foreach ($tree as $node) {
            if ($node->getId() == $this->filters[self::PARAMETER_SUBUNIT]) {
                return true;
            }
        }
        throw new InvalidParamException('No Valid Subunit Found');
    }

}