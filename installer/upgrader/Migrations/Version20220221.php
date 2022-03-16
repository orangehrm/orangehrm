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

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Column;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Tools\Migrations\V5\LangStringHelper;

class Version20220221
{
    use EntityManagerHelperTrait;

    protected ?LangStringHelper $langStringHelper = null;

    public function up(): void
    {
        $modules = ['admin','general','pim','leave','time'];
        $modules = ['admin','general','pim','leave','attendance'];
        foreach ($modules as $module){
            $groupId = $this->getLangStringHelper()->getGroupId($module);
            $langArray = $this->getLangStringHelper()->getLangStringArray($module);
            $this->getLangStringHelper()->deleteNonCustomLangStrings($groupId);
            $this->getLangStringHelper()->versionMigrateLangStrings($langArray, $groupId);
        }
    }

    /**
     * @return LangStringHelper
     */
    public function getLangStringHelper(): LangStringHelper
    {
        if (is_null($this->langStringHelper)) {
            $this->langStringHelper = new LangStringHelper();
        }
        return $this->langStringHelper;
    }

    /**
     * @param string $entityClass
     * @param string $alias
     * @param string|null $indexBy
     * @return QueryBuilder
     */
    protected function createQueryBuilder(): QueryBuilder
    {
        return $this->getEntityManager()->getConnection()->createQueryBuilder();
    }

    /**
     * @return AbstractSchemaManager
     * @throws Exception
     */
    protected function getSchemaManager(): AbstractSchemaManager
    {
        return $this->getEntityManager()->getConnection()->createSchemaManager();
    }

    /**
     * @param AbstractSchemaManager $sm
     * @param string $tableName
     * @param string $columnName
     * @return Column|null
     * @throws Exception
     */
    protected function getTableColumn(AbstractSchemaManager $sm, string $tableName, string $columnName): ?Column
    {
        return $sm->listTableColumns($tableName)[$columnName] ?? null;
    }

    /**
     * @return Connection
     * @throws Exception
     */
    private function getConnection(): Connection
    {
        $conn = $this->getEntityManager()->getConnection();
        $conn->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        return $conn;
    }
}
