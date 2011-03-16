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
class addEmployeeAction extends sfAction {

    private $employeeService;
    private $userService;

    /**
     * @param sfForm $form
     * @return
     */
    public function setForm(sfForm $form) {
        if (is_null($this->form)) {
            $this->form = $form;
        }
    }

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

    public function execute($request) {

        $this->showBackButton = true;
        $loggedInEmpNum = $this->getUser()->getEmployeeNumber();
        $adminMode = $this->getUser()->hasCredential(Auth::ADMIN_ROLE);

        if(!$adminMode) {
            //shud b redirected 2 ESS user view
            $this->redirect('pim/viewPersonalDetails?empNumber='. $loggedInEmpNum);
        }

        $this->setForm(new AddEmployeeForm(array(), array(), true));

        if ($this->getUser()->hasFlash('templateMessage')) {
            list($this->messageType, $this->message) = $this->getUser()->getFlash('templateMessage');
        }

        if ($request->isMethod('post')) {

            $this->form->bind($request->getPostParameters(), $request->getFiles());
            $photoFile = $request->getFiles();

            //in case if file size exceeds 1MB
            if($photoFile['photofile']['name'] != "" && ($photoFile['photofile']['size'] == 0 || $photoFile['photofile']['size'] > 1000000)) {
                $this->getUser()->setFlash('templateMessage', array('warning', __('Adding Employee Failed Due to Photograph Size Exceeded 1MB')));
                $this->redirect('pim/addEmployee');
            }

            //in case a user already exists with same user name
            $posts = $this->form->getValues();
            $userService = $this->getUserService();
            $user = $userService->getUserByUserName($posts['user_name']);

            if($user instanceof Users) {

                $this->getUser()->setFlash('templateMessage', array('warning', __('Adding Employee Failed. User Name Already Exists')));
                
            }

            //if everything seems ok save employee and create a user account
            if ($this->form->isValid()) {
                
                try {
                    $fileType = $photoFile['photofile']['type'];
                    
                    if($fileType != "image/gif" && $fileType != "image/jpeg" && $fileType != "image/jpg" && $fileType != "image/png" && $fileType != "image/pjpeg") {

                        $this->getUser()->setFlash('templateMessage', array('warning', __('Only Types jpg, png, and gif Are Supported')));
                        $this->redirect('pim/addEmployee');
                        
                    } else {
                        $empNumber = $this->saveEmployee($this->form);
                        $this->saveUser($this->form, $empNumber);
                        $this->redirect('pim/viewPersonalDetails?empNumber='. $empNumber);
                    }

                } catch(Exception $e) {
                    print($e->getMessage());
                }
            }
        }
    }

    private function saveEmployee(sfForm $form) {

        $posts = $form->getValues();
        $file = $posts['photofile'];

        //saving employee
        $employee = new Employee();
        $employee->firstName = $posts['firstName'];
        $employee->lastName = $posts['lastName'];
        $employee->middleName = $posts['middleName'];
        $employee->employeeId = $posts['employeeId'];

        $employeeService = $this->getEmployeeService();
        $employeeService->addEmployee($employee);

        $empNumber = $employee->empNumber;

        //saving emp picture
        if(($file instanceof sfValidatedFile) && $file->getOriginalName() != "") {
            $empPicture = new EmpPicture();
            $empPicture->emp_number = $empNumber;
            $tempName = $file->getTempName();

            $empPicture->picture = file_get_contents($tempName);
            ;
            $empPicture->filename = $file->getOriginalName();
            $empPicture->file_type = $file->getType();
            $empPicture->size = $file->getSize();
            $empPicture->save();
        }

        return $empNumber;
    }

    private function saveUser(sfForm $form, $empNumber) {

        $posts = $form->getValues();

        if(trim($posts['user_name']) != "") {
            $userService = $this->getUserService();

            if(trim($posts['user_password']) != "" && $posts['user_password'] == $posts['re_password']) {
                $user = new Users();
                $user->user_name = $posts['user_name'];
                $user->user_password = md5($posts['user_password']);
                $user->emp_number = $empNumber;
                $user->status = $posts['status'];
                $user->created_by = "USR001";
                $user->is_admin = "No";
                $user->date_entered = date("Y-m-d");
                $userService->saveUser($user);
            }
        }
    }

    private function getUserService() {

        if(is_null($this->userService)) {
            $this->userService = new UserService();
        }

        return $this->userService;
    }
}
?>
