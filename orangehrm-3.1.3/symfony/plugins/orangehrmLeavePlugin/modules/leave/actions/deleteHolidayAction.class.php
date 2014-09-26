<?php

/*
 *
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
 * delete holiday(s)
 */
class deleteHolidayAction extends baseLeaveAction {

    private $holidayService;

    /**
     * get Method for Holiday Service
     *
     * @return HolidayService $holidayService
     */
    public function getHolidayService() {
        if (is_null($this->holidayService)) {
            $this->holidayService = new HolidayService();
        }
        return $this->holidayService;
    }

    /**
     * Set HolidayService
     * @param HolidayService $holidayService
     */
    public function setHolidayService(HolidayService $holidayService) {
        $this->holidayService = $holidayService;
    }

    /**
     * view Holiday list
     * @param sfWebRequest $request
     */
    public function execute($request) {

        $holidayPermissions = $this->getDataGroupPermissions('holidays');

        if ($holidayPermissions->canDelete()) {            

            $holidayIds = $request->getPostParameter('chkSelectRow[]');            
            if (!empty($holidayIds)) {

                $form = new DefaultListForm() ;
                $form->bind($request->getParameter($form->getName()));  
                
                if ($form->isValid()) {
                    foreach ($holidayIds as $key => $id) {
                        $this->getHolidayService()->deleteHoliday($id);
                    }
                    $this->getUser()->setFlash('success', __(TopLevelMessages::DELETE_SUCCESS));                    
                } else {
                    $this->getUser()->setFlash('warning', __(TopLevelMessages::ACCESS_DENIED));
                }
            } else {
                $this->getUser()->setFlash('warning', __(TopLevelMessages::SELECT_RECORDS));
            }

            $this->redirect('leave/viewHolidayList');
        }
    }

}
