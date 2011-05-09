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
class saveDeleteEducationAction extends basePimAction {
    
    /**
     * @param sfForm $form
     * @return
     */
    public function setEducationForm(sfForm $form) {
        if (is_null($this->educationForm)) {
            $this->educationForm = $form;
        }
    }
    
    public function execute($request) {

        $education = $request->getParameter('education');
        $empNumber = (isset($education['emp_number']))?$education['emp_number']:$request->getParameter('empNumber');

        if (!$this->isAdminSupervisorOrEssUser($empNumber)) {
            $this->getUser()->setFlash('templateMessage', array('warning', __('Access Denied!')));
            $this->redirect($this->getRequest()->getReferer());
            return;
        }
        
        $this->setEducationForm(new EmployeeEducationForm(array(), array('empNumber' => $empNumber), true));

        if ($request->isMethod('post')) {
            if ( $request->getParameter('option') == "save") {

                $this->educationForm->bind($request->getParameter($this->educationForm->getName()));

                if ($this->educationForm->isValid()) {
                    $education = $this->getEducation($this->educationForm);
                    $this->getEmployeeService()->saveEducation($education);
                    $this->getUser()->setFlash('templateMessage', array('success', __('Education Details Saved Successfully')));
                } else {
                    $this->getUser()->setFlash('templateMessage', array('warning', __('Form Validation Failed.')));
                }
            }

            //this is to delete 
            if ($request->getParameter('option') == "delete") {
                $deleteIds = $request->getParameter('delEdu');

                if(count($deleteIds) > 0) {
                    $this->getEmployeeService()->deleteEducation($empNumber, $request->getParameter('delEdu'));
                    $this->getUser()->setFlash('templateMessage', array('success', __('Education Detail(s) Deleted Successfully')));
                }
            }
        }
        $this->getUser()->setFlash('qualificationSection', 'education');
        $this->redirect('pim/viewQualifications?empNumber='. $empNumber . '#education');
    }

    private function getEducation(sfForm $form) {

        $post = $form->getValues();

        $education = $this->getEmployeeService()->getEducation($post['emp_number'], $post['code']);

        if(!$education instanceof EmployeeEducation) {
            $education = new EmployeeEducation();
        }

        $education->emp_number = $post['emp_number'];
        $education->code = $post['code'];
        $education->major = $post['major'];
        $education->year = $post['year'];
        $education->gpa = $post['gpa'];
        $education->start_date = $post['start_date'];
        $education->end_date = $post['end_date'];

        return $education;
    }
}
?>