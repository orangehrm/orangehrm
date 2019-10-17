<?php
/*
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

class EmployeeSubscriptionService extends BaseService {

    /**
     * @var sysConf
     */
    private $sysConf = null;

    /**
     * @param int    $empId
     * @param string $name
     * @param string $email
     * @param string $instanceId
     *
     * @return bool
     * @throws Exception
     */
    public function subscribe(int $empId, string $name, string $email): bool {
        $employeeSubscription = $this->getEmployeeSubscription($empId);
        $employeeSubscription->setEmployeeId($empId);
        $employeeSubscription->setStatus(1);
        $employeeSubscription->setCreatedAt(date('Y-m-d'));
        $employeeSubscription->save();

        //send info into remote server
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getSysConf()->getRegistrationUrl() . '/subscribe.php');
        curl_setopt($ch, CURLOPT_POST, 1);

        $data = http_build_query(
            [
                'name'        => $name,
                'email'       => $email,
                'instance_id' => $this->getInstanceId(),
            ]
        );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($http_status === 200);
    }

    /**
     * Unsubscribe employee from the mail service
     *
     * @param int $empId
     *
     * @return bool
     */
    public function unsubscribe(int $empId): bool {
        $employeeSubscription = $this->getEmployeeSubscription($empId);
        $employeeSubscription->setEmployeeid($empId);
        $employeeSubscription->setStatus(0);
        $employeeSubscription->save();

        return true;
        //@todo send api request to external service
    }

    /**
     * @param int $empId
     *
     * @return EmployeeSubscription
     */
    private function getEmployeeSubscription(int $empId): EmployeeSubscription {
        $q = Doctrine_Query::create()
            ->from('EmployeeSubscription')
            ->where('employee_id = ?', $empId);

        $employeeSubscription = $q->fetchOne();
        if (empty($employeeSubscription)) {
            $employeeSubscription = new EmployeeSubscription();
        }

        return $employeeSubscription;
    }

    /**
     * Check emloyee already subscribed
     *
     * @param int $empId
     *
     * @return bool
     * @throws Doctrine_Query_Exception
     */
    public function isSubscribed(int $empId): bool {
        $q = Doctrine_Query::create()
            ->from('EmployeeSubscription')
            ->where('employee_id = ?', $empId);

        $employeeSubscription = $q->fetchOne();

        return (!empty($employeeSubscription));
    }
    /**
     * Get instance of sysConf
     * @return null|sysConf
     */
    private function getSysConf(): sysConf {
        if (!class_exists(sysConf)) {
            require_once(sfConfig::get('sf_root_dir') . "/../lib/confs/sysConf.php");
        }

        if (is_null($this->sysConf)) {
            $this->sysConf = new sysConf();
        }

        return $this->sysConf;
    }

    /**
     * @return string
     * @throws CoreServiceException
     */
    private function getInstanceId(): string {
        $configService = new ConfigService();

        return $configService->getInstanceIdentifier();
    }
}
