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
class EmployeeSubscriberForm extends sfForm {

    /**
     * @var EmployeeService
     */
    private $employeeService = null;

    /**
     * Get EmployeeService
     * @returns EmployeeService
     */
    public function getEmployeeService(): EmployeeService {
        if (is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
            $this->employeeService->setEmployeeDao(new EmployeeDao());
        }

        return $this->employeeService;
    }

    /**
     * @inheritDoc
     */
    public function configure() {
        $this->setWidgets($this->getWidgets());
        $this->setValidators($this->getValidators());

        $this->widgetSchema->setNameFormat('subscriber[%s]');
        $this->getWidgetSchema()->setLabels($this->getFormLabels());
    }

    /**
     * Get widgets
     * @return array of widget objects
     */
    private function getWidgets(): array {
        $empNumber = $this->getOption('empNumber');

        $employee = $this->getEmployeeService()->getEmployee($empNumber);
        $widgets = [];

        $widgets['empNumber'] = new sfWidgetFormInputHidden([], ['value' => $empNumber]);
        $widgets['name'] = new sfWidgetFormInputText();
        $widgets['email'] = new sfWidgetFormInputText();

        $widgets['name']->setDefault($employee->getFirstName() ?: $employee->getLastName());
        $widgets['email']->setDefault($employee->getEmpWorkEmail());

        return $widgets;
    }

    /**
     * Get Validators
     * @return \sfValidatorString
     */
    private function getValidators(): array {

        return [
            'empNumber' => new sfValidatorString(array('required' => true)),
            'name'       => new sfValidatorString(array('required' => true, 'max_length' => 50)),
            'email'       => new sfValidatorEmail(array('required' => true, 'max_length' => 50, 'trim' => true)),
        ];
    }

    /**
     *
     * @return array
     */
    protected function getFormLabels(): array {
        $required = '<em> *</em>';

        return [
            'name' => __('Name') . $required,
            'email' => __('Email') . $required,
        ];
    }
}

