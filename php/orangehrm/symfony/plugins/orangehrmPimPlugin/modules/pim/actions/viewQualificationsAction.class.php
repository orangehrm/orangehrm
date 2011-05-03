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

        if (!$this->isAdminSupervisorOrEssUser($empNumber)) {
            $this->getUser()->setFlash('templateMessage', array('warning', __('Access Denied!')));
            $this->redirect($this->getRequest()->getReferer());
            return;
        }
        
        if ($this->getUser()->hasFlash('templateMessage')) {
            list($this->messageType, $this->message) = $this->getUser()->getFlash('templateMessage');
        }

        $this->setWorkExperienceForm(new WorkExperienceForm(array(), array('empNumber' => $empNumber), true));
        $this->setEducationForm(new EmployeeEducationForm(array(), array('empNumber' => $empNumber), true));
        $this->setSkillForm(new EmployeeSkillForm(array(), array('empNumber' => $empNumber), true));
        $this->setLanguageForm(new EmployeeLanguageForm(array(), array('empNumber' => $empNumber), true));
        $this->setLicenseForm(new EmployeeLicenseForm(array(), array('empNumber' => $empNumber), true));        
    }
}
?>