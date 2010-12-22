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
class defineLeavePeriodAction extends sfAction {

    private $leavePeriodService;

    public function getLeavePeriodService() {

        if (is_null($this->leavePeriodService)) {
            $leavePeriodService = new LeavePeriodService();
            $leavePeriodService->setLeavePeriodDao(new LeavePeriodDao());
            $this->leavePeriodService = $leavePeriodService;
        }

        return $this->leavePeriodService;
    }

    /**
     * @param sfForm $form
     * @return
     */
    public function setForm(sfForm $form) {
        if(is_null($this->form)) {
            $this->form	= $form;
        }
    }

    public function execute($request) {
        if (!Auth::instance()->hasRole(Auth::ADMIN_ROLE)) {
            $this->forward('leave', 'showLeavePeriodNotDefinedWarning');
        }

        $this->setForm(new LeavePeriodForm(array(), array(), true));
        OrangeConfig::getInstance()->loadAppConf();
        $this->isLeavePeriodDefined = OrangeConfig::getInstance()->getAppConfValue(Config::KEY_LEAVE_PERIOD_DEFINED);
        $this->currentLeavePeriod = $this->getLeavePeriodService()->getCurrentLeavePeriod();
        if ($this->isLeavePeriodDefined) {
            $endDate = $this->currentLeavePeriod->getEndDateFormatted('F d');
            $nextPeriodStartDateTimestamp = strtotime('+1 day', strtotime($this->currentLeavePeriod->getEndDate()));
            $startMonthValue = (int) date('m', $nextPeriodStartDateTimestamp);
            $startDateValue = (int) date('d', $nextPeriodStartDateTimestamp);
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
            $leavePeriodService = $this->getLeavePeriodService();

            $this->form->bind($request->getParameter($this->form->getName()));
            if($this->form->isValid()) {

                $this->_setLeapYearLeavePeriodDetails($this->form);
                $leavePeriodDataHolder = $this->_getPopulatedLeavePeriodDataHolder();
                $fullStartDate = $leavePeriodService->generateStartDate($leavePeriodDataHolder);

                $leavePeriodDataHolder->setLeavePeriodStartDate($fullStartDate);
                $fullEndDate = $leavePeriodService->generateEndDate($leavePeriodDataHolder);
                $currentLeavePeriod = $leavePeriodService->getCurrentLeavePeriod();
                
                $this->getUser()->setFlash('templateMessage', array('success', 'Leave Period Was Saved Successfully'));

                if (!is_null($currentLeavePeriod)) {
                    $leavePeriodService->adjustCurrentLeavePeriod($fullEndDate);
                } else {

                    $leavePeriod = new LeavePeriod();
                    $leavePeriod->setStartDate($fullStartDate);
                    $leavePeriod->setEndDate($fullEndDate);
                    $leavePeriodService->saveLeavePeriod($leavePeriod);
                }
                $this->redirect('coreLeave/defineLeavePeriod');
            }
        }
    }

    private function _setLeapYearLeavePeriodDetails(sfForm $form) {

        $post   =	$form->getValues();
        if ($post['cmbStartMonth'] == 2 &&
                $post['cmbStartDate'] == 29) {

            $nonLeapYearLeavePeriodStartDate = $post['cmbStartMonthForNonLeapYears'];
            $nonLeapYearLeavePeriodStartDate .= '-';
            $nonLeapYearLeavePeriodStartDate .= $post['cmbStartDateForNonLeapYears'];

            ParameterService::setParameter('nonLeapYearLeavePeriodStartDate', $nonLeapYearLeavePeriodStartDate);
            ParameterService::setParameter('isLeavePeriodStartOnFeb29th', 'Yes');
            ParameterService::setParameter('leavePeriodStartDate', '');
        } else {

            $leavePeriodStartDate = $post['cmbStartMonth'];
            $leavePeriodStartDate .= '-';
            $leavePeriodStartDate .= $post['cmbStartDate'];

            ParameterService::setParameter('leavePeriodStartDate', $leavePeriodStartDate);
            ParameterService::setParameter('nonLeapYearLeavePeriodStartDate', '');
            ParameterService::setParameter('isLeavePeriodStartOnFeb29th', 'No');
        }
    }

    private function _getPopulatedLeavePeriodDataHolder() {

        $leavePeriodDataHolder = new LeavePeriodDataHolder();

        $isLeavePeriodStartOnFeb29th = ParameterService::getParameter('isLeavePeriodStartOnFeb29th');
        $nonLeapYearLeavePeriodStartDate = ParameterService::getParameter('nonLeapYearLeavePeriodStartDate');
        $leavePeriodStartDate = ParameterService::getParameter('leavePeriodStartDate');


        $leavePeriodDataHolder->setIsLeavePeriodStartOnFeb29th($isLeavePeriodStartOnFeb29th);
        $leavePeriodDataHolder->setNonLeapYearLeavePeriodStartDate($nonLeapYearLeavePeriodStartDate);
        $leavePeriodDataHolder->setStartDate($leavePeriodStartDate);
        $leavePeriodDataHolder->setDateFormat('Y-m-d');
        $leavePeriodDataHolder->setCurrentDate(date('Y-m-d'));

        return $leavePeriodDataHolder;
    }
}
?>
