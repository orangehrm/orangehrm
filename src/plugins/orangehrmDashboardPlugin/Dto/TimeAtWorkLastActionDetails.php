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

namespace OrangeHRM\Dashboard\Dto;

class TimeAtWorkLastActionDetails
{
    /**
     * @var string|null
     */
    private ?string $state = null;

    /**
     * @var string|null
     */
    private ?string $utcDate = null;

    /**
     * @var string|null
     */
    private ?string $utcTime = null;

    /**
     * @var string|null
     */
    private ?string $userDate = null;

    /**
     * @var string|null
     */
    private ?string $userTime = null;

    /**
     * @var string|null
     */
    private ?string $timezoneOffset = null;

    /**
     * @param string|null $status
     * @param string|null $utcDate
     * @param string|null $utcTime
     * @param string|null $userDate
     * @param string|null $userTime
     * @param string|null $timezoneOffset
     */
    public function __construct(
        ?string $status = null,
        ?string $utcDate = null,
        ?string $utcTime = null,
        ?string $userDate = null,
        ?string $userTime = null,
        ?string $timezoneOffset = null
    ) {
        $this->state = $status;
        $this->utcDate = $utcDate;
        $this->utcTime = $utcTime;
        $this->userDate = $userDate;
        $this->userTime = $userTime;
        $this->timezoneOffset = $timezoneOffset;
    }

    /**
     * @return string|null
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * @return string|null
     */
    public function getUtcDate(): ?string
    {
        return $this->utcDate;
    }

    /**
     * @return string|null
     */
    public function getUtcTime(): ?string
    {
        return $this->utcTime;
    }

    /**
     * @return string|null
     */
    public function getUserDate(): ?string
    {
        return $this->userDate;
    }

    /**
     * @return string|null
     */
    public function getUserTime(): ?string
    {
        return $this->userTime;
    }

    /**
     * @return string|null
     */
    public function getTimezoneOffset(): ?string
    {
        return $this->timezoneOffset;
    }
}
