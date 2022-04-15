<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AdminWebServiceHelper
 *
 * @author nirmal
 */
class CorporateDirectoryWebServiceHelper {

    protected $employeeDirectoryService;
    protected $employeeService;

    public function setEmployeeDirectoryService(EmployeeDirectoryService $employeeDirectoryService) {
        $this->employeeDirectoryService = $employeeDirectoryService;
    }

    public function getEmployeeDirectoryService() {
        if (!$this->employeeDirectoryService instanceof EmployeeDirectoryService) {
            $this->employeeDirectoryService = new EmployeeDirectoryService();
            $this->employeeDirectoryService->setEmployeeDirectoryDao(new EmployeeDirectoryDao());
        }
        return $this->employeeDirectoryService;
    }

    public function getEmployeeService() {
        if (is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
            $this->employeeService->setEmployeeDao(new EmployeeDao());
        }
        return $this->employeeService;
    }

    public function setEmployeeService(EmployeeService $employeeService) {
        $this->employeeService = $employeeService;
    }

    /**
     * Get Corporate directory employee details as array
     * @param type $includeTerminated
     * @return Array
     */
    public function getCorporateDirectoryEmployeeDetailsAsArray($includeTerminated = false) {
        $filters = array();
        if ($includeTerminated) {
            $filters['termination'] = EmployeeSearchForm::WITH_TERMINATED;
        }

        $count = $this->getEmployeeDirectoryService()->getSearchEmployeeCount($filters);

        $parameterHolder = new EmployeeSearchParameterHolder();
        $parameterHolder->setLimit($count);
        $parameterHolder->setFilters($filters);

        $employees = $this->getEmployeeDirectoryService()->searchEmployees($parameterHolder);
        $employeeList = array();
        foreach ($employees as $employee) {
            $employeeDetails = array(
                'emp_number' => $employee->getEmpNumber(),
                'employee_id' => $employee->getEmployeeId(),
                'emp_firstname' => $employee->getFirstName(),
                'emp_lastname' => $employee->getLastName(),
                'home_telephone' => $employee->getEmpHmTelephone(),
                'work_telephone' => $employee->getEmpWorkTelephone(),
                'mobile' => $employee->getEmpMobile(),
                'work_email' => $employee->getEmpWorkEmail(),
                'other_email' => $employee->getEmpOthEmail(),
                'profile_image_url' => url_for('directory/viewDirectoryPhoto?empNumber=' . $employee->getEmpNumber()),
                'terminated' => $employee->getTerminationId()
            );

            $empPicture = $this->getEmployeeService()->getEmployeePicture($employee->getEmpNumber());
            if ($empPicture instanceof EmpPicture) {
                $base64Picture = base64_encode($empPicture->getPicture());
                $employeeDetails['profile_picture'] = array('image_string' => $base64Picture,
                    'image_type' => $empPicture->getFileType());
            } else {
                $employeeDetails['profile_picture'] = null;
            }

            if ($employee->getLocations()->getFirst() instanceof Location) {
                $employeeDetails['location_id'] = $employee->getLocations()->getFirst()->getId();
            } else {
                $employeeDetails['location_id'] = null;
            }

            if ($employee->getJobTitleCode() instanceof JobTitle) {
                $employeeDetails['job_title_id'] = $employee->getJobTitleCode()->getId();
            } else {
                $employeeDetails['job_title_id'] = null;
            }

            if ($employee->getSubDivision() instanceof Subunit) {
                $employeeDetails['subunit_id'] = $employee->getSubDivision()->getId();
            } else {
                $employeeDetails['subunit_id'] = null;
            }

            $employeeList[] = $employeeDetails;
        }
        return $employeeList;
    }

}
