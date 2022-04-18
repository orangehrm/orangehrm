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

namespace OrangeHRM\Leave\Service;

use Exception;
use OrangeHRM\Core\Exception\ConfigurationException;
use OrangeHRM\Core\Traits\ClassHelperTrait;
use OrangeHRM\Core\Traits\LoggerTrait;
use OrangeHRM\Leave\Traits\Service\LeaveConfigServiceTrait;
use OrangeHRM\Leave\WorkSchedule\BasicWorkSchedule;
use OrangeHRM\Leave\WorkSchedule\WorkScheduleInterface;

class WorkScheduleService
{
    use LeaveConfigServiceTrait;
    use ClassHelperTrait;
    use LoggerTrait;

    /**
     * @var string|null
     */
    protected ?string $workScheduleImplementationClass = null;

    /**
     * @var WorkScheduleInterface[]
     */
    protected array $employeeWorkSchedulePool = [];

    /**
     * @param int $empNumber
     * @return WorkScheduleInterface|BasicWorkSchedule
     */
    public function getWorkSchedule(int $empNumber): WorkScheduleInterface
    {
        if (isset($this->employeeWorkSchedulePool[$empNumber])) {
            return $this->employeeWorkSchedulePool[$empNumber];
        }

        if (is_null($this->workScheduleImplementationClass)) {
            $this->workScheduleImplementationClass = $this->getLeaveConfigService()->getWorkScheduleImplementation();

            $fallbackNamespace = 'OrangeHRM\\Leave\\WorkSchedule\\';
            if (!$this->getClassHelper()->classExists(
                $this->workScheduleImplementationClass,
                $fallbackNamespace
            )) {
                throw new ConfigurationException(
                    'Work Schedule implementation class ' .
                    $this->workScheduleImplementationClass . ' does not exist.'
                );
            }

            $this->workScheduleImplementationClass = $this->getClassHelper()->getClass(
                $this->workScheduleImplementationClass,
                $fallbackNamespace
            );
        }

        try {
            $workSchedule = new $this->workScheduleImplementationClass();
        } catch (Exception $e) {
            $this->getLogger()->error(
                'Error constructing work schedule implementation ' .
                $this->workScheduleImplementationClass,
            );
            $this->getLogger()->error($e->getTraceAsString());
            throw new ConfigurationException('Work schedule implementation not configured', 0, $e);
        }

        if (!$workSchedule instanceof WorkScheduleInterface) {
            throw new ConfigurationException(
                'Invalid work schedule implementation class ' .
                $this->workScheduleImplementationClass
            );
        }

        $workSchedule->setEmpNumber($empNumber);
        $this->employeeWorkSchedulePool[$empNumber] = $workSchedule;
        return $workSchedule;
    }
}
