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

namespace OrangeHRM\WorkspaceNotifications\Dto;

use DateTimeInterface;

final class WorkspaceNotificationRecipient
{
    private string $fullName;
    private ?string $subunit;
    private ?string $metadata;
    private ?DateTimeInterface $startDate;
    private ?DateTimeInterface $endDate;

    /**
     * @param string $fullName   "Firstname Lastname" — already trimmed.
     * @param ?string $subunit   Per-employee sub-unit (NOT the registration filter).
     * @param ?string $metadata  Event-specific extra (e.g. "Annual" leave-type name).
     * @param ?DateTimeInterface $startDate  Period start (LEAVE_TODAY only — null for BIRTHDAY).
     * @param ?DateTimeInterface $endDate    Period end (LEAVE_TODAY only — null for BIRTHDAY).
     */
    public function __construct(
        string $fullName,
        ?string $subunit = null,
        ?string $metadata = null,
        ?DateTimeInterface $startDate = null,
        ?DateTimeInterface $endDate = null
    ) {
        $this->fullName = $fullName;
        $this->subunit = $subunit;
        $this->metadata = $metadata;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getSubunit(): ?string
    {
        return $this->subunit;
    }

    public function getMetadata(): ?string
    {
        return $this->metadata;
    }

    public function getStartDate(): ?DateTimeInterface
    {
        return $this->startDate;
    }

    public function getEndDate(): ?DateTimeInterface
    {
        return $this->endDate;
    }
}
