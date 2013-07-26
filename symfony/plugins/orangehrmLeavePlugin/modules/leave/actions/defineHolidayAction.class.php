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
class defineHolidayAction extends baseLeaveAction {

    protected $holidayService;
    protected $leavePeriodService;

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

    /**
     * Add Holiday
     * @param sfWebRequest $request
     */
    public function execute($request) {

        //Keep Menu in Leave/Config 
        $request->setParameter('initialActionName', 'viewHolidayList');

        $this->holidayPermissions = $this->getDataGroupPermissions('holidays');

        $editId = $request->getParameter('hdnEditId');
        $this->id = $editId;

        $valuesForForm = array('holidayPermissions' => $this->holidayPermissions, 'holidayId' => $editId);
        $this->form = $this->getForm($valuesForForm);

        $this->editMode = false; // Pass edit mode for teh view
        $this->form->editMode = false; // Pass edit mode for form

        if ($editId && $editId != '') {
            $this->form->setDefaultValues($editId);
        }

        if (($editId && $editId != '') || $request->getParameter('hdnEditMode') == 'yes') {
            $this->editMode = true;
            $this->form->editMode = true;
        }

        if ($request->isMethod('post')) {

            $this->form->bind($request->getParameter($this->form->getName()));
            
            if ($this->holidayPermissions->canCreate() || $this->holidayPermissions->canUpdate()) {

                if ($this->form->isValid()) {
                    $post = $this->form->getValues();
                    /* Save holiday */

                    $date = $post['date'];
                    $holidayId = $post['id'];
                    
                    if (($post['id'] == '' && !$this->holidayPermissions->canCreate()) || 
                            ($post['id'] != '' && !$this->holidayPermissions->canUpdate())) {
                        $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
                    }                    

                    /* Read the holiday by date */
                    $holidayObjectDate = $this->getHolidayService()->readHolidayByDate($date);

                    $allowToAdd = true;

                    if ($this->editMode) {
                        $holidayObject = $this->getHolidayService()->readHoliday($holidayId);

                        if ($date != $holidayObjectDate->getDate() && $holidayObjectDate->getRecurring()) {
                            $allowToAdd = false;
                        }
                    } else {
                        /* Days already added can not be selected to add */
                        if ($date == $holidayObjectDate->getDate() || $holidayObjectDate->getRecurring() == 1) {
                            $allowToAdd = false;
                        }
                    }

                    /* Error will not return if the date if not in the correct format */
                    if (!$allowToAdd && !is_null($date)) {
                        $this->templateMessage = array('WARNING', __('Failed to Save: Date Already Assigned'));
                    } else {


                        $holidayObject = $this->getHolidayService()->readHoliday($post['id']);
                        $holidayObject->setDescription($post['description']);
                        $holidayObject->setDate($post['date']);

                        $recurringValue = $post['recurring'] == 'on' ? 1 : 0;
                        $holidayObject->setRecurring($recurringValue);

                        $holidayObject->setLength($post['length']);
                        $this->getHolidayService()->saveHoliday($holidayObject);                        

                        if ($post['id'] != '') {
                            $this->getUser()->setFlash('success', __(TopLevelMessages::UPDATE_SUCCESS));
                        } else {
                            $this->getUser()->setFlash('success', __(TopLevelMessages::SAVE_SUCCESS));
                        }
                    
                        $this->redirect('leave/viewHolidayList');
                    }
                }
            } else {
                $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
            }
        } else {
            // Check if user has permissions
            if (!$this->holidayPermissions->canRead() || 
                    ($this->editMode && !$this->holidayPermissions->canUpdate()) || 
                    (!$this->editMode && !$this->holidayPermissions->canCreate())) {
                $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
            }
        }
    }

    /**
     * Get form object 
     * @return
     */
    public function getForm($valuesForForm) {
        if (!($this->form instanceof HolidayForm)) {
            $this->form = new HolidayForm($valuesForForm);
        }
        return $this->form;
    }

}
