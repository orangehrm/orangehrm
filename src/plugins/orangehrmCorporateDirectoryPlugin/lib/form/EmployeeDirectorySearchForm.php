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

class EmployeeDirectorySearchForm extends BaseForm {

    public function configure() {

        $widgets['emp_name'] = new ohrmWidgetEmployeeNameAutoFill(array('jsonList' => $this->getEmployeeListAsJson()), array('class' => 'formInputText'));
        $this->setWidgets($widgets);
        $this->setvalidators(array(
            'emp_name' => new ohrmValidatorEmployeeNameAutoFill()
        ));

        /* Setting job titles */
        $this->_setJobTitleWidget();

        $formExtension = PluginFormMergeManager::instance();
        $formExtension->mergeForms($this, 'viewDirectory', 'EmployeeDirectorySearchForm');

        $this->getWidgetSchema()->setNameFormat('searchDirectory[%s]');
        $this->getWidgetSchema()->setLabels($this->getFormLabels());
    }

    protected function getFormLabels() {
        $labels = array(
            'emp_name' => __('Name'),
            'job_title' => __('Job Title'),
            'location' => __('Location')
        );

        return $labels;
    }

    public function getStylesheets() {
        $styleSheets = parent::getStylesheets();
        $styleSheets[plugin_web_path('orangehrmCorporateDirectoryPlugin', 'css/viewDirectorySuccess.css')] = 'all';
        return $styleSheets;
    }

    private function _setJobTitleWidget() {

        $jobTitleList = $this->getJobTitleService()->getJobTitleList();
        $choices = array('0' => __('All'));

        foreach ($jobTitleList as $job) {
            $choices[$job->getId()] = $job->getJobTitleName();
        }

        $this->setWidget('job_title', new sfWidgetFormChoice(array('choices' => $choices)));
        $this->setValidator('job_title', new sfValidatorChoice(array('choices' => array_keys($choices))));
    }

    public function getJobTitleService() {
        if (is_null($this->jobTitleService)) {
            $this->jobTitleService = new JobTitleService();
            $this->jobTitleService->setJobTitleDao(new JobTitleDao());
        }
        return $this->jobTitleService;
    }

    
    public function getEmployeeListAsJson() {
        $jsonArray = array();
        $employeeService = new EmployeeService();
        $employeeService->setEmployeeDao(new EmployeeDao());
        $employeeService instanceof EmployeeService;
        $employeeList = $employeeService->getEmployeePropertyList(array('empNumber', 'firstName', 'lastName', 'middleName', 'termination_id'), 'lastName', 'ASC');

        $terminationLabel = ' (' . __('Past Employee') . ')';
        $jsonArray[] = array('name' => __('All'), 'id' => '');
        foreach ($employeeList as $employee) {
            $name = $employee['firstName'] . " " . $employee['middleName'];
            $name = trim(trim($name) . " " . $employee['lastName']);
            if ($employee['termination_id']) {
                $name .= $terminationLabel;
            }
            $jsonArray[] = array('name' => $name, 'id' => $employee['empNumber']);
        }

        $jsonString = json_encode($jsonArray);

        return $jsonString;
    }
}
