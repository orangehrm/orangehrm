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

use OrangeHRM\Admin\Dto\EmailSubscriptionSearchFilterParams;
use OrangeHRM\Entity\EmailNotification;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\ORM\Paginator;

class EmailSubscriptionDao extends BaseDao
{
    /**
     * @param EmailSubscriptionSearchFilterParams $emailSubscriptionSearchFilterParams
     * @return EmailNotification[]
     */
    public function getEmailSubscriptions(
        EmailSubscriptionSearchFilterParams $emailSubscriptionSearchFilterParams
    ): array {
        $paginator = $this->getEmailSubscriptionList($emailSubscriptionSearchFilterParams);
        return $paginator->getQuery()->execute();
    }

    /**
     * @param EmailSubscriptionSearchFilterParams $emailSubscriptionSearchFilterParams
     * @return Paginator
     */
    public function getEmailSubscriptionList(
        EmailSubscriptionSearchFilterParams $emailSubscriptionSearchFilterParams
    ): Paginator {
        $q = $this->createQueryBuilder(EmailNotification::class, 'emailSubscription');
        $this->setSortingAndPaginationParams($q, $emailSubscriptionSearchFilterParams);
        return $this->getPaginator($q);
    }

    /**
     * @param EmailSubscriptionSearchFilterParams $emailSubscriptionSearchFilterParams
     * @return int
     */
    public function getEmailSubscriptionListCount(
        EmailSubscriptionSearchFilterParams $emailSubscriptionSearchFilterParams
    ): int {
        $paginator = $this->getEmailSubscriptionList($emailSubscriptionSearchFilterParams);
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
     * @param EmailNotification $emailNotification
     * @return EmailNotification
     */
    public function saveEmailSubscription(EmailNotification $emailNotification): EmailNotification
    {
        $this->persist($emailNotification);
        return $emailNotification;
    }
}
