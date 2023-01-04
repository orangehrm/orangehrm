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

namespace OrangeHRM\Claim\Dao;

use Exception;
use OrangeHRM\Claim\Dto\ClaimEventSearchFilterParams;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\ClaimEvent;
use OrangeHRM\ORM\ListSorter;
use OrangeHRM\ORM\Paginator;

class ClaimEventDao extends BaseDao
{
    /**
     * @param ClaimEvent $claimEvent
     * @return ClaimEvent
     */
    public function saveEvent(ClaimEvent $claimEvent): ClaimEvent
    {
        $this->persist($claimEvent);
        return $claimEvent;
    }

    /**
     * @param ClaimEventSearchFilterParams $claimEventSearchFilterParams
     * @return array
     */
    public function getClaimEvents(ClaimEventSearchFilterParams $claimEventSearchFilterParams): array
    {
        return $this->getClaimEventPaginator($claimEventSearchFilterParams)->getQuery()->execute();
    }

    /**
     * @param ClaimEventSearchFilterParams $claimEventSearchFilterParams
     * @return Paginator
     */
    public function getClaimEventPaginator(ClaimEventSearchFilterParams $claimEventSearchFilterParams): Paginator
    {
        $qb=$this->createQueryBuilder(ClaimEvent::class, 'ce');
        $this->setSortingAndPaginationParams($qb, $claimEventSearchFilterParams);
        return $this->getPaginator($qb);
    }

    /**
     * @return array
     * @throws DaoException
     */
    public function getClaimEventList(): array
    {
        try {
            $qb= $this->createQueryBuilder(ClaimEvent::class, 'ce');
            $qb->addOrderBy('ce.name', ListSorter::ASCENDING);
            return $qb->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
}
