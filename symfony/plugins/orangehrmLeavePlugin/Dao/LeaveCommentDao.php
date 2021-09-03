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

namespace OrangeHRM\Leave\Dao;

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\Leave;
use OrangeHRM\Entity\LeaveComment;
use OrangeHRM\Leave\Dto\LeaveCommentSearchFilterParams;
use OrangeHRM\ORM\Paginator;

class LeaveCommentDao extends BaseDao
{
    /**
     * @param LeaveCommentSearchFilterParams $leaveCommentSearchFilterParams
     * @return array
     */
    public function searchLeaveComments(
        LeaveCommentSearchFilterParams $leaveCommentSearchFilterParams
    ): array {
        $paginator = $this->getSearchLeaveCommentPaginator($leaveCommentSearchFilterParams);
        return $paginator->getQuery()->execute();
    }

    /**
     * Get Count of Search Query
     *
     * @param LeaveCommentsearchFilterParams $leaveCommentSearchParams
     * @return int
     */
    public function getSearchLeaveCommentsCount(
        LeaveCommentsearchFilterParams $leaveCommentSearchParams
    ): int {
        $paginator = $this->getSearchLeaveCommentPaginator($leaveCommentSearchParams);
        return $paginator->count();
    }

    /**
     * @param LeaveCommentSearchFilterParams $leaveCommentSearchParams
     * @return Paginator
     */
    private function getSearchLeaveCommentPaginator(
        LeaveCommentSearchFilterParams $leaveCommentSearchParams
    ): Paginator {
        $q = $this->createQueryBuilder(LeaveComment::class, 'leaveComment');
        $this->setSortingAndPaginationParams($q, $leaveCommentSearchParams);

        if (!empty($leaveCommentSearchParams->getLeave())) {
            $q->leftJoin('leaveComment.leave', 'leave');
            $q->andWhere('leave.id = :leaveId')
                ->setParameter('leaveId', $leaveCommentSearchParams->getLeave()->getId());
        }
        $q->addOrderBy('leaveComment.createdAt');

        return $this->getPaginator($q);
    }

    /**
     * @param LeaveComment $leaveComment
     * @return LeaveComment
     */
    public function saveLeaveComment(LeaveComment $leaveComment): LeaveComment
    {
        $this->persist($leaveComment);
        return $leaveComment;
    }

    /**
     * @param int $leaveId
     * @return object|null|Leave
     */
    public function getLeaveById(int $leaveId): ?Leave
    {
        return $this->getRepository(Leave::class)->find($leaveId);
    }
}
