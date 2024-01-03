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

namespace OrangeHRM\Buzz\Dao;

use OrangeHRM\Buzz\Dto\BuzzLikeOnCommentSearchFilterParams;
use OrangeHRM\Buzz\Dto\BuzzLikeOnShareSearchFilterParams;
use OrangeHRM\Buzz\Traits\Service\BuzzServiceTrait;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\BuzzLikeOnComment;
use OrangeHRM\Entity\BuzzLikeOnShare;
use OrangeHRM\ORM\QueryBuilderWrapper;

class BuzzLikeDao extends BaseDao
{
    use BuzzServiceTrait;

    /**
     * @param BuzzLikeOnShare $buzzLikeOnShare
     * @return BuzzLikeOnShare
     */
    public function saveBuzzLikeOnShare(BuzzLikeOnShare $buzzLikeOnShare): BuzzLikeOnShare
    {
        $this->persist($buzzLikeOnShare);
        return $buzzLikeOnShare;
    }

    /**
     * @param BuzzLikeOnComment $buzzLikeOnComment
     * @return BuzzLikeOnComment
     */
    public function saveBuzzLikeOnComment(BuzzLikeOnComment $buzzLikeOnComment): BuzzLikeOnComment
    {
        $this->persist($buzzLikeOnComment);
        return $buzzLikeOnComment;
    }

    /**
     * @param int $shareId
     * @param int $empNumber
     * @return int
     */
    public function deleteBuzzLikeOnShare(int $shareId, int $empNumber): int
    {
        $qb = $this->createQueryBuilder(BuzzLikeOnShare::class, 'shareLike');
        $qb->delete()
            ->andWhere($qb->expr()->eq('shareLike.share', ':shareId'))
            ->andWhere($qb->expr()->eq('shareLike.employee', ':empNumber'))
            ->setParameter('shareId', $shareId)
            ->setParameter('empNumber', $empNumber);

        return $qb->getQuery()->execute();
    }

    /**
     * @param int $commentId
     * @param int $empNumber
     * @return int
     */
    public function deleteBuzzLikeOnComment(int $commentId, int $empNumber): int
    {
        $qb = $this->createQueryBuilder(BuzzLikeOnComment::class, 'commentLike');
        $qb->delete()
            ->andWhere($qb->expr()->eq('commentLike.comment', ':commentId'))
            ->andWhere($qb->expr()->eq('commentLike.employee', ':empNumber'))
            ->setParameter('commentId', $commentId)
            ->setParameter('empNumber', $empNumber);

        return $qb->getQuery()->execute();
    }

    /**
     * @param int $shareId
     * @param int $empNumber
     * @return BuzzLikeOnShare|null
     */
    public function getBuzzLikeOnShareByShareIdAndEmpNumber(int $shareId, int $empNumber): ?BuzzLikeOnShare
    {
        return $this->getRepository(BuzzLikeOnShare::class)->findOneBy(
            [
                'share' => $shareId,
                'employee' => $empNumber
            ]
        );
    }

    /**
     * @param int $commentId
     * @param int $empNumber
     * @return BuzzLikeOnComment|null
     */
    public function getBuzzLikeOnCommentByShareIdAndEmpNumber(int $commentId, int $empNumber): ?BuzzLikeOnComment
    {
        return $this->getRepository(BuzzLikeOnComment::class)->findOneBy(
            [
                'comment' => $commentId,
                'employee' => $empNumber
            ]
        );
    }

    /**
     * @param BuzzLikeOnShareSearchFilterParams $buzzLikeOnShareSearchFilterParams
     * @return BuzzLikeOnShare[]
     */
    public function getBuzzLikeOnShareList(BuzzLikeOnShareSearchFilterParams $buzzLikeOnShareSearchFilterParams): array
    {
        $qb = $this->getBuzzLikeOnShareQueryBuilderWrapper($buzzLikeOnShareSearchFilterParams)->getQueryBuilder();
        return $qb->getQuery()->execute();
    }

    /**
     * @param BuzzLikeOnShareSearchFilterParams $buzzLikeOnShareSearchFilterParams
     * @return int
     */
    public function getBuzzLikeOnShareCount(BuzzLikeOnShareSearchFilterParams $buzzLikeOnShareSearchFilterParams): int
    {
        $qb = $this->getBuzzLikeOnShareQueryBuilderWrapper($buzzLikeOnShareSearchFilterParams)->getQueryBuilder();
        return $this->getPaginator($qb)->count();
    }

    /**
     * @param BuzzLikeOnShareSearchFilterParams $buzzLikeOnShareSearchFilterParams
     * @return QueryBuilderWrapper
     */
    private function getBuzzLikeOnShareQueryBuilderWrapper(BuzzLikeOnShareSearchFilterParams $buzzLikeOnShareSearchFilterParams): QueryBuilderWrapper
    {
        $qb = $this->createQueryBuilder(BuzzLikeOnShare::class, 'shareLike');
        $qb->leftJoin('shareLike.employee', 'employee');

        $qb->andWhere($qb->expr()->isNull('employee.purgedAt'));

        if (!is_null($buzzLikeOnShareSearchFilterParams->getShareId())) {
            $qb->andWhere($qb->expr()->eq('shareLike.share', ':shareId'))
                ->setParameter('shareId', $buzzLikeOnShareSearchFilterParams->getShareId());
        }

        $this->setSortingAndPaginationParams($qb, $buzzLikeOnShareSearchFilterParams);

        return $this->getQueryBuilderWrapper($qb);
    }

    /**
     * @param BuzzLikeOnCommentSearchFilterParams $buzzLikeOnCommentSearchFilterParams
     * @return BuzzLikeOnComment[]
     */
    public function getBuzzLikeOnCommentList(BuzzLikeOnCommentSearchFilterParams $buzzLikeOnCommentSearchFilterParams): array
    {
        $qb = $this->getBuzzLikeOnCommentQueryBuilderWrapper($buzzLikeOnCommentSearchFilterParams)->getQueryBuilder();
        return $qb->getQuery()->execute();
    }

    /**
     * @param BuzzLikeOnCommentSearchFilterParams $buzzLikeOnCommentSearchFilterParams
     * @return int
     */
    public function getBuzzLikeOnCommentCount(BuzzLikeOnCommentSearchFilterParams $buzzLikeOnCommentSearchFilterParams): int
    {
        $qb = $this->getBuzzLikeOnCommentQueryBuilderWrapper($buzzLikeOnCommentSearchFilterParams)->getQueryBuilder();
        return $this->getPaginator($qb)->count();
    }

    /**
     * @param BuzzLikeOnCommentSearchFilterParams $buzzLikeOnCommentSearchFilterParams
     * @return QueryBuilderWrapper
     */
    private function getBuzzLikeOnCommentQueryBuilderWrapper(BuzzLikeOnCommentSearchFilterParams $buzzLikeOnCommentSearchFilterParams): QueryBuilderWrapper
    {
        $qb = $this->createQueryBuilder(BuzzLikeOnComment::class, 'commentLike');
        $qb->leftJoin('commentLike.employee', 'employee');

        $qb->andWhere($qb->expr()->isNull('employee.purgedAt'));

        if (!is_null($buzzLikeOnCommentSearchFilterParams->getCommentId())) {
            $qb->andWhere($qb->expr()->eq('commentLike.comment', ':commentId'))
                ->setParameter('commentId', $buzzLikeOnCommentSearchFilterParams->getCommentId());
        }

        $this->setSortingAndPaginationParams($qb, $buzzLikeOnCommentSearchFilterParams);

        return $this->getQueryBuilderWrapper($qb);
    }
}
