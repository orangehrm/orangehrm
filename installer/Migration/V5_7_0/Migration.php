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
use OrangeHRM\Installer\Util\V1\LangStringHelper;

class Migration extends AbstractMigration
{
    protected ?LangStringHelper $langStringHelper = null;

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

        $groups = ['admin'];
        foreach ($groups as $group) {
            $this->getLangStringHelper()->insertOrUpdateLangStrings(__DIR__, $group);
        }

        $this->dropOldTables();
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): string
    {
        return '5.7.0';
    }

    /**
     * @return LangStringHelper
     */
    private function getLangStringHelper(): LangStringHelper
    {
        if (is_null($this->langStringHelper)) {
            $this->langStringHelper = new LangStringHelper(
                $this->getConnection()
            );
        }
        return $this->langStringHelper;
    }

    private function dropOldTables(): void
    {
        $this->getSchemaManager()->dropTable('ohrm_user_selection_rule');
        $this->getSchemaManager()->dropTable('ohrm_role_user_selection_rule');
        $this->getSchemaManager()->dropTable('ohrm_leave_entitlement_adjustment');
        $this->getSchemaManager()->dropTable('ohrm_leave_adjustment');
        $this->getSchemaManager()->dropTable('ohrm_available_group_field');
        $this->getSchemaManager()->dropTable('hs_hr_mailnotifications');
        $this->getSchemaManager()->dropTable('hs_hr_custom_export');
        $this->getSchemaManager()->dropTable('hs_hr_custom_import');
        $this->getSchemaManager()->dropTable('hs_hr_module');
        $this->getSchemaManager()->dropTable('ohrm_advanced_report');
        $this->getSchemaManager()->dropTable('ohrm_beacon_notification');
        $this->getSchemaManager()->dropTable('ohrm_datapoint');
        $this->getSchemaManager()->dropTable('ohrm_datapoint_type');
    }
}
