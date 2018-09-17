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
class purgeEmployeeAction extends sfAction
{
    /**
     * @param sfRequest $request
     * @return mixed|void
     * @throws sfException
     */
    public function execute($request)
    {
        // TODO: Implement execute() method.
        $this->getUser()->setFlash('warning', null);
        $this->getUser()->setFlash('success', null);

        $value = $request->hasParameter('check_authenticate');
        $data = $request->getParameterHolder()->getAll();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $value) {
            $userId = sfContext::getInstance()->getUser()->getAttribute('auth.userId');

            if ($this->getAuthenticateVerifyService()->isCurrentPassword($userId, $data['confirm_password'])) {
                $this->getUser()->setFlash('success', __(CommonMessages::CREDENTIALS_VALID));
                $this->setTemplate('purgeAllRecords', 'maintenance');
                $this->form = new PurgeForm();
            } else {
                $this->form = new PurgeAuthenticateForm();
                $this->getUser()->setFlash('warning', __(CommonMessages::CREDENTIALS_REQUIRED));
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->form = new PurgeAuthenticateForm();
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && !$value) {
            if (empty($data['employee']['empId']) or $data['employee']['empName'] == 'Type for hints...') {
                $this->getUser()->setFlash('success', __(TopLevelMessages::SELECT_RECORDS));
                $this->setTemplate('purgeAllRecords', 'maintenance');
                $this->form = new PurgeForm();
            } else {
                $this->purge($data);
                $this->setTemplate('purgeAllRecords', 'maintenance');
                $this->form = new PurgeForm();
            }
        }
    }

    /**
     * @return AuthenticateVerifyService|mixed
     */
    protected function getAuthenticateVerifyService()
    {
        if (!isset($this->authVerifyService)) {
            $this->authVerifyService = new AuthenticateVerifyService();
        }
        return $this->authVerifyService;
    }

    /**
     * @return EmployeeService|mixed
     */
    public function getEmployeeService()
    {
        if (!isset($this->employeeService)) {
            $this->employeeService = new EmployeeService();
        }
        return $this->employeeService;
    }

    /**
     * @return MaintenanceService|mixed
     */
    public function getMaintenanceService()
    {
        if (!isset($this->maintenanceService)) {
            $this->maintenanceService = new MaintenanceService();
        }
        return $this->maintenanceService;
    }

    /**
     * @param $data
     * this will purge employee data
     */
    protected function purge($data)
    {
        try {
            $empNumber = $data['employee']['empId'];
            $employee = $this->getEmployeeService()->getEmployee($empNumber);

            if (empty($employee) || empty($employee->getTerminationId())) {
                $this->getUser()->setFlash('success', __(TopLevelMessages::SELECT_RECORDS));
                $this->setTemplate('purgeAllRecords', 'pim');
                $this->form = new PurgeForm();
            } else {
                $this->getMaintenanceService()->purgeEmployee($empNumber);
                $this->getUser()->setFlash('success', __(TopLevelMessages::DELETE_SUCCESS));
                $this->setTemplate('purgeAllRecords', 'pim');
                $this->form = new PurgeForm();
            }
        } catch (Exception $e) {
            $this->getUser()->setFlash('success', __(TopLevelMessages::DELETE_FAILURE));
            $this->setTemplate('purgeAllRecords', 'pim');
            $this->form = new PurgeForm();
        }

    }
}

