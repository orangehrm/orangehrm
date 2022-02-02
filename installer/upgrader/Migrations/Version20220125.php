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

namespace OrangeHRM\Tools\Migrations;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\ColumnDiff;
use Doctrine\DBAL\Schema\TableDiff;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;

class Version20220125
{
    use EntityManagerHelperTrait;

    protected function createQueryBuilder(): QueryBuilder
    {
        return $this->getEntityManager()->getConnection()->createQueryBuilder();
    }

    protected function getSchemaManager(): AbstractSchemaManager
    {
        return $this->getEntityManager()->getConnection()->createSchemaManager();
    }

    /**
     * @param AbstractSchemaManager $sm
     * @param string $tableName
     * @param string $columnName
     * @return Column|null
     */
    protected function getTableColumn(AbstractSchemaManager $sm, string $tableName, string $columnName): ?Column
    {
        return $sm->listTableColumns($tableName)[$columnName] ?? null;
    }

    public function up(): void
    {
        $sm = $this->getSchemaManager();
        $sourceIdColumn = $this->getTableColumn($sm, 'ohrm_i18n_lang_string', 'source_id');
        $unitIdColumn = $this->getTableColumn($sm, 'ohrm_i18n_lang_string', 'unit_id');
        $newUnitIdColumn = clone $unitIdColumn;
        $newUnitIdColumn->setType(Type::getType(Types::STRING));
        $newUnitIdColumn->setLength(255);
        $unitIdColumnDiff = new ColumnDiff('unit_id', $newUnitIdColumn, [], $unitIdColumn);

        $diff = new TableDiff('ohrm_i18n_lang_string', [], [$unitIdColumnDiff], [$sourceIdColumn]);
        $diff->removedForeignKeys = ['sourceId'];
        $sm->alterTable($diff);
        $sm->dropTable('ohrm_i18n_source');
    }
}
