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

namespace OrangeHRM\Core\Authorization\Dao;

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\Screen;

class ScreenDao extends BaseDao
{
    /**
     * Get screen for given module and action
     *
     * @param string $module Module Name
     * @param string $action
     * @return Screen|null
     */
    public function getScreen(string $module, string $action): ?Screen
    {
        $q = $this->createQueryBuilder(Screen::class, 's');
        $q->leftJoin('s.module', 'm');
        $q->andWhere('m.name = :moduleName')
            ->setParameter('moduleName', $module);
        $q->andWhere('s.actionUrl = :actionUrl')
            ->setParameter('actionUrl', $action);

        return $q->getQuery()->getOneOrNullResult();
    }
}
