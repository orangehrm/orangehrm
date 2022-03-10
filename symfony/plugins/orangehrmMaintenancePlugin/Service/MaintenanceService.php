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

namespace OrangeHRM\Maintenance\Service;

use OrangeHRM\Core\Exception\DaoException;
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
     * @return array
     * @throws DaoException
     */
    public function getPurgeEmployeeList(): array
    {
        return $this->getMaintenanceDao()->getEmployeePurgingList();
    }

    /**
     * @param $matchByValues
     * @param $table
     * @return mixed
     * @throws DaoException
     */
    public function extractDataFromEmpNumber($matchByValues, $table): array
    {
        return $this->getMaintenanceDao()->extractDataFromEmpNumber($matchByValues, $table);
    }

    /**
     * @param $entity
     * @return bool
     * @throws DaoException
     */
    public function saveEntity($entity): bool
    {
        return $this->getMaintenanceDao()->saveEntity($entity);
    }

    /**
     * @return Doctrine_Collection
     * @throws DaoException
     */
    public function getVacancyListToPurge()
    {
        //TODO
        return $this->getMaintenanceDao()->getVacancyListToPurge();
    }

    /**
     * @param $vacancyId
     * @return Doctrine_Collection
     * @throws DaoException
     */
    public function getDeniedCandidatesToKeepDataByVacnacyId($vacancyId)
    {
        //TODO
        return $this->getMaintenanceDao()->getDeniedCandidatesToKeepDataByVacnacyId($vacancyId);
    }
}
