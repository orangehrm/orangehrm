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
class updateAttachmentAction extends basePimAction {

    /**
     * Add / update employee attachment
     *
     * @param int $empNumber Employee number
     *
     * @return boolean true if successfully assigned, false otherwise
     */
    public function execute($request) {

        $loggedInEmpNum = $this->getUser()->getEmployeeNumber();
        $loggedInUserName = $_SESSION['fname'];

        $this->form = new EmployeeAttachmentForm(array(),
                        array('loggedInUser' => $loggedInEmpNum,
                            'loggedInUserName' => $loggedInUserName), true);

        if ($this->getRequest()->isMethod('post')) {

            $attachId = $request->getParameter('seqNO');
            $screen = $request->getParameter('screen');
            
            $permission = $this->getDataGroupPermissions($screen. '_attachment', $request->getParameter('EmpID'));

            if ((empty($attachId) && $permission->canCreate()) || (!empty($attachId) && $permission->canUpdate())) {

                // Handle the form submission
                $this->form->bind($request->getPostParameters(), $request->getFiles());

                if ($this->form->isValid()) {

                    $empNumber = $this->form->getValue('EmpID');
                    if (!$this->IsActionAccessible($empNumber)) {
                        $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
                    }

                    $this->form->save();
                    $this->getUser()->setFlash('listAttachmentPane.success', __(TopLevelMessages::SAVE_SUCCESS));
                } else {

                    $validationMsg = '';
                    foreach ($this->form->getWidgetSchema()->getPositions() as $widgetName) {
                        if ($this->form[$widgetName]->hasError()) {
                            $validationMsg .= __(TopLevelMessages::FILE_SIZE_SAVE_FAILURE);
                        }
                    }

                    $this->getUser()->setFlash('saveAttachmentPane.warning',$validationMsg);
                    $this->getUser()->setFlash('attachmentComments', $request->getParameter('txtAttDesc'));
                    $this->getUser()->setFlash('attachmentSeqNo', $request->getParameter('seqNO'));
                }
            } else {
                $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action')); 
            }
        }

        $this->redirect($this->getRequest()->getReferer() . '#attachments');
    }

}
