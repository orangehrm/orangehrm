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
class EmployeeLicenseForm extends sfForm {
    
    private $employeeService;
    public $fullName;
    private $widgets = array();
    public $empLicenseList;

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
        $this->licensePermissions = $this->getOption('licensePermissions');

        $empNumber = $this->getOption('empNumber');
        $employee = $this->getEmployeeService()->getEmployee($empNumber);
        $this->fullName = $employee->getFullName();

        $this->empLicenseList = $this->getEmployeeService()->getEmployeeLicences($empNumber);
        $widgets = array('emp_number' => new sfWidgetFormInputHidden(array(), array('value' => $empNumber)));
        $validators = array('emp_number' => new sfValidatorString(array('required' => true)));
        
        if ($this->licensePermissions->canRead()) {

            $licenseWidgets = $this->getLicenseWidgets();
            $licenseValidators = $this->getLicenseValidators();

            if (!($this->licensePermissions->canUpdate() || $this->licensePermissions->canCreate()) ) {
                foreach ($licenseWidgets as $widgetName => $widget) {
                    $widget->setAttribute('disabled', 'disabled');
                }
            }
            $widgets = array_merge($widgets, $licenseWidgets);
            $validators = array_merge($validators, $licenseValidators);
        }
        $this->setWidgets($widgets);
        $this->setValidators($validators);
        
        $this->widgetSchema->setNameFormat('license[%s]');
        
        $this->getWidgetSchema()->setLabels($this->getFormLabels());

    }
    
    /**
     * Get widgets
     * @return array of widget objects 
     */
    private function getLicenseWidgets() {
        $widgets = array();
        
        $widgets['code'] = new sfWidgetFormSelect(array('choices' => $this->_getLicenseList()));
        $widgets['license_no'] = new sfWidgetFormInputText();
        $widgets['date'] = new ohrmWidgetDatePicker(array(), array('id' => 'license_date'));
        $widgets['renewal_date'] = new ohrmWidgetDatePicker(array(), array('id' => 'license_renewal_date'));
        return $widgets;        
    }

    /**
     * Get Validators
     * @return \sfValidatorString 
     */
    private function getLicenseValidators() {
        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
        $validators = array(
            'code' => new sfValidatorString(array('required' => true,'max_length' => 13)),
            'license_no' => new sfValidatorString(array('required' => false,'max_length' => 50)),
            'date' => new ohrmDateValidator(array('date_format'=>$inputDatePattern, 'required' => false), array('invalid'=>'Date format should be'. $inputDatePattern)),
            'renewal_date' => new ohrmDateValidator(array('date_format'=>$inputDatePattern, 'required' => false),array('invalid'=>'Date format should be'. $inputDatePattern))
        );
        
        return $validators;
    }
    
    private function _getLicenseList() {
        $licenseService = new LicenseService();
        $licenseList = $licenseService->getLicenseList();
        $list = array("" => "-- " . __('Select') . " --");

        foreach($licenseList as $license) {
            $list[$license->getId()] = $license->getName();
        }
        
        // Clear already used license items
        foreach ($this->empLicenseList as $empLicense) {
            if (isset($list[$empLicense->licenseId])) {
                unset($list[$empLicense->licenseId]);
            }
        }
        return $list;
    }
    
    /**
     *
     * @return array
     */
    protected function getFormLabels() {
        $required = '<em> *</em>';
        $labels = array(
            'code' => __('License Type') . $required,
            'license_no' => __('License Number'),
            'date' => __('Issued Date'),
            'renewal_date' => __('Expiry Date'),
        );
        return $labels;
    }
}
?>