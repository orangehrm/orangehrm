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

/**
 * Description of EmployeeSearchForm
 *
 * @author dewmal
 */
class BuzzEmployeeSearchForm extends BaseForm{
    
    public function configure() {

        $widgets['emp_name'] = new ohrmWidgetEmployeeNameAutoFill(array('jsonList' => $this->getEmployeeListAsJson()), array('class' => 'formInputText'));
        $this->setWidgets($widgets);
        $this->setvalidators(array(
            'emp_name' => new ohrmValidatorEmployeeNameAutoFill()
        ));

       

       

        $this->getWidgetSchema()->setNameFormat('searchChatter[%s]');
        $this->getWidgetSchema()->setLabels($this->getFormLabels());
    }

    protected function getFormLabels() {
        $labels = array(
            'emp_name' => __('Name')
            
        );

        return $labels;
    }

    public function getStylesheets() {
        $styleSheets = parent::getStylesheets();
        $styleSheets[plugin_web_path('orangehrmCorporateDirectoryPlugin', 'css/viewDirectorySuccess.css')] = 'all';
        return $styleSheets;
    }

    

   

    
    public function getEmployeeListAsJson() {
        $jsonArray = array();
        $employeeService = new EmployeeService();
        $employeeService->setEmployeeDao(new EmployeeDao());
        $employeeList = $employeeService->getEmployeeList();

        $jsonArray[] = array('name' => __('All'), 'id' => '');
        foreach ($employeeList as $employee) {
            $name = $employee->getFirstName() . " " . $employee->getMiddleName();
            $name = trim(trim($name) . " " . $employee->getLastName());
            if ($employee->getTerminationId()) {
                $name = $name . ' (' . __('Past Employee') . ')';
            }
                $jsonArray[] = array('name' => $name, 'id' => $employee->getEmpNumber());
            }
        
        $jsonString = json_encode($jsonArray);

        return $jsonString;
    }
}
