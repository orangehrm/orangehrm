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

namespace OrangeHRM\Installer\Util;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Types\Types;
use OrangeHRM\Installer\Util\V1\SchemaHelper;

class DatabaseUserPermissionEvaluator
{
    private Connection $connection;
    private ?SchemaHelper $schemaHelper = null;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return Connection
     */
    protected function getConnection(): Connection
    {
        return $this->connection;
    }

    /**
     * @return SchemaHelper
     */
    protected function getSchemaHelper(): SchemaHelper
    {
        if (!$this->schemaHelper instanceof SchemaHelper) {
            $this->schemaHelper = new SchemaHelper($this->getConnection());
        }
        return $this->schemaHelper;
    }

    protected function evalCreatePermission(): void
    {
        $this->getSchemaHelper()->createTable('_ohrm_temp_table')
            ->addColumn('id', Types::INTEGER, ['Autoincrement' => true])
            ->addColumn('col_nullable', Types::STRING, ['Notnull' => false, 'Length' => 255])
            ->addColumn('col_date', Types::DATETIME_MUTABLE)
            ->addColumn('col_timestamp', Types::DATETIMETZ_MUTABLE)
            ->addColumn('col_bool', Types::BOOLEAN)
            ->addColumn('col_text', Types::TEXT)
            ->addColumn('col_json', Types::JSON)
            ->addUniqueIndex(['col_nullable'], 'index')
            ->setPrimaryKey(['id'])
            ->create();
    }

    protected function evalAlterPermission(): void
    {
        $this->getSchemaHelper()->addColumn('_ohrm_temp_table', '`col_blob`', Types::BLOB);
        $this->getSchemaHelper()->changeColumn(
            '_ohrm_temp_table',
            'col_blob',
            ['Default' => null, 'Notnull' => false]
        );
        $this->getSchemaHelper()->renameColumn('_ohrm_temp_table', 'col_blob', 'col_blob_changed');
        $this->getSchemaHelper()->dropColumn('_ohrm_temp_table', 'col_blob_changed');
    }

    protected function evalForeignKeyPermission(): void
    {
        $this->getSchemaHelper()->addColumn('_ohrm_temp_table', 'foreign_id', Types::INTEGER);
        $this->getSchemaHelper()->createTable('_ohrm_temp_foreign_table')
            ->addColumn('id', Types::INTEGER, ['Autoincrement' => true])
            ->addColumn('col', Types::STRING, ['Length' => 255])
            ->setPrimaryKey(['id'])
            ->create();

        $foreignKeyConstraint = new ForeignKeyConstraint(
            ['foreign_id'],
            '_ohrm_temp_foreign_table',
            ['id'],
            'fk_id',
            ['onDelete' => 'SET NULL']
        );
        $this->getSchemaHelper()->addForeignKey('_ohrm_temp_table', $foreignKeyConstraint);
        $this->getSchemaHelper()->dropForeignKeys('_ohrm_temp_table', ['fk_id']);
    }

    protected function evalDropPermission(): void
    {
        $this->getConnection()->createSchemaManager()->dropTable('_ohrm_temp_foreign_table');
        $this->getConnection()->createSchemaManager()->dropTable('_ohrm_temp_table');
    }

    public function evalPrivilegeDatabaseUserPermission(): void
    {
        $this->evalCreatePermission();
        $this->evalAlterPermission();
        $this->evalForeignKeyPermission();
        $this->evalDropPermission();
    }
}
