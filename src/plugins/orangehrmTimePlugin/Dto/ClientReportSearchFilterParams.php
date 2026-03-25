<?php

namespace OrangeHRM\Time\Dto;

use InvalidArgumentException;
use OrangeHRM\Leave\Dto\DateRangeSearchFilterParams;

class ClientReportSearchFilterParams extends DateRangeSearchFilterParams
{
    public const ALLOWED_SORT_FIELDS = [
        'customer.name',
        'project.name',
        'employee.lastName'
    ];

    public const INCLUDE_TIMESHEETS_APPROVED_ONLY = 'onlyApproved';
    public const INCLUDE_TIMESHEETS_ALL = 'all';

    public const INCLUDE_TIMESHEETS = [
        self::INCLUDE_TIMESHEETS_APPROVED_ONLY,
        self::INCLUDE_TIMESHEETS_ALL,
    ];

    public const TIMESHEET_APPROVED_STATE = 'APPROVED';

    private string $includeTimesheets = self::INCLUDE_TIMESHEETS_ALL;

    private ?int $customerId = null;
    private ?int $projectId = null;
    private ?int $empNumber = null;
    private ?bool $approvedOnly = null;

    public function getCustomerId(): ?int
    {
        return $this->customerId;
    }

    public function setCustomerId(?int $customerId): void
    {
        $this->customerId = $customerId;
    }

    public function getProjectId(): ?int
    {
        return $this->projectId;
    }

    public function setProjectId(?int $projectId): void
    {
        $this->projectId = $projectId;
    }

    public function getEmpNumber(): ?int
    {
        return $this->empNumber;
    }

    public function setEmpNumber(?int $empNumber): void
    {
        $this->empNumber = $empNumber;
    }

    public function getApprovedOnly(): ?bool
    {
        return $this->approvedOnly;
    }

    public function setApprovedOnly(?bool $approvedOnly): void
    {
        $this->approvedOnly = $approvedOnly;
    }

    public function getIncludeTimesheets(): string
    {
        return $this->includeTimesheets;
    }

    public function setIncludeTimesheets(?string $includeTimesheets): void
    {
        if (is_null($includeTimesheets)) {
            return;
        }

        if (!in_array($includeTimesheets, self::INCLUDE_TIMESHEETS)) {
            throw new InvalidArgumentException();
        }

        $this->includeTimesheets = $includeTimesheets;
    }
}