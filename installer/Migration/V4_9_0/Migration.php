<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

namespace OrangeHRM\Installer\Migration\V4_9_0;

use Doctrine\DBAL\Types\Types;
use OrangeHRM\Installer\Util\V1\AbstractMigration;

class Migration extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up(): void
    {
        if (!$this->getSchemaHelper()->tableExists(['ohrm_theme'])) {
            $this->getSchemaHelper()->createTable('ohrm_theme')
                ->addColumn('theme_id', Types::INTEGER, ['Length' => 11, 'Autoincrement' => true])
                ->addColumn('theme_name', Types::STRING, ['Length' => 100, 'Notnull' => false])
                ->addColumn('main_logo', Types::BLOB, ['Notnull' => false])
                ->addColumn('variables', Types::TEXT, ['Notnull' => false])
                ->setPrimaryKey(['theme_id'])
                ->create();

            $this->getDataGroupHelper()->insertScreenPermissions(__DIR__ . '/permission/screen.yaml');

            $adminMenuId = $this->createQueryBuilder()
                ->select('menu_item.id')
                ->from('ohrm_menu_item', 'menu_item')
                ->where('menu_item.menu_title = :menuTitle')
                ->setParameter('menuTitle', 'Admin')
                ->andWhere('level = :level')
                ->setParameter('level', 1)
                ->executeQuery()
                ->fetchOne();
            $this->insertMenuItems('Corporate Branding', 'Add Theme', $adminMenuId, 2, 700, '', 1);
        }

        $this->insertTheme(
            'default',
            '{"primaryColor":"#f28b38","secondaryColor":"#f3f3f3","buttonSuccessColor":"#56ac40","buttonCancelColor":"#848484"}'
        );

        if (!$this->getSchemaHelper()->columnExists('ohrm_theme', 'social_media_icons')) {
            $this->getSchemaHelper()->addColumn('ohrm_theme', 'social_media_icons', Types::TEXT, [
                'Notnull' => true,
                'Default' => 'inline',
            ]);
        }

        if (!$this->getSchemaHelper()->columnExists('ohrm_theme', 'login_banner')) {
            $this->getSchemaHelper()->addColumn('ohrm_theme', 'login_banner', Types::BLOB);
        }

        $this->createQueryBuilder()
            ->delete('ohrm_marketplace_addon')
            ->andWhere('ohrm_marketplace_addon.plugin_name = :pluginName')
            ->setParameter('pluginName', 'orangehrmCorporateBrandingPlugin')
            ->executeQuery();

        $brandingGroupId = $this->createQueryBuilder()
            ->select('i18nGroup.id')
            ->from('ohrm_i18n_group', 'i18nGroup')
            ->where('i18nGroup.name = :name')
            ->setParameter('name', 'branding')
            ->fetchOne();
        if ($brandingGroupId === false) {
            $this->createQueryBuilder()
                ->insert('ohrm_i18n_group')
                ->values(
                    [
                        'name' => ':name',
                        'title' => ':title',
                    ]
                )
                ->setParameter('name', 'branding')
                ->setParameter('title', 'Corporate Branding')
                ->executeQuery();
        }

        if (!$this->getSchemaHelper()->tableExists(['ohrm_registration_event_queue'])) {
            $this->getSchemaHelper()->createTable('ohrm_registration_event_queue')
                ->addColumn('id', Types::INTEGER, ['Autoincrement' => true])
                ->addColumn('event_type', Types::INTEGER, ['Notnull' => true])
                ->addColumn('published', Types::SMALLINT, ['Unsigned' => true, 'NotNull' => true, 'Default' => 0])
                ->addColumn('event_time', Types::DATETIME_MUTABLE, ['Default' => null, 'Notnull' => false])
                ->addColumn('publish_time', Types::DATETIME_MUTABLE, ['Default' => null, 'Notnull' => false])
                ->addColumn('data', Types::TEXT, ['Default' => null, 'Notnull' => false])
                ->setPrimaryKey(['id'])
                ->create();
        }
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): string
    {
        return '4.9';
    }

    /**
     * @param string $menuItem
     * @param string $screenName
     * @param int $parentId
     * @param int $level
     * @param int $order_hint
     * @param string $urlExtras
     * @param int $status
     */
    private function insertMenuItems(
        string $menuItem,
        string $screenName,
        int $parentId,
        int $level,
        int $order_hint,
        string $urlExtras,
        int $status
    ): void {
        $screenId = $this->getConnection()->createQueryBuilder()
            ->select('screen.id')
            ->from('ohrm_screen', 'screen')
            ->where('screen.name = :screenName')
            ->setParameter('screenName', $screenName)
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
                    'url_extras' => ':urlExtras',
                    'status' => 'status'
                ]
            )
            ->setParameter('menuTitle', $menuItem)
            ->setParameter('screenId', $screenId)
            ->setParameter('parentId', $parentId)
            ->setParameter('level', $level)
            ->setParameter('orderHint', $order_hint)
            ->setParameter('urlExtras', $urlExtras)
            ->setParameter('status', $status)
            ->executeQuery();
    }

    /**
     * @param string $themeName
     * @param string $variables
     * @return void
     */
    private function insertTheme(string $themeName, string $variables): void
    {
        $themeId = $this->createQueryBuilder()
            ->select('theme.theme_id')
            ->from('ohrm_theme', 'theme')
            ->where('theme.theme_name = :name')
            ->setParameter('name', $themeName)
            ->fetchOne();

        if ($themeId === false) {
            $this->createQueryBuilder()
                ->insert('ohrm_theme')
                ->values(
                    [
                        'theme_name' => ':themeName',
                        'variables' => ':variables'
                    ]
                )
                ->setParameter('themeName', $themeName)
                ->setParameter('variables', $variables)
                ->executeQuery();
        }
    }
}
