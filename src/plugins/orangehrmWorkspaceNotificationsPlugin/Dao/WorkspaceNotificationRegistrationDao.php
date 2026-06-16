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

namespace OrangeHRM\WorkspaceNotifications\Dao;

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\WorkspaceNotificationRegistration;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\WorkspaceNotifications\Dto\WorkspaceNotificationRegistrationSearchFilterParams;

class WorkspaceNotificationRegistrationDao extends BaseDao
{
    /**
     * @return WorkspaceNotificationRegistration[]
     */
    public function listRegistrations(WorkspaceNotificationRegistrationSearchFilterParams $filterParams): array
    {
        return $this->getRegistrationsPaginator($filterParams)->getQuery()->execute();
    }

    public function countRegistrations(WorkspaceNotificationRegistrationSearchFilterParams $filterParams): int
    {
        return $this->getRegistrationsPaginator($filterParams)->count();
    }

    private function getRegistrationsPaginator(
        WorkspaceNotificationRegistrationSearchFilterParams $filterParams
    ): Paginator {
        $q = $this->createQueryBuilder(WorkspaceNotificationRegistration::class, 'r');
        $this->setSortingAndPaginationParams($q, $filterParams);
        return $this->getPaginator($q);
    }

    /**
     * @return WorkspaceNotificationRegistration[]
     */
    public function listActiveRegistrations(): array
    {
        $q = $this->createQueryBuilder(WorkspaceNotificationRegistration::class, 'r')
            ->andWhere('r.active = :active')
            ->setParameter('active', true)
            ->orderBy('r.id', 'ASC');
        return $q->getQuery()->getResult();
    }

    public function getRegistration(int $id): ?WorkspaceNotificationRegistration
    {
        $registration = $this->getRepository(WorkspaceNotificationRegistration::class)->find($id);
        return $registration instanceof WorkspaceNotificationRegistration ? $registration : null;
    }

    public function saveRegistration(WorkspaceNotificationRegistration $registration): WorkspaceNotificationRegistration
    {
        $this->persist($registration);
        return $registration;
    }

    public function deleteRegistration(WorkspaceNotificationRegistration $registration): void
    {
        $this->remove($registration);
    }

    /**
     * @param int[] $keepIds Registrations whose IDs are in this list survive; the rest are deleted.
     */
    public function deleteRegistrationsNotIn(array $keepIds): void
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->delete(WorkspaceNotificationRegistration::class, 'r');
        if (count($keepIds) > 0) {
            $qb->andWhere('r.id NOT IN (:ids)')->setParameter('ids', $keepIds);
        }
        $qb->getQuery()->execute();
    }
}
