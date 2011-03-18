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
 * Form class for employee list in PIM
 */
class EmployeeSearchForm extends BaseForm {

    public function configure() {

        $this->setWidgets(array(
            'employee_name' => new sfWidgetFormInputText(),
            'supervisor_name' => new sfWidgetFormInputText(),
            'id' => new sfWidgetFormInputText(),
            'job_title' => new sfWidgetFormSelect(array('choices'=>array())),
            'employee_status' => new sfWidgetFormSelect(array('choices'=>array())),
            'sub_unit' => new sfWidgetFormSelect(array('choices'=>array())),

        ));

        $this->widgetSchema->setNameFormat('empsearch[%s]');

        $this->setValidators(array(
            'employee_name' => new sfValidatorString(array('required' => false)),
            'supervisor_name' => new sfValidatorString(array('required' => false)),
            'id' => new sfValidatorString(array('required' => false)),
            'job_title' => new sfValidatorString(array('required' => false)),
            'employee_status' => new sfValidatorString(array('required' => false)),
            'sub_unit' => new sfValidatorString(array('required' => false)),
        ));
    }

}

