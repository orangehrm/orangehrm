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
class saveDeleteSkillAction extends basePimAction {

    /**
     * @param sfForm $form
     * @return
     */
    public function setSkillForm(sfForm $form) {
        if (is_null($this->skillForm)) {
            $this->skillForm = $form;
        }
    }

    public function execute($request) {
        $form = new DefaultListForm();
        $form->bind($request->getParameter($form->getName()));
        $skill = $request->getParameter('skill');
        $empNumber = (isset($skill['emp_number'])) ? $skill['emp_number'] : $request->getParameter('empNumber');

        if (!$this->IsActionAccessible($empNumber)) {
            $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
        }

        $this->skillPermissions = $this->getDataGroupPermissions('qualification_skills', $empNumber);

        $this->setSkillForm(new EmployeeSkillForm(array(), array('empNumber' => $empNumber, 'skillPermissions' => $this->skillPermissions), true));

        if ($request->isMethod('post')) {
            if ($request->getParameter('option') == "save") {
                if ($this->skillPermissions->canCreate() || $this->skillPermissions->canUpdate()) {

                    $this->skillForm->bind($request->getParameter($this->skillForm->getName()));

                    if ($this->skillForm->isValid()) {
                        $skill = $this->getSkill($this->skillForm);
                        $this->getEmployeeService()->saveEmployeeSkill($skill);
                        $this->getUser()->setFlash('skill.success', __(TopLevelMessages::SAVE_SUCCESS));
                    } else {
                        $this->getUser()->setFlash('skill.warning', __('Form Validation Failed'));
                    }
                }
            }

            //this is to delete 
            if ($request->getParameter('option') == "delete") {
                if ($this->skillPermissions->canDelete()) {
                    $deleteIds = $request->getParameter('delSkill');

                    if (count($deleteIds) > 0) {
                        if ($form->isValid()) {
                            $this->getEmployeeService()->deleteEmployeeSkills($empNumber, $request->getParameter('delSkill'));
                            $this->getUser()->setFlash('skill.success', __(TopLevelMessages::DELETE_SUCCESS));
                        }
                    }
                }
            }
        }
        $this->getUser()->setFlash('qualificationSection', 'skill');
        $this->redirect('pim/viewQualifications?empNumber=' . $empNumber . '#skill');
    }

    private function getSkill(sfForm $form) {

        $post = $form->getValues();

        $skill = $this->getEmployeeService()->getEmployeeSkills($post['emp_number'], $post['code']);

        if (!$skill instanceof EmployeeSkill) {
            $skill = new EmployeeSkill();
        }

        $skill->emp_number = $post['emp_number'];
        $skill->skillId = $post['code'];
        $skill->years_of_exp = $post['years_of_exp'];
        $skill->comments = $post['comments'];

        return $skill;
    }

}