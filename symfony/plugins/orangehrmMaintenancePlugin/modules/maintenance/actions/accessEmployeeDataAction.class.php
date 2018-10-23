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
     * @return mixed|string
     * @throws CoreServiceException
     * @throws sfException
     */
    public function execute($request)
    {
        $this->header = 'Download Personal Data';
        $this->instanceId = $this->getConfigService()->getInstanceIdentifier();

        $this->getUser()->setFlash('warning', null);
        $this->getUser()->setFlash('success', null);

        $data = $request->getParameterHolder()->getAll();
        $checkIfReqestToAuthenticate = $request->hasParameter('check_authenticate');
        $requestmethod = $request->getMethod();

        if ($requestmethod == 'GET') {
            $this->purgeAuthenticateForm = new PurgeAuthenticateForm();
            $this->setTemplate('purgeEmployee', 'maintenance');
        } elseif ($requestmethod == 'POST' and $checkIfReqestToAuthenticate) {
            $userId = sfContext::getInstance()->getUser()->getAttribute('auth.userId');
            if ($this->getSystemUserService()->isCurrentPassword($userId, $data['confirm_password'])) {
                $this->accsessAllDataForm = new AccsessEmployeeDataForm();
            } else {
                $this->purgeAuthenticateForm = new PurgeAuthenticateForm();
                $this->setTemplate('purgeEmployee', 'maintenance');
                $this->getUser()->setFlash('warning', __(CommonMessages::INCORRECT_PASSWORD));
            }
        } elseif ($requestmethod == 'POST' and !$checkIfReqestToAuthenticate) {
            $employeeDataArray = $this->getEmployeeData($data);
            $downloadableForamat = $this->getDownloadFormatStrategy();
            ob_clean();
            header("Content-type: application/json; charset=UTF-8");
            header("Pragma:''");
            header("Content-Disposition: attachment; filename=" . $downloadableForamat->getDownloadFileName($data['employee']['empId']));
            echo $downloadableForamat->getFormattedString($employeeDataArray);
            return sfView::NONE;
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
     * @param $data
     * @return array
     */
    protected function getEmployeeData($data)
    {
        try {
            $empNumber = $data['employee']['empId'];
            $employee = $this->getEmployeeService()->getEmployee($empNumber);
            if (empty($employee)) {
                $this->getUser()->setFlash('warning', __(ValidationMessages::EMPLOYEE_DOES_NOT_EXIST));
            } else {
                $employeeDataArray = $this->getMaintenanceManager()->accessEmployeeData($empNumber);
                return $employeeDataArray;
            }
        } catch (Exception $e) {
            $this->getUser()->setFlash('warning', __(TopLevelMessages::ACCESS_EMPLOYEE_DATA_FAILED));
        }
    }

    /**
     * @return mixed
     */
    public function getDownloadFormatStrategy()
    {
        if (!isset($this->purgeableEntities)) {
            $this->purgeableEntities = sfYaml::load(realpath(dirname(__FILE__) . '/../../../config/gdpr_purge_employee_strategy.yml'));
        }
        return new $this->purgeableEntities['DownloadFormat'];
    }

    /**
     * @return ConfigService|mixed
     */
    public function getConfigService() {
        if (is_null($this->configService)) {
            $this->configService = new ConfigService();
            $this->configService->setConfigDao(new ConfigDao());
        }
        return $this->configService;
    }
}
