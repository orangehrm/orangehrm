<?php

/*
  // OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
  // all the essential functionalities required for any enterprise.
  // Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com

  // OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
  // the GNU General Public License as published by the Free Software Foundation; either
  // version 2 of the License, or (at your option) any later version.

  // OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
  // without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
  // See the GNU General Public License for more details.

  // You should have received a copy of the GNU General Public License along with this program;
  // if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
  // Boston, MA  02110-1301, USA
 */

/**
 * Form class for employee contact detail
 */
class EmployeeJobDetailsForm extends BaseForm {

    public $fullName;
    public $attachment;
    public $jobSpecAttachment;
    public $empTermination;
    private $companyStructureService;
    private $jobTitleService;
    public $isJoinDateChanged = false;

    public function getJobTitleService() {
        if (is_null($this->jobTitleService)) {
            $this->jobTitleService = new JobTitleService();
            $this->jobTitleService->setJobTitleDao(new JobTitleDao());
        }
        return $this->jobTitleService;
    }

    const CONTRACT_KEEP = 1;
    const CONTRACT_DELETE = 2;
    const CONTRACT_UPLOAD = 3;
    
    

    public function configure() {

        $empNumber = $this->getOption('empNumber');
        $employee = $this->getOption('employee');
        $this->fullName = $employee->getFullName();

        $jobTitleId = $employee->job_title_code;
        $empTerminatedId = $employee->termination_id;
        $jobTitles = $this->_getJobTitles($jobTitleId);

        $employeeStatuses = $this->_getEmpStatuses();


        $eeoCategories = $this->_getEEOCategories();
        $subDivisions = $this->_getSubDivisions();
        $locations = $this->_getLocations($employee);

        $empService = new EmployeeService();

        $attachmentList = $empService->getEmployeeAttachments($empNumber, 'contract');
        if (count($attachmentList) > 0) {
            $this->attachment = $attachmentList[0];
        }

        $contractUpdateChoices = array(self::CONTRACT_KEEP => __('Keep Current'),
            self::CONTRACT_DELETE => __('Delete Current'),
            self::CONTRACT_UPLOAD => __('Replace Current'));

        // Note: Widget names were kept from old non-symfony version
        $this->setWidgets(array(
            'emp_number' => new sfWidgetFormInputHidden(),
            // TODO: Use sfWidgetFormChoice() instead
            'job_title' => new sfWidgetFormSelect(array('choices' => $jobTitles)),
            'emp_status' => new sfWidgetFormSelect(array('choices' => $employeeStatuses)), // employement status
            'terminated_date' => new ohrmWidgetDatePicker(array(), array('id' => 'job_terminated_date')),
            'termination_reason' => new sfWidgetFormTextarea(),
            'eeo_category' => new sfWidgetFormSelect(array('choices' => $eeoCategories)),
            'sub_unit' => new sfWidgetFormSelect(array('choices' => $subDivisions)), // sub division id
            'location' => new sfWidgetFormSelect(array('choices' => $locations)), // sub division name (not used)
            'joined_date' => new ohrmWidgetDatePicker(array(), array('id' => 'job_joined_date')),
            'contract_start_date' => new ohrmWidgetDatePicker(array(), array('id' => 'job_contract_start_date')),
            'contract_end_date' => new ohrmWidgetDatePicker(array(), array('id' => 'job_contract_end_date')),
            'contract_file' => new sfWidgetFormInputFile(),
            'contract_update' => new sfWidgetFormChoice(array('expanded' => true, 'choices' => $contractUpdateChoices)),
        ));

        // Default values
        $this->setDefault('emp_number', $empNumber);
        $this->setDefault('emp_status', $employee->emp_status);

        if (!empty($jobTitleId)) {
            $this->setDefault('job_title', $jobTitleId);

            $jobTitle = $this->getJobTitleService()->getJobTitleById($jobTitleId);
            $this->jobSpecAttachment = $jobTitle->getJobSpecificationAttachment();
        }

        if (!empty($empTerminatedId)) {
            $this->empTermination = $employee->getEmployeeTerminationRecord();
        }

        $this->setDefault('eeo_category', $employee->eeo_cat_code);

        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
        sfContext::getInstance()->getConfiguration()->loadHelpers('OrangeDate');

        $this->setDefault('sub_unit', $employee->work_station);

        // Assign first location
        $locationList = $employee->locations;
        if (count($locationList) > 0) {
            $this->setDefault('location', $locationList[0]->id);
        }

        $contracts = $employee->contracts;
        if (count($contracts) > 0) {
            $contract = $contracts[0];
            $this->setDefault('contract_start_date', set_datepicker_date_format($contract->start_date));
            $this->setDefault('contract_end_date', set_datepicker_date_format($contract->end_date));
        }


        $this->setDefault('joined_date', set_datepicker_date_format($employee->joined_date));

        $this->setDefault('contract_update', self::CONTRACT_KEEP);


        $this->setValidators(array(
            'emp_number' => new sfValidatorString(array('required' => true)),
            'job_title' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($jobTitles))),
            'emp_status' => new sfValidatorString(array('required' => false)),
            'terminated_date' => new ohrmDateValidator(
                    array('date_format' => $inputDatePattern, 'required' => false),
                    array('invalid' => 'Date format should be ' . $inputDatePattern)),
            'termination_reason' => new sfValidatorString(array('required' => false)),
            'eeo_category' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($eeoCategories))),
            'sub_unit' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($subDivisions))),
            'location' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($locations))),
            'joined_date' => new ohrmDateValidator(
                    array('date_format' => $inputDatePattern, 'required' => false),
                    array('invalid' => 'Date format should be ' . $inputDatePattern)),
            'contract_start_date' => new ohrmDateValidator(
                    array('date_format' => $inputDatePattern, 'required' => false),
                    array('invalid' => 'Date format should be ' . $inputDatePattern)),
            'contract_end_date' => new ohrmDateValidator(
                    array('date_format' => $inputDatePattern, 'required' => false),
                    array('invalid' => 'Date format should be ' . $inputDatePattern)),
            'contract_file' => new sfValidatorFile(array('required' => false,
                'max_size' => 1000000), array('max_size' => __(TopLevelMessages::FILE_SIZE_SAVE_FAILURE))),
            'contract_update' => new sfValidatorString(array('required' => false)),
        ));


        $this->widgetSchema->setNameFormat('job[%s]');

        // set up post validator method
        $this->validatorSchema->setPostValidator(
                new sfValidatorCallback(array(
                    'callback' => array($this, 'postValidate')
                ))
        );
        
        $formExtension = PluginFormMergeManager::instance();
        $formExtension->mergeForms($this, 'viewJobDetails', 'EmployeeJobDetailsForm');        
    }

    public function postValidate($validator, $values) {

        $update = $values['contract_update'];
        $file = $values['contract_file'];

        if ($update == self::CONTRACT_UPLOAD && empty($file)) {
            $message = __('Upload file missing');
            $error = new sfValidatorError($validator, $message);
            throw new sfValidatorErrorSchema($validator, array('' => $error));
        }

        return $values;
    }

    /**
     * Get Employee object with values filled using form values
     */
    public function getEmployee() {

        $employeeService = new EmployeeService();
        $employee = $employeeService->getEmployee($this->getValue('emp_number'));
        $joinedDate = $employee->joined_date;
        $jobTitle = $this->getValue('job_title');
        $employee->job_title_code = $jobTitle;
        $empStatus = $this->getValue('emp_status');
        if ($empStatus == '') {
            $employee->emp_status = null;
        } else {
            $employee->emp_status = $empStatus;
        }

        $eeoCat = $this->getValue('eeo_category');
        if ($eeoCat == '') {
            $employee->eeo_cat_code = null;
        } else {
            $employee->eeo_cat_code = $eeoCat;
        }
        $employee->work_station = $this->getValue('sub_unit');
        $employee->joined_date = $this->getValue('joined_date');
        
        if( $joinedDate != '' && $joinedDate != $this->getValue('joined_date')){
            $this->isJoinDateChanged = true ;
        }

        // Location

        $location = $this->getValue('location');

        $foundLocation = false;

        //
        // Unlink all locations except current.
        //	
        foreach ($employee->getLocations() as $empLocation) {
            if ($location == $empLocation->id) {
                $foundLocation = true;
            } else {
                $employee->unlink('locations', $empLocation->id);
            }
        }

        //
        // Link location if not already linked
        //
        if (!$foundLocation) {
            $employee->link('locations', $location);
        }

        // contract details
        $empContract = new EmpContract();
        $empContract->emp_number = $employee->empNumber;
        $empContract->start_date = $this->getValue('contract_start_date');
        $empContract->end_date = $this->getValue('contract_end_date');
        $empContract->contract_id = 1;

        $employee->contracts[0] = $empContract;

        return $employee;
    }

    private function _getJobTitles($jobTitleId) {

        $jobTitleList = $this->getJobTitleService()->getJobTitleList("", "", false);
        $choices = array('' => '-- ' . __('Select') . ' --');

        foreach ($jobTitleList as $job) {
            if (($job->getIsDeleted() == JobTitle::ACTIVE) || ($job->getId() == $jobTitleId)) {
                $name = ($job->getIsDeleted() == JobTitle::DELETED) ? $job->getJobTitleName() . " (".__("Deleted").")" : $job->getJobTitleName();
                $choices[$job->getId()] = $name;
            }
        }
        return $choices;
    }

    private function _getEEOCategories() {
        $jobService = new JobCategoryService();
        $categories = $jobService->getJobCategoryList();
        $choices = array('' => '-- ' . __('Select') . ' --');

        foreach ($categories as $category) {
            $choices[$category->getId()] = $category->getName();
        }
        return $choices;
    }

    private function _getEmpStatuses() {
        $empStatusService = new EmploymentStatusService();

        $choices = array('' => '-- ' . __('Select') . ' --');

        $statuses = $empStatusService->getEmploymentStatusList();

        foreach ($statuses as $status) {
            $choices[$status->getId()] = $status->getName();
        }

        return $choices;
    }

    public function getCompanyStructureService() {
        if (is_null($this->companyStructureService)) {
            $this->companyStructureService = new CompanyStructureService();
            $this->companyStructureService->setCompanyStructureDao(new CompanyStructureDao());
        }
        return $this->companyStructureService;
    }

    public function setCompanyStructureService(CompanyStructureService $companyStructureService) {
        $this->companyStructureService = $companyStructureService;
    }

    private function _getSubDivisions() {

        $subUnitList = array('' => '-- ' . __('Select') . ' --');
        $treeObject = $this->getCompanyStructureService()->getSubunitTreeObject();

        $tree = $treeObject->fetchTree();

        foreach ($tree as $node) {
            if ($node->getId() != 1) {
                $subUnitList[$node->getId()] = str_repeat('&nbsp;&nbsp;', $node['level'] - 1) . $node['name'];
            }
        }

//        asort($subUnitList);
        return($subUnitList);
    }

    private function _getLocations(Employee $employee) {
        $locationList = array('' => '-- ' . __('Select') . ' --');

        $locationService = new LocationService();
        $locations = $locationService->getLocationList();        

        $accessibleLocations = UserRoleManagerFactory::getUserRoleManager()->getAccessibleEntityIds('Location');
        
        $empLocations = $employee->getLocations();        
        
        foreach ($empLocations as $location) {
            $accessibleLocations[] = $location->getId();
        }
        
        foreach ($locations as $location) {
            if (in_array($location->id, $accessibleLocations)) {
                $locationList[$location->id] = $location->name;
            }
        }

        return($locationList);
    }

    /**
     * Save employee contract
     */
    public function updateAttachment() {

        $empNumber = $this->getValue('emp_number');
        //$attachId = $this->getValue('seqNO');

        $update = $this->getValue('contract_update');
        $empAttachment = false;
        $file = $this->getValue('contract_file');

        if ($update == self::CONTRACT_DELETE) {
            $q = Doctrine_Query :: create()->delete('EmployeeAttachment a')
                            ->where('emp_number = ?', $empNumber)
                            ->andWhere('screen = ?', "contract");
            $result = $q->execute();
        } else if ($update == self::CONTRACT_UPLOAD || !empty($file)) {
            // find existing
            $q = Doctrine_Query::create()
                            ->select('a.emp_number, a.attach_id')
                            ->from('EmployeeAttachment a')
                            ->where('a.emp_number = ?', $empNumber)
                            ->andWhere('screen = ?', "contract");
            $result = $q->execute();

            if ($result->count() == 1) {
                $empAttachment = $result[0];
            }

            //
            // New file upload
            //
            $newFile = false;

            if ($empAttachment === false) {

                $empAttachment = new EmployeeAttachment();
                $empAttachment->emp_number = $empNumber;

                $q = Doctrine_Query::create()
                                ->select('MAX(a.attach_id)')
                                ->from('EmployeeAttachment a')
                                ->where('a.emp_number = ?', $empNumber);
                $result = $q->execute(array(), Doctrine::HYDRATE_ARRAY);

                if (count($result) != 1) {
                    throw new PIMServiceException('MAX(a.attach_id) failed.');
                }
                $attachId = is_null($result[0]['MAX']) ? 1 : $result[0]['MAX'] + 1;

                $empAttachment->attach_id = $attachId;
                $newFile = true;
            }

            $tempName = $file->getTempName();


            $empAttachment->size = $file->getSize();
            $empAttachment->filename = $file->getOriginalName();
            $empAttachment->attachment = file_get_contents($tempName);
            ;
            $empAttachment->file_type = $file->getType();
            $empAttachment->screen = EmployeeAttachment::SCREEN_JOB_CONTRACT;

            $empAttachment->attached_by = $this->getOption('loggedInUser');
            $empAttachment->attached_by_name = $this->getOption('loggedInUserName');

            $empAttachment->save();
        }
    }
    
    public function getIsJoinDateChanged(){
        return $this->isJoinDateChanged;
    }

}

