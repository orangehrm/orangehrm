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

namespace OrangeHRM\Leave\Dao;

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\Leave;
use OrangeHRM\Entity\LeaveComment;
use OrangeHRM\Entity\LeaveRequest;
use OrangeHRM\Entity\LeaveRequestComment;
use OrangeHRM\Leave\Dto\LeaveCommentSearchFilterParams;
use OrangeHRM\Leave\Dto\LeaveRequestCommentSearchFilterParams;
use OrangeHRM\ORM\Paginator;

class LeaveRequestCommentDao extends BaseDao
{
    /**
     * @param LeaveRequestCommentSearchFilterParams $leaveRequestCommentSearchFilterParams
     * @return array
     */
    public function searchLeaveRequestComments(
        LeaveRequestCommentSearchFilterParams $leaveRequestCommentSearchFilterParams
    ): array {
        $paginator = $this->getSearchLeaveRequestCommentPaginator($leaveRequestCommentSearchFilterParams);
        return $paginator->getQuery()->execute();
    }

    /**
     * Get Count of Search Query
     *
     * @param LeaveRequestCommentSearchFilterParams $leaveRequestCommentSearchParams
     * @return int
     */
    public function getSearchLeaveRequestCommentsCount(
        LeaveRequestCommentSearchFilterParams $leaveRequestCommentSearchParams
    ): int {
        $paginator = $this->getSearchLeaveRequestCommentPaginator($leaveRequestCommentSearchParams);
        return $paginator->count();
    }

    /**
     * @param LeaveRequestCommentSearchFilterParams $leaveRequestCommentSearchParams
     * @return Paginator
     */
    private function getSearchLeaveRequestCommentPaginator(
        LeaveRequestCommentSearchFilterParams $leaveRequestCommentSearchParams
    ): Paginator {
        $q = $this->createQueryBuilder(LeaveRequestComment::class, 'leaveRequestComment');
        $this->setSortingAndPaginationParams($q, $leaveRequestCommentSearchParams);

        if (!empty($leaveRequestCommentSearchParams->getLeaveRequestId())) {
            $q->andWhere('leaveRequestComment.leaveRequest = :leaveRequestId')
                ->setParameter('leaveRequestId', $leaveRequestCommentSearchParams->getLeaveRequestId());
        }

        return $this->getPaginator($q);
    }

    /**
     * @param LeaveRequestComment $leaveRequestComment
     * @return LeaveRequestComment
     */
    public function saveLeaveRequestComment(LeaveRequestComment $leaveRequestComment): LeaveRequestComment
    {
        $this->persist($leaveRequestComment);
        return $leaveRequestComment;
    }

    /**
     * @param int $leaveRequestId
     * @return LeaveRequest|null
     * @deprecated
     */
    public function getLeaveRequestById(int $leaveRequestId): ?LeaveRequest
    {
        return $this->getRepository(LeaveRequest::class)->find($leaveRequestId);
    }

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
     * @param LeaveCommentSearchFilterParams $leaveCommentSearchParams
     * @return int
     */
    public function getSearchLeaveCommentsCount(
        LeaveCommentSearchFilterParams $leaveCommentSearchParams
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

        if (!empty($leaveCommentSearchParams->getLeaveId())) {
            $q->leftJoin('leaveComment.leave', 'leave');
            $q->andWhere('leave.id = :leaveId')
                ->setParameter('leaveId', $leaveCommentSearchParams->getLeaveId());
        }

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
     * @return null|Leave
     * @deprecated
     */
    public function getLeaveById(int $leaveId): ?Leave
    {
        return $this->getRepository(Leave::class)->find($leaveId);
    }
}
