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

namespace OrangeHRM\Installer\Migration\V5_7_0;

use OrangeHRM\Installer\Util\V1\AbstractMigration;

class Migration extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up(): void
    {
        $localizationDataGroupId = $this->getDataGroupHelper()->getDataGroupIdByName(
            'apiv2_admin_localization_languages'
        );
        $this->createQueryBuilder()->update('ohrm_data_group')
            ->andWhere('id = :id')
            ->set('can_delete', ':value')
            ->setParameter('id', $localizationDataGroupId)
            ->setParameter('value', 1)
            ->executeQuery();
        $this->createQueryBuilder()->update('ohrm_user_role_data_group')
            ->andWhere('data_group_id = :dataGroupId')
            ->andWhere('user_role_id = :userRoleId')
            ->set('can_delete', ':value')
            ->setParameter('dataGroupId', $localizationDataGroupId)
            ->setParameter('userRoleId', $this->getDataGroupHelper()->getUserRoleIdByName('Admin'))
            ->setParameter('value', 1)
            ->executeQuery();

        $this->getDataGroupHelper()->insertDataGroupPermissions(__DIR__ . '/permission/data_group.yaml');
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): string
    {
        return '5.7.0';
    }
}
