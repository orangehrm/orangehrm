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
 * Class purgeEmployeeAction
 */
class purgeEmployeeAction extends sfAction
{

    private $maintenanceManager;
    private $employeeService;

    /**
     * @param sfRequest $request
     * @return mixed|void
     * @throws CoreServiceException
     * @throws sfException
     */
    public function execute($request)
    {
        $this->header = 'Purge Employee Records';
        $this->instanceId = $this->getConfigService()->getInstanceIdentifier();
        $this->getUser()->setFlash('warning', null);
        $this->getUser()->setFlash('success', null);

        $checkIfReqestToAuthenticate = $request->hasParameter('check_authenticate');
        $requestmethod = $request->getMethod();
        $data = $request->getParameterHolder()->getAll();

        if ($requestmethod === 'POST' && $checkIfReqestToAuthenticate) {
            $userId = sfContext::getInstance()->getUser()->getAttribute('auth.userId');
            if ($this->getSystemUserService()->isCurrentPassword($userId, $data['confirm_password'])) {
                $this->setTemplate('purgeAllRecords', 'maintenance');
                $this->purgeform = new PurgeEmployeeForm();
            } else {
                $this->purgeAuthenticateForm = new PurgeAuthenticateForm();
                $this->getUser()->setFlash('warning', __(CommonMessages::INCORRECT_PASSWORD));
            }
        } elseif ($requestmethod === 'GET') {
            $this->purgeAuthenticateForm = new PurgeAuthenticateForm();
        } elseif ($requestmethod === 'POST' && !$checkIfReqestToAuthenticate) {
            if (empty($data['employee']['empId']) or $data['employee']['empName'] == 'Type for hints...') {
                $this->getUser()->setFlash('success', __(TopLevelMessages::SELECT_RECORDS));
                $this->setTemplate('purgeAllRecords', 'maintenance');
                $this->purgeform = new PurgeEmployeeForm();
            } else {
                $this->purge($data);
                $this->setTemplate('purgeAllRecords', 'maintenance');
                $this->purgeform = new PurgeEmployeeForm();
            }
        }
    }

    /**
     * @return mixed|SystemUserService
     */
    protected function getSystemUserService()
    {
        if (!isset($this->systemUserService)) {
            $this->systemUserService = new SystemUserService();
        }
        return $this->systemUserService;
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
     * @return MaintenanceManager
     */
    public function getMaintenanceManager()
    {
        if (!isset($this->maintenanceManager)) {
            $this->maintenanceManager = new MaintenanceManager();
        }
        return $this->maintenanceManager;
    }

    /**
     * @param $data
     */
    protected function purge($data)
    {
        try {
            $empNumber = $data['employee']['empId'];
            $employee = $this->getEmployeeService()->getEmployee($empNumber);

            if (empty($employee) || empty($employee->getTerminationId())) {
                $this->getUser()->setFlash('warning', __(ValidationMessages::EMPLOYEE_DOES_NOT_EXIST));
                $this->setTemplate('purgeAllRecords', 'maintenance');
                $this->purgeform = new PurgeEmployeeForm();
            } else {
                $this->getMaintenanceManager()->purgeEmployeeData($empNumber);
                $this->getUser()->setFlash('success', __(TopLevelMessages::PURGE_SUCCESS));
            }
        } catch (Exception $e) {
            $this->getUser()->setFlash('warning', __(TopLevelMessages::DELETE_FAILURE));
            $this->setTemplate('purgeAllRecords', 'maintenance');
            $this->purgeform = new PurgeEmployeeForm();
        }
    }

    /**
     * @return ConfigService|mixed
     */
    public function getConfigService()
    {
        if (is_null($this->configService)) {
            $this->configService = new ConfigService();
            $this->configService->setConfigDao(new ConfigDao());
        }
        return $this->configService;
    }
}
