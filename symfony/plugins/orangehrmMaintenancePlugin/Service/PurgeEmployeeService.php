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

use Exception;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Maintenance\Dao\PurgeEmployeeDao;
use OrangeHRM\Maintenance\Dto\InfoArray;
use OrangeHRM\Maintenance\PurgeStrategy\PurgeStrategy;
use OrangeHRM\ORM\Exception\TransactionException;
use Symfony\Component\Yaml\Yaml;

class PurgeEmployeeService
{
    use EntityManagerHelperTrait;

    private const GDPR_PURGE_EMPLOYEE = 'gdpr_purge_employee_strategy';

    private ?PurgeEmployeeDao $employeePurgeDao = null;
    private ?array $purgeableEntities = null;

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
     * @param int $empNumber
     * @throws TransactionException
     */
    public function purgeEmployeeData(int $empNumber): void
    {
        $this->beginTransaction();
        try {
            $purgeableEntities = $this->getPurgeableEntities(self::GDPR_PURGE_EMPLOYEE);
            foreach ($purgeableEntities as $purgeableEntityClassName => $purgeStrategies) {
                foreach ($purgeStrategies['PurgeStrategy'] as $strategy => $strategyInfoArray) {
                    $infoArray = new InfoArray($strategyInfoArray);
                    $purgeStrategy = $this->getPurgeStrategy(
                        $purgeableEntityClassName,
                        $strategy,
                        $infoArray
                    );
                    $purgeStrategy->purge($empNumber);
                }
            }
            $this->getEntityManager()->flush();
            $this->commitTransaction();
        } catch (Exception $exception) {
            $this->rollBackTransaction();
            throw new TransactionException($exception);
        }
    }

    /**
     * @param string $fileName
     * @return array
     */
    public function getPurgeableEntities(string $fileName): array
    {
        if (is_null($this->purgeableEntities)) {
            $path = realpath(dirname(__FILE__, 2)) . '/config/' . $fileName . '.yaml';
            $this->purgeableEntities = Yaml::parseFile($path);
        }
        return $this->purgeableEntities['Entities'];
    }

    /**
     * @param string $purgeableEntityClassName
     * @param string $strategy
     * @param InfoArray $infoArray
     * @return PurgeStrategy
     */
    public function getPurgeStrategy(
        string $purgeableEntityClassName,
        string $strategy,
        InfoArray $infoArray
    ): PurgeStrategy {
        $purgeStrategyClass = 'OrangeHRM\\Maintenance\\PurgeStrategy\\' . $strategy . "PurgeStrategy";
        return new $purgeStrategyClass($purgeableEntityClassName, $infoArray);
    }
}
