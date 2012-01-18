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
 * define a holiday
 */
class defineHolidayAction extends sfAction {

    private $holidayService;
    private $leavePeriodService; 
           

    /**
     * Returns Leave Period
     * @return LeavePeriodService
     */
    public function getLeavePeriodService() {

        if (is_null($this->leavePeriodService)) {
            $leavePeriodService = new LeavePeriodService();
            $leavePeriodService->setLeavePeriodDao(new LeavePeriodDao());
            $this->leavePeriodService = $leavePeriodService;
        }

        return $this->leavePeriodService;
    }
    
    /**
     * Returns Leave Period
     * @return LeavePeriodService
     */
    public function setLeavePeriodService($leavePeriodService) {
        $this->leavePeriodService = $leavePeriodService;
    }    

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
    
    public function preExecute() {
        if ($this->getUser()->getAttribute('auth.isAdmin') != 'Yes') {
            $this->redirect('leave/viewMyLeaveList');
        }
    }    
    
    /**
     * Add Holiday
     * @param sfWebRequest $request
     */
    public function execute($request) {

        $this->form = $this->getForm();
        $editId = $request->getParameter('hdnEditId');

        $this->editMode = false; // pass edit mode for teh view
        $this->form->editMode = false; // pass edit mode for form

        if ($editId && $editId != "") {
            $this->form->setDefaultValues($editId);
        }

        if (($editId && $editId != "") || $request->getParameter('hdnEditMode') == 'yes') {
            $this->editMode = true;
            $this->form->editMode = true;
        }

        if ($request->isMethod('post')) {

            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $post = $this->form->getValues();
                // save holiday

                if ($post['hdnHolidayId'] != "") {
                    $this->getUser()->setFlash('templateMessage', array('SUCCESS', __('Holiday Successfully Updated')));
                } else {
                    $this->getUser()->setFlash('templateMessage', array('SUCCESS', __('Holiday Successfully Saved')));
                }

                $date = $post['txtDate'];
                $hid  = $post['hdnHolidayId'];
                
                // read the holiday by date
                $holidayObjectDate = $this->getHolidayService()->readHolidayByDate($date);

                $allowToAdd = true;

                if($this->editMode) {
                    $holidayObject = $this->getHolidayService()->readHoliday($hid);
                    // if the selected date is already in a holiday not allow to add
                    /*if(($holidayObject->getDate() != $date && $date == $holidayObjectDate->getDate()) || $holidayObjectDate->getRecurring() == 1) {
                      $allowToAdd = false;
                    }*/
                    if($date != $holidayObjectDate->getDate() && $holidayObjectDate->getRecurring()) {
                        $allowToAdd = false;
                    }
                } else {
                    // days already added can not be selected to add
                    if($date == $holidayObjectDate->getDate() || $holidayObjectDate->getRecurring() == 1) {
                        $allowToAdd = false;
                    }

                }

                // Error will not return if the date if not in the correct format
                if(!$allowToAdd && !is_null($date)) {
                    $this->templateMessage = array('WARNING', __('The Date Is Already Assigned to Another Holiday'));
                } else {

                    //first creating the leave period if the date belongs to next leave period
                    if($this->getLeavePeriodService()->isWithinNextLeavePeriod(strtotime($post['txtDate']))) {
                        $this->getLeavePeriodService()->createNextLeavePeriod($post['txtDate']);
                    }

                    $holidayObject = $this->getHolidayService()->readHoliday($post['hdnHolidayId']);
                    $holidayObject->setDescription($post['txtDescription']);
                    $holidayObject->setDate($post['txtDate']);

                    $recurringValue = $post['chkRecurring'] == 'on' ? 1 : 0;
                    $holidayObject->setRecurring($recurringValue);

                    $holidayObject->setLength($post['selLength']);
                    $this->getHolidayService()->saveHoliday($holidayObject);
                    $this->redirect('leave/viewHolidayList');
                }
            }
        }
    }
    
    /**
     * Get form object 
     * @return
     */
    public function getForm() {
        return new HolidayForm();
    }    

}
