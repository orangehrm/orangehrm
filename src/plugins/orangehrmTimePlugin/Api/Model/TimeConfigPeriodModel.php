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

/**
 * @OA\Schema(
 *     schema="Time-TimeConfigPeriodModel",
 *     type="object",
 *     @OA\Property(property="startDay", type="string", format="date"),
 * )
 */
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Time\Dto\TimeConfigPeriod;

class TimeConfigPeriodModel implements Normalizable
{
    /**
     * @var TimeConfigPeriod
     */
    private TimeConfigPeriod $timeConfigPeriod;

    /**
     * @param TimeConfigPeriod $timeConfigPeriod
     */
    public function __construct(TimeConfigPeriod $timeConfigPeriod)
    {
        $this->timeConfigPeriod = $timeConfigPeriod;
    }

    /**
     * @return TimeConfigPeriod
     */
    public function getTimeConfigPeriod(): TimeConfigPeriod
    {
        return $this->timeConfigPeriod;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $timeConfigPeriod = $this->getTimeConfigPeriod();
        return [
            'startDay' => $timeConfigPeriod->getStartDay()
        ];
    }
}
