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
 * Form class for Custom fields
 */
class CustomFieldForm extends BaseForm {

    
    public function configure() {
        
        $screens = $this->getScreens();
        
        $types = $this->getFieldTypes();
    
        $this->setWidgets(array(
            'field_num' => new sfWidgetFormInputHidden(),
            'name' => new sfWidgetFormInputText(),
            'type' => new sfWidgetFormSelect(array('choices' => $types)),
            'screen' => new sfWidgetFormSelect(array('choices' => $screens)),            
            'extra_data' => new sfWidgetFormInputText(),
        ));

        //
        // Remove default -- select -- option from valid values
        unset($types['']);
        unset($screens['']);
        
        $this->setValidators(array(
            'field_num' => new sfValidatorNumber(array('required' => false, 'min'=> 1, 'max'=>10)),
            'name' => new sfValidatorString(array('required' => true, 'max_length'=>250)),
            'type' => new sfValidatorChoice(array('choices' => array_keys($types))),
            'screen' => new sfValidatorChoice(array('choices' => array_keys($screens))),
            'extra_data' => new sfValidatorString(array('required' => false, 'trim'=>true, 'max_length'=>250))
        ));
       
        $this->widgetSchema->setNameFormat('customField[%s]');
    }
    
    public function getFieldTypes() {
        $types = array('' => '-- ' . __('Select') . ' --',
                      CustomFields::FIELD_TYPE_STRING => __('Text or Number'),
                      CustomFields::FIELD_TYPE_SELECT => __('Drop Down'));        
        
        return $types;
    }
    
    public function getScreens() {
        $screens = array('' =>  '-- ' . __('Select') . ' --',
                             'personal'=> __('Personal Details'),
                             'contact' => __('Contact Details'),
                             'emergency' => __('Emergency Contacts'),
                             'dependents' => __('Dependents'),
                             'immigration' => __('Immigration'),
                             'Qualifications' => __('Qualifications'),
                             'taxexemptions' => __('Tax Exemptions'),
                             'salary' => __('Salary'));
        return $screens;
    }

}