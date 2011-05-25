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
require_once ROOT_PATH . '/lib/common/LocaleUtil.php';

class EmployeePersonalDetailsForm extends BaseForm {

    private $nationalityService;
    private $employeeService;
    private $widgets = array();
    private $gender;
    public $fullName;

    /**
     * Get NationalityService
     * @returns NationalityService
     */
    public function getNationalityService() {
        if(is_null($this->nationalityService)) {
            $this->nationalityService = new NationalityService();
        }
        return $this->nationalityService;
    }

    /**
     * Set NationalityService
     * @param NationalityService $nationalityService
     */
    public function setNationalityService(NationalityService $nationalityService) {
        $this->nationalityService = $nationalityService;
    }

    /**
     * Get EmployeeService
     * @returns EmployeeService
     */
    public function getEmployeeService() {
        if(is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
            $this->employeeService->setEmployeeDao(new EmployeeDao());
        }
        return $this->employeeService;
    }

    /**
     * Set EmployeeService
     * @param EmployeeService $employeeService
     */
    public function setEmployeeService(EmployeeService $employeeService) {
        $this->employeeService = $employeeService;
    }

    public function configure() {
        
        $ess = $this->getOption('ESS', false);
        $empNumber = $this->getOption('empNumber');
        $employee = $this->getEmployeeService()->getEmployee($empNumber);
        $this->gender = ($employee->emp_gender != "")?$employee->emp_gender:"";
        $this->fullName = $employee->getFullName();

        //initializing the components
        $this->widgets = array(
            'txtEmpID' => new sfWidgetFormInputHidden(),
            'txtEmpLastName' => new sfWidgetFormInputText(),
            'txtEmpFirstName' => new sfWidgetFormInputText(),
			'txtEmpMiddleName' => new sfWidgetFormInputText(),
            'txtEmpNickName' => new sfWidgetFormInputText(),
            'optGender' => new sfWidgetFormChoice(array('expanded' => true, 'choices'  => array(1 => __("Male"), 2 => __("Female")))),
            'cmbNation' => new sfWidgetFormSelect(array('choices' => $this->getNationalityList())),
            'txtOtherID' => new sfWidgetFormInputText(),
            'cmbMarital' => new sfWidgetFormSelect(array('choices'=>array(0 => "-- " . __('Select') . " --", 'Single' => __('Single'), 'Married' => __('Married'), 'Other' => __('Other')))),
            'chkSmokeFlag' => new sfWidgetFormInputCheckbox(),
            'txtLicExpDate' => new sfWidgetFormInputText(),
            'txtMilitarySer' => new sfWidgetFormInputText(),
            'cmbEthnicRace' => new sfWidgetFormSelect(array('choices'=> $this->getEthnicalRaceList())),
        );

        //setting default values
        $this->widgets['txtEmpID']->setAttribute('value', $employee->empNumber);
        $this->widgets['txtEmpLastName']->setAttribute('value', $employee->lastName);
        $this->widgets['txtEmpFirstName']->setAttribute('value', $employee->firstName);
        $this->widgets['txtEmpMiddleName']->setAttribute('value', $employee->middleName);
        $this->widgets['txtEmpNickName']->setAttribute('value', $employee->nickName);

        //setting the default selected nation code
        $this->widgets['cmbNation']->setDefault($employee->nation_code);

        //setting the default value for ethnical code
        $this->widgets['cmbEthnicRace']->setDefault($employee->ethnic_race_code);


        //setting default marital status
        $this->widgets['cmbMarital']->setDefault($employee->emp_marital_status);
          
        if($employee->smoker) {
            $this->widgets['chkSmokeFlag']->setAttribute('checked', 'checked');
        }

        $this->widgets['chkSmokeFlag']->setAttribute('value', 1);
        $this->widgets['txtLicExpDate']->setAttribute('value', ohrm_format_date($employee->emp_dri_lice_exp_date));
        $this->widgets['txtMilitarySer']->setAttribute('value', $employee->militaryService);
        $this->widgets['optGender']->setDefault($this->gender);
        $this->widgets['txtOtherID']->setAttribute('value', $employee->otherId);
      
        // Widgets for non-ess mode only
        //if (!$ess) {
            //initializing and setting default values
            $this->widgets['txtEmployeeId'] = new sfWidgetFormInputText();
            $this->widgets['txtEmployeeId']->setAttribute('value', $employee->employeeId);

            $this->widgets['txtNICNo']  = new sfWidgetFormInputText();
            $this->widgets['txtNICNo']->setAttribute('value', $employee->ssn);

            $this->widgets['txtSINNo'] = new sfWidgetFormInputText();
            $this->widgets['txtSINNo']->setAttribute('value', $employee->sin);

            $this->widgets['DOB'] = new sfWidgetFormInputText();
            $this->widgets['DOB']->setAttribute('value', ohrm_format_date($employee->emp_birthday));
            
            $this->widgets['txtLicenNo'] = new sfWidgetFormInputText();
            $this->widgets['txtLicenNo']->setAttribute('value', $employee->licenseNo);
        //}
        
        $this->setWidgets($this->widgets);

        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();

        //setting server side validators
        $this->setValidators(array(
            'txtEmpID' => new sfValidatorString(array('required' => true)),
            'txtEmployeeId' => new sfValidatorString(array('required' => false)),
            'txtEmpFirstName' => new sfValidatorString(array('required' => true, 'max_length' => 30, 'trim' => true),
                   array('required' => 'First Name Empty!', 'max_length' => 'First Name Length exceeded 30 characters')),
            'txtEmpMiddleName' => new sfValidatorString(array('required' => false, 'max_length' => 30, 'trim' => true), array('max_length' => 'Middle Name Length exceeded 30 characters')),
            'txtEmpLastName' => new sfValidatorString(array('required' => true, 'max_length' => 30, 'trim' => true),
                   array('required' => 'Last Name Empty!', 'max_length' => 'Last Name Length exceeded 30 characters')),
            'txtEmpNickName' => new sfValidatorString(array('required' => false, 'trim' => true)),
            'optGender' => new sfValidatorChoice(array('required' => false,
                                                       'choices' => array(Employee::GENDER_MALE, Employee::GENDER_FEMALE),
                                                       'multiple' => false)),
            'cmbNation' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->getNationalityList()))),
            'txtOtherID' => new sfValidatorString(array('required' => false, 'max_length' => 30), array('max_length' => 'Last Name Length exceeded 30 characters')),
            'cmbMarital' => new sfValidatorString(array('required' => false)),
            'chkSmokeFlag' => new sfValidatorString(array('required' => false)),
            'txtLicExpDate' => new ohrmDateValidator(array('date_format'=>$inputDatePattern, 'required'=>false), array('invalid'=>"Date format should be $inputDatePattern")),
            'txtMilitarySer' => new sfValidatorString(array('required' => false)),
            'cmbEthnicRace' => new sfValidatorChoice(array('required' => false, 'choices'=> array_keys($this->getEthnicalRaceList()))),

        ));
        //if (!$ess) {
            $this->setValidator('txtNICNo', new sfValidatorString(array('required' => false)));
            $this->setValidator('txtSINNo', new sfValidatorString(array('required' => false, 'max_length' => 30), array('max_length' => 'First Name Length exceeded 30 characters')));
            $this->setValidator('txtLicenNo', new sfValidatorString(array('required' => false, 'max_length' => 30), array('max_length' => 'License No length exceeded 30 characters')));
            $this->setValidator('DOB', new ohrmDateValidator(array('date_format'=>$inputDatePattern, 'required'=>false), array('invalid'=>"Date format should be $inputDatePattern")));
        //}

        $this->widgetSchema->setNameFormat('personal[%s]');
    }

    private function getNationalityList() {
        $nationalityService = $this->getNationalityService();
        $nationalities = $nationalityService->getNationalityList();
        $list = array(0 => "-- " . __('Select') . " --");
        
        foreach($nationalities as $nationality) {
            $list[$nationality->getNatCode()] = $nationality->getNatName();
        }
        return $list;
    }

    private function getEthnicalRaceList() {
        $nationalityService = $this->getNationalityService();
        $races = $nationalityService->getEthnicRaceList();
        $list = array(0 => "-- " . __('Select') . " --");

        foreach($races as $race) {
            $list[$race->getEthnicRaceCode()] = $race->getEthnicRaceDesc();
        }
        return $list;
    }

    /**
     * Get Employee object with values filled using form values
     */
    public function getEmployee() {

        $ess = $this->getOption('ESS', false);

        $employee = new Employee();
        $employee->empNumber = $this->getValue('txtEmpID');
        $employee->firstName = $this->getValue('txtEmpFirstName');
        $employee->middleName = $this->getValue('txtEmpMiddleName');
        $employee->lastName = $this->getValue('txtEmpLastName');
        $employee->nickName = $this->getValue('txtEmpNickName');

        $nation = $this->getValue('cmbNation');
        $employee->nation_code = ($nation != '0') ? $nation : null;
        $employee->otherId = $this->getValue('txtOtherID');

        $employee->emp_marital_status = $this->getValue('cmbMarital');
        $employee->smoker = $this->getValue('chkSmokeFlag');
        $employee->emp_gender = $this->getValue('optGender');

        $employee->emp_dri_lice_exp_date = $this->getValue('txtLicExpDate');

        $employee->militaryService = $this->getValue('txtMilitarySer');

        $race = $this->getValue('cmbEthnicRace');
        if ($race != '0') {
            $employee->ethnic_race_code = $race;
        }

        if (!$ess) {
            $employee->employeeId = $this->getValue('txtEmployeeId');
            $employee->ssn = $this->getValue('txtNICNo');
            $employee->sin = $this->getValue('txtSINNo');
            $employee->emp_birthday = $this->getValue('DOB');
            $employee->licenseNo = $this->getValue('txtLicenNo');
        }

        return $employee;
    }

}

