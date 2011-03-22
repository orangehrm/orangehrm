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
 * delete employees list action
 */
class deleteEmployeesAction extends sfAction {

    private $employeeService;

    /**
     * Get EmployeeService
     * @returns EmployeeService
     */
    public function getEmployeeService() {
        if (is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
            $this->employeeService->setEmployeeDao(new EmployeeDao());
        }
        return $this->employeeService;
    }

    /**
     * Set EmployeeService
     * @param EmployeeService $employeeService
     */
    public function setEmployeeService(EmployeeService $employeeService) {
        $this->employeeService = $employeeService;
    }

    /**
     * Delete action. Deletes the employees with the given ids
     */
    public function execute($request) {

        // Check if admin mode - temporary solution - use symfony
        $adminMode = $this->getUser()->hasCredential(Auth::ADMIN_ROLE);

        if (!$adminMode) {
            return $this->forward("pim", "unauthorized");
        }

        $ids = $request->getParameter('ids');

        $employeeService = $this->getEmployeeService();
        $count = $employeeService->deleteEmployee($ids);

        if ($count == count($ids)) {
            $this->getUser()->setFlash('templateMessage', array('success', __('The Selected Employee(s) Were Deleted Successfully.')));
        } else {
            $this->getUser()->setFlash('templateMessage', array('failure', __('A Problem Occured When Deleting The Selected Employees.')));
        }

        $this->redirect('pim/viewEmployeeList');
    }


}
