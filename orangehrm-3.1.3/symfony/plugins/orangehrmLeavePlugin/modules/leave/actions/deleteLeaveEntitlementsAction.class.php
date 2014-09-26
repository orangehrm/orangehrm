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
 *
 */

/**
 * Description of deleteLeaveEntitlementsAction
 */
class deleteLeaveEntitlementsAction extends sfAction {
    
    protected $leaveEntitlementService;
    
    public function getLeaveEntitlementService() {
        if (empty($this->leaveEntitlementService)) {
            $this->leaveEntitlementService = new LeaveEntitlementService();
        }
        return $this->leaveEntitlementService;
    }

    public function setLeaveEntitlementService($leaveEntitlementService) {
        $this->leaveEntitlementService = $leaveEntitlementService;
    }

    // protected by screen permissions.     
    public function execute($request) {
        $form = new DefaultListForm();
        $form->bind($request->getParameter($form->getName()));
        $ids = $request->getParameter('chkSelectRow');
        
        if (count($ids) > 0) {
            try{
                if ($form->isValid()) {
                    $this->getLeaveEntitlementService()->deleteLeaveEntitlements($ids);
                    $this->getUser()->setFlash('success', __(TopLevelMessages::DELETE_SUCCESS));
                }
            }catch(Exception $e){
                $this->getUser()->setFlash('warning.nofade',$e->getMessage());
            }
        } else {
            $this->getUser()->setFlash('warning', __(TopLevelMessages::SELECT_RECORDS));
            
        }
        
        $this->redirect('leave/viewLeaveEntitlements?savedsearch=1');
    }
}
