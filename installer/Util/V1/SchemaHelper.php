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

namespace OrangeHRM\Installer\Util\V1;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\ColumnDiff;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Schema\TableDiff;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use OrangeHRM\Installer\Util\V1\Dto\Table;

class SchemaHelper
{
    private AbstractSchemaManager $schemaManager;

    private Connection $connection;


    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->schemaManager = $connection->createSchemaManager();
    }

    /**
     * @return Connection
     */
    protected function getConnection(): Connection
    {
        return $this->connection;
    }

    /**
     * @param string $tableName
     * @param string $columnName
     */
    public function dropColumn(string $tableName, string $columnName): void
    {
        $column = $this->getTableColumn($tableName, $columnName);
        $diff = new TableDiff($tableName, [], [], [$column]);
        $this->getSchemaManager()->alterTable($diff);
    }

    /**
     * @param string $tableName
     * @param string $columnName
     * @return Column|null
     */
    public function getTableColumn(string $tableName, string $columnName): ?Column
    {
        return $this->getSchemaManager()->listTableColumns($tableName)[$columnName] ?? null;
    }

    /**
     * @return AbstractSchemaManager
     */
    public function getSchemaManager(): AbstractSchemaManager
    {
        return $this->schemaManager;
    }

    /**
     * @param string $tableName
     * @param string $columnName
     * @param string $type e.g. \Doctrine\DBAL\Types\Types\Types::STRING, Types::INTEGER
     * @param array $options ['Type' => \Doctrine\DBAL\Types\Type, 'Length' => int|null, 'Precision' => int, 'Scale' => int, 'Unsigned' => bool, 'Fixed' => bool, 'Notnull' => bool, 'Default' => mixed, 'Autoincrement' => bool, 'Comment' => string|null
     */
    public function addColumn(string $tableName, string $columnName, string $type, array $options = []): void
    {
        $column = new Column($columnName, Type::getType($type), $options);
        $diff = new TableDiff($tableName, [$column]);
        $this->getSchemaManager()->alterTable($diff);
    }

    /**
     * @param string $tableName
     * @param string $columnName
     * @param array $options ['Type' => \Doctrine\DBAL\Types\Type, 'Length' => int|null, 'Precision' => int, 'Scale' => int, 'Unsigned' => bool, 'Fixed' => bool, 'Notnull' => bool, 'Default' => mixed, 'Autoincrement' => bool, 'Comment' => string|null]
     */
    public function changeColumn(string $tableName, string $columnName, array $options = []): void
    {
        $column = $this->getTableColumn($tableName, $columnName);
        $newColumn = clone $column;
        $newColumn->setOptions($options);
        $columnDiff = new ColumnDiff($columnName, $newColumn, [], $column);
        $diff = new TableDiff($tableName, [], [$columnDiff]);
        $this->getSchemaManager()->alterTable($diff);
    }

    /**
     * @param string $tableName
     * @param string $currentColumnName
     * @param string $newColumnName
     */
    public function renameColumn(string $tableName, string $currentColumnName, string $newColumnName): void
    {
        $column = $this->getTableColumn($tableName, $currentColumnName);
        $newColumn = new Column($newColumnName, $column->getType(), [
            'Length' => $column->getLength(),
            'Precision' => $column->getPrecision(),
            'Scale' => $column->getScale(),
            'Unsigned' => $column->getUnsigned(),
            'Fixed' => $column->getFixed(),
            'Notnull' => $column->getNotnull(),
            'Default' => $column->getDefault(),
            'Autoincrement' => $column->getAutoincrement(),
            'Comment' => $column->getComment(),
        ]);
        $columnDiff = new ColumnDiff($currentColumnName, $newColumn, [], $column);
        $diff = new TableDiff($tableName, [], [$columnDiff]);
        $this->getSchemaManager()->alterTable($diff);
    }

    /**
     * @param string $tableName
     * @param string[] $foreignKeys
     */
    public function dropForeignKeys(string $tableName, array $foreignKeys): void
    {
        $diff = new TableDiff($tableName);
        $diff->removedForeignKeys = $foreignKeys;
        $this->getSchemaManager()->alterTable($diff);
    }

    /**
     * @param string $localTableName
     * @param ForeignKeyConstraint $foreignKeyConstraint
     */
    public function addForeignKey(string $localTableName, ForeignKeyConstraint $foreignKeyConstraint): void
    {
        $diff = new TableDiff($localTableName);
        $diff->addedForeignKeys = [$foreignKeyConstraint];
        $this->getSchemaManager()->alterTable($diff);
    }

    /**
     * @param string $name
     * @param string $charset e.g. utf8mb4, utf8
     * @param string|null $collate e.g. utf8mb4_unicode_ci, utf8_general_ci
     * @return Table
     */
    public function createTable(string $name, string $charset = 'utf8', ?string $collate = null): Table
    {
        $table = new Table($name);

        // Only applicable for `src/vendor/doctrine/dbal/src/Platforms/MySQLPlatform.php`
        $table->addOption('charset', $charset);
        $table->addOption('collate', $collate ?? $charset . '_unicode_ci');
        $table->addOption('engine', 'InnoDB');
        $table->setSchemaManager($this->getSchemaManager());
        return $table;
    }

    /**
     * @param array $table
     * @return bool
     */
    public function tableExists(array $table): bool
    {
        return $this->getSchemaManager()->tablesExist($table);
    }

    /**
     * @param string $tableName
     * @return void
     */
    public function dropPrimaryKey(string $tableName): void
    {
        $table = $this->getSchemaManager()->listTableDetails($tableName);
        $this->getSchemaManager()->dropIndex($table->getPrimaryKey(), $table);
    }

    /**
     * @return \Doctrine\DBAL\Driver\Connection
     */
    private function getWrappedConnection(): \Doctrine\DBAL\Driver\Connection
    {
        return $this->getConnection()->getWrappedConnection();
    }

    /**
     * @return void
     */
    public function disableConstraints(): void
    {
        $pdo = $this->getWrappedConnection();
        $pdo->exec('SET FOREIGN_KEY_CHECKS=0;');
    }

    /**
     * @return void
     */
    public function enableConstraints(): void
    {
        $pdo = $this->getWrappedConnection();
        $pdo->exec('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * @param string $tableName
     * @param string $column
     * @return bool
     */
    public function columnExists(string $tableName, string $column): bool
    {
        $table = $this->getSchemaManager()->listTableDetails($tableName);
        return $table->hasColumn($column);
    }
}
