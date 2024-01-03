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

use Exception;
use OrangeHRM\Core\Traits\EventDispatcherTrait;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Maintenance\Dao\PurgeDao;
use OrangeHRM\Maintenance\Dto\InfoArray;
use OrangeHRM\Maintenance\Event\MaintenanceEvent;
use OrangeHRM\Maintenance\Event\PurgeEmployee;
use OrangeHRM\Maintenance\PurgeStrategy\PurgeStrategy;
use OrangeHRM\ORM\Exception\TransactionException;
use OrangeHRM\Pim\Service\EmployeePictureService;
use Symfony\Component\Yaml\Yaml;

class PurgeService
{
    use EntityManagerHelperTrait;
    use EventDispatcherTrait;

    private const GDPR_PURGE_EMPLOYEE = 'gdpr_purge_employee_strategy';
    private const GDPR_PURGE_CANDIDATE = 'gdpr_purge_candidate_strategy';

    private ?PurgeDao $purgeDao = null;
    private ?EmployeePictureService $employeePictureService = null;
    private ?array $purgeableEntities = null;

    /**
     * @return PurgeDao
     */
    public function getPurgeDao(): PurgeDao
    {
        return $this->purgeDao ??= new PurgeDao();
    }

    public function getEmployeePictureService(): EmployeePictureService
    {
        return $this->employeePictureService ??= new EmployeePictureService();
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

            $this->getEmployeePictureService()->deleteEmpPictureETagByEmpNumber($empNumber);

            $this->getEventDispatcher()->dispatch(
                new PurgeEmployee($empNumber),
                MaintenanceEvent::PURGE_EMPLOYEE_END
            );
            $this->commitTransaction();

            $this->getEventDispatcher()->dispatch(
                new PurgeEmployee($empNumber),
                MaintenanceEvent::PURGE_EMPLOYEE_FINISHED
            );
        } catch (Exception $exception) {
            $this->rollBackTransaction();
            throw new TransactionException($exception);
        }
    }

    /**
     * @param int $vacancyId
     * @throws TransactionException
     */
    public function purgeCandidateData(int $vacancyId): void
    {
        $this->beginTransaction();
        try {
            $purgeableEntities = $this->getPurgeableEntities(self::GDPR_PURGE_CANDIDATE);
            foreach ($purgeableEntities as $purgeableEntityClassName => $purgeStrategies) {
                foreach ($purgeStrategies['PurgeStrategy'] as $strategy => $strategyInfoArray) {
                    $infoArray = new InfoArray($strategyInfoArray);
                    $purgeStrategy = $this->getPurgeStrategy(
                        $purgeableEntityClassName,
                        $strategy,
                        $infoArray
                    );
                    $purgeStrategy->purge($vacancyId);
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
        $purgeStrategyClass = 'OrangeHRM\\Maintenance\\PurgeStrategy\\' . $strategy . 'PurgeStrategy';
        return new $purgeStrategyClass($purgeableEntityClassName, $infoArray);
    }
}
