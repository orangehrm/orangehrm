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

namespace OrangeHRM\Installer\Migration\V5_9_0;

use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Types\Types;
use OrangeHRM\Installer\Util\V1\AbstractMigration;

class Migration extends AbstractMigration
{
    public function up(): void
    {
        $this->createSlackTables();

        $this->getDataGroupHelper()->insertScreenPermissions(__DIR__ . '/permission/screen.yaml');
        $this->getDataGroupHelper()->insertApiPermissions(__DIR__ . '/permission/api.yaml');
        $this->insertSlackNotificationMenuItem();
    }

    public function getVersion(): string
    {
        return '5.9.0';
    }

    private function createSlackTables(): void
    {
        if (!$this->getSchemaHelper()->tableExists(['ohrm_slack_setting'])) {
            $this->getSchemaHelper()->createTable('ohrm_slack_setting')
                ->addColumn('id', Types::INTEGER, ['Autoincrement' => true, 'Notnull' => true])
                ->addColumn('is_enabled', Types::BOOLEAN, ['Notnull' => true, 'Default' => false])
                ->addColumn('timezone', Types::STRING, ['Length' => 64, 'Notnull' => true, 'Default' => 'UTC'])
                ->addColumn('daily_send_time', Types::STRING, ['Length' => 5, 'Notnull' => true, 'Default' => '09:00'])
                ->addColumn('created_at', Types::DATETIME_MUTABLE, ['Notnull' => false, 'Default' => null])
                ->addColumn('updated_at', Types::DATETIME_MUTABLE, ['Notnull' => false, 'Default' => null])
                ->setPrimaryKey(['id'])
                ->create();
        }

        if (!$this->getSchemaHelper()->tableExists(['ohrm_slack_registration'])) {
            $this->getSchemaHelper()->createTable('ohrm_slack_registration')
                ->addColumn('id', Types::INTEGER, ['Autoincrement' => true, 'Notnull' => true])
                ->addColumn('event_type', Types::STRING, ['Length' => 32, 'Notnull' => true])
                ->addColumn('webhook_url', Types::TEXT, ['Notnull' => true])
                ->addColumn('channel_label', Types::STRING, ['Length' => 100, 'Notnull' => false, 'Default' => null])
                ->addColumn('subunit_id', Types::INTEGER, ['Notnull' => false, 'Default' => null])
                ->addColumn('is_active', Types::BOOLEAN, ['Notnull' => true, 'Default' => true])
                ->addColumn('last_delivery_status', Types::STRING, ['Length' => 20, 'Notnull' => false, 'Default' => null])
                ->addColumn('last_delivery_at', Types::DATETIME_MUTABLE, ['Notnull' => false, 'Default' => null])
                ->addColumn('last_delivery_error', Types::TEXT, ['Notnull' => false, 'Default' => null])
                ->addColumn('created_at', Types::DATETIME_MUTABLE, ['Notnull' => false, 'Default' => null])
                ->addColumn('updated_at', Types::DATETIME_MUTABLE, ['Notnull' => false, 'Default' => null])
                ->setPrimaryKey(['id'])
                ->create();

            $subunitConstraint = new ForeignKeyConstraint(
                ['subunit_id'],
                'ohrm_subunit',
                ['id'],
                'slack_registration_subunit',
                ['onDelete' => 'SET NULL']
            );
            $this->getSchemaHelper()->addForeignKey('ohrm_slack_registration', $subunitConstraint);
        }

        if (!$this->getSchemaHelper()->tableExists(['ohrm_slack_log'])) {
            $this->getSchemaHelper()->createTable('ohrm_slack_log')
                ->addColumn('id', Types::INTEGER, ['Autoincrement' => true, 'Notnull' => true])
                ->addColumn('registration_id', Types::INTEGER, ['Notnull' => false, 'Default' => null])
                ->addColumn('event_type', Types::STRING, ['Length' => 32, 'Notnull' => true])
                ->addColumn('event_date', Types::DATE_MUTABLE, ['Notnull' => true])
                ->addColumn('status', Types::STRING, ['Length' => 20, 'Notnull' => true])
                ->addColumn('recipient_count', Types::INTEGER, ['Notnull' => true, 'Default' => 0])
                ->addColumn('error_message', Types::TEXT, ['Notnull' => false, 'Default' => null])
                ->addColumn('created_at', Types::DATETIME_MUTABLE, ['Notnull' => false, 'Default' => null])
                ->setPrimaryKey(['id'])
                ->create();

            $registrationConstraint = new ForeignKeyConstraint(
                ['registration_id'],
                'ohrm_slack_registration',
                ['id'],
                'slack_log_registration',
                ['onDelete' => 'CASCADE']
            );
            $this->getSchemaHelper()->addForeignKey('ohrm_slack_log', $registrationConstraint);
        }

        $dedupeIndex = new Index(
            'idx_slack_log_dedupe',
            ['registration_id', 'event_date', 'status']
        );
        $this->getSchemaManager()->createIndex($dedupeIndex, 'ohrm_slack_log');

        if (!$this->getConnection()->createQueryBuilder()
            ->select('id')->from('ohrm_slack_setting')->setMaxResults(1)
            ->executeQuery()->fetchOne()) {
            $this->getConnection()->createQueryBuilder()
                ->insert('ohrm_slack_setting')
                ->values([
                    'is_enabled' => ':enabled',
                    'timezone' => ':timezone',
                    'daily_send_time' => ':sendTime',
                ])
                ->setParameter('enabled', false, Types::BOOLEAN)
                ->setParameter('timezone', 'UTC')
                ->setParameter('sendTime', '09:00')
                ->executeQuery();
        }
    }

    private function insertSlackNotificationMenuItem(): void
    {
        $adminId = $this->createQueryBuilder()
            ->select('menu_item.id')
            ->from('ohrm_menu_item', 'menu_item')
            ->where('menu_item.menu_title = :menuTitle')
            ->setParameter('menuTitle', 'Admin')
            ->andWhere('level = :level')
            ->setParameter('level', 1)
            ->executeQuery()
            ->fetchOne();

        $configurationId = $this->createQueryBuilder()
            ->select('menu_item.id')
            ->from('ohrm_menu_item', 'menu_item')
            ->where('menu_item.menu_title = :menuTitle')
            ->setParameter('menuTitle', 'Configuration')
            ->andWhere('level = :level')
            ->setParameter('level', 2)
            ->andWhere('parent_id = :parentId')
            ->setParameter('parentId', $adminId)
            ->executeQuery()
            ->fetchOne();

        $screenId = $this->createQueryBuilder()
            ->select('screen.id')
            ->from('ohrm_screen', 'screen')
            ->where('screen.name = :screenName')
            ->setParameter('screenName', 'Admin - Slack Notification Configuration')
            ->executeQuery()
            ->fetchOne();

        $this->createQueryBuilder()
            ->insert('ohrm_menu_item')
            ->values(
                [
                    'menu_title' => ':menuTitle',
                    'screen_id' => ':screenId',
                    'parent_id' => ':parentId',
                    'level' => ':level',
                    'order_hint' => ':orderHint',
                    'status' => ':status',
                ]
            )
            ->setParameter('menuTitle', 'Slack Notification Configuration')
            ->setParameter('screenId', $screenId)
            ->setParameter('parentId', $configurationId)
            ->setParameter('level', 3)
            ->setParameter('orderHint', 1100)
            ->setParameter('status', 1)
            ->executeQuery();
    }
}
