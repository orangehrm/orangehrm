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
 * Description of baseAction
 *
 * @author dewmal
 */
abstract class BaseBuzzAction extends ohrmBaseAction {

    protected $buzzService;
    protected $buzzConfigService;
    protected $BuzzUserService;
    protected $ohrmCookieManager;
    protected $employeeService;
    /**
     * @var BuzzNotificationService|null
     */
    protected $buzzNotificationService = null;

    const EMP_DELETED = 'empDeleted';
    const EMP_NUMBER = 'empNumber';
    const EMP_NAME = 'empName';
    const EMP_JOB_TITLE = 'jobTitle';
    const LABEL_EMPLOYEE_DELETED = 'Deleted Employee';

    /**
     * 
     * @return BuzzService
     */
    protected function getBuzzService() {
        if (!$this->buzzService instanceof BuzzService) {
            $this->buzzService = new BuzzService();
        }
        return $this->buzzService;
    }

    /**
     * Get Employee Service
     * @return EmployeeService
     */
    protected function getEmployeeService() {
        if (!$this->employeeService instanceof EmployeeService) {
            $this->employeeService = new EmployeeService();
        }
        return $this->employeeService;
    }

    /**
     * 
     * @return BuzzConfigService
     */
    protected function getBuzzConfigService() {
        if (!$this->buzzConfigService instanceof BuzzConfigService) {
            $this->buzzConfigService = new BuzzConfigService();
        }
        return $this->buzzConfigService;
    }

    /**
     * 
     * @return BuzzUserService
     */
    protected function getBuzzUserService() {
        if (!$this->buzzUserService instanceof BuzzUserService) {
            $this->buzzUserService = new BuzzUserService();
        }
        return $this->buzzUserService;
    }

    /**
     * 
     * @return CookieManager
     */
    protected function getOhrmCookieManager() {
        if (!$this->ohrmCookieManager instanceof CookieManager) {
            $this->ohrmCookieManager = new CookieManager();
        }
        return $this->ohrmCookieManager;
    }

    /**
     * get logged in employee number
     * @return employee number
     * @throws Exception
     */
    public function getLogedInEmployeeNumber() {
        $employeeNumber = $this->getBuzzUserService()->getEmployeeNumber();
        return $employeeNumber;
    }

    /**
     * get loged in employee user role
     * @return type
     */
    public function getLoggedInEmployeeUserRole() {
        $employeeUserRole = $this->getBuzzUserService()->getEmployeeUserRole();
        return $employeeUserRole;
    }

    /**
     * @return BuzzNotificationService
     */
    public function getBuzzNotificationService(): BuzzNotificationService
    {
        if (!($this->buzzNotificationService instanceof BuzzNotificationService)) {
            $this->buzzNotificationService = new BuzzNotificationService();
        }
        return $this->buzzNotificationService;
    }

    /**
     * @param BuzzNotificationService $buzzNotificationService
     */
    public function setBuzzNotificationService(BuzzNotificationService $buzzNotificationService)
    {
        $this->buzzNotificationService = $buzzNotificationService;
    }

}
