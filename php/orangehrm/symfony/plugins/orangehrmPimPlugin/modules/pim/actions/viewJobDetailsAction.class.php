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

/**
 * ViewJobDetailsAction
 */
class viewJobDetailsAction extends basePimAction {

    public function execute($request) {
        
        $loggedInEmpNum = $this->getUser()->getEmployeeNumber();
        $loggedInUserName = $_SESSION['fname'];
        
        $job = $request->getParameter('job');
        $empNumber = (isset($job['emp_number'])) ? $job['emp_number']: $request->getParameter('empNumber');
        $this->empNumber = $empNumber;

        $this->ownRecords = ($loggedInEmpNum == $empNumber)?true:false;

        $adminMode = $this->getUser()->hasCredential(Auth::ADMIN_ROLE);
        $supervisorMode = $this->isSupervisor($loggedInEmpNum, $empNumber);

        $this->essMode = false;
        if (!$supervisorMode && !$adminMode) {
            if ($empNumber == $loggedInEmpNum) {
                $this->essMode = true;
            } else {
                $this->getUser()->setFlash('templateMessage', array('warning', __('Access Denied!')));
                $this->redirect($this->getRequest()->getReferer());
                return;
            }
        }
                       
        if ($this->getUser()->hasFlash('templateMessage')) {
            list($this->messageType, $this->message) = $this->getUser()->getFlash('templateMessage');
        }
        
        $employee = $this->getEmployeeService()->getEmployee($empNumber);
        $param = array('empNumber' => $empNumber, 'ESS' => $this->essMode,
                       'employee' => $employee,
                       'loggedInUser' => $loggedInEmpNum,
                       'loggedInUserName' => $loggedInUserName);
        
        $this->form = new EmployeeJobDetailsForm(array(), $param, true);

        if ($this->getRequest()->isMethod('post')) {

            // Handle the form submission           
            $this->form->bind($request->getParameter($this->form->getName()), 
                    $request->getFiles($this->form->getName()));

            if ($this->form->isValid()) {

                // validate either ADMIN, supervisor for employee or employee himself
                // save data
                $service = new EmployeeService();
                $service->saveJobDetails($this->form->getEmployee(), false);
                $this->form->updateAttachment();
                
                
                $this->getUser()->setFlash('templateMessage', array('success', __('Job Details Updated Successfully')));  
            } else {
                $validationMsg = '';
                foreach($this->form->getWidgetSchema()->getPositions() as $widgetName) {
                    if($this->form[$widgetName]->hasError()) {
                        $validationMsg .= __($this->form[$widgetName]->getError()->getMessageFormat());
                    }
                }

                $this->getUser()->setFlash('templateMessage', array('warning', $validationMsg));
            }
            
            $this->redirect('pim/viewJobDetails?empNumber=' . $empNumber);
        }

    }


}
