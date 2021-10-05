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

namespace OrangeHRM\Admin\Api\Model;

use OrangeHRM\Admin\Traits\Service\WorkShiftServiceTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Traits\Service\NormalizerServiceTrait;
use OrangeHRM\Entity\WorkShift;
use OrangeHRM\Pim\Api\Model\EmployeeModel;

class WorkShiftDetailedModel implements Normalizable
{
    use NormalizerServiceTrait;
    use DateTimeHelperTrait;
    use WorkShiftServiceTrait;

    private WorkShift $workShift;

    /**
     * @param WorkShift $workShift
     */
    public function __construct(WorkShift $workShift)
    {
        $this->workShift = $workShift;
    }

    /**
     * @return WorkShift
     */
    private function getWorkShift(): WorkShift
    {
        return $this->workShift;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $detailedWorkShift = $this->getWorkShift();
        $employees = $this->getNormalizerService()->normalizeArray(
            EmployeeModel::class,
            $this->getWorkShiftService()
                ->getWorkShiftDao()
                ->getEmployeeListByWorkShiftId($detailedWorkShift->getId())
        );
        return [
            'id' => $detailedWorkShift->getId(),
            'name' => $detailedWorkShift->getName(),
            'hoursPerDay' => $detailedWorkShift->getHoursPerDay(),
            'startTime' => $this->getDateTimeHelper()->formatDateTimeToTimeString(
                $detailedWorkShift->getStartTime()
            ),
            'endTime' => $this->getDateTimeHelper()->formatDateTimeToTimeString(
                $detailedWorkShift->getEndTime()
            ),
            'employees' => $employees
        ];
    }
}
