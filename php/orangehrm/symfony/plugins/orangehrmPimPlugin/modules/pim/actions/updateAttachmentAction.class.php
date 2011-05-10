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
 * Actions class for PIM module updateAttachmentAction
 */

class updateAttachmentAction extends sfAction {
        
    /**
     * Add / update employee attachment
     *
     * @param int $empNumber Employee number
     *
     * @return boolean true if successfully assigned, false otherwise
     */
    public function execute($request) {
        
        $loggedInEmpNum = $this->getUser()->getEmployeeNumber();
        $loggedInUserName = "Admin";
        
        $this->form = new EmployeeAttachmentForm(array(), 
                array('loggedInUser' => $loggedInEmpNum,
                      'loggedInUserName' => $loggedInUserName), true);

        if ($this->getRequest()->isMethod('post')) {


            // Handle the form submission
            $this->form->bind($request->getPostParameters(), $request->getFiles());

            if ($this->form->isValid()) {

                // validate either ADMIN, supervisor for employee or employee himself
                // save data

                $this->form->save();
                $this->getUser()->setFlash('attachmentMessage', array('success', __('Attachment Updated Successfully')));                
            } else {

                $validationMsg = '';
                foreach($this->form->getWidgetSchema()->getPositions() as $widgetName) {
                    if($this->form[$widgetName]->hasError()) {
                        $validationMsg .= __($this->form[$widgetName]->getError()->getMessageFormat());
                    }
                }

                $this->getUser()->setFlash('attachmentMessage', array('warning', $validationMsg));
                $this->getUser()->setFlash('attachmentComments', $request->getParameter('txtAttDesc'));
                $this->getUser()->setFlash('attachmentSeqNo', $request->getParameter('seqNO'));
            }
        }
       
        $this->redirect($this->getRequest()->getReferer() . '#attachments');
    }

}
