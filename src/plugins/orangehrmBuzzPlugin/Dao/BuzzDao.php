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

namespace OrangeHRM\Buzz\Dao;

use OrangeHRM\Buzz\Dto\BuzzCommentSearchFilterParams;
use OrangeHRM\Buzz\Dto\BuzzFeedFilterParams;
use OrangeHRM\Buzz\Dto\BuzzFeedPost;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\BuzzComment;
use OrangeHRM\Entity\BuzzLikeOnComment;
use OrangeHRM\Entity\BuzzLikeOnShare;
use OrangeHRM\Entity\BuzzLink;
use OrangeHRM\Entity\BuzzPhoto;
use OrangeHRM\Entity\BuzzPost;
use OrangeHRM\Entity\BuzzShare;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\ORM\QueryBuilderWrapper;

class BuzzDao extends BaseDao
{
    /**
     * @return BuzzFeedPost[]
     */
    public function getBuzzFeedPosts(BuzzFeedFilterParams $buzzFeedFilterParams): array
    {
        $q = $this->getBuzzFeedPostsQueryBuilder($buzzFeedFilterParams)->getQueryBuilder()
            ->leftJoin('post.links', 'links');

        $sharesCount = $this->createQueryBuilder(BuzzShare::class, 's')
            ->select('COUNT(s.id)')
            ->andWhere('s.type = :typeShare')
            ->andWhere('IDENTITY(s.post) = post.id')
            ->getQuery()
            ->getDQL();
        $liked = $this->createQueryBuilder(BuzzLikeOnShare::class, 'likeOnShare')
            ->select('COUNT(likeOnShare.id)')
            ->andWhere('IDENTITY(likeOnShare.share) = share.id')
            ->andWhere('IDENTITY(likeOnShare.employee) = :loggedInEmpNumber')
            ->getQuery()
            ->getDQL();
        $select = 'NEW ' . BuzzFeedPost::class .
            '(employee.empNumber, employee.lastName, employee.firstName, employee.middleName, employee.employeeId,' .
            'IDENTITY(employee.employeeTerminationRecord), share.id, share.type, share.createdAtUtc, share.numOfLikes,' .
            "share.numOfComments, ($sharesCount), ($liked), share.text, post.id, post.text, post.createdAtUtc," .
            'postOwner.empNumber, postOwner.lastName, postOwner.firstName, postOwner.middleName, postOwner.employeeId,' .
            'IDENTITY(postOwner.employeeTerminationRecord), SIZE(post.photos), links.link)';
        $q->select($select)
            ->setParameter('typeShare', BuzzShare::TYPE_SHARE)
            ->setParameter('loggedInEmpNumber', $buzzFeedFilterParams->getAuthUserEmpNumber());

        return $q->getQuery()->execute();
    }

    /**
     * @param BuzzFeedFilterParams $buzzFeedFilterParams
     * @return int
     */
    public function getBuzzFeedPostsCount(BuzzFeedFilterParams $buzzFeedFilterParams): int
    {
        return $this->count($this->getBuzzFeedPostsQueryBuilder($buzzFeedFilterParams)->getQueryBuilder());
    }

    /**
     * @param BuzzFeedFilterParams $buzzFeedFilterParams
     * @return QueryBuilderWrapper
     */
    private function getBuzzFeedPostsQueryBuilder(BuzzFeedFilterParams $buzzFeedFilterParams): QueryBuilderWrapper
    {
        $q = $this->createQueryBuilder(BuzzShare::class, 'share')
            ->leftJoin('share.employee', 'employee')
            ->leftJoin('share.post', 'post')
            ->leftJoin('post.employee', 'postOwner');
        $q->andWhere($q->expr()->isNull('employee.purgedAt'));
        $q->andWhere($q->expr()->isNull('employee.employeeTerminationRecord'));
        $q->andWhere($q->expr()->isNull('postOwner.purgedAt'));
        $this->setSortingAndPaginationParams($q, $buzzFeedFilterParams);

        if ($buzzFeedFilterParams->getShareId() !== null) {
            $q->andWhere('share.id = :shareId')
                ->setParameter('shareId', $buzzFeedFilterParams->getShareId());
        }

        return $this->getQueryBuilderWrapper($q);
    }

    /**
     * @param int $postId
     * @return int[]
     */
    public function getBuzzPhotoIdsByPostId(int $postId): array
    {
        $q = $this->createQueryBuilder(BuzzPhoto::class, 'photo')
            ->select('photo.id')
            ->andWhere('photo.post = :postId')
            ->setParameter('postId', $postId);
        return array_map(static fn ($result) => (int)$result['id'], $q->getQuery()->getArrayResult());
    }

    /**
     * @param BuzzPost $buzzPost
     * @return BuzzPost
     */
    public function saveBuzzPost(BuzzPost $buzzPost): BuzzPost
    {
        $this->persist($buzzPost);
        return $buzzPost;
    }

    /**
     * @param BuzzShare $buzzShare
     * @return BuzzShare
     */
    public function saveBuzzShare(BuzzShare $buzzShare): BuzzShare
    {
        $this->persist($buzzShare);
        return $buzzShare;
    }

    /**
     * @param BuzzLink $buzzVideo
     * @return BuzzLink
     */
    public function saveBuzzVideo(BuzzLink $buzzVideo): BuzzLink
    {
        $this->persist($buzzVideo);
        return $buzzVideo;
    }

    /**
     * @param BuzzPhoto $buzzPhoto
     * @return BuzzPhoto
     */
    public function saveBuzzPhoto(BuzzPhoto $buzzPhoto): BuzzPhoto
    {
        $this->persist($buzzPhoto);
        return $buzzPhoto;
    }

    /**
     * @param BuzzComment $buzzComment
     * @return BuzzComment
     */
    public function saveBuzzComment(BuzzComment $buzzComment): BuzzComment
    {
        $this->persist($buzzComment);
        return $buzzComment;
    }

    /**
     * @param BuzzComment $buzzComment
     */
    public function deleteBuzzComment(BuzzComment $buzzComment): void
    {
        $this->remove($buzzComment);
    }

    /**
     * @param int $photoId
     * @return BuzzPhoto|null
     */
    public function getBuzzPhotoById(int $photoId): ?BuzzPhoto
    {
        return $this->getRepository(BuzzPhoto::class)->find($photoId);
    }

    /**
     * @param int $shareId
     * @return BuzzShare|null
     */
    public function getBuzzShareById(int $shareId): ?BuzzShare
    {
        $q = $this->createQueryBuilder(BuzzShare::class, 'share')
            ->leftJoin('share.employee', 'employee')
            ->leftJoin('share.post', 'post')
            ->leftJoin('post.employee', 'postOwner');
        $q->andWhere($q->expr()->isNull('employee.purgedAt'))
            ->andWhere($q->expr()->isNull('employee.employeeTerminationRecord'))
            ->andWhere($q->expr()->isNull('postOwner.purgedAt'))
            ->andWhere('share.id = :shareId')
            ->setParameter('shareId', $shareId);

        return $q->getQuery()->getOneOrNullResult();
    }

    /**
     * @param int $commentId
     * @param int|null $shareId
     * @return BuzzComment|null
     */
    public function getBuzzCommentById(int $commentId, ?int $shareId = null): ?BuzzComment
    {
        $q = $this->createQueryBuilder(BuzzComment::class, 'comment')
            ->leftJoin('comment.employee', 'employee')
            ->leftJoin('comment.share', 'share');
        $q->andWhere($q->expr()->isNull('employee.purgedAt'))
            ->andWhere('comment.id = :commentId')
            ->setParameter('commentId', $commentId);
        if ($shareId !== null) {
            $q->andWhere('share.id = :shareId')
                ->setParameter('shareId', $shareId);
        }

        return $q->getQuery()->getOneOrNullResult();
    }

    /**
     * @param BuzzCommentSearchFilterParams $filterParams
     * @return BuzzComment[]
     */
    public function getBuzzComments(BuzzCommentSearchFilterParams $filterParams): array
    {
        return $this->getBuzzCommentsPaginator($filterParams)->getQuery()->execute();
    }

    /**
     * @param BuzzCommentSearchFilterParams $filterParams
     * @return int
     */
    public function getBuzzCommentsCount(BuzzCommentSearchFilterParams $filterParams): int
    {
        return $this->getBuzzCommentsPaginator($filterParams)->count();
    }

    /**
     * @param BuzzCommentSearchFilterParams $filterParams
     * @return Paginator
     */
    private function getBuzzCommentsPaginator(BuzzCommentSearchFilterParams $filterParams): Paginator
    {
        $q = $this->createQueryBuilder(BuzzComment::class, 'comment')
            ->leftJoin('comment.employee', 'employee')
            ->leftJoin('comment.share', 'share');
        $this->setSortingAndPaginationParams($q, $filterParams);
        $q->andWhere($q->expr()->isNull('employee.purgedAt'))
            ->andWhere('IDENTITY(comment.share) = :shareId')
            ->setParameter('shareId', $filterParams->getShareId());

        return $this->getPaginator($q);
    }

    /**
     * @param int $commentId
     * @param int $empNumber
     * @return bool
     */
    public function isEmployeeLikedOnComment(int $commentId, int $empNumber): bool
    {
        $q = $this->createQueryBuilder(BuzzLikeOnComment::class, 'likeOnComment')
            ->andWhere('IDENTITY(likeOnComment.comment) = :commentId')
            ->andWhere('IDENTITY(likeOnComment.employee) = :empNumber')
            ->setParameter('commentId', $commentId)
            ->setParameter('empNumber', $empNumber);
        return $this->count($q) > 0;
    }

    /**
     * @param int $postId
     * @return int
     */
    public function deleteBuzzPost(int $postId): int
    {
        $qb = $this->createQueryBuilder(BuzzPost::class, 'post');

        $qb->delete()
            ->andWhere($qb->expr()->eq('post.id', ':id'))
            ->setParameter('id', $postId);

        return $qb->getQuery()->execute();
    }

    /**
     * @param int $shareId
     * @return int
     */
    public function deleteBuzzShare(int $shareId): int
    {
        $qb = $this->createQueryBuilder(BuzzShare::class, 'share');

        $qb->delete()
            ->andWhere($qb->expr()->eq('share.id', ':id'))
            ->setParameter('id', $shareId);

        return $qb->getQuery()->execute();
    }

    /**
     * @return int[]
     */
    public function getBuzzShareIdList(): array
    {
        $qb = $this->createQueryBuilder(BuzzShare::class, 'share');
        $qb->select('share.id');
        return array_column($qb->getQuery()->getArrayResult(), 'id');
    }

    /**
     * @param int $empNumber
     * @return int[]
     */
    public function getBuzzShareIdsByEmpNumber(int $empNumber): array
    {
        $qb = $this->createQueryBuilder(BuzzShare::class, 'share');
        $qb->select('share.id');
        $qb->andWhere($qb->expr()->eq('share.employee', ':empNumber'))
            ->setParameter('empNumber', $empNumber);


        return array_column($qb->getQuery()->getArrayResult(), 'id');
    }

    /**
     * @param int $postId
     * @return BuzzLink|null
     */
    public function getBuzzLinkByPostId(int $postId): ?BuzzLink
    {
        $q = $this->createQueryBuilder(BuzzLink::class, 'link')
            ->select()
            ->andWhere('link.post = :postId')
            ->setParameter('postId', $postId);

        return $q->getQuery()->getOneOrNullResult();
    }
}
