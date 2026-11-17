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

namespace OrangeHRM\Authentication\Dao;

use DateTime;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\LoginLog;
use OrangeHRM\ORM\ListSorter;

class LoginLogDao extends BaseDao
{
    /**
     * @param LoginLog $loginLog
     * @return LoginLog
     */
    public function saveLoginLog(LoginLog $loginLog): LoginLog
    {
        $this->persist($loginLog);
        return $loginLog;
    }

    /**
     * Base URLs that users with the predefined Admin role signed in on since the given time,
     * most recently used first
     *
     * @param DateTime $since
     * @return string[]
     */
    public function getAdminBaseUrlsSince(DateTime $since): array
    {
        $q = $this->createQueryBuilder(LoginLog::class, 'loginLog');
        $q->select('loginLog.baseUrl AS baseUrl', 'MAX(loginLog.loginTime) AS lastLogin');
        $q->andWhere($q->expr()->eq('loginLog.userRoleName', ':adminRole'))
            ->setParameter('adminRole', 'Admin');
        $q->andWhere($q->expr()->eq('loginLog.userRolePredefined', ':predefined'))
            ->setParameter('predefined', 1);
        $q->andWhere($q->expr()->isNotNull('loginLog.baseUrl'));
        $q->andWhere($q->expr()->gte('loginLog.loginTime', ':since'))
            ->setParameter('since', $since);
        $q->groupBy('loginLog.baseUrl');
        $q->orderBy('lastLogin', ListSorter::DESCENDING);
        return array_column($q->getQuery()->getArrayResult(), 'baseUrl');
    }
}
