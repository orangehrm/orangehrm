<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Maintenance\Service;

use OrangeHRM\Maintenance\AccessStrategy\AccessStrategy;
use OrangeHRM\Maintenance\Dao\MaintenanceDao;
use OrangeHRM\ORM\Doctrine;
use OrangeHRM\ORM\Exception\TransactionException;
use PHPUnit\Util\Exception;
use Symfony\Component\Yaml\Yaml;

/**
 * Class MaintenanceService
 */
class MaintenanceService
{
    private ?MaintenanceDao $maintenanceDao = null;
    public const EMPLOYEE_GDPR = 'gdpr_access_employee_strategy';
    private ?array $purgeableEntities = null;

    /**
     * @param string $fileName
     * @return array
     */
    public function getPurgeableEntities(string $fileName): array
    {
        if (!isset($this->purgeableEntities)) {
            $this->purgeableEntities = Yaml::parse(
                file_get_contents(realpath(dirname(__FILE__) . '/../config/' . $fileName . '.yml'))
            );
        }

        return $this->purgeableEntities['Entities'];
    }

    /**
     * @param string $accessibleEntityClassName
     * @param string $strategy
     * @param array $strategyInfoArray
     * @return AccessStrategy
     */
    public function getAccessStrategy(
        string $accessibleEntityClassName,
        string $strategy,
        array $strategyInfoArray
    ): AccessStrategy {
        $accessStrategy = 'OrangeHRM\Maintenance\AccessStrategy' . "\\" . $strategy . "AccessStrategy";
        return new $accessStrategy($accessibleEntityClassName, $strategyInfoArray);
    }

    /**
     * @throws TransactionException
     * @throws \Doctrine\DBAL\Exception
     */
    public function accessEmployeeData(int $empNumber): array
    {
        $connection = Doctrine::getEntityManager()->getConnection();
        try {
            $connection->beginTransaction();
            $accessibleEntities = $this->getPurgeableEntities(self::EMPLOYEE_GDPR);
            $entityAccessData = [];

            foreach ($accessibleEntities as $accessibleEntityClassName => $accessStrategies) {
                if (array_key_exists("AccessStrategy", $accessStrategies)) {
                    foreach ($accessStrategies['AccessStrategy'] as $strategy => $strategyInfoArray) {
                        $strategy = $this->getAccessStrategy($accessibleEntityClassName, $strategy, $strategyInfoArray);
                        $data = $strategy->access($empNumber);
                        if ($data) {
                            $entityAccessData[$accessibleEntityClassName] = $data;
                        }
                    }
                }
            }

            $connection->commit();
            return $entityAccessData;
        } catch (Exception $e) {
            $connection->rollback();
            throw new TransactionException($e);
        }
    }

    /**
     * @return maintenanceDao
     */
    public function getMaintenanceDao(): MaintenanceDao
    {
        if (!isset($this->maintenanceDao)) {
            $this->maintenanceDao = new MaintenanceDao();
        }
        return $this->maintenanceDao;
    }

    /**
     * @param $matchByValues
     * @param $table
     * @return mixed
     */
    public function extractDataFromEmpNumber($matchByValues, $table): array
    {
        return $this->getMaintenanceDao()->extractDataFromEmpNumber($matchByValues, $table);
    }
}
