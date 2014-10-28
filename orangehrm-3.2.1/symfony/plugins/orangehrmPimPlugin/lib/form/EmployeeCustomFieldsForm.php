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
 * Form class for employee personal details
 */
class EmployeeCustomFieldsForm extends BaseForm {

    public function configure() {

        $customFields = $this->getOption('customFields', false);

        $this->setWidget('EmpID', new sfWidgetFormInputHidden());
        $this->setValidator('EmpID', new sfValidatorInteger(array('required' => true, 'min'=>0)));
		
       
        foreach ($customFields as $customField) {
            $fieldName = "custom" . $customField->getId();

            if ($customField->type == CustomField::FIELD_TYPE_SELECT) {

                $options = $customField->getOptions();
                $this->setWidget($fieldName, new sfWidgetFormSelect(array('choices'=>$options)));
                $this->setValidator($fieldName, new sfValidatorChoice(array('required' => false,
                                       'trim'=>true, 'choices'=>$options)));
            } else {
                $this->setWidget($fieldName, new sfWidgetFormInputText());
                $this->setValidator($fieldName, new sfValidatorString(array('required' => false, 'max_length' => 250)));
            }
        }
        
    }

    /**
     * Save employee custom fields
     */
    public function save() {

        $values = $this->getValues();
        $empNumber = $values['EmpID'];

        unset($values['EmpID']);

        try {
            $q = Doctrine_Query::create()
                ->update('Employee')
                ->set($values, array())
                ->where('empNumber = ?', $empNumber);

            $result = $q->execute();

        } catch (Exception $e) {
            throw new PIMServiceException($e->getMessage());
        }
    }


}

