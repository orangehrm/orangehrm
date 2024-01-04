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

namespace OrangeHRM\Installer\Migration\V4_6_0;

use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Schema\UniqueConstraint;
use Doctrine\DBAL\Types\Types;
use OrangeHRM\Installer\Util\V1\AbstractMigration;
use Symfony\Component\Yaml\Yaml;

class Migration extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up(): void
    {
        if (!$this->getSchemaHelper()->tableExists(['ohrm_i18n_group'])) {
            $this->getSchemaHelper()->createTable('ohrm_i18n_group')
                ->addColumn('id', Types::INTEGER, ['Autoincrement' => true])
                ->addColumn('name', Types::STRING, ['Length' => 255])
                ->addColumn('title', Types::STRING, ['Length' => 255, 'Default' => null, 'Notnull' => false])
                ->setPrimaryKey(['id'])
                ->create();
        }

        if (!$this->getSchemaHelper()->tableExists(['ohrm_i18n_language'])) {
            $this->getSchemaHelper()->createTable('ohrm_i18n_language')
                ->addColumn('id', Types::INTEGER, ['Autoincrement' => true])
                ->addColumn('name', Types::STRING, ['Length' => 255, 'Default' => null, 'Notnull' => false])
                ->addColumn('code', Types::STRING, ['Length' => 100, 'Notnull' => true])
                ->addColumn(
                    'enabled',
                    Types::SMALLINT,
                    ['Unsigned' => true, 'Notnull' => false, 'Default' => 1]
                )
                ->addColumn(
                    'added',
                    Types::SMALLINT,
                    ['Unsigned' => true, 'Notnull' => false, 'Default' => 0]
                )
                ->addColumn('modified_at', Types::DATETIME_MUTABLE, ['Default' => null, 'Notnull' => false])
                ->setPrimaryKey(['id'])
                ->addUniqueConstraint(['code'])
                ->create();
        }

        if (!$this->getSchemaHelper()->tableExists(['ohrm_i18n_lang_string'])) {
            $this->getSchemaHelper()->createTable('ohrm_i18n_lang_string')
                ->addColumn('id', Types::INTEGER, ['Autoincrement' => true])
                ->addColumn('unit_id', Types::INTEGER, ['Notnull' => true])
                ->addColumn('source_id', Types::INTEGER)
                ->addColumn('group_id', Types::INTEGER, ['Default' => null, 'Notnull' => false])
                ->addColumn(
                    'value',
                    Types::TEXT,
                    ['Notnull' => true, 'CustomSchemaOptions' => ['collation' => 'utf8mb4_bin']]
                )
                ->addColumn('note', Types::TEXT, ['Notnull' => false])
                ->addColumn('version', Types::STRING, ['Length' => 20, 'Default' => null, 'Notnull' => false])
                ->setPrimaryKey(['id'])
                ->create();
        }

        if (!$this->getSchemaHelper()->tableExists(['ohrm_i18n_translate'])) {
            $this->getSchemaHelper()->createTable('ohrm_i18n_translate')
                ->addColumn('id', Types::INTEGER, ['Autoincrement' => true])
                ->addColumn('lang_string_id', Types::INTEGER, ['Notnull' => false])
                ->addColumn('language_id', Types::INTEGER, ['Notnull' => false])
                ->addColumn('value', Types::TEXT)
                ->addColumn('translated', Types::SMALLINT, ['Unsigned' => true, 'Default' => 1])
                ->addColumn('customized', Types::SMALLINT, ['Unsigned' => true, 'Default' => 0])
                ->addColumn('version', Types::STRING, ['Length' => 20, 'Default' => null, 'Notnull' => false])
                ->addColumn(
                    'modified_at',
                    Types::DATETIMETZ_MUTABLE,
                    ['Notnull' => false]
                )
                ->setPrimaryKey(['id'])
                ->create();
        }

        if (!$this->getSchemaHelper()->tableExists(['ohrm_i18n_source'])) {
            $this->getSchemaHelper()->createTable('ohrm_i18n_source')
                ->addColumn('id', Types::INTEGER, ['Autoincrement' => true])
                ->addColumn('source', Types::TEXT, ['Length' => 18, 'Notnull' => false])
                ->addColumn('modified_at', Types::DATETIME_MUTABLE, ['Notnull' => false,])
                ->setPrimaryKey(['id'])
                ->create();
        }

        $foreignKeyConstraint = new ForeignKeyConstraint(
            ['group_id'],
            'ohrm_i18n_group',
            ['id'],
            'groupId',
            ['onDelete' => 'SET NULL']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_i18n_lang_string', $foreignKeyConstraint);

        $foreignKeyConstraint = new ForeignKeyConstraint(
            ['language_id'],
            'ohrm_i18n_language',
            ['id'],
            'languageId',
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_i18n_translate', $foreignKeyConstraint);

        $foreignKeyConstraint = new ForeignKeyConstraint(
            ['lang_string_id'],
            'ohrm_i18n_lang_string',
            ['id'],
            'langStringId',
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_i18n_translate', $foreignKeyConstraint);

        $foreignKeyConstraint = new ForeignKeyConstraint(
            ['source_id'],
            'ohrm_i18n_source',
            ['id'],
            'sourceId',
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_i18n_lang_string', $foreignKeyConstraint);

        $uniqueConstraint = new UniqueConstraint(
            'translateUniqueId',
            ['lang_string_id', 'language_id']
        );
        $this->getSchemaHelper()->getSchemaManager()->createUniqueConstraint($uniqueConstraint, 'ohrm_i18n_translate');
        $this->getDataGroupHelper()->insertScreenPermissions(__DIR__ . '/permission/screen.yaml');
        $languages = $this->readlanguageYaml(__DIR__ . '/language/languages.yaml');
        $this->insertLanguages($languages);
        $groups = $this->readlanguageYaml(__DIR__ . '/language/groups.yaml');
        $this->insertGroups($groups);

        $adminMenuId = $this->createQueryBuilder()
            ->select('menu_item.id')
            ->from('ohrm_menu_item', 'menu_item')
            ->where('menu_item.menu_title = :menuTitle')
            ->setParameter('menuTitle', 'Admin')
            ->andWhere('level = :level')
            ->setParameter('level', 1)
            ->executeQuery()
            ->fetchOne();
        $adminConfigurationMenuId = $this->createQueryBuilder()
            ->select('menu_item.id')
            ->from('ohrm_menu_item', 'menu_item')
            ->where('menu_item.menu_title = :menuTitle')
            ->setParameter('menuTitle', 'Configuration')
            ->andWhere('level = :level')
            ->setParameter('level', 2)
            ->andWhere('parent_id = :parentId')
            ->setParameter('parentId', $adminMenuId)
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
                    'status' => ':status'
                ]
            )
            ->setParameter('menuTitle', 'Language Packages')
            ->setParameter(
                'screenId',
                $this->getDataGroupHelper()->getScreenIdByModuleAndUrl(
                    $this->getDataGroupHelper()->getModuleIdByName('admin'),
                    'languagePackage'
                )
            )
            ->setParameter('parentId', $adminConfigurationMenuId)
            ->setParameter('level', 3)
            ->setParameter('orderHint', 350)
            ->setParameter('urlExtras', null)
            ->setParameter('status', 1)
            ->executeQuery();
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): string
    {
        return '4.6';
    }

    /**
     * @param string $filepath
     * @return array
     */
    private function readlanguageYaml(string $filepath): array
    {
        $yaml = Yaml::parseFile($filepath);
        $array = array_shift($yaml);
        return $array;
    }

    /**
     * @param array $languages
     * @return void
     */
    private function insertLanguages(array $languages): void
    {
        foreach ($languages as $language) {
            $this->createQueryBuilder()
                ->insert('ohrm_i18n_language')
                ->values(
                    [
                        'name' => ':name',
                        'code' => ':code',
                        'enabled' => ':enabled',
                        'added' => ':added'
                    ]
                )
                ->setParameter('name', $language['name'])
                ->setParameter('code', $language['code'])
                ->setParameter('enabled', $language['enabled'])
                ->setParameter('added', $language['added'])
                ->executeQuery();
        }
    }

    /**
     * @param array $groups
     * @return void
     */
    private function insertGroups(array $groups): void
    {
        foreach ($groups as $group) {
            $this->createQueryBuilder()
                ->insert('ohrm_i18n_group')
                ->values(
                    [
                        'name' => ':name',
                        'title' => ':title'
                    ]
                )
                ->setParameter('name', $group['name'])
                ->setParameter('title', $group['title'])
                ->executeQuery();
        }
    }
}
