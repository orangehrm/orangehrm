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

namespace OrangeHRM\Time\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\CollectionNormalizable;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Traits\Service\NormalizerServiceTrait;
use OrangeHRM\Entity\TimesheetItem;
use OrangeHRM\Time\Dto\DetailedTimesheet;

/**
 * @OA\Schema(
 *     schema="Time-DetailedTimesheetModel",
 *     type="object",
 *     @OA\Property(property="project", type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="name", type="string"),
 *         @OA\Property(property="deleted", type="boolean"),
 *     ),
 *     @OA\Property(property="customer", type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="name", type="string"),
 *         @OA\Property(property="deleted", type="boolean"),
 *     ),
 *     @OA\Property(property="activity", type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="name", type="string"),
 *         @OA\Property(property="deleted", type="boolean"),
 *     ),
 *     @OA\Property(property="total", type="object",
 *         @OA\Property(property="hours", type="integer"),
 *         @OA\Property(property="minutes", type="integer"),
 *         @OA\Property(property="label", type="string"),
 *     ),
 *     @OA\Property(property="dates", type="object",
 *         @OA\AdditionalProperties(
 *             @OA\Property(property="id", type="integer"),
 *             @OA\Property(property="date", type="string", format="date"),
 *             @OA\Property(property="comment", type="string", nullable=true),
 *             @OA\Property(property="duration", type="string", nullable=true),
 *         ),
 *     ),
 * )
 */
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
