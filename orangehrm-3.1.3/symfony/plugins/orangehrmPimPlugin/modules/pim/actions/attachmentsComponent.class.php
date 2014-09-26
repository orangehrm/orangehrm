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

class attachmentsComponent extends sfComponent {

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
     * Execute method of component
     * 
     * @param type $request 
     */
    public function execute($request) {       

        $this->attEditPane = false;
        $this->attSeqNO = false;
        $this->attComments = '';
        $this->scrollToAttachments = false;
        
        $this->permission = $this->getDataGroupPermissions($this->screen . '_attachment', $this->empNumber);        

        if ($this->getUser()->hasFlash('attachmentMessage')) {  
            
            $this->scrollToAttachments = true;
            
            list($this->attachmentMessageType, $this->attachmentMessage) = $this->getUser()->getFlash('attachmentMessage');
                       
            if ($this->attachmentMessageType == 'warning') {
                $this->attEditPane = true;
                if ( $this->getUser()->hasFlash('attachmentComments') ) {
                    $this->attComments = $this->getUser()->getFlash('attachmentComments');
                }
                
                if ( $this->getUser()->hasFlash('attachmentSeqNo')) {
                    $tmpNo = $this->getUser()->getFlash('attachmentSeqNo');
                    $tmpNo = trim($tmpNo);
                    if (!empty($tmpNo)) {
                        $this->attSeqNO = $tmpNo;
                    }
                }
            }
        } else {
            $this->attachmentMessageType = '';
            $this->attachmentMessage = '';
        }

        
        $this->employee = $this->getEmployeeService()->getEmployee($this->empNumber);
        $this->attachmentList = $this->getEmployeeService()->getEmployeeAttachments($this->empNumber, $this->screen);          
        $this->form = new EmployeeAttachmentForm(array(),  array(), true);  
        $this->deleteForm = new EmployeeAttachmentDeleteForm(array(), array(), true);
    }

    protected function getDataGroupPermissions($dataGroups, $empNumber) { 
        $loggedInEmpNum = $this->getUser()->getEmployeeNumber();
        
        $entities = array('Employee' => $empNumber);
        
        $self = $empNumber == $loggedInEmpNum;
        
         return $this->getContext()->getUserRoleManager()->getDataGroupPermissions($dataGroups, array(), array(), $self, $entities);
    }
}

