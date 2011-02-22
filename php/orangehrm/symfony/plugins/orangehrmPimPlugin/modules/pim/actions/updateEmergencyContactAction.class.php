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
 * Actions class for PIM module updateEmergencyContact
 */

class updateEmergencyContactAction extends sfAction {

    /**
     * Add / update employee emergencyContact
     *
     * @param int $empNumber Employee number
     *
     * @return boolean true if successfully assigned, false otherwise
     */
    public function execute($request) {

        $this->form = new EmployeeEmergencyContactForm(array(), array(), true);

        if ($this->getRequest()->isMethod('post')) {

            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $this->form->save();
                $this->getUser()->setFlash('templateMessage', array('success', 'Personal Details Saved Successfully'));
            }
        }

        $empNumber = $request->getParameter('empNumber');

        $this->redirect('pim/viewEmergencyContacts?empNumber='. $empNumber);
    }

}
