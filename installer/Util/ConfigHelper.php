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

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;

class ConfigHelper
{
    private ?Connection $connection;

    /**
     * @param Connection|null $connection
     */
    public function __construct(?Connection $connection = null)
    {
        $this->connection = $connection;
    }

    /**
     * @return Connection
     */
    protected function getConnection(): Connection
    {
        if ($this->connection instanceof Connection) {
            return $this->connection;
        }
        return \OrangeHRM\Installer\Util\Connection::getConnection();
    }

    /**
     * @return AbstractSchemaManager
     */
    protected function getSchemaManager(): AbstractSchemaManager
    {
        return $this->getConnection()->createSchemaManager();
    }

    /**
     * @param string $name
     * @param null|string $default
     * @return null|string
     */
    public function getConfigValue(string $name, string $default = null)
    {
        $table = $this->getSchemaManager()->listTableDetails('hs_hr_config');
        $keyFieldsColumnName = $table->hasColumn('name') ? 'name' : '`key`';
        $result = $this->getConnection()->createQueryBuilder()
            ->select('config.value')
            ->from('hs_hr_config', 'config')
            ->where("config.$keyFieldsColumnName = :configName")
            ->setParameter('configName', $name)
            ->setMaxResults(1)
            ->fetchOne();
        return $result === false ? $default : $result;
    }

    /**
     * @param string $name
     * @param string|null $value
     */
    public function setConfigValue(string $name, ?string $value): void
    {
        $table = $this->getSchemaManager()->listTableDetails('hs_hr_config');
        $keyFieldsColumnName = $table->hasColumn('name') ? 'name' : '`key`';

        $currentValue = $this->getConfigValue($name);
        if (is_null($currentValue)) {
            $qb = $this->getConnection()->createQueryBuilder()
                ->insert('hs_hr_config')
                ->values(["$keyFieldsColumnName" => ':configName', 'value' => ':value']);
        } else {
            $qb = $this->getConnection()->createQueryBuilder()
                ->update('hs_hr_config', 'config')
                ->set('config.value', ':value')
                ->andWhere("config.$keyFieldsColumnName = :configName");
        }

        $qb->setParameter('value', $value)
            ->setParameter('configName', $name)
            ->executeQuery();
    }

    /**
     * @param string $name
     */
    public function deleteConfigValue(string $name): void
    {
        $table = $this->getSchemaManager()->listTableDetails('hs_hr_config');
        $keyFieldsColumnName = $table->hasColumn('name') ? 'name' : '`key`';

        $currentValue = $this->getConfigValue($name);
        Logger::getLogger()->info("Deleting: `$name` => `$currentValue` from `hs_hr_config`");

        $this->getConnection()->createQueryBuilder()
            ->delete('hs_hr_config')
            ->andWhere("hs_hr_config.$keyFieldsColumnName = :configName")
            ->setParameter('configName', "$name")
            ->executeQuery();
    }
}
