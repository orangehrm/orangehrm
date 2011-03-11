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
class viewPhotographAction extends sfAction {

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
    public function setForm(sfForm $form) {
        if (is_null($this->form)) {
            $this->form = $form;
        }
    }
    
    public function execute($request) {

        $loggedInEmpNum = $this->getUser()->getEmployeeNumber();
        $this->showBackButton = true;
        $picture = $request->getPostParameters();
        $empNumber = (isset($picture['emp_number']))?$picture['emp_number']:$request->getParameter('empNumber');
        $this->empNumber = $empNumber;

        //hiding the back button if its self ESS view
        if($loggedInEmpNum == $empNumber) {
            
            $this->showBackButton = false;
        }

        if ($this->getUser()->hasFlash('templateMessage')) {
            list($this->messageType, $this->message) = $this->getUser()->getFlash('templateMessage');
        }

        //as part of making users childish by hiding delete button
        $employeeService = $this->getEmployeeService();
        $empPicture = $employeeService->getPicture($empNumber);
        $this->showDeleteButton = 1;
        
        if(!$empPicture instanceof EmpPicture) {
            $this->showDeleteButton = 0;
        }

        $param = array('empNumber' => $empNumber);
        $this->setForm(new EmployeePhotographForm(array(), $param, true));

        //this is for saving a picture
        if ($request->isMethod('post')) {

            $this->form->bind($request->getPostParameters(), $request->getFiles());
            $photoFile = $request->getFiles();

            //in case if file size exceeds 1MB
            if($photoFile['photofile']['size'] == 0) {
                $this->getUser()->setFlash('templateMessage', array('warning', __('Photograph Size Should Be Less Than 1MB')));
            }
            
            if ($this->form->isValid()) {

                $this->saveEmployeePicture($empNumber, $photoFile);
                $this->getUser()->setFlash('templateMessage', array('success', __('Employee Photograph Uploaded Successfully')));
                
            }
            $this->redirect("pim/viewPhotograph?empNumber=" . $empNumber);
        }

        //this is for deleting a picture
        if($request->getParameter('option') == "delete") {

            $employeeService = $this->getEmployeeService();
            $employeeService->deletePhoto($empNumber);
            $this->getUser()->setFlash('templateMessage', array('success', __('Employee Photograph Deleted Successfully')));
            $this->redirect("pim/viewPhotograph?empNumber=" . $empNumber);
        }
    }

    private function saveEmployeePicture($empNumber, $file) {

        $employeeService = $this->getEmployeeService();
        $empPicture = $employeeService->getPicture($empNumber);

        if(!$empPicture instanceof EmpPicture) {
            $empPicture = new EmpPicture();
            $empPicture->emp_number = $empNumber;
        }

        $empPicture->picture = file_get_contents($file['photofile']['tmp_name']);
        $empPicture->filename = $file['photofile']['name'];
        $empPicture->file_type = $file['photofile']['type'];
        $empPicture->size = $file['photofile']['size'];
        $empPicture->save();
        
    }
}
?>
