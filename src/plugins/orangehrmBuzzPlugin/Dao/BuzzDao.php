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

use OrangeHRM\Buzz\Dto\BuzzCommentSearchFilterParams;
use OrangeHRM\Buzz\Dto\BuzzFeedFilterParams;
use OrangeHRM\Buzz\Dto\BuzzFeedPost;
use OrangeHRM\Buzz\Dto\BuzzPostShareSearchFilterParams;
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
            ->andWhere('share.type = :typePost')
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
            'IDENTITY(employee.employeeTerminationRecord), share.id, share.type, share.createdAtUtc,' .
            "share.numOfLikes, share.numOfComments, ($sharesCount), ($liked), share.text, post.id, post.text," .
            'post.createdAtUtc, postOwner.empNumber, postOwner.lastName, postOwner.firstName, postOwner.middleName,' .
            'postOwner.employeeId, IDENTITY(postOwner.employeeTerminationRecord), SIZE(post.photos), links.link)';
        $q->select($select)
            ->setParameter('typeShare', BuzzShare::TYPE_SHARE)
            ->setParameter('typePost', BuzzShare::TYPE_POST)
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
     * @return BuzzPost|null
     */
    public function getBuzzPostById(int $postId): ?BuzzPost
    {
        $q = $this->createQueryBuilder(BuzzPost::class, 'post')
            ->leftJoin('post.employee', 'employee')
            ->leftJoin('post.photos', 'photos')
            ->leftJoin('post.links', 'links');
        $q->andWhere($q->expr()->isNull('employee.purgedAt'))
            ->andWhere('post.id = :postId')
            ->setParameter('postId', $postId);
        return $q->getQuery()->getOneOrNullResult();
    }

    /**
     * @param int $postId
     * @return string
     */
    public function getBuzzPostTypeByPostId(int $postId): string
    {
        $q = $this->createQueryBuilder(BuzzPost::class, 'post')
            ->leftJoin('post.links', 'links')
            ->select('SIZE(post.photos) AS photoCount', 'links.link')
            ->andWhere('post.id = :postId')
            ->setParameter('postId', $postId);
        $result = $q->getQuery()->getOneOrNullResult();
        if ($result['photoCount'] > 0) {
            return BuzzShare::POST_TYPE_PHOTO;
        } elseif ($result['link'] !== null) {
            return BuzzShare::POST_TYPE_VIDEO;
        }
        return BuzzShare::POST_TYPE_TEXT;
    }

    /**
     * @param array $deletedPhotoIds
     * @param int $postId
     * @return int
     */
    public function deleteBuzzPostPhotos(array $deletedPhotoIds, int $postId): int
    {
        $q = $this->createQueryBuilder(BuzzPhoto::class, 'photos');
        $q->delete()
            ->where($q->expr()->in('photos.id', ':ids'))
            ->andWhere('photos.post = :postId')
            ->setParameter('ids', $deletedPhotoIds)
            ->setParameter('postId', $postId);
        return $q->getQuery()->execute();
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

    public function adjustLikeAndCommentCountsOnShares(): void
    {
        $likesCountQuery = $this->createQueryBuilder(BuzzLikeOnShare::class, 'l')
            ->leftJoin('l.employee', 'le')
            ->select('COUNT(l.id)')
            ->andWhere('IDENTITY(l.share) = share.id');
        $likesCountQuery->andWhere($likesCountQuery->expr()->isNull('le.purgedAt'));
        $likesCount = $likesCountQuery->getQuery()->getDQL();

        $commentsCountQuery = $this->createQueryBuilder(BuzzComment::class, 'c')
            ->leftJoin('c.employee', 'ce')
            ->select('COUNT(c.id)')
            ->andWhere('IDENTITY(c.share) = share.id');
        $commentsCountQuery->andWhere($commentsCountQuery->expr()->isNull('ce.purgedAt'));
        $commentsCount = $commentsCountQuery->getQuery()->getDQL();

        $this->createQueryBuilder(BuzzShare::class, 'share')
            ->update()
            ->set('share.numOfLikes', "($likesCount)")
            ->set('share.numOfComments', "($commentsCount)")
            ->getQuery()
            ->execute();
    }

    public function adjustLikeCountOnComments(): void
    {
        $likesOnCommentCountQuery = $this->createQueryBuilder(BuzzLikeOnComment::class, 'lc')
            ->leftJoin('lc.employee', 'lce')
            ->select('COUNT(lc.id)')
            ->andWhere('IDENTITY(lc.comment) = comment.id');
        $likesOnCommentCountQuery->andWhere($likesOnCommentCountQuery->expr()->isNull('lce.purgedAt'));
        $likesOnCommentCount = $likesOnCommentCountQuery->getQuery()->getDQL();

        $this->createQueryBuilder(BuzzComment::class, 'comment')
            ->update()
            ->set('comment.numOfLikes', "($likesOnCommentCount)")
            ->getQuery()
            ->execute();
    }

    /**
     * @param int $postId
     * @return BuzzShare|null
     */
    public function getBuzzShareByPostId(int $postId): ?BuzzShare
    {
        $q = $this->createQueryBuilder(BuzzShare::class, 'share')
            ->andWhere('share.post = :postId')
            ->andWhere('share.type = :type')
            ->setParameter('postId', $postId)
            ->setParameter('type', BuzzShare::TYPE_POST);

        return $q->getQuery()->getOneOrNullResult();
    }

    /**
     * @param BuzzPostShareSearchFilterParams $buzzPostShareSearchFilterParams
     * @return BuzzShare[]
     */
    public function getBuzzPostSharesList(BuzzPostShareSearchFilterParams $buzzPostShareSearchFilterParams): array
    {
        $qb = $this->getBuzzPostSharesQueryBuilderWrapper($buzzPostShareSearchFilterParams)->getQueryBuilder();
        return $qb->getQuery()->execute();
    }

    /**
     * @param BuzzPostShareSearchFilterParams $buzzPostShareSearchFilterParams
     * @return int
     */
    public function getBuzzPostSharesCount(BuzzPostShareSearchFilterParams $buzzPostShareSearchFilterParams): int
    {
        return $this->count($this->getBuzzPostSharesQueryBuilderWrapper($buzzPostShareSearchFilterParams)->getQueryBuilder());
    }

    /**
     * @param BuzzPostShareSearchFilterParams $buzzPostShareSearchFilterParams
     * @return QueryBuilderWrapper
     */
    private function getBuzzPostSharesQueryBuilderWrapper(BuzzPostShareSearchFilterParams $buzzPostShareSearchFilterParams): QueryBuilderWrapper
    {
        $qb = $this->createQueryBuilder(BuzzShare::class, 'share');
        $qb->andWhere($qb->expr()->eq('share.post', ':postId'))
            ->setParameter('postId', $buzzPostShareSearchFilterParams->getPostId());
        $qb->andWhere($qb->expr()->eq('share.type', ':type'))
            ->setParameter('type', BuzzShare::TYPE_SHARE);

        $this->setSortingAndPaginationParams($qb, $buzzPostShareSearchFilterParams);

        return $this->getQueryBuilderWrapper($qb);
    }
}
