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

namespace OrangeHRM\Entity\Decorator;

use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\BuzzLikeOnShare;
use OrangeHRM\Entity\BuzzShare;
use OrangeHRM\Entity\Employee;

class BuzzLikeOnShareDecorator
{
    use EntityManagerHelperTrait;
    use DateTimeHelperTrait;

    protected BuzzLikeOnShare $buzzLikeOnShare;

    public function __construct(BuzzLikeOnShare $buzzLikeOnShare)
    {
        $this->buzzLikeOnShare = $buzzLikeOnShare;
    }

    /**
     * @return BuzzLikeOnShare
     */
    protected function getBuzzLikeOnShare(): BuzzLikeOnShare
    {
        return $this->buzzLikeOnShare;
    }

    /**
     * @param int $empNumber
     */
    public function setEmployeeByEmpNumber(int $empNumber): void
    {
        $employee = $this->getReference(Employee::class, $empNumber);
        $this->getBuzzLikeOnShare()->setEmployee($employee);
    }

    /**
     * @param int $shareId
     */
    public function setShareByShareId(int $shareId): void
    {
        $share = $this->getReference(BuzzShare::class, $shareId);
        $this->getBuzzLikeOnShare()->setShare($share);
    }

    /**
     * @return string
     */
    public function getLikedAtDate(): string
    {
        $dateTime = $this->getBuzzLikeOnShare()->getLikedAtUtc();
        return $this->getDateTimeHelper()->formatDate($dateTime);
    }

    /**
     * @return string
     */
    public function getLikedAtTime(): string
    {
        $dateTime = $this->getBuzzLikeOnShare()->getLikedAtUtc();
        return $this->getDateTimeHelper()->formatDateTimeToTimeString($dateTime);
    }
}
