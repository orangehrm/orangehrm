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

namespace OrangeHRM\Buzz\Dao;

use OrangeHRM\Buzz\Dto\BuzzLikeSearchFilterParams;
use OrangeHRM\Buzz\Traits\Service\BuzzServiceTrait;
use OrangeHRM\Core\Dao\BaseDao;
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
     * @param BuzzLikeSearchFilterParams $buzzLikeSearchFilterParams
     * @return BuzzLikeOnShare[]
     */
    public function getBuzzLikeOnShareList(BuzzLikeSearchFilterParams $buzzLikeSearchFilterParams): array
    {
        $qb = $this->getBuzzLikeOnShareQueryBuilderWrapper($buzzLikeSearchFilterParams)->getQueryBuilder();
        return $qb->getQuery()->execute();
    }

    /**
     * @param BuzzLikeSearchFilterParams $buzzLikeSearchFilterParams
     * @return int
     */
    public function getBuzzLikeOnShareCount(BuzzLikeSearchFilterParams $buzzLikeSearchFilterParams): int
    {
        $qb = $this->getBuzzLikeOnShareQueryBuilderWrapper($buzzLikeSearchFilterParams)->getQueryBuilder();
        return $this->getPaginator($qb)->count();
    }

    /**
     * @param BuzzLikeSearchFilterParams $buzzLikeSearchFilterParams
     * @return QueryBuilderWrapper
     */
    private function getBuzzLikeOnShareQueryBuilderWrapper(BuzzLikeSearchFilterParams $buzzLikeSearchFilterParams): QueryBuilderWrapper
    {
        $qb = $this->createQueryBuilder(BuzzLikeOnShare::class, 'shareLike');
        $qb->leftJoin('shareLike.employee', 'employee');

        $qb->andWhere($qb->expr()->isNull('employee.purgedAt'));

        if (!is_null($buzzLikeSearchFilterParams->getShareId())) {
            $qb->andWhere($qb->expr()->eq('shareLike.share', ':share'))
                ->setParameter('share', $buzzLikeSearchFilterParams->getShareId());
        }

        $this->setSortingAndPaginationParams($qb, $buzzLikeSearchFilterParams);

        return $this->getQueryBuilderWrapper($qb);
    }
}
