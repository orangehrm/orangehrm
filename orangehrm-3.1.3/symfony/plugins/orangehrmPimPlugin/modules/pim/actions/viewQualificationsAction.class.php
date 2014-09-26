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
class viewQualificationsAction extends basePimAction {
    
    /**
     * @param sfForm $form
     * @return
     */
    public function setWorkExperienceForm(sfForm $form) {
        if (is_null($this->workExperienceForm)) {
            $this->workExperienceForm = $form;
        }
    }

    /**
     * @param sfForm $form
     * @return
     */
    public function setEducationForm(sfForm $form) {
        if (is_null($this->educationForm)) {
            $this->educationForm = $form;
        }
    }
    
    /**
     * @param sfForm $form
     * @return
     */
    public function setSkillForm(sfForm $form) {
        if (is_null($this->skillForm)) {
            $this->skillForm = $form;
        }
    }    
    
    /**
     * @param sfForm $form
     * @return
     */
    public function setLanguageForm(sfForm $form) {
        if (is_null($this->languageForm)) {
            $this->languageForm = $form;
        }
    } 
    
    /**
     * @param sfForm $form
     * @return
     */
    public function setLicenseForm(sfForm $form) {
        if (is_null($this->licenseForm)) {
            $this->licenseForm = $form;
        }
    } 
    
    public function execute($request) {
        
        $this->showBackButton = false;
        $empNumber = $request->getParameter('empNumber');
        $this->empNumber = $empNumber;
        
        $this->workExperiencePermissions = $this->getDataGroupPermissions('qualification_work', $empNumber);
        $this->educationPermissions = $this->getDataGroupPermissions('qualification_education', $empNumber);
        $this->skillPermissions = $this->getDataGroupPermissions('qualification_skills', $empNumber);
        $this->languagePermissions = $this->getDataGroupPermissions('qualification_languages', $empNumber);
        $this->licensePermissions = $this->getDataGroupPermissions('qualification_license', $empNumber);

        if (!$this->IsActionAccessible($empNumber)) {
            $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
        }
        
        $this->_setMessage();

        $this->setWorkExperienceForm(new WorkExperienceForm(array(), array('empNumber' => $empNumber, 
            'workExperiencePermissions' => $this->workExperiencePermissions), true));
        $this->setEducationForm(new EmployeeEducationForm(array(), array('empNumber' => $empNumber, 
            'educationPermissions' => $this->educationPermissions), true));
        $this->setSkillForm(new EmployeeSkillForm(array(), array('empNumber' => $empNumber, 
            'skillPermissions' => $this->skillPermissions), true));
        $this->setLanguageForm(new EmployeeLanguageForm(array(), array('empNumber' => $empNumber, 
            'languagePermissions' => $this->languagePermissions), true));
        $this->setLicenseForm(new EmployeeLicenseForm(array(), array('empNumber' => $empNumber, 
            'licensePermissions' => $this->licensePermissions), true));  
        
        $this->listForm = new DefaultListForm();
    }
    
    protected function _setMessage() {
        $this->section = '';
        if ($this->getUser()->hasFlash('qualificationSection')) {
            $this->section = $this->getUser()->getFlash('qualificationSection');
        } 
    }
}
?>