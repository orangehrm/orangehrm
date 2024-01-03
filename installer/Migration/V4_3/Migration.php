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

namespace OrangeHRM\Installer\Migration\V4_3;

use Doctrine\DBAL\Types\Types;
use OrangeHRM\Installer\Util\V1\AbstractMigration;

class Migration extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up(): void
    {
        $this->createQueryBuilder()
            ->insert('ohrm_module')
            ->values(
                [
                    'name' => ':name',
                    'status' => ':status'
                ]
            )
            ->setParameter('name', 'marketPlace')
            ->setParameter('status', 1)
            ->executeQuery();

        $this->getDataGroupHelper()->insertScreenPermissions(__DIR__ . '/permission/screen.yaml');

        $this->createQueryBuilder()
            ->insert('hs_hr_config')
            ->values(
                [
                    '`key`' => ':key',
                    'value' => ':value'
                ]
            )
            ->setParameter('key', 'base_url')
            ->setParameter('value', 'https://marketplace.orangehrm.com')
            ->executeQuery();

        $this->getDataGroupHelper()->insertDataGroupPermissions(__DIR__ . '/permission/data_group.yaml');

        $dataGroupId = $this->getDataGroupHelper()->getDataGroupIdByName('Marketplace');
        $homeScreenId = $this->createQueryBuilder()
            ->select('screen.id')
            ->from('ohrm_screen', 'screen')
            ->where('action_url =:actionUrl')
            ->setParameter('actionUrl', 'ohrmAddons')
            ->executeQuery()
            ->fetchOne();

        $this->createQueryBuilder()
            ->insert('ohrm_data_group_screen')
            ->values(
                [
                    'data_group_id' => ':dataGroupId',
                    'screen_id' => ':screenId',
                    'permission' => ':permission'
                ]
            )
            ->setParameter('dataGroupId', $dataGroupId)
            ->setParameter('screenId', $homeScreenId)
            ->setParameter('permission', 1)
            ->executeQuery();


        if (!$this->getSchemaManager()->tablesExist('ohrm_marketplace_addon')) {
            $this->getSchemaHelper()->createTable('ohrm_marketplace_addon')
                ->addColumn('addon_id', Types::INTEGER, ['Length' => 11, 'Autoincrement' => true])
                ->addColumn('title', Types::STRING, ['Length' => 100])
                ->addColumn('date', Types::DATETIMETZ_MUTABLE)
                ->addColumn('status', Types::STRING, ['Length' => 30])
                ->addColumn('version', Types::STRING, ['Length' => 100])
                ->addColumn('plugin_name', Types::STRING, ['Length' => 255])
                ->setPrimaryKey(['addon_id'])
                ->create();
        }
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): string
    {
        return '4.3';
    }
}
