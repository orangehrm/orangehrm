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
require_once ROOT_PATH . '/lib/common/LocaleUtil.php';
/**
 * Form class for employee contact detail
 */
class EmployeeJobDetailsForm extends BaseForm {

    public $fullName;
    public $jobSpecName;
    public $jobSpecDescription;
    public $jobSpecDuties;
    
    const CONTRACT_KEEP = 1;
    const CONTRACT_DELETE = 2;
    const CONTRACT_UPLOAD = 3;
    
    public function configure() {

        $empNumber = $this->getOption('empNumber');
        $employee = $this->getOption('employee');
        $this->fullName = $employee->getFullName();
        
        $jobTitles = $this->_getJobTitles();
        
        $jobTitleId = $employee->job_title_code;  
        $employeeStatuses = $this->_getEmpStatuses($jobTitleId);

        
        $eeoCategories = $this->_getEEOCategories();
        $subDivisions = $this->_getSubDivisions();
        $locations = $this->_getLocations();
        
        $contractUpdateChoices = array(self::CONTRACT_KEEP =>__('Keep Current'), 
                                       self::CONTRACT_DELETE => __('Delete Current'),
                                       self::CONTRACT_UPLOAD => __('Upload New'));
            
        // Note: Widget names were kept from old non-symfony version
        $this->setWidgets(array(
            'emp_number' => new sfWidgetFormInputHidden(),

            // TODO: Use sfWidgetFormChoice() instead
            'job_title' => new sfWidgetFormSelect(array('choices'=>$jobTitles)),

            'emp_status' => new sfWidgetFormSelect(array('choices'=>$employeeStatuses)), // employement status
            'eeo_category' => new sfWidgetFormSelect(array('choices'=>$eeoCategories)),
            'sub_unit' => new sfWidgetFormSelect(array('choices'=>$subDivisions)), // sub division id
            'location' => new sfWidgetFormSelect(array('choices'=>$locations)), // sub division name (not used)
            'joined_date' => new sfWidgetFormInputText(),
            'contract_start_date' => new sfWidgetFormInputText(),
            'contract_end_date' => new sfWidgetFormInputText(),
            'contract_file' => new sfWidgetFormInputFile(), 
            'contract_update' => new sfWidgetFormChoice(array('expanded' => true, 'choices'  => $contractUpdateChoices)),
        ));
        
        // Default values
        $this->setDefault('emp_number', $empNumber);

        if (!empty($jobTitleId)) {
            $this->setDefault('job_title', $jobTitleId);
            $this->setDefault('emp_status', $employee->emp_status);
            
            $jobSpec = $employee->jobTitle->JobSpecifications;
            if (!empty($jobSpec)) {
                $this->jobSpecName = $jobSpec->jobspec_name;
                $this->jobSpecDescription = $jobSpec->jobspec_desc;
                $this->jobSpecDuties = $jobSpec->jobspec_duties;
            }
        }
        
        $this->setDefault('eeo_category', $employee->eeo_cat_code);
        
        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
        sfContext::getInstance()->getConfiguration()->loadHelpers('OrangeDate');
        
        $this->setDefault('sub_unit', $employee->work_station);
        
        // Assign first location
        $locationList = $employee->locations;
        if (count($locationList) > 0) {
            $this->setDefault('location', $locationList[0]->loc_code);
        }
        
        $contracts = $employee->contracts;
        if (count($contracts) > 0) {
            $contract = $contracts[0];
            $this->setDefault('contract_start_date', ohrm_format_date($contract->start_date));
            $this->setDefault('contract_end_date', ohrm_format_date($contract->end_date));
        }

        
        $this->setDefault('joined_date', ohrm_format_date($employee->joined_date));
                
        $this->setDefault('contract_update', self::CONTRACT_KEEP);

        
        $this->setValidators(array(
            'emp_number' => new sfValidatorString(array('required' => true)),
            'job_title' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($jobTitles))),
            'emp_status' => new sfValidatorString(array('required' => false)),
            'eeo_category' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($eeoCategories))),
            'sub_unit' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($subDivisions))),
            'location' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($locations))),
            'joined_date' => new ohrmDateValidator(
                array('date_format'=>$inputDatePattern, 'required' => false),
                array('invalid'=>'Date format should be '. strtoupper($inputDatePattern))),
            'contract_start_date' => new ohrmDateValidator(
                array('date_format'=>$inputDatePattern, 'required' => false),
                array('invalid'=>'Date format should be '. strtoupper($inputDatePattern))),
            'contract_end_date' => new ohrmDateValidator(
                array('date_format'=>$inputDatePattern, 'required' => false),
                array('invalid'=>'Date format should be '. strtoupper($inputDatePattern))),
            
            'contract_file' => new sfValidatorFile(array('required' => false, 
                'max_size'=>1000000), array('max_size' => __('Contract Details File Size Exceeded.'))),
            'contract_update' => new sfValidatorString(array('required' => false)),
        ));
        
        
        $this->widgetSchema->setNameFormat('job[%s]');
    }

    /**
     * Get Employee object with values filled using form values
     */
    public function getEmployee() {

        $employee = new Employee();
        $employee->empNumber = $this->getValue('emp_number');

        $jobTitle = $this->getValue('job_title');
        if ($jobTitle != '') {
            $employee->job_title_code = $jobTitle;
        }
        $empStatus = $this->getValue('emp_status');
        if ($empStatus != '') {
            $employee->emp_status = $empStatus;
        }
        $eeoCat = $this->getValue('eeo_category');
        if ($eeoCat != '') {
            $employee->eeo_cat_code = $eeoCat;
        }
        $employee->work_station = $this->getValue('sub_unit');
        $employee->joined_date = $this->getValue('joined_date');
        
        // Location
        $location = $this->getValue('location');
        if ( $location != '') {
            $empLocation = new EmpLocations();
            $empLocation->empNumber = $employee->empNumber;
            $empLocation->loc_code = $location;
            $employee->locations[0] = $empLocation;
        }
                
        // contract
        $contractStartDate = $this->getValue('contract_start_date');
        $contractEndDate = $this->getValue('contract_end_date');
        
        if (!empty($contractStartDate) && !empty($contractEndDate)) {
            $empContract = new EmpContract();
            $empContract->emp_number = $employee->empNumber;
            $empContract->start_date = $contractStartDate;
            $empContract->end_date = $contractEndDate;
            $empContract->contract_id = 1;

            $employee->contracts[0] = $empContract;
        }
        // contract details

        return $employee;
    }
    
    private function _getJobTitles() {
        $jobService = new JobService();
        $jobList = $jobService->getJobTitleList();
        $choices = array('' => '-- ' . __('Select') . ' --');

        foreach ($jobList as $job) {
            $choices[$job->getId()] = $job->getName();
        }
        return $choices;
    }
    
    private function _getEEOCategories() {
        $jobService = new JobService();
        $categories = $jobService->getJobCategoryList('eec_desc');
        $choices = array('' => '-- ' . __('Select') . ' --');

        foreach ($categories as $category) {
            $choices[$category->getEecCode()] = $category->getEecDesc();
        }
        return $choices;
    }    
    
    private function _getEmpStatuses($jobTitle) {
        $jobService = new JobService();

        $choices = array('' => '-- ' . __('Select') . ' --');
        
        if (!empty($jobTitle)) {
            $statuses = $jobService->getEmployeeStatusForJob($jobTitle);

            foreach ($statuses as $status) {
                $choices[$status->getId()] = $status->getName();
            }
        }
        return $choices;
    }   
    
    private function _getSubDivisions() {
        $companyService = new CompanyService();

        $subUnitList = array('' => '-- ' . __('Select') . ' --');
        $tree = $companyService->getSubDivisionTree();

        foreach($tree as $node) {

            // Add nodes, indenting correctly. Skip root node
            if ($node->getId() != 1) {
                if($node->depth == "") {
                    $node->depth = 1;
                }
                $indent = str_repeat('&nbsp;&nbsp;', $node->depth - 1);
                $subUnitList[$node->getId()] = $indent . $node->getTitle();
            }
        }

        return($subUnitList);
    }   
    
    private function _getLocations() {
        $companyService = new CompanyService();

        $locationList = array('' => '-- ' . __('Select') . ' --');
        $locations = $companyService->getCompanyLocation('loc_name');

        foreach($locations as $location) {
            $locationList[$location->loc_code] = $location->loc_name;
        }

        return($locationList);
    }      
    
}

