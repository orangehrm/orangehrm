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

namespace OrangeHRM\Entity\Decorator;

use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\EmployeeTerminationRecord;
use OrangeHRM\Entity\TerminationReason;

class EmployeeTerminationRecordDecorator
{
    use EntityManagerHelperTrait;
    use DateTimeHelperTrait;

    /**
     * @var EmployeeTerminationRecord
     */
    protected EmployeeTerminationRecord $employeeTerminationRecord;

    /**
     * @param EmployeeTerminationRecord $employeeTerminationRecord
     */
    public function __construct(EmployeeTerminationRecord $employeeTerminationRecord)
    {
        $this->employeeTerminationRecord = $employeeTerminationRecord;
    }

    /**
     * @return EmployeeTerminationRecord
     */
    protected function getEmployeeTerminationRecord(): EmployeeTerminationRecord
    {
        return $this->employeeTerminationRecord;
    }

    /**
     * @return string|null
     */
    public function getDate(): ?string
    {
        $date = $this->getEmployeeTerminationRecord()->getDate();
        return $this->getDateTimeHelper()->formatDateTimeToYmd($date);
    }

    /**
     * @param int $id
     */
    public function setTerminationReasonById(int $id): void
    {
        /** @var TerminationReason|null $terminationReason */
        $terminationReason = $this->getReference(TerminationReason::class, $id);
        $this->getEmployeeTerminationRecord()->setTerminationReason($terminationReason);
    }
}
