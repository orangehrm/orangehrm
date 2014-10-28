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
 * defineLeavePeriodAction
 */
class defineLeavePeriodAction extends baseLeaveAction {

    private $leavePeriodService;
    private $leaveRequestService;
    protected $menuService;

    public function getMenuService() {

        if (!$this->menuService instanceof MenuService) {
            $this->menuService = new MenuService();
        }

        return $this->menuService;
    }

    public function setMenuService(MenuService $menuService) {
        $this->menuService = $menuService;
    }

    public function getLeavePeriodService() {

        if (is_null($this->leavePeriodService)) {
            $leavePeriodService = new LeavePeriodService();
            $leavePeriodService->setLeavePeriodDao(new LeavePeriodDao());
            $this->leavePeriodService = $leavePeriodService;
        }

        return $this->leavePeriodService;
    }

    /**
     * @return LeaveRequestService
     */
    public function getLeaveRequestService() {
        if (is_null($this->leaveRequestService)) {
            $this->leaveRequestService = new LeaveRequestService();
            $this->leaveRequestService->setLeaveRequestDao(new LeaveRequestDao());
        }
        return $this->leaveRequestService;
    }

    /**
     * @param sfForm $form
     * @return
     */
    public function setForm(sfForm $form) {
        if (is_null($this->form)) {
            $this->form = $form;
        }
    }

    public function execute($request) {

        $this->leavePeriodPermissions = $this->getDataGroupPermissions('leave_period');

        if (!Auth::instance()->hasRole(Auth::ADMIN_ROLE) && !$this->leavePeriodPermissions->canRead()) {
            $this->forward('leave', 'showLeavePeriodNotDefinedWarning');
        }

        $values = array('leavePeriodPermissions' => $this->leavePeriodPermissions);

        $this->setForm(new LeavePeriodForm(array(), $values, true));
        $this->isLeavePeriodDefined = OrangeConfig::getInstance()->getAppConfValue(ConfigService::KEY_LEAVE_PERIOD_DEFINED);
        $this->latestSatrtDate = $this->getLeavePeriodService()->getCurrentLeavePeriodStartDateAndMonth();
        if ($this->isLeavePeriodDefined) {
            $this->currentLeavePeriod = $this->getLeavePeriodService()->getCurrentLeavePeriodByDate(date('Y-m-d'));
            $endDate = date('F d', strtotime($this->currentLeavePeriod[1]));
            $startMonthValue = $this->latestSatrtDate->getLeavePeriodStartMonth();
            $startDateValue = $this->latestSatrtDate->getLeavePeriodStartDay();
        } else {
            $endDate = '-';
            $startMonthValue = 0;
            $startDateValue = 0;
        }

        $this->endDate = $endDate;
        $this->startMonthValue = $startMonthValue;
        $this->startDateValue = $startDateValue;

        if ($this->getUser()->hasFlash('templateMessage')) {
            list($this->messageType, $this->message) = $this->getUser()->getFlash('templateMessage');
        }

        // this section is for saving leave period
        if ($request->isMethod('post')) {
            if ($this->leavePeriodPermissions->canUpdate()) {
                $leavePeriodService = $this->getLeavePeriodService();

                $this->form->bind($request->getParameter($this->form->getName()));
                if ($this->form->isValid()) {

                    $leavePeriodHistory = new LeavePeriodHistory();
                    $leavePeriodHistory->setLeavePeriodStartMonth($this->form->getValue('cmbStartMonth'));
                    $leavePeriodHistory->setLeavePeriodStartDay($this->form->getValue('cmbStartDate'));
                    $leavePeriodHistory->setCreatedAt(date('Y-m-d'));

                    $this->getLeavePeriodService()->saveLeavePeriodHistory($leavePeriodHistory);

                    // Enable leave module menu items the first time leave period is defined
                    if (!$this->isLeavePeriodDefined) {
                        $this->getMenuService()->enableModuleMenuItems('leave');
                    }
                    
                    $this->getUser()->getAttributeHolder()->remove(mainMenuComponent::MAIN_MENU_USER_ATTRIBUTE);

                    $this->getUser()->setFlash('success', __(TopLevelMessages::SAVE_SUCCESS));

                    $this->redirect('leave/defineLeavePeriod');
                }
            }
        }
    }

}

?>
