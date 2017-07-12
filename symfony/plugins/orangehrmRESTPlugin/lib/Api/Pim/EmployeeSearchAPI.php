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
use Orangehrm\Rest\Api\Pim\Entity\Employee;
use Orangehrm\Rest\Http\Response;

class EmployeeSearchAPI extends EndPoint
{

    /**
     * Employee constants
     */
    const PARAMETER_NAME = "name";
    const PARAMETER_ID = "code";
    const PARAMETER_JOB_TITLE = "jobTitle";
    const PARAMETER_STATUS = "status";
    const PARAMETER_UNIT = "unit";
    const PARAMETER_SUPERVISOR = "supervisor";
    const PARAMETER_LIMIT = 'limit';
    const PARAMETER_GENDER = 'gender';
    const PARAMETER_INCLUDE = 'include';
    const PARAMETER_DOB = 'dob';
    const PARAMETER_OFFSET = 'page';// in employee search it is named as offset

    const WITHOUT_TERMINATED = 1;
    const WITH_TERMINATED = 2;
    const ONLY_TERMINATED = 3;

    /**
     * Relation parameters
     */
    const PAGE = '/employee/search?page=';

    /**
     * @var EmployeeService
     */
    protected $employeeService = null;

    protected $jobTitleService;

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
     * Get employee list
     *
     * @return Response
     * @throws RecordNotFoundException
     */
    public function getEmployeeList()
    {
        $employeeList = array();
        $relationsArray = array();

        $parameterHolder = $this->buildSearchParamHolder();

        if (empty($parameterHolder)) {
            $employeeList = $this->getEmployeeService()->getEmployeeList();
        } else {

            $employeeList = $this->getEmployeeService()->searchEmployees($parameterHolder);

            if (empty($employeeList[0])) {
                throw new RecordNotFoundException("Employee Not Found");
            }

            if (!empty($parameterHolder->getLimit())) {
                  $relationsArray = $this->getRelations($relationsArray, $parameterHolder);
            }

        }


        return new Response($this->buildEmployeeData($employeeList), $relationsArray);

    }

    /**
     * build search parameters
     *
     * @return \EmployeeSearchParameterHolder|null
     */
    protected function buildSearchParamHolder()
    {
        $filters = array();
        $parameterHolder = new \EmployeeSearchParameterHolder();
        $searchLimit = null;
        $searchOffset = null;

        if (!empty($this->getRequestParams()->getQueryParam(self::PARAMETER_NAME))) {
            $filters['employee_name'] = $this->getRequestParams()->getQueryParam(self::PARAMETER_NAME);
        }

        if (!empty($this->getRequestParams()->getQueryParam(self::PARAMETER_ID))) {
            $filters['id'] = $this->getRequestParams()->getQueryParam(self::PARAMETER_ID);
        }
        if (!empty($this->getRequestParams()->getQueryParam(self::PARAMETER_INCLUDE))) {
            $filters['termination'] = $this->getIncludeId($this->getRequestParams()->getQueryParam(self::PARAMETER_INCLUDE));
        }
        if (!empty($this->getRequestParams()->getQueryParam(self::PARAMETER_JOB_TITLE))) {
            $filters['job_title'] = $this->getTitleId($this->getRequestParams()->getQueryParam(self::PARAMETER_JOB_TITLE));
        }
        if (!empty($this->getRequestParams()->getQueryParam(self::PARAMETER_STATUS))) {
            $filters['employee_status'] = $this->getStatusId($this->getRequestParams()->getQueryParam(self::PARAMETER_STATUS));
        }
        if (!empty($this->getRequestParams()->getQueryParam(self::PARAMETER_SUPERVISOR))) {
            $filters['supervisor_name'] = $this->getRequestParams()->getQueryParam(self::PARAMETER_SUPERVISOR);
        }
        if (!empty($this->getRequestParams()->getQueryParam(self::PARAMETER_UNIT))) {
            $filters['sub_unit'] = $this->getSubUnitId($this->getRequestParams()->getQueryParam(self::PARAMETER_UNIT));
        }
        if (!empty($this->getRequestParams()->getQueryParam(self::PARAMETER_DOB))) {
            $filters['dob'] = date("Y-m-d",
                strtotime($this->getRequestParams()->getQueryParam(self::PARAMETER_DOB)));  // "1989-09-7"
        }
        if (!empty($this->getRequestParams()->getQueryParam(self::PARAMETER_GENDER))) {
            $genderString = $this->getRequestParams()->getQueryParam(self::PARAMETER_GENDER);

            if ($genderString == 'male') {
                $filters['gender'] = 1;
            } else {
                if ($genderString == 'female') {
                    $filters['gender'] = 2;
                }
            }
        }
        if (is_numeric($this->getRequestParams()->getQueryParam(self::PARAMETER_LIMIT))) {
            $searchLimit = $this->getRequestParams()->getQueryParam(self::PARAMETER_LIMIT);
        }
        if (is_numeric($this->getRequestParams()->getQueryParam(self::PARAMETER_OFFSET))) {
            $searchOffset = $this->getRequestParams()->getQueryParam(self::PARAMETER_OFFSET);
        }
        if (empty($filters)) {
            return null;
        }

        $parameterHolder->setFilters($filters);
        $parameterHolder->setLimit($searchLimit);
        $parameterHolder->setOffset($searchOffset);
        $parameterHolder->setReturnType(\EmployeeSearchParameterHolder::RETURN_TYPE_OBJECT);

        return $parameterHolder;
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

    /**
     * @param $employeeService
     */
    public function setEmployeeService($employeeService)
    {
        $this->employeeService = $employeeService;
    }

    /**
     * Build Employee
     *
     * @param $employeeList
     * @return array
     */
    private function buildEmployeeData($employeeList)
    {
        $data = array();
        foreach ($employeeList as $employee) {
            $emp = new Employee($employee->getFirstName(), $employee->getMiddleName(), $employee->getLastName(),
                $employee->getEmployeeId());

            $emp->buildEmployee($employee);

            $data[] = $emp->toArray();
        }
        return $data;
    }

    /**
     * Get Search Relations
     *
     * @param $relationsArray
     * @param $parameterHolder
     * @return mixed
     */
    private function getRelations($relationsArray, $parameterHolder)
    {
        $count = $this->getEmployeeService()->getSearchEmployeeCount($parameterHolder->getFilters());
        $limit = $parameterHolder->getLimit();
        $offset = $parameterHolder->getOffset();
        $pages = ($count / $limit) - 1;
        $url = $this->getRequestParams()->getRequestUri();

        if (empty($offset) || $offset == 0) {
            $relationsArray['next'] = self::PAGE . '1';
            $relationsArray['previous'] = '';
        } else {
            if ($offset > 0) {

                if ($offset < $pages) {
                    $relationsArray['next'] = self::PAGE . ($offset + 1);
                    $relationsArray['previous'] = self::PAGE . ($offset - 1);
                } elseif ($offset >= $pages) {

                    $relationsArray['next'] = '';
                    $relationsArray['previous'] = self::PAGE . ($offset - 1);
                }

            }
        }

        return $relationsArray;
    }

    public function getSearchParamValidation()
    {
        return array(
            self::PARAMETER_NAME => array('StringType' => true),
            self::PARAMETER_DOB => array('Date' => array('Y-m-d')),

        );
    }

    protected function getStatusId($status)
    {
        $empStatusService = new \EmploymentStatusService();

        $statuses = $empStatusService->getEmploymentStatusList();

        foreach ($statuses as $statusObj) {
            if ($statusObj->getId() === $status) {
                return $statusObj->getId();
            }
        }
        throw new InvalidParamException('Invalid Status');
    }

    protected function getSubUnitId($subUnit)
    {
        $companyStructureService = new \CompanyStructureService();
        $treeObject = $companyStructureService->getSubunitTreeObject();

        $tree = $treeObject->fetchTree();

        foreach ($tree as $node) {
            if ($node->getId() === $subUnit) {
                return $node->getId();
            }
        }
        throw new InvalidParamException('Invalid Subunit');
    }

    protected function getTitleId($titleId)
    {
        $jobTitleList = $this->getJobTitleService()->getJobTitleList();
        foreach ($jobTitleList as $title) {

            if ($title->getId() == $titleId) {
                return $title->getId();

            }
        }
        throw new InvalidParamException('Invalid Title');
    }

    protected function getIncludeId($include)
    {
        if ('TERMINATED_ONLY' == $include) {
            return self::ONLY_TERMINATED;
        }
        if ('WITHOUT_TERMINATED' == $include) {
            return self::WITHOUT_TERMINATED;
        }
        if ('WITH_TERMINATED' == $include) {
            return self::WITH_TERMINATED;
        } else {
            throw new InvalidParamException('Invalid Include Value');
        }
    }


}
