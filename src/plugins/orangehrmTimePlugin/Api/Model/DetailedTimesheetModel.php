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

namespace OrangeHRM\Time\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\CollectionNormalizable;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Traits\Service\NormalizerServiceTrait;
use OrangeHRM\Entity\TimesheetItem;
use OrangeHRM\Time\Dto\DetailedTimesheet;

class DetailedTimesheetModel implements CollectionNormalizable
{
    use DateTimeHelperTrait;
    use NormalizerServiceTrait;

    private DetailedTimesheet $detailedTimesheet;

    /**
     * @param DetailedTimesheet $detailedTimesheet
     */
    public function __construct(DetailedTimesheet $detailedTimesheet)
    {
        $this->detailedTimesheet = $detailedTimesheet;
    }

    public function toArray(): array
    {
        $timesheetRows = [];
        foreach ($this->detailedTimesheet->getRows() as $timesheetRow) {
            $row = [
                'project' => [
                    'id' => $timesheetRow->getProject()->getId(),
                    'name' => $timesheetRow->getProject()->getName(),
                    'deleted' => $timesheetRow->getProject()->isDeleted(),
                ],
                'customer' => [
                    'id' => $timesheetRow->getProject()->getCustomer()->getId(),
                    'name' => $timesheetRow->getProject()->getCustomer()->getName(),
                    'deleted' => $timesheetRow->getProject()->getCustomer()->isDeleted(),
                ],
                'activity' => [
                    'id' => $timesheetRow->getProjectActivity()->getId(),
                    'name' => $timesheetRow->getProjectActivity()->getName(),
                    'deleted' => $timesheetRow->getProjectActivity()->isDeleted(),
                ],
                'total' => $this->getNormalizerService()->normalize(
                    TotalDurationModel::class,
                    $timesheetRow->getTotal()
                ),
            ];
            foreach ($timesheetRow->getTimesheetItems() as $timesheetItem) {
                if (!$timesheetItem instanceof TimesheetItem) {
                    continue;
                }
                $date = $this->getDateTimeHelper()->formatDateTimeToYmd($timesheetItem->getDate());
                $duration = $timesheetItem->getDuration()
                    ? $this->getDateTimeHelper()->convertSecondsToTimeString($timesheetItem->getDuration())
                    : null;
                $row['dates'][$date] = [
                    'id' => $timesheetItem->getId(),
                    'date' => $date,
                    'comment' => $timesheetItem->getComment(),
                    'duration' => $duration,
                ];
            }
            $timesheetRows[] = $row;
        }
        return $timesheetRows;
    }
}
