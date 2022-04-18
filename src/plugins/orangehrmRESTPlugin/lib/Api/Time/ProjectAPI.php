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
use Orangehrm\Rest\Api\Exception\InvalidParamException;
use Orangehrm\Rest\Api\Exception\RecordNotFoundException;
use Orangehrm\Rest\Api\Exception\BadRequestException;
use Orangehrm\Rest\Api\Time\Entity\Project;

use Orangehrm\Rest\Http\Response;

class ProjectAPI extends EndPoint
{

    const PARAMETER_CUSTOMER_ID = "customerId";
    const PARAMETER_NAME = "name";
    const PARAMETER_DESCRIPTION = "description";
    const PARAMETER_ADMIN_IDS = "adminIds";
    const PARAMETER_PROJECT_ID = "projectId";

    private $projectService;
    private $customerService;
    private $employeeService;

    /**
     *
     * @return ProjectService
     */
    public function getCustomerService()
    {
        if (is_null($this->customerService)) {
            $this->customerService = new \CustomerService();
        }
        return $this->customerService;
    }

    /**
     *
     * @return ProjectService
     */
    public function getProjectService()
    {
        if (is_null($this->projectService)) {
            $this->projectService = new \ProjectService();
        }
        return $this->projectService;
    }

    public function setProjectService(\ProjectService $projectService)
    {
       $this->projectService = $projectService;
    }

    /**
     * Get Employee service
     *
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
     * Get projects
     *
     * @return Response
     * @throws RecordNotFoundException
     */
    public function getProjects()
    {
        $projects = $this->getProjectService()->getAllProjects($activeOnly = true);

        foreach ($projects as $project) {
            $projectEntity = new Project();
            $projectEntity->build($project);
            $responseArray[] = $projectEntity->toArray();
        }
        if (count($responseArray) > 0) {
            return new Response($responseArray, array());
        } else {
            throw new RecordNotFoundException('No Projects Found');
        }

    }

    /**
     * Save project
     *
     * @return Response
     */
    public function saveProject()
    {
        $filters = $this->filterParameters();
        $this->validateParameters($filters);
        $project = $this->buildProjectAndSave(null, $filters);

        if (!empty($filters['admins'])) {
            $this->saveProjectAdmins($filters['admins'], $project->getProjectId());
        }

        return new Response(array('success' => 'Successfully Saved','projectId'=>$project->getProjectId()));
    }

    /**
     * Update project
     *
     * @return Response
     * @throws InvalidParamException
     * @throws RecordNotFoundException
     */
    function updateProject()
    {
        $filters = $this->filterParameters();
        $projectId = $filters[self::PARAMETER_PROJECT_ID];
        if (empty($projectId)) {
            throw new InvalidParamException("Project Id Needed");
        }
        $project = $this->getProjectService()->getProjectById($projectId);

        if ($project instanceof \Project && $project->is_deleted != 1) {

            $this->validateParameters($filters,true,$project);
            $savedProject = $this->buildProjectAndSave($project, $filters);
            $projectAdmins = $filters['admins'];
            $existingProjectAdmins = $project->getProjectAdmin();
            $idList = array();

            if(is_array($projectAdmins)){

                if ($existingProjectAdmins[0]->getEmpNumber() != "") {
                    foreach ($existingProjectAdmins as $existingProjectAdmin) {
                        $id = $existingProjectAdmin->getEmpNumber();
                        if (!in_array($id, $projectAdmins)) {
                            $existingProjectAdmin->delete();
                        } else {
                            $idList[] = $id;
                        }
                    }
                }

                $this->resultArray = array();

                $adminList = array_diff($projectAdmins, $idList);
                $newList = array();
                foreach ($adminList as $admin) {
                    $newList[] = $admin;
                }
                $projectAdmins = $newList;
                $this->saveProjectAdmins($projectAdmins, $savedProject->getProjectId());
            }

            return new Response(array('success' => 'Successfully Updated'));
        } else {
            throw new RecordNotFoundException("Project Not Found");
        }

    }

    /**
     * Delete Project
     *
     * @return Response
     * @throws InvalidParamException
     * @throws RecordNotFoundException
     */
    public function deleteProject()
    {
        $filters = $this->filterDeleteParameters();
        $projectId = $filters[self::PARAMETER_PROJECT_ID];
        $project = $this->getProjectService()->getProjectById($projectId);
        if ($project instanceof \Project && $project->getIsDeleted() == 0) {
            $hasTimeSheets = $this->getProjectService()->hasProjectGotTimesheetItems($projectId);

            if (!$hasTimeSheets) {
                $this->getProjectService()->deleteProject($projectId);
                return new Response(array('success' => 'Successfully Deleted'));

            } else {
                throw new InvalidParamException("Not Allowed to Delete Project(s) Which Have Time Logged Against Them");
            }
        } else {
            throw new RecordNotFoundException("Project Not Found");
        }

    }

    /**
     * Build project and save project
     *
     * @param \Project $project
     * @param $filters
     */
    protected function buildProjectAndSave($project, $filters)
    {
        if ($project == null) {
            $project = new \Project();
        }

        $project->setCustomerId($filters[self::PARAMETER_CUSTOMER_ID]);
        $project->setName($filters[self::PARAMETER_NAME]);
        if(!empty($filters[self::PARAMETER_DESCRIPTION])){
            $project->setDescription($filters[self::PARAMETER_DESCRIPTION]);
        }
        $project->save();
        return $project;

    }

    /**
     * Filter Parameters
     *
     * @return array
     * @throws BadRequestException
     * @throws InvalidParamException
     */
    protected function filterParameters()
    {
        $filters[] = array();

        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_CUSTOMER_ID))) {
            $filters[self::PARAMETER_CUSTOMER_ID] = $this->getRequestParams()->getPostParam(self::PARAMETER_CUSTOMER_ID);
        } else {
            throw new InvalidParamException('Customer Id Needed');
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_NAME))) {
            $filters[self::PARAMETER_NAME] = $this->getRequestParams()->getPostParam(self::PARAMETER_NAME);
        } else {
            throw new InvalidParamException('Project Name Needed');
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_DESCRIPTION))) {
            $filters[self::PARAMETER_DESCRIPTION] = $this->getRequestParams()->getPostParam(self::PARAMETER_DESCRIPTION);
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_PROJECT_ID))) {
            $filters[self::PARAMETER_PROJECT_ID] = $this->getRequestParams()->getPostParam(self::PARAMETER_PROJECT_ID);
        }
        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_ADMIN_IDS))) {
            $filters[self::PARAMETER_ADMIN_IDS] = $this->getRequestParams()->getPostParam(self::PARAMETER_ADMIN_IDS);
            $adminIdList = explode(",", $filters[self::PARAMETER_ADMIN_IDS]);
            if (count($adminIdList) > 5 | !$this->no_dupes($adminIdList)) {
                throw new BadRequestException('Only 5 Admins Can Be Added And No Duplicates Can Be Contained');
            } else {
                foreach ($adminIdList as $adminId) {
                    if (is_numeric($adminId)) {
                        $this->validateEmployee($adminId);
                    } else {
                        throw new InvalidParamException('Invalid Admin Id : ' . $adminId);
                    }
                }
                $filters['admins'] = $adminIdList;
            }
        }

        return $filters;
    }

    /**
     * Filter delete parameters
     *
     * @return array
     * @throws InvalidParamException
     */
    protected function filterDeleteParameters()
    {
        $filters[] = array();

        if (!empty($this->getRequestParams()->getPostParam(self::PARAMETER_PROJECT_ID))) {
            $filters[self::PARAMETER_PROJECT_ID] = $this->getRequestParams()->getPostParam(self::PARAMETER_PROJECT_ID);
        } else {
            throw new InvalidParamException("Project ID Needed");
        }

        return $filters;
    }

    public function getPostValidationRules()
    {
        return array(
            self::PARAMETER_CUSTOMER_ID => array("IntVal" => true, 'NotEmpty' => true, 'Length' => array(0, 5)),
            self::PARAMETER_NAME => array('StringType' => true, 'NotEmpty' => true, 'Length' => array(1, 52)),
            self::PARAMETER_DESCRIPTION => array('Length' => array(0, 256)),
        );
    }

    public function getPutValidationRules()
    {
        return array(
            self::PARAMETER_PROJECT_ID => array("IntVal" => true, 'NotEmpty' => true, 'Length' => array(0, 5)),
            self::PARAMETER_CUSTOMER_ID => array("IntVal" => true, 'NotEmpty' => true, 'Length' => array(0, 5)),
            self::PARAMETER_NAME => array('StringType' => true, 'NotEmpty' => true, 'Length' => array(1, 52)),
            self::PARAMETER_DESCRIPTION => array('Length' => array(0, 256)),
        );
    }

    public function getDeleteValidationRules()
    {
        return array(
            self::PARAMETER_PROJECT_ID => array("IntVal" => true, 'NotEmpty' => true, 'Length' => array(0, 5))

        );
    }

    /**
     * Validate parameters
     *
     * @param $filters
     * @throws InvalidParamException
     */
    protected function validateParameters($filters,$isUpdate = false,$project = null)
    {
        $customer = $this->getCustomerService()->getCustomerById($filters[self::PARAMETER_CUSTOMER_ID]);

        if ($customer instanceof \Customer && $customer->is_deleted != 1) {
            if(!$isUpdate){
                $projectName = $this->getProjectService()->getProjectByName($filters[self::PARAMETER_NAME],$filters[self::PARAMETER_CUSTOMER_ID]);
                if ($projectName > 0) {
                    throw new InvalidParamException('Project Name Exists');
                }
            }else {
                if($project->getName() != $filters[self::PARAMETER_NAME]){
                    $projectName = $this->getProjectService()->getProjectByName($filters[self::PARAMETER_NAME],$filters[self::PARAMETER_CUSTOMER_ID]);
                    if ($projectName > 0) {
                        throw new InvalidParamException('Project Name Exists');
                    }
                }
            }

        } else {
            throw new InvalidParamException("Customer Not Found");
        }

    }

    /**
     * Validate employee
     *
     * @param $empId
     * @throws RecordNotFoundException
     */
    protected function validateEmployee($empId)
    {
        $employee = $this->getEmployeeService()->getEmployee($empId);
        if (!$employee instanceof \Employee) {
            throw new RecordNotFoundException("Admin Not Found :" . $empId);
        }
    }
    protected function validateProject($projectId){

        $project = $this->projectService()->getProjectById($projectId);
        if (!$project instanceof \Project && $project->isDeleted() != 1) {
            throw new RecordNotFoundException("Project Not Found");
        }

    }

    /**
     * Save project admins
     *
     * @param $projectAdmins
     * @param $projectId
     */
    protected function saveProjectAdmins($projectAdmins, $projectId)
    {

        if ($projectAdmins[0] != null) {
            for ($i = 0; $i < count($projectAdmins); $i++) {
                $projectAdmin = new \ProjectAdmin();
                $projectAdmin->setProjectId($projectId);
                $projectAdmin->setEmpNumber($projectAdmins[$i]);
                $projectAdmin->save();
            }
        }
    }

    /**
     * Checking admins list for duplicates
     *
     * @param array $input_array
     * @return bool
     */
    function no_dupes(array $input_array)
    {
        return count($input_array) === count(array_flip($input_array));
    }

}

