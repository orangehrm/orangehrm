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

namespace OrangeHRM\Maintenance\Service;

use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Maintenance\Dao\PurgeEmployeeDao;
use OrangeHRM\Maintenance\PurgeStrategy\PurgeStrategy;
use Symfony\Component\Yaml\Yaml;
use OrangeHRM\Maintenance\PurgeStrategy\ReplaceWithValuePurgeStrategy;

class PurgeEmployeeService
{
    use EntityManagerHelperTrait;

    private const GDPR_PURGE_EMPLOYEE = 'gdpr_purge_employee_strategy';

    private ?PurgeEmployeeDao $employeePurgeDao = null;
    private array $purgeableEntities;

    /**
     * @return PurgeEmployeeDao
     */
    public function getPurgeEmployeeDao(): PurgeEmployeeDao
    {
        if (is_null($this->employeePurgeDao)) {
            $this->employeePurgeDao = new PurgeEmployeeDao();
        }
        return $this->employeePurgeDao;
    }

    /**
     * @param $entity
     * @return mixed
     */
    public function saveEntity($entity)
    {
        return $this->employeePurgeDao->saveEntity($entity);
    }

    public function purgeEmployeeData(int $empNumber)
    {
        $purgeableEntities = $this->getPurgeableEntities(self::GDPR_PURGE_EMPLOYEE);
        foreach ($purgeableEntities as $purgeableEntityClassName => $purgeStrategies) {
            foreach ($purgeStrategies['PurgeStrategy'] as $strategy => $strategyInfoArray) {
                $purgeStrategy = $this->getPurgeStrategy(
                    $purgeableEntityClassName,
                    $strategy,
                    $strategyInfoArray
                );
                $purgeStrategy->purge($empNumber);
            }
        }
    }


//        $connection = Doctrine_Manager::getInstance()->getCurrentConnection();
//        try {
//            $connection->beginTransaction();
//            $purgeableEntities = $this->getPurgeableEntities(self::EMPLOYEE_GDPR);
//            foreach ($purgeableEntities as $purgeableEntityClassName => $purgeStrategies) {
//                foreach ($purgeStrategies['PurgeStrategy'] as $strategy => $strategyInfoArray) {
//                    $strategy = $this->getPurgeStrategy($purgeableEntityClassName, $strategy, $strategyInfoArray);
//                    $strategy->purge($empNumber);
//                }
//            }
//            $connection->commit();
//        } catch (Exception $e) {
//            $connection->rollback();
//            Logger::getLogger('maintenance')->error($e->getCode() . ' - ' . $e->getMessage(), $e);
//            throw new Exception($e->getMessage(), $e->getCode(), $e);
//        }


    /**
     * @param string $fileName
     * @return array
     */
    public function getPurgeableEntities(string $fileName): array
    {
        if (!isset($this->purgeableEntities)) {
            $path = realpath(dirname(__FILE__, 2)) . '/config/' . $fileName . '.yaml';
            $this->purgeableEntities = Yaml::parseFile($path);
        }
        return $this->purgeableEntities['Entities'];
    }

    /**
     * @param string $purgeableEntityClassName
     * @param string $strategy
     * @param array $strategyInfoArray
     * @return PurgeStrategy
     */
    public function getPurgeStrategy(string $purgeableEntityClassName, string $strategy, array $strategyInfoArray): PurgeStrategy
    {
//        return new ReplaceWithValuePurgeStrategy($purgeableEntityClassName, $strategyInfoArray);
        $purgeStrategyClass = 'OrangeHRM\\Maintenance\\PurgeStrategy\\' . $strategy . "PurgeStrategy";
        return new $purgeStrategyClass($purgeableEntityClassName, $strategyInfoArray);
    }

    /**
     * @param array $matchByValues
     * @param string $table
     * @return array
     * LIKE A WRAPPER FUNCTION REMOVE LATER MAYBE
     */
    public function extractDataFromEmpNumber(array $matchByValues, string $table): array
    {
        return $this->getPurgeEmployeeDao()->extractDataFromEmpNumber($matchByValues, $table);
    }
}
