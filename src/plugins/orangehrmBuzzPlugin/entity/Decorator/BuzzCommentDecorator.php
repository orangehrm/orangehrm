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

use OrangeHRM\Buzz\Traits\Service\BuzzServiceTrait;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\BuzzComment;
use OrangeHRM\Entity\BuzzShare;
use OrangeHRM\Entity\Employee;

class BuzzCommentDecorator
{
    use EntityManagerHelperTrait;
    use DateTimeHelperTrait;
    use BuzzServiceTrait;
    use AuthUserTrait;

    /**
     * @var BuzzComment
     */
    protected BuzzComment $buzzComment;

    /**
     * @param BuzzComment $buzzComment
     */
    public function __construct(BuzzComment $buzzComment)
    {
        $this->buzzComment = $buzzComment;
    }

    /**
     * @return BuzzComment
     */
    protected function getBuzzComment(): BuzzComment
    {
        return $this->buzzComment;
    }

    /**
     * @param int $empNumber
     */
    public function setEmployeeByEmpNumber(int $empNumber): void
    {
        /** @var Employee|null $employee */
        $employee = $this->getReference(Employee::class, $empNumber);
        $this->getBuzzComment()->setEmployee($employee);
    }

    /**
     * @param int $shareId
     */
    public function setShareById(int $shareId): void
    {
        /** @var BuzzShare|null $buzzShare */
        $buzzShare = $this->getReference(BuzzShare::class, $shareId);
        $this->getBuzzComment()->setShare($buzzShare);
    }

    /**
     * @return string|null in Y-m-d format
     */
    public function getCreatedDate(): string
    {
        return $this->getDateTimeHelper()->formatDate($this->getBuzzComment()->getCreatedAtUtc());
    }

    /**
     * @return string|null in H:i format
     */
    public function getCreatedTime(): string
    {
        return $this->getDateTimeHelper()->formatDateTimeToTimeString($this->getBuzzComment()->getCreatedAtUtc());
    }

    /**
     * @return bool
     */
    public function isAuthEmployeeLiked(): bool
    {
        return $this->getBuzzService()->getBuzzDao()->isEmployeeLikedOnComment(
            $this->getBuzzComment()->getId(),
            $this->getAuthUser()->getEmpNumber()
        );
    }

    public function increaseNumOfLikesByOne(): void
    {
        $this->getBuzzComment()->setNumOfLikes($this->getBuzzComment()->getNumOfLikes() + 1);
    }

    public function decreaseNumOfLikesByOne(): void
    {
        $this->getBuzzComment()->setNumOfLikes($this->getBuzzComment()->getNumOfLikes() - 1);
    }
}
