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
 *
 */

class EducationForm extends BaseForm {
    
    private $educationService;
    
    public function getEducationService() {
        
        if (!($this->educationService instanceof EducationService)) {
            $this->educationService = new EducationService();
        }
        
        return $this->educationService;
    }

    public function setEducationService($educationService) {
        $this->educationService = $educationService;
    }

    public function configure() {

        $this->setWidgets(array(
            'id' => new sfWidgetFormInputHidden(),
            'name' => new sfWidgetFormInputText()
        ));

        $this->setValidators(array(
            'id' => new sfValidatorNumber(array('required' => false)),
            'name' => new sfValidatorString(array('required' => true, 'max_length' => 120))
        ));

        $this->widgetSchema->setNameFormat('education[%s]');

        $this->setDefault('id', '');
	}
    
    public function save() {
        
        $id = $this->getValue('id');
        
        if (empty($id)) {
            $education = new Education();
            $message = array('messageType' => 'success', 'message' => __(TopLevelMessages::SAVE_SUCCESS));
        } else {
            $education = $this->getEducationService()->getEducationById($id);
            $message = array('messageType' => 'success', 'message' => __(TopLevelMessages::UPDATE_SUCCESS));
        }
        
        $education->setName($this->getValue('name'));
        $this->getEducationService()->saveEducation($education);        
        
        return $message;
        
    }

}
