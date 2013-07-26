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
 * Action class for PIM module delete memberships
 *
 */
class deleteMembershipsAction extends basePimAction {

    /**
     * Delete employee memberships
     *
     * @param int $empNumber Employee number
     *
     * @return boolean true if successfully deleted, false otherwise
     */
    public function execute($request) {

        $empNumber = $request->getParameter('empNumber', false);
        $this->form = new EmployeeMembershipsDeleteForm(array(), array('empNumber' => $empNumber), true);

        $this->form->bind($request->getParameter($this->form->getName()));
        $membershipPermissions = $this->getDataGroupPermissions('membership', $empNumber);
        
        if ($membershipPermissions->canDelete()) {
            if ($this->form->isValid()) {

                if (!$empNumber) {
                    throw new PIMServiceException("No Employee ID given");
                }
                
                $selectedRecordIds = $request->getParameter('chkmemdel', array());
                
                if (count($selectedRecordIds) > 0) {

                    $membershipDetails  = $this->_getSelectedMembershipDetails($request->getParameter('chkmemdel', array()));
                    $empNumber          = $membershipDetails[0];
                    $membershipIds      = $membershipDetails[1];  

                    if (!empty($empNumber) && !empty($membershipIds)) {

                        $service = new EmployeeService();
                        $service->deleteEmployeeMemberships($empNumber, $membershipIds);
                        $this->getUser()->setFlash('memberships.success', __(TopLevelMessages::DELETE_SUCCESS));

                    }
                }

            }
        }
        $this->redirect('pim/viewMemberships?empNumber=' . $empNumber);
        
    }
    
    private function _getSelectedMembershipDetails($records) {
        
        $empNumber = null;
        $membershipIds = array();
        
        foreach ($records as $record) {
            
            $items = explode(" ", $record);
            
            $empNumber = trim($items[0]);
            $membershipIds[] = trim($items[1]);
            
        }
        
        return array($empNumber, $membershipIds);
        
    }

}
