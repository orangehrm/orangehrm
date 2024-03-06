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

namespace OrangeHRM\Installer\Util;

use DateTime;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Types\Types;
use Exception;
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
            ->addColumn('col_string', Types::STRING, ['Notnull' => true, 'Length' => 255])
            ->addColumn('col_nullable', Types::STRING, ['Notnull' => false, 'Default' => null, 'Length' => 255])
            ->addColumn('col_date', Types::DATETIME_MUTABLE)
            ->addColumn('col_timestamp', Types::DATETIMETZ_MUTABLE)
            ->addColumn('col_bool', Types::BOOLEAN)
            ->addColumn('col_text', Types::TEXT)
            ->addColumn('col_json', Types::JSON)
            ->addUniqueIndex(['col_string'], 'index')
            ->setPrimaryKey(['id'])
            ->create();
    }

    protected function evalCrudPermission(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->getConnection()->createQueryBuilder()
                ->insert('_ohrm_temp_table')
                ->setValue('col_string', ':valueString')
                ->setValue('col_nullable', ':valueNullable')
                ->setValue('col_date', ':valueDate')
                ->setValue('col_timestamp', ':valueTimestamp')
                ->setValue('col_bool', ':valueBool')
                ->setValue('col_text', ':valueText')
                ->setValue('col_json', ':valueJson')
                ->setParameter('valueString', "String $i")
                ->setParameter('valueNullable', $i % 3 == 0 ? null : 'String')
                ->setParameter('valueDate', new DateTime('2022-01-01'), Types::DATETIME_MUTABLE)
                ->setParameter('valueTimestamp', new DateTime('2022-12-31'), Types::DATETIMETZ_MUTABLE)
                ->setParameter('valueBool', $i % 2 == 0, Types::BOOLEAN)
                ->setParameter('valueText', 'Text')
                ->setParameter('valueJson', ['key' => "value $i"], Types::JSON)
                ->executeStatement();
        }

        $count = $this->getConnection()->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('_ohrm_temp_table')
            ->executeQuery()
            ->fetchOne();
        if ($count != 10) {
            throw new Exception('Invalid record count');
        }

        // Start - order by test
        $this->getConnection()->createQueryBuilder()
            ->select('_ohrm_temp_table.id')
            ->from('_ohrm_temp_table')
            ->andWhere('_ohrm_temp_table.col_nullable = :valueString')
            ->andWhere('_ohrm_temp_table.col_bool = :valueTrue')
            ->addOrderBy('_ohrm_temp_table.col_string', 'DESC')
            ->addOrderBy('_ohrm_temp_table.id')
            ->setParameter('valueString', 'String')
            ->setParameter('valueTrue', false, Types::BOOLEAN)
            ->executeQuery();
        // End - order by test

        // Start - sub query test
        $affectedRows = $this->getConnection()->createQueryBuilder()
            ->delete('_ohrm_temp_table')
            ->where('_ohrm_temp_table.col_string = :valueString')
            ->setParameter('valueString', 'String 8')
            ->executeStatement();
        if ($affectedRows != 1) {
            throw new Exception('Invalid affected row count');
        }

        $q = $this->getConnection()->createQueryBuilder()
            ->select('COUNT(_ohrm_temp_table.id)')
            ->from('_ohrm_temp_table')
            ->andWhere('_ohrm_temp_table.col_nullable = _temp_table.col_nullable')
            ->andWhere('_ohrm_temp_table.col_bool = _temp_table.col_bool');

        $results = $this->getConnection()->createQueryBuilder()
            ->select('_temp_table.id', sprintf('(%s) AS groupCount', $q->getSQL()))
            ->from('_ohrm_temp_table', '_temp_table')
            ->executeQuery();
        $expected = [
            ['id' => '1', 'groupCount' => '0'],
            ['id' => '2', 'groupCount' => '3'],
            ['id' => '3', 'groupCount' => '2'],
            ['id' => '4', 'groupCount' => '0'],
            ['id' => '5', 'groupCount' => '2'],
            ['id' => '6', 'groupCount' => '3'],
            ['id' => '7', 'groupCount' => '0'],
            ['id' => '8', 'groupCount' => '3'],
            ['id' => '10', 'groupCount' => '0'],
        ];
        $resultsAssoc = $results->fetchAllAssociative();
        foreach ($expected as $i => $expectedRow) {
            if ($resultsAssoc[$i] != $expectedRow) {
                throw new Exception('Invalid result');
            }
        }
        // End - sub query test

        // Start - having clause test
        $results = $this->getConnection()->createQueryBuilder()
            ->select('_temp_table.id, _temp_table.col_bool')
            ->from('_ohrm_temp_table', '_temp_table')
            ->andWhere('_temp_table.id < 3')
            ->orWhere('_temp_table.id > 6')
            ->andHaving('_temp_table.col_bool = :valueTrue')
            ->setParameter('valueTrue', true, Types::BOOLEAN)
            ->executeQuery();
        $expected = [
            ['id' => '1', 'col_bool' => '1'],
            ['id' => '7', 'col_bool' => '1'],
        ];
        $resultsAssoc = $results->fetchAllAssociative();
        foreach ($expected as $i => $expectedRow) {
            if ($resultsAssoc[$i] != $expectedRow) {
                throw new Exception('Invalid result');
            }
        }
        // End - having clause test

        // Start - update query test
        $affectedRows = $this->getConnection()->createQueryBuilder()
            ->update('_ohrm_temp_table')
            ->set('_ohrm_temp_table.col_nullable', ':updatedString')
            ->andWhere('_ohrm_temp_table.col_nullable = :valueString')
            ->andWhere('_ohrm_temp_table.col_bool = :valueTrue')
            ->setParameter('updatedString', '[Updated] String')
            ->setParameter('valueString', 'String')
            ->setParameter('valueTrue', true, Types::BOOLEAN)
            ->executeStatement();
        if ($affectedRows != 2) {
            throw new Exception('Invalid affected row count');
        }
        // End - update query test

        $count = $this->getConnection()->createQueryBuilder()
            ->select('COUNT(_ohrm_temp_table.id)')
            ->from('_ohrm_temp_table')
            ->executeQuery()
            ->fetchOne();
        if ($count != 9) {
            throw new Exception('Invalid record count');
        }

        // Start - delete query test
        $affectedRows = $this->getConnection()->createQueryBuilder()
            ->delete('_ohrm_temp_table')
            ->andWhere('_ohrm_temp_table.col_nullable = :valueString')
            ->andWhere('_ohrm_temp_table.col_bool = :valueTrue')
            ->setParameter('valueString', 'String')
            ->setParameter('valueTrue', false, Types::BOOLEAN)
            ->executeStatement();
        if ($affectedRows != 3) {
            throw new Exception('Invalid affected row count');
        }
        // End - delete query test

        $count = $this->getConnection()->createQueryBuilder()
            ->select('COUNT(_ohrm_temp_table.id)')
            ->from('_ohrm_temp_table')
            ->executeQuery()
            ->fetchOne();
        if ($count != 6) {
            throw new Exception('Invalid record count');
        }
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
        $this->getSchemaHelper()->addColumn(
            '_ohrm_temp_table',
            'foreign_id',
            Types::INTEGER,
            ['Default' => null, 'Notnull' => false]
        );
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
            ['onDelete' => 'SET NULL', 'onUpdate' => 'NO ACTION']
        );
        $this->getSchemaHelper()->addForeignKey('_ohrm_temp_table', $foreignKeyConstraint);

        $this->getConnection()->createQueryBuilder()
            ->delete('_ohrm_temp_table')
            ->executeStatement();

        for ($i = 0; $i < 10; $i++) {
            $id = null;
            if ($i % 2 == 0) {
                $id = 100 + $i;
                $this->getConnection()->createQueryBuilder()
                    ->insert('_ohrm_temp_foreign_table')
                    ->setValue('id', ':valueId')
                    ->setValue('col', ':valueString')
                    ->setParameter('valueId', $id)
                    ->setParameter('valueString', "#$i")
                    ->executeStatement();
            }

            $this->getConnection()->createQueryBuilder()
                ->insert('_ohrm_temp_table')
                ->setValue('col_string', ':valueString')
                ->setValue('col_nullable', ':valueNullable')
                ->setValue('col_date', ':valueDate')
                ->setValue('col_timestamp', ':valueTimestamp')
                ->setValue('col_bool', ':valueBool')
                ->setValue('col_text', ':valueText')
                ->setValue('col_json', ':valueJson')
                ->setValue('foreign_id', ':valueId')
                ->setParameter('valueString', "String $i")
                ->setParameter('valueNullable', $i % 3 == 0 ? null : 'String')
                ->setParameter('valueDate', new DateTime('2022-01-01'), Types::DATETIME_MUTABLE)
                ->setParameter('valueTimestamp', new DateTime('2022-12-31'), Types::DATETIMETZ_MUTABLE)
                ->setParameter('valueBool', $i % 2 == 0, Types::BOOLEAN)
                ->setParameter('valueText', 'Text')
                ->setParameter('valueJson', ['key' => "value $i"], Types::JSON)
                ->setParameter('valueId', $id)
                ->executeStatement();
        }

        $count = $this->getConnection()->createQueryBuilder()
            ->select('COUNT(_ohrm_temp_foreign_table.id)')
            ->from('_ohrm_temp_foreign_table')
            ->executeQuery()
            ->fetchOne();
        if ($count != 5) {
            throw new Exception('Invalid record count');
        }

        $results = $this->getConnection()->createQueryBuilder()
            ->select('_temp_foreign_table.id', '_temp_foreign_table.col', '_temp_table.col_string')
            ->from('_ohrm_temp_table', '_temp_table')
            ->leftJoin(
                '_temp_table',
                '_ohrm_temp_foreign_table',
                '_temp_foreign_table',
                '_temp_foreign_table.id = _temp_table.foreign_id'
            )
            ->setFirstResult(2)
            ->setMaxResults(3)
            ->addOrderBy('_temp_table.col_string')
            ->executeQuery();
        $expected = [
            ['id' => '102', 'col' => '#2', 'col_string' => 'String 2'],
            ['id' => null, 'col' => null, 'col_string' => 'String 3'],
            ['id' => '104', 'col' => '#4', 'col_string' => 'String 4'],
        ];
        $resultsAssoc = $results->fetchAllAssociative();
        foreach ($expected as $i => $expectedRow) {
            if ($resultsAssoc[$i] != $expectedRow) {
                throw new Exception('Invalid result');
            }
        }

        $this->getSchemaHelper()->dropForeignKeys('_ohrm_temp_table', ['fk_id']);
    }

    protected function evalDropPermission(): void
    {
        $this->getSchemaHelper()->disableConstraints();
        $this->getConnection()->createSchemaManager()->dropTable('_ohrm_temp_foreign_table');
        $this->getConnection()->createSchemaManager()->dropTable('_ohrm_temp_table');
        $this->getSchemaHelper()->enableConstraints();
    }

    public function evalPrivilegeDatabaseUserPermission(): void
    {
        try {
            $this->getSchemaHelper()->disableConstraints();
            $this->getConnection()->createSchemaManager()->dropTable('_ohrm_temp_foreign_table');
            $this->getSchemaHelper()->enableConstraints();
        } catch (Exception $e) {
        }
        try {
            $this->getSchemaHelper()->disableConstraints();
            $this->getConnection()->createSchemaManager()->dropTable('_ohrm_temp_table');
            $this->getSchemaHelper()->enableConstraints();
        } catch (Exception $e) {
        }

        $this->evalCreatePermission();
        $this->evalCrudPermission();
        $this->evalAlterPermission();
        $this->evalForeignKeyPermission();
        $this->evalDropPermission();
    }
}
