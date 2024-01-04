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

namespace OrangeHRM\Installer\Migration\V4_1_2;

use OrangeHRM\Installer\Util\V1\AbstractMigration;

class Migration extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up(): void
    {
        $adminUserRoleId = $this->createQueryBuilder()
            ->select('user_role.id')
            ->from('ohrm_user_role', 'user_role')
            ->where('user_role.name = :role')
            ->setParameter('role', 'Admin')
            ->executeQuery()
            ->fetchOne();

        $timeModuleId = $this->createQueryBuilder()
            ->select('module.id')
            ->from('ohrm_module', 'module')
            ->where('module.name = :module')
            ->setParameter('module', 'time')
            ->executeQuery()
            ->fetchOne();

        $this->createQueryBuilder()
            ->update('ohrm_module_default_page', 'module_default')
            ->set('module_default.priority', ':priority')
            ->setParameter('priority', 200)
            ->where('module_default.module_id = :moduleId')
            ->setParameter('moduleId', $timeModuleId)
            ->andWhere('module_default.user_role_id = :roleId')
            ->setParameter('roleId', $adminUserRoleId)
            ->andWhere('module_default.action = :action')
            ->setParameter('action', 'time/defineTimesheetPeriod')
            ->executeQuery();
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): string
    {
        return '4.1.2';
    }
}
