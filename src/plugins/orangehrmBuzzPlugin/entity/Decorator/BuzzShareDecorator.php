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
use OrangeHRM\Entity\BuzzShare;
use OrangeHRM\Entity\Employee;

class BuzzShareDecorator
{
    use EntityManagerHelperTrait;

    /**
     * @var BuzzShare
     */
    protected BuzzShare $buzzShare;

    /**
     * @param BuzzShare $buzzShare
     */
    public function __construct(BuzzShare $buzzShare)
    {
        $this->buzzShare = $buzzShare;
    }

    /**
     * @return BuzzShare
     */
    protected function getBuzzShare(): BuzzShare
    {
        return $this->buzzShare;
    }

    /**
     * @param int $empNumber
     */
    public function setEmployeeByEmpNumber(int $empNumber): void
    {
        /** @var Employee|null $employee */
        $employee = $this->getReference(Employee::class, $empNumber);
        $this->getBuzzShare()->setEmployee($employee);
    }

    public function increaseNumOfCommentsByOne(): void
    {
        $this->getBuzzShare()->setNumOfComments($this->getBuzzShare()->getNumOfComments() + 1);
    }

    public function decreaseNumOfCommentsByOne(): void
    {
        $this->getBuzzShare()->setNumOfComments($this->getBuzzShare()->getNumOfComments() - 1);
    }

    public function increaseNumOfLikesByOne(): void
    {
        $this->getBuzzShare()->setNumOfLikes($this->getBuzzShare()->getNumOfLikes() + 1);
    }

    public function decreaseNumOfLikesByOne(): void
    {
        $this->getBuzzShare()->setNumOfLikes($this->getBuzzShare()->getNumOfLikes() - 1);
    }
}
