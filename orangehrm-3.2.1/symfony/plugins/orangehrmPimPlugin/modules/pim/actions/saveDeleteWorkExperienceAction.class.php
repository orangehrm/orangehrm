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
class saveDeleteWorkExperienceAction extends basePimAction {

    /**
     * @param sfForm $form
     * @return
     */
    public function setWorkExperienceForm(sfForm $form) {
        if (is_null($this->workExperienceForm)) {
            $this->workExperienceForm = $form;
        }
    }

    public function execute($request) {
        $form = new DefaultListForm();
        $form->bind($request->getParameter($form->getName()));
        $experience = $request->getParameter('experience');
        $empNumber = (isset($experience['emp_number'])) ? $experience['emp_number'] : $request->getParameter('empNumber');

        if (!$this->IsActionAccessible($empNumber)) {
            $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
        }

        $this->workExperiencePermissions = $this->getDataGroupPermissions('qualification_work', $empNumber);

        $this->setWorkExperienceForm(new WorkExperienceForm(array(), array('empNumber' => $empNumber, 'workExperiencePermissions' => $this->workExperiencePermissions), true));

        //this is to save work experience
        if ($request->isMethod('post')) {
            if ($request->getParameter('option') == "save") {
                if ($this->workExperiencePermissions->canCreate() || $this->workExperiencePermissions->canUpdate()) {

                    $this->workExperienceForm->bind($request->getParameter($this->workExperienceForm->getName()));

                    if ($this->workExperienceForm->isValid()) {
                        $workExperience = $this->getWorkExperience($this->workExperienceForm);
                        $this->setOperationName(($workExperience->getSeqno() == '') ? 'ADD WORK EXPERIENCE' : 'CHANGE WORK EXPERIENCE');
                        $this->getEmployeeService()->saveEmployeeWorkExperience($workExperience);
                        $this->getUser()->setFlash('workexperience.success', __(TopLevelMessages::SAVE_SUCCESS));
                    } else {
                        $this->getUser()->setFlash('workexperience.warning', __('Form Validation Failed.'));
                    }
                }
            }

            //this is to delete work experience
            if ($request->getParameter('option') == "delete") {
                if ($this->workExperiencePermissions->canDelete()) {
                    $deleteIds = $request->getParameter('delWorkExp');

                    if (count($deleteIds) > 0) {
                        $this->setOperationName('DELETE WORK EXPERIENCE');
                        if ($form->isValid()) {
                            $this->getEmployeeService()->deleteEmployeeWorkExperienceRecords($empNumber, $request->getParameter('delWorkExp'));
                            $this->getUser()->setFlash('workexperience.success', __(TopLevelMessages::DELETE_SUCCESS));
                        }
                    }
                }
            }
        }
        $this->getUser()->setFlash('qualificationSection', 'workexperience');
        $this->redirect('pim/viewQualifications?empNumber=' . $empNumber . '#workexperience');
    }

    private function getWorkExperience(sfForm $form) {

        $post = $form->getValues();

        $workExperience = $this->getEmployeeService()->getEmployeeWorkExperienceRecords($post['emp_number'], $post['seqno']);

        if (!$workExperience instanceof EmpWorkExperience) {
            $workExperience = new EmpWorkExperience();
        }

        $workExperience->emp_number = $post['emp_number'];
        $workExperience->seqno = $post['seqno'];
        $workExperience->employer = $post['employer'];
        $workExperience->jobtitle = $post['jobtitle'];
        $workExperience->from_date = $post['from_date'];
        $workExperience->to_date = $post['to_date'];
        $workExperience->comments = $post['comments'];

        return $workExperience;
    }

}

?>