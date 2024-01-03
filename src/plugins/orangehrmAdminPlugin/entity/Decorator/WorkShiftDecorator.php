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

namespace OrangeHRM\Entity\Decorator;

use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\WorkShift;

class WorkShiftDecorator
{
    use EntityManagerHelperTrait;
    use DateTimeHelperTrait;

    private WorkShift $workShift;

    /**
     * WorkShiftDecorator constructor.
     * @param WorkShift $workShift
     */
    public function __construct(WorkShift $workShift)
    {
        $this->workShift = $workShift;
    }

    /**
     * @return string|null
     */
    public function getStartTime(): ?string
    {
        return $this->getDateTimeHelper()->formatDateTimeToTimeString($this->getWorkShift()->getStartTime());
    }

    /**
     * @return WorkShift
     */
    protected function getWorkShift(): WorkShift
    {
        return $this->workShift;
    }

    /**
     * @return string|null
     */
    public function getEndTime(): ?string
    {
        return $this->getDateTimeHelper()->formatDateTimeToTimeString($this->getWorkShift()->getEndTime());
    }
}
