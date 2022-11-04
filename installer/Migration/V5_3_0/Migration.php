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

namespace OrangeHRM\Installer\Migration\V5_3_0;

use Doctrine\DBAL\Schema\ColumnDiff;
use Doctrine\DBAL\Schema\TableDiff;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use OrangeHRM\Installer\Util\V1\AbstractMigration;

class Migration extends AbstractMigration
{
    protected ?LangStringHelper $langStringHelper = null;

    /**
     * @inheritDoc
     */
    public function up(): void
    {
        $this->getDataGroupHelper()->insertApiPermissions(__DIR__ . '/permission/api.yaml');
        $this->getDataGroupHelper()->insertDataGroupPermissions(__DIR__ . '/permission/data_group.yaml');

        $oldGroups = ['buzz', 'general'];
        foreach ($oldGroups as $group) {
            $this->getLangStringHelper()->insertOrUpdateLangStrings($group);
        }

        $this->getSchemaManager()->dropTable('ohrm_buzz_unlike_on_comment');
        $this->getSchemaManager()->dropTable('ohrm_buzz_unlike_on_share');
        $this->getSchemaHelper()->dropColumn('ohrm_buzz_share', 'number_of_unlikes');
        $this->getSchemaHelper()->dropColumn('ohrm_buzz_comment', 'number_of_unlikes');
        $this->getSchemaHelper()->dropColumns('ohrm_buzz_link', ['type', 'title', 'description']);

        $this->getConnection()->executeStatement(
            'ALTER TABLE ohrm_buzz_post CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );
        $this->getConnection()->executeStatement(
            'ALTER TABLE ohrm_buzz_share CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );
        $this->getConnection()->executeStatement(
            'ALTER TABLE ohrm_buzz_comment CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );

        $this->getSchemaHelper()->disableConstraints();
        $this->changeTableColumns('ohrm_buzz_comment', [
            'employee_number' => [
                'Notnull' => true,
                'Type' => Type::getType(Types::INTEGER),
            ],
            'comment_text' => [
                'Notnull' => false,
                'Default' => null,
                'CustomSchemaOptions' => ['collation' => 'utf8mb4_unicode_ci'],
            ],
            'updated_at' => [
                'Type' => Type::getType(Types::DATETIME_MUTABLE),
                'CustomSchemaOptions' => ['collation' => 'utf8mb4_unicode_ci'],
            ],
        ]);

        $this->changeTableColumns('ohrm_buzz_like_on_comment', [
            'employee_number' => [
                'Notnull' => true,
                'Type' => Type::getType(Types::INTEGER),
            ],
        ]);

        $this->changeTableColumns('ohrm_buzz_like_on_share', [
            'employee_number' => [
                'Notnull' => true,
                'Type' => Type::getType(Types::INTEGER),
            ],
        ]);

        $this->changeTableColumns('ohrm_buzz_photo', [
            'photo' => [
                'Notnull' => false,
                'Default' => null,
                'Type' => Type::getType(Types::BLOB),
            ],
            'filename' => [
                'Notnull' => false,
                'Default' => null,
            ],
            'file_type' => [
                'Notnull' => false,
                'Default' => null,
            ],
            'size' => [
                'Notnull' => false,
                'Default' => null,
            ],
            'width' => [
                'Notnull' => false,
                'Default' => null,
            ],
            'height' => [
                'Notnull' => false,
                'Default' => null,
            ],
        ]);

        $this->changeTableColumns('ohrm_buzz_post', [
            'employee_number' => [
                'Notnull' => true,
                'Type' => Type::getType(Types::INTEGER),
            ],
            'text' => [
                'Notnull' => false,
                'Default' => null,
                'CustomSchemaOptions' => ['collation' => 'utf8mb4_unicode_ci'],
            ],
            'updated_at' => [
                'Type' => Type::getType(Types::DATETIME_MUTABLE),
                'CustomSchemaOptions' => ['collation' => 'utf8mb4_unicode_ci'],
            ],
        ]);

        $this->changeTableColumns('ohrm_buzz_share', [
            'employee_number' => [
                'Notnull' => true,
                'Type' => Type::getType(Types::INTEGER),
            ],
            'type' => [
                'Notnull' => true,
                'Type' => Type::getType(Types::SMALLINT),
            ],
            'text' => [
                'Notnull' => false,
                'Default' => null,
                'CustomSchemaOptions' => ['collation' => 'utf8mb4_unicode_ci'],
            ],
            'updated_at' => [
                'Type' => Type::getType(Types::DATETIME_MUTABLE),
                'CustomSchemaOptions' => ['collation' => 'utf8mb4_unicode_ci'],
            ],
        ]);
        $this->getSchemaHelper()->enableConstraints();
    }

    /**
     * @param string $tableName
     * @param array<string, array> $columnOptions
     */
    private function changeTableColumns(string $tableName, array $columnOptions): void
    {
        $changedColumns = [];
        foreach ($columnOptions as $columnName => $options) {
            $column = $this->getSchemaHelper()->getTableColumn($tableName, $columnName);
            $newColumn = clone $column;
            $newColumn->setOptions($options);
            $changedColumns[] = new ColumnDiff($columnName, $newColumn, [], $column);
        }
        if (!empty($changedColumns)) {
            $diff = new TableDiff($tableName, [], $changedColumns);
            $this->getSchemaManager()->alterTable($diff);
        }
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): string
    {
        return '5.3.0';
    }

    /**
     * @return LangStringHelper
     */
    public function getLangStringHelper(): LangStringHelper
    {
        if (is_null($this->langStringHelper)) {
            $this->langStringHelper = new LangStringHelper(
                $this->getConnection()
            );
        }
        return $this->langStringHelper;
    }
}
