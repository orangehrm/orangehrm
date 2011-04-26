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
 * Actions class for PIM module updateDependentAction
 */

class updateCustomFieldsAction extends sfAction {

    /**
     * Add / update employee customFields
     *
     * @param int $empNumber Employee number
     *
     * @return boolean true if successfully assigned, false otherwise
     */
    public function execute($request) {
        
        // this should probably be kept in session?
        $screen = $request->getParameter('screen');

        $customFieldsService = new CustomFieldsService();
        $customFieldList = $customFieldsService->getCustomFieldList($screen);

        $this->form = new EmployeeCustomFieldsForm(array(), array('customFields'=>$customFieldList), true);

        if ($this->getRequest()->isMethod('post')) {


            // Handle the form submission
            $this->form->bind($request->getPostParameters());

            if ($this->form->isValid()) {

                // validate either ADMIN, supervisor for employee or employee himself
                // save data

                $this->form->save();
                $this->getUser()->setFlash('templateMessage', array('success', __('Custom Fields Updated Successfully')));                
            } else {
                $this->getUser()->setFlash('templateMessage', array('warning', __('Custom Fields Form Validation Failed.')));
            }
        }                    

                    
        $empNumber = $request->getParameter('empNumber');

        $this->redirect('pim/viewPersonalDetails?empNumber=' . $empNumber);
    }

}
