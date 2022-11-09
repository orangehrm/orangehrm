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

use OrangeHRM\Buzz\Dto\BuzzFeedFilterParams;
use OrangeHRM\Buzz\Dto\BuzzFeedPost;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\BuzzLikeOnShare;
use OrangeHRM\Entity\BuzzPhoto;
use OrangeHRM\Entity\BuzzShare;
use OrangeHRM\Entity\BuzzLink;
use OrangeHRM\Entity\BuzzPost;

class BuzzDao extends BaseDao
{
    /**
     * @return BuzzFeedPost[]
     */
    public function getBuzzFeedPosts(BuzzFeedFilterParams $buzzFeedFilterParams): array
    {
        $q = $this->createQueryBuilder(BuzzShare::class, 'share')
            ->leftJoin('share.employee', 'employee')
            ->leftJoin('share.post', 'post')
            ->leftJoin('post.employee', 'postOwner')
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
            'IDENTITY(employee.employeeTerminationRecord), share.id, share.type, share.createdAt, share.numOfLikes,' .
            "share.numOfComments, ($sharesCount), ($liked), share.text, post.id, post.text, post.createdAt," .
            'postOwner.empNumber, postOwner.lastName, postOwner.firstName, postOwner.middleName, postOwner.employeeId,' .
            'IDENTITY(postOwner.employeeTerminationRecord), SIZE(post.photos), links.link)';
        $q->select($select)
            ->setParameter('typeShare', BuzzShare::TYPE_SHARE)
            ->setParameter('loggedInEmpNumber', $buzzFeedFilterParams->getAuthUserEmpNumber());

        $q->andWhere($q->expr()->isNull('employee.purgedAt'));
        $q->andWhere($q->expr()->isNull('employee.employeeTerminationRecord'));
        $q->andWhere($q->expr()->isNull('postOwner.purgedAt'));
        $this->setSortingAndPaginationParams($q, $buzzFeedFilterParams);

        return $q->getQuery()->execute();
    }

    /**
     * @param BuzzFeedFilterParams $buzzFeedFilterParams
     * @return int
     */
    public function getBuzzFeedPostsCount(BuzzFeedFilterParams $buzzFeedFilterParams): int
    {
        $q = $this->createQueryBuilder(BuzzShare::class, 'share')
            ->leftJoin('share.employee', 'employee')
            ->leftJoin('share.post', 'post')
            ->leftJoin('post.employee', 'postOwner');
        $q->andWhere($q->expr()->isNull('employee.purgedAt'));
        $q->andWhere($q->expr()->isNull('employee.employeeTerminationRecord'));
        $q->andWhere($q->expr()->isNull('postOwner.purgedAt'));
        $this->setSortingAndPaginationParams($q, $buzzFeedFilterParams);

        return $this->count($q);
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
        return $q->getQuery()->getSingleColumnResult();
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
     * @param int $photoId
     * @return BuzzPhoto|null
     */
    public function getBuzzPhotoById(int $photoId): ?BuzzPhoto
    {
        return $this->getRepository(BuzzPhoto::class)->find($photoId);
    }
}
