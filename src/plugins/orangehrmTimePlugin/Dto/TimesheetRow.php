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

namespace OrangeHRM\Time\Dto;

use DateTime;
use LogicException;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\Project;
use OrangeHRM\Entity\ProjectActivity;
use OrangeHRM\Entity\TimesheetItem;

class TimesheetRow
{
    use DateTimeHelperTrait;

    private Project $project;
    private ProjectActivity $projectActivity;
    private array $dates;
    private int $total = 0;

    /**
     * @param Project $project
     * @param ProjectActivity $projectActivity
     * @param DateTime[] $dates
     */
    public function __construct(Project $project, ProjectActivity $projectActivity, array $dates)
    {
        $this->project = $project;
        $this->projectActivity = $projectActivity;
        $this->setDates($dates);
    }

    /**
     * @return Project
     */
    public function getProject(): Project
    {
        return $this->project;
    }

    /**
     * @return ProjectActivity
     */
    public function getProjectActivity(): ProjectActivity
    {
        return $this->projectActivity;
    }

    /**
     * @param DateTime[] $dates
     */
    protected function setDates(array $dates): void
    {
        foreach ($dates as $date) {
            $date = $this->getDateTimeHelper()->formatDateTimeToYmd($date);
            $this->dates[$date] = null;
        }
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @param int $by
     * @return int
     */
    public function incrementTotal(int $by): int
    {
        $this->total = $this->total + $by;
        return $this->total;
    }

    /***
     * @param TimesheetItem $timesheetItem
     */
    public function assignTimesheetItem(TimesheetItem $timesheetItem): void
    {
        $date = $this->getDateTimeHelper()->formatDateTimeToYmd($timesheetItem->getDate());
        if (!is_null($this->dates[$date])) {
            throw new LogicException(
                "Already assigned a timesheet item for $date with item id:" . $timesheetItem->getId() .
                '. Project id:' . $this->getProject()->getId() . ', Activity id:' . $this->getProjectActivity()->getId()
            );
        }
        $this->dates[$date] = $timesheetItem;
    }

    /**
     * @return TimesheetItem[]
     */
    public function getTimesheetItems(): array
    {
        return array_values($this->dates);
    }
}
