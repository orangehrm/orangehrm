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

    protected static $searchFields = array(
            'employeeId' => 'Emp. ID', 
            'firstName' => 'Emp. First Name', 
            'middleName' => 'Emp. Middle Name', 
            'lastName' => 'Emp. Last Name', 
            'jobTitle' => 'Job Title', 
            'employeeStatus' => 'Employment Status', 
            'subDivision' => 'Sub-Division', 
            'supervisor' => 'Supervisor', 
        );
            
    public function configure() {

        $choices = array('-1'=> '- Select -') + self::$searchFields;

        $this->setWidgets(array(
            'search_by' => new sfWidgetFormSelect(array('choices' => $choices)),
            'search_for' => new sfWidgetFormInputText(),
        ));
        
        $this->widgetSchema->setNameFormat('empsearch[%s]');
        
        $this->setValidators(array(
            'search_by' => new sfValidatorChoice(
                                array(
            						'choices' => array_keys(self::$searchFields),
                                	'required' => true),
                                array('required' => 'Please select a field to search',
                                      'invalid' => 'Please select a field to search')),
            'search_for' => new sfValidatorString(array('required' => false)),
        ));
    }
    
}

