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

use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;

/**
 * @OA\Schema(
 *     schema="Time-TotalDurationModel",
 *     type="object",
 *     @OA\Property(property="hours", type="integer"),
 *     @OA\Property(property="minutes", type="string"),
 *     @OA\Property(property="label", type="string"),
 * )
 */
class TotalDurationModel implements Normalizable
{
    use DateTimeHelperTrait;

    private int $duration;

    /**
     * @param int $duration
     */
    public function __construct(int $duration)
    {
        $this->duration = $duration;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $hours = floor($this->duration / 3600);
        $minutes = ($this->duration / 60) % 60;
        return [
            'hours' => $hours,
            'minutes' => $minutes,
            'label' => $this->getDateTimeHelper()->convertSecondsToTimeString($this->duration),
        ];
    }
}
