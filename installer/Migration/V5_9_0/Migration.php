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
use OrangeHRM\Installer\Util\V1\LangStringHelper;

class Migration extends AbstractMigration
{
    protected ?LangStringHelper $langStringHelper = null;

    public function up(): void
    {
        $this->createSlackTables();

        $this->getDataGroupHelper()->insertScreenPermissions(__DIR__ . '/permission/screen.yaml');
        $this->getDataGroupHelper()->insertApiPermissions(__DIR__ . '/permission/api.yaml');
        $this->insertSlackNotificationMenuItem();

        // Localised strings for the new Slack notification screen — admin group.
        $this->getLangStringHelper()->insertOrUpdateLangStrings(__DIR__, 'admin');
    }

    private function getLangStringHelper(): LangStringHelper
    {
        if ($this->langStringHelper === null) {
            $this->langStringHelper = new LangStringHelper($this->getConnection());
        }
        return $this->langStringHelper;
    }

    public function getVersion(): string
    {
        return '5.9.0';
    }

    private function createSlackTables(): void
    {
        // Schema:
        //   - Global enable flag lives in hs_hr_config (LDAP pattern). No standalone settings table.
        //   - ohrm_slack_registration: one row per (event_type, channel) destination. Multi-subunit
        //     filtering via join table. provider column reserves space for future Teams/Discord/etc.
        //   - ohrm_slack_registration_subunit: M:N join (registration ↔ subunit).
        //   - ohrm_slack_log: per-dispatch idempotency ledger + failure log.

        if (!$this->getSchemaHelper()->tableExists(['ohrm_slack_registration'])) {
            $this->getSchemaHelper()->createTable('ohrm_slack_registration')
                ->addColumn('id', Types::INTEGER, ['Autoincrement' => true, 'Notnull' => true])
                ->addColumn('provider', Types::STRING, ['Length' => 20, 'Notnull' => true, 'Default' => 'slack'])
                ->addColumn('event_type', Types::STRING, ['Length' => 32, 'Notnull' => true])
                ->addColumn('webhook_url', Types::TEXT, ['Notnull' => true])
                ->addColumn('channel_label', Types::STRING, ['Length' => 100, 'Notnull' => false, 'Default' => null])
                ->addColumn('timezone', Types::STRING, ['Length' => 64, 'Notnull' => true, 'Default' => 'UTC'])
                ->addColumn('daily_send_time', Types::STRING, ['Length' => 5, 'Notnull' => true, 'Default' => '09:00'])
                ->addColumn('is_active', Types::BOOLEAN, ['Notnull' => true, 'Default' => true])
                ->addColumn('created_at', Types::DATETIME_MUTABLE, ['Notnull' => false, 'Default' => null])
                ->addColumn('updated_at', Types::DATETIME_MUTABLE, ['Notnull' => false, 'Default' => null])
                ->setPrimaryKey(['id'])
                ->create();
        }

        if (!$this->getSchemaHelper()->tableExists(['ohrm_slack_registration_subunit'])) {
            $this->getSchemaHelper()->createTable('ohrm_slack_registration_subunit')
                ->addColumn('registration_id', Types::INTEGER, ['Notnull' => true])
                ->addColumn('subunit_id', Types::INTEGER, ['Notnull' => true])
                ->setPrimaryKey(['registration_id', 'subunit_id'])
                ->create();

            $this->getSchemaHelper()->addForeignKey(
                'ohrm_slack_registration_subunit',
                new ForeignKeyConstraint(
                    ['registration_id'],
                    'ohrm_slack_registration',
                    ['id'],
                    'slack_reg_subunit_reg_fk',
                    ['onDelete' => 'CASCADE']
                )
            );
            $this->getSchemaHelper()->addForeignKey(
                'ohrm_slack_registration_subunit',
                new ForeignKeyConstraint(
                    ['subunit_id'],
                    'ohrm_subunit',
                    ['id'],
                    'slack_reg_subunit_sub_fk',
                    ['onDelete' => 'CASCADE']
                )
            );
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

            $this->getSchemaHelper()->addForeignKey(
                'ohrm_slack_log',
                new ForeignKeyConstraint(
                    ['registration_id'],
                    'ohrm_slack_registration',
                    ['id'],
                    'slack_log_registration',
                    ['onDelete' => 'CASCADE']
                )
            );

            $this->getSchemaManager()->createIndex(
                new Index(
                    'idx_slack_log_dedupe',
                    ['registration_id', 'event_date', 'status']
                ),
                'ohrm_slack_log'
            );
        }

        // Seed the global enable flag in hs_hr_config (same pattern as boolean configs like
        // `dashboard.employees_on_leave_today.show_only_accessible`). The key is introduced
        // by this migration so a direct insert is safe — no existence check needed.
        $this->getConnection()->createQueryBuilder()
            ->insert('hs_hr_config')
            ->values(['name' => ':name', 'value' => ':value'])
            ->setParameter('name', self::CONFIG_KEY_SLACK_ENABLED)
            ->setParameter('value', '0')
            ->executeQuery();
    }

    private const CONFIG_KEY_SLACK_ENABLED = 'slack.notifications.enabled';

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
