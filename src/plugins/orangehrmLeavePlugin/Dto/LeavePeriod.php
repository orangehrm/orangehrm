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

namespace OrangeHRM\Leave\Dto;

use DateTime;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;

class LeavePeriod
{
    use DateTimeHelperTrait;

    /**
     * @var DateTime|null
     */
    private ?DateTime $startDate;

    /**
     * @var DateTime|null
     */
    private ?DateTime $endDate;

    /**
     * @param DateTime|null $startDate
     * @param DateTime|null $endDate
     */
    public function __construct(?DateTime $startDate = null, ?DateTime $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * @return DateTime|null
     */
    public function getStartDate(): ?DateTime
    {
        return $this->startDate;
    }

    /**
     * @return string|null
     */
    public function getYmdStartDate(): ?string
    {
        return $this->getDateTimeHelper()->formatDateTimeToYmd($this->getStartDate());
    }

    /**
     * @param DateTime|null $startDate
     */
    public function setStartDate(?DateTime $startDate): void
    {
        $this->startDate = $startDate;
    }

    /**
     * @return DateTime|null
     */
    public function getEndDate(): ?DateTime
    {
        return $this->endDate;
    }

    /**
     * @return string|null
     */
    public function getYmdEndDate(): ?string
    {
        return $this->getDateTimeHelper()->formatDateTimeToYmd($this->getEndDate());
    }

    /**
     * @param DateTime|null $endDate
     */
    public function setEndDate(?DateTime $endDate): void
    {
        $this->endDate = $endDate;
    }
}
