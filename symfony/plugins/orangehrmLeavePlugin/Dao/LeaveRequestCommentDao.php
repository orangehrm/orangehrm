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
use OrangeHRM\Entity\LeaveRequest;
use OrangeHRM\Entity\LeaveRequestComment;
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
     * @param LeaveRequestCommentsearchFilterParams $leaveRequestCommentSearchParams
     * @return int
     */
    public function getSearchLeaveRequestCommentsCount(
        LeaveRequestCommentsearchFilterParams $leaveRequestCommentSearchParams
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
            $q->leftJoin('leaveRequestComment.leaveRequest', 'leaveRequest');
            $q->andWhere('leaveRequest.id = :leaveRequestId')
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
     * @return LeaveRequest|null|object
     */
    public function getLeaveRequestById(int $leaveRequestId): ?LeaveRequest
    {
        return $this->getRepository(LeaveRequest::class)->find($leaveRequestId);
    }
}
