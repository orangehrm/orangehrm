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

        //as part of making users childish by hiding delete button
        $employeeService = $this->getEmployeeService();
        $empPicture = $employeeService->getPicture($empNumber);
        $this->showDeleteButton = 1;
        
        if(!$empPicture instanceof EmpPicture) {
            $this->showDeleteButton = 0;
        }

        $param = array('empNumber' => $empNumber);
        $this->setForm(new EmployeePhotographForm(array(), $param, true));
        $this->fileModify = 0;
        $this->newWidth = 0;
        $this->newHeight = 0;

        //this is for saving a picture
        if ($request->isMethod('post')) {

            $this->form->bind($request->getPostParameters(), $request->getFiles());
            $photoFile = $request->getFiles();

            //in case if file size exceeds 1MB
            if($photoFile['photofile']['size'] == 0 || $photoFile['photofile']['size'] > 1000000) {
                
                $this->messageType = "warning";
                $this->message = __('Upload Failed. File size should be less than IMB');
            }

            if ($this->form->isValid()) {
                
                $fileType = $photoFile['photofile']['type'];

                if($fileType != "image/gif" && $fileType != "image/jpeg" && $fileType != "image/jpg" && $fileType != "image/png" && $fileType != "image/pjpeg") {
                    
                    $this->messageType = "warning";
                    $this->message = __('Only Types jpg, png, and gif Are Supported');
                } else {
                
                    list($width, $height) = getimagesize($photoFile['photofile']['tmp_name']);

                    //flags from server
                    $this->fileModify = 1;
                    $this->showDeleteButton = 1;

                    $this->pictureSizeAdjust($height, $width);
                    $this->saveEmployeePicture($empNumber, $photoFile);
                    $this->messageType = "success";
                    $this->message = __('Employee Photograph Uploaded Successfully');
                }
            }
        }

        //this is for deleting a picture
        if($request->getParameter('option') == "delete") {

            $employeeService = $this->getEmployeeService();
            $employeeService->deletePhoto($empNumber);

            $this->showDeleteButton = 0;
            $this->fileModify = 1;

            //set default picture size
            $this->newWidth = 150;
            $this->newHeight = 176;

            $this->messageType = "success";
            $this->message = __('Employee Photograph Deleted Successfully');
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

    private function pictureSizeAdjust($imgHeight, $imgWidth) {
        
        $newHeight = 0;
        $newWidth = 0;

        //algorithm for image resizing
        //resizing by width - assuming width = 150,
        //resizing by height - assuming height = 180

        $propHeight = floor(($imgHeight/$imgWidth) * 150);
        $propWidth = floor(($imgWidth/$imgHeight) * 180);

        if($propHeight <= 180) {
            $newHeight = $propHeight;
            $newWidth = 150;
        }

        if($propWidth <= 150) {
            $newWidth = $propWidth;
            $newHeight = 180;
        }

        $this->newWidth = $newWidth;
        $this->newHeight = $newHeight;
    }
}
?>
