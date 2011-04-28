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
class EmployeeLanguageForm extends sfForm {
    
    private $employeeService;
    public $fullName;
    private $widgets = array();
    public $empLanguageList;

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

        $empNumber = $this->getOption('empNumber');
        $employee = $this->getEmployeeService()->getEmployee($empNumber);
        $this->fullName = $employee->getFullName();

        $this->empLanguageList = $this->getEmployeeService()->getLanguage($empNumber);

        $i18nHelper = sfContext::getInstance()->getI18N();
        
        $availableLanguageList = $this->_getLanguageList();
        $this->langTypeList = array("" => '-- ' . $i18nHelper->__('Select') . ' --',
                               1 => $i18nHelper->__('Writing'),
                               2 => $i18nHelper->__('Speaking'),
                               3 => $i18nHelper->__('Reading'));
        $this->competencyList = array("" => '-- ' . $i18nHelper->__('Select') . ' --',
                                 1 => $i18nHelper->__('Poor'),
                                 2 => $i18nHelper->__('Basic'),
                                 3 => $i18nHelper->__('Good'),
                                 4 => $i18nHelper->__('Mother Tongue'));
        
        //initializing the components
        $this->widgets = array(
            'emp_number' => new sfWidgetFormInputHidden(),
            'code' => new sfWidgetFormSelect(array('choices' => $availableLanguageList)),
            'lang_type' => new sfWidgetFormSelect(array('choices' => $this->langTypeList)),
            'competency' => new sfWidgetFormSelect(array('choices' => $this->competencyList)),
            'comments' => new sfWidgetFormTextarea()
        );

        $this->widgets['emp_number']->setDefault($empNumber);
        $this->setWidgets($this->widgets);

        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
        
        $this->setValidator('emp_number', new sfValidatorString(array('required' => false)));
        $this->setValidator('code', new sfValidatorChoice(array('choices' => array_keys($availableLanguageList))));
        $this->setValidator('lang_type', new sfValidatorChoice(array('choices' => array_keys($this->langTypeList))));
        $this->setValidator('competency', new sfValidatorChoice(array('choices' => array_keys($this->competencyList))));
        $this->setValidator('comments', new sfValidatorString(array('required' => false, 'max_length' => 100)));

        $this->widgetSchema->setNameFormat('language[%s]');
    }
    
    public function getLangTypeDesc($langType) {
        $langTypeDesc = "";
        if (isset($this->langTypeList[$langType])) {
            $langTypeDesc = $this->langTypeList[$langType];
        }    
        return $langTypeDesc;
    }
    
    public function getCompetencyDesc($competency) {
        $competencyDesc = "";
        if (isset($this->competencyList[$competency])) {
            $competencyDesc = $this->competencyList[$competency];
        }
        return $competencyDesc;
    }

    private function _getLanguageList() {
        $skillService = new SkillService();
        $languageList = $skillService->getLanguageList();
        $list = array("" => "-- " . __('Select') . " --");

        foreach($languageList as $language) {
            $list[$language->getLangCode()] = $language->getLangName();
        }

        return $list;
    }
}
?>