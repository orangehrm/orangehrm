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
 * Boston, MA 02110-1301, USA
 */

/**
 * Class accessEmployeeDataAction
 */
class accessEmployeeDataAction extends sfAction
{
    /**
     * @param sfRequest $request
     * @return mixed|void
     */
    public function execute($request)
    {
        $this->getUser()->setFlash('warning', null);
        $this->getUser()->setFlash('success', null);

        $data = $request->getParameterHolder()->getAll();
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $this->accsessAllDataForm = new AccsessAllDataForm();
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST' and empty($data['employee']['empId'])) {
            $this->accsessAllDataForm = new AccsessAllDataForm();
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST' and !empty($data['employee']['empId'])) {
            $this->getEmployeeData($data);
            $this->accsessAllDataForm = new AccsessAllDataForm();
        }
    }

    /**
     * @return MaintenanceManager|mixed
     */
    public function getMaintenanceManager()
    {
        if (!isset($this->maintenanceManager)) {
            $this->maintenanceManager = new MaintenanceManager();
        }
        return $this->maintenanceManager;
    }

    /**
     * @return EmployeeService
     */
    public function getEmployeeService()
    {
        if (!isset($this->employeeService)) {
            $this->employeeService = new EmployeeService();
        }
        return $this->employeeService;
    }

    /**
     * @param $data
     */
    protected function getEmployeeData($data)
    {
        try {
            $empNumber = $data['employee']['empId'];
            $employee = $this->getEmployeeService()->getEmployee($empNumber);

            if (empty($employee)) {
                $this->getUser()->setFlash('warning', __(ValidationMessages::EMPLOYEE_DOES_NOT_EXIST));
            }
            else {
                $this->getMaintenanceManager()->accessEmployeeData($empNumber);
            }
        } catch (Exception $e) {
            $this->getUser()->setFlash('success', __(TopLevelMessages::DELETE_FAILURE));
        }
    }
}
