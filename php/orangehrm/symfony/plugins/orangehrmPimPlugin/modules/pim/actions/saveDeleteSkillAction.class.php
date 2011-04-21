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
class saveDeleteSkillAction extends sfAction {

    private $employeeService;
    
    /**
     * Get EmployeeService
     * @returns EmployeeService
     */
    public function getEmployeeService() {
        if(is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
            $this->employeeService->setEmployeeDao(new EmployeeDao());
        }
        return $this->employeeService;
    }

    /**
     * Set EmployeeService
     * @param EmployeeService $employeeService
     */
    public function setEmployeeService(EmployeeService $employeeService) {
        $this->employeeService = $employeeService;
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
    
    public function execute($request) {

        $skill = $request->getParameter('skill');
        $empNumber = (isset($skill['emp_number']))?$skill['emp_number']:$request->getParameter('empNumber');

        $this->setSkillForm(new EmployeeSkillForm(array(), array('empNumber' => $empNumber), true));

        if ($request->isMethod('post')) {
            if ( $request->getParameter('option') == "save") {

                $this->skillForm->bind($request->getParameter($this->skillForm->getName()));

                if ($this->skillForm->isValid()) {
                    $skill = $this->getSkill($this->skillForm);
                    $this->getEmployeeService()->saveSkill($skill);
                    $this->getUser()->setFlash('templateMessage', array('success', __('Skill Details Saved Successfully')));
                } else {
                    $this->getUser()->setFlash('templateMessage', array('warning', __('Form Validation Failed.')));
                }
            }

            //this is to delete 
            if ($request->getParameter('option') == "delete") {
                $deleteIds = $request->getParameter('delSkill');

                if(count($deleteIds) > 0) {
                    $this->getEmployeeService()->deleteSkill($empNumber, $request->getParameter('delSkill'));
                    $this->getUser()->setFlash('templateMessage', array('success', __('Skill Details(s) Deleted Successfully')));
                }
            }
        }

        $this->redirect('pim/viewQualifications?empNumber='. $empNumber);
    }

    private function getSkill(sfForm $form) {

        $post = $form->getValues();

        $skill = $this->getEmployeeService()->getSkill($post['emp_number'], $post['code']);

        if(!$skill instanceof EmployeeSkill) {
            $skill = new EmployeeSkill();
        }

        $skill->emp_number = $post['emp_number'];
        $skill->code = $post['code'];
        $skill->years_of_exp = $post['years_of_exp'];
        $skill->comments = $post['comments'];

        return $skill;
    }
}
?>