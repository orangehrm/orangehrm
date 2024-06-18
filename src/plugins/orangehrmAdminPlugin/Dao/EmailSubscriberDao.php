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

namespace OrangeHRM\Admin\Dao;

use OrangeHRM\Admin\Dto\EmailSubscriberSearchFilterParams;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\EmailNotification;
use OrangeHRM\Entity\EmailSubscriber;
use OrangeHRM\ORM\Paginator;

class EmailSubscriberDao extends BaseDao
{
    /**
     * @param int $emailSubscriptionId
     * @param EmailSubscriberSearchFilterParams $emailSubscriberSearchFilterParams
     * @return EmailSubscriber[]
     */
    public function getEmailSubscribersByEmailSubscriptionId(
        int $emailSubscriptionId,
        EmailSubscriberSearchFilterParams $emailSubscriberSearchFilterParams
    ): array {
        $paginator = $this->getEmailSubscriberByEmailSubscriptionIdPaginator(
            $emailSubscriptionId,
            $emailSubscriberSearchFilterParams
        );
        return $paginator->getQuery()->execute();
    }

    /**
     * @param int $emailSubscriptionId
     * @param EmailSubscriberSearchFilterParams $emailSubscriberSearchFilterParams
     * @return Paginator
     */
    protected function getEmailSubscriberByEmailSubscriptionIdPaginator(
        int $emailSubscriptionId,
        EmailSubscriberSearchFilterParams $emailSubscriberSearchFilterParams
    ): Paginator {
        $q = $this->createQueryBuilder(EmailSubscriber::class, 'emailSubscriber');
        $q->leftJoin('emailSubscriber.emailNotification', 'emailNotification');
        $q->andWhere('emailNotification.id = :emailSubscriptionId');
        $q->setParameter('emailSubscriptionId', $emailSubscriptionId);
        $this->setSortingAndPaginationParams($q, $emailSubscriberSearchFilterParams);

        if (is_bool($emailSubscriberSearchFilterParams->getEnabled())) {
            $q->andWhere('emailNotification.enabled = :enabled')
                ->setParameter('enabled', $emailSubscriberSearchFilterParams->getEnabled());
        }
        return $this->getPaginator($q);
    }

    /**
     * @param int $emailSubscriptionId
     * @param EmailSubscriberSearchFilterParams $emailSubscriberSearchFilterParams
     * @return int
     */
    public function getEmailSubscriberListCountByEmailSubscriptionId(
        int $emailSubscriptionId,
        EmailSubscriberSearchFilterParams $emailSubscriberSearchFilterParams
    ): int {
        $paginator = $this->getEmailSubscriberByEmailSubscriptionIdPaginator(
            $emailSubscriptionId,
            $emailSubscriberSearchFilterParams
        );
        return $paginator->count();
    }

    /**
     * @param int $emailSubscriptionId
     * @return EmailNotification|null
     */
    public function getEmailSubscriptionById(int $emailSubscriptionId): ?EmailNotification
    {
        $emailSubscription = $this->getRepository(EmailNotification::class)->find($emailSubscriptionId);
        return ($emailSubscription instanceof EmailNotification) ? $emailSubscription : null;
    }

    /**
     * @param EmailSubscriber $emailSubscriber
     * @return EmailSubscriber
     */
    public function saveEmailSubscriber(EmailSubscriber $emailSubscriber): EmailSubscriber
    {
        $this->persist($emailSubscriber);
        return $emailSubscriber;
    }

    /**
     * @param int $emailSubscriberId
     * @param int $subscriptionId
     * @return EmailSubscriber|null
     */
    public function getEmailSubscriberById(int $emailSubscriberId, int $subscriptionId): ?EmailSubscriber
    {
        $emailSubscription = $this->getEmailSubscriptionById($subscriptionId);
        $emailSubscriber = $this->getRepository(EmailSubscriber::class)
            ->findOneBy(['id' => $emailSubscriberId, 'emailNotification' => $emailSubscription]);
        return ($emailSubscriber instanceof EmailSubscriber) ? $emailSubscriber : null;
    }

    /**
     * @param int[] $ids
     * @return int[]
     */
    public function getExistingEmailSubscriberIds(array $ids): array
    {
        $qb = $this->createQueryBuilder(EmailSubscriber::class, 'emailSubscriber');
        $qb->select('emailSubscriber.id')
            ->andWhere($qb->expr()->in('emailSubscriber.id', ':ids'))
            ->setParameter('ids', $ids)
            ->addOrderBy('emailSubscriber.id');

        return $qb->getQuery()->getSingleColumnResult();
    }

    /**
     * @param int[] $emailSubscriberIds
     * @return int
     */
    public function deleteEmailSubscribersByIds(array $emailSubscriberIds): int
    {
        $q = $this->createQueryBuilder(EmailSubscriber::class, 'es');
        $q->delete()
            ->where($q->expr()->in('es.id', ':ids'))
            ->setParameter('ids', $emailSubscriberIds);
        return $q->getQuery()->execute();
    }
}
