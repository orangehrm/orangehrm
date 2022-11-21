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
 * Boston, MA 02110-1301, USA
 */

namespace OrangeHRM\Entity\Decorator;

use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\BuzzComment;
use OrangeHRM\Entity\BuzzLikeOnComment;
use OrangeHRM\Entity\Employee;

class BuzzLikeOnCommentDecorator
{
    use EntityManagerHelperTrait;
    use DateTimeHelperTrait;

    protected BuzzLikeOnComment $buzzLikeOnComment;

    public function __construct(BuzzLikeOnComment $buzzLikeOnComment)
    {
        $this->buzzLikeOnComment = $buzzLikeOnComment;
    }

    /**
     * @return BuzzLikeOnComment
     */
    protected function getBuzzLikeOnComment(): BuzzLikeOnComment
    {
        return $this->buzzLikeOnComment;
    }

    /**
     * @param int $empNumber
     */
    public function setEmployeeByEmpNumber(int $empNumber): void
    {
        $employee = $this->getReference(Employee::class, $empNumber);
        $this->getBuzzLikeOnComment()->setEmployee($employee);
    }

    /**
     * @param int $commentId
     */
    public function setCommentByCommentId(int $commentId): void
    {
        $comment = $this->getReference(BuzzComment::class, $commentId);
        $this->getBuzzLikeOnComment()->setComment($comment);
    }

    /**
     * @return string
     */
    public function getLikedAtDate(): string
    {
        $dateTime = $this->getBuzzLikeOnComment()->getLikedAtUtc();
        return $this->getDateTimeHelper()->formatDate($dateTime);
    }

    /**
     * @return string
     */
    public function getLikedAtTime(): string
    {
        $dateTime = $this->getBuzzLikeOnComment()->getLikedAtUtc();
        return $this->getDateTimeHelper()->formatDateTimeToTimeString($dateTime);
    }
}
