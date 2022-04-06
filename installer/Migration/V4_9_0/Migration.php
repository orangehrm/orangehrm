<?php

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
        if (!$this->getSchemaHelper()->tableExists('ohrm_theme')) {
            $this->getSchemaHelper()->createTable('ohrm_theme')
                ->addColumn('theme_id', Types::INTEGER, ['Length' => 11, 'Autoincrement' => true])
                ->addColumn('theme_name', Types::INTEGER, ['Notnull' => true])
                ->addColumn('published', Types::SMALLINT, ['Notnull' => true, 'Default' => 0])
                ->addColumn('event_time', Types::DATETIME_MUTABLE, ['Notnull' => false, 'Default' => null])
                ->addColumn('publish_time', Types::DATETIME_MUTABLE, ['Notnull' => false, 'Default' => null])
                ->addColumn('data', Types::TEXT, ['Notnull' => false, 'Default' => null])
                ->setPrimaryKey(['theme_id'])
                ->create();
        }
        $this->getDataGroupHelper()->insertScreenPermissions(__DIR__ . '/permission/screen.yaml');
        $this->insertMenuItems('Corporate Branding', 'Add Theme', 1, 2, 700, '', 1);
        $this->insertTheme(1, 'default', '{"primaryColor":"#f28b38","secondaryColor":"#f3f3f3","buttonSuccessColor":"#56ac40","buttonCancelColor":"#848484"}');
        $this->getSchemaHelper()->addColumn('ohrm_theme', 'social_media_icons', Types::TEXT, [
            'Notnull' => false,
            'Default' => 'inline',
        ]);
        $this->getSchemaHelper()->addColumn('ohrm_theme', 'login_banner', Types::BLOB);

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

        $this->createQueryBuilder()
            ->delete('ohrm_marketplace_addon')
            ->andWhere('ohrm_marketplace_addon.plugin_name = :pluginName')
            ->setParameter('pluginName', 'orangehrmCorporateBrandingPlugin')
            ->executeQuery();

        $this->updateConfig('4.9', 'instance.version');
        $this->updateConfig('81', 'instance.increment_number');

        if (!$this->getSchemaHelper()->tableExists('ohrm_registration_event_queue')) {
            $this->getSchemaHelper()->createTable('ohrm_registration_event_queue')
                ->addColumn('id', Types::INTEGER, ['Autoincrement' => true])
                ->addColumn('published', Types::SMALLINT, ['Unsigned'=> true,'NotNull' => true,'Default' => 0])
                ->addColumn('event_type', Types::DATETIME_MUTABLE, ['Default' => null])
                ->addColumn('main_logo', Types::BLOB)
                ->addColumn('variables', Types::TEXT)
                ->setPrimaryKey(['id'])
                ->create();
        }
    }

    /**
     * @param string $menuItem
     * @param int $screenId
     * @param int $parentId
     * @param int $level
     * @param int $order_hint
     * @param string $urlExtras
     * @param int $status
     * @return void
     */
    private function insertMenuItems(
        string $menuItem,
        string $screenName,
        int    $parentId,
        int    $level,
        int    $order_hint,
        string $urlExtras,
        int    $status
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
                    'parent_id' => ':ParentId',
                    'level' => ':level',
                    'order_hint' => ':orderHint',
                    'url_extras' => ':urlExtras',
                    'status' => 'status'
                ]
            )
            ->setParameter('menuTitle', $menuItem)
            ->setParameter('screenId', $screenId)
            ->setParameter('level', $level)
            ->setParameter('ParentId', $parentId)
            ->setParameter('level', $level)
            ->setParameter('orderHint', $order_hint)
            ->setParameter('urlExtras', $urlExtras)
            ->setParameter('status', $status)
            ->executeQuery();
    }

    /**
     * @param int $themeId
     * @param string $themeName
     * @param string $variables
     * @return void
     */
    private function insertTheme(int $themeId, string $themeName, string $variables): void
    {
        $this->createQueryBuilder()
            ->insert('ohrm_theme')
            ->values(
                [
                    'theme_id' => ':themeId',
                    'theme_name' => ':themeName',
                    'variables' => ':variables'
                ]
            )
            ->setParameter('themeId', $themeId)
            ->setParameter('themeName', $themeName)
            ->setParameter('variables', $variables)
            ->executeQuery();
    }

    /**
     * @param string $value
     * @param string $key
     * @return void
     */
    private function updateConfig(string $value, string $key): void
    {
        $this->createQueryBuilder()
            ->update('hs_hr_config', 'config')
            ->set('config.value', ':value')
            ->setParameter('value', $value)
            ->andWhere('config.key = :key')
            ->setParameter('key', $key)
            ->executeQuery();
    }
}
