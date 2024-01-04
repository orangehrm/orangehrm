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
use DateTimeZone;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Installer\Util\V1\SchemaHelper;

class MigrationHelper
{
    private ?Connection $connection;
    private SchemaHelper $schemaHelper;

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
     * @return SchemaHelper
     */
    protected function getSchemaHelper(): SchemaHelper
    {
        return $this->schemaHelper ??= new SchemaHelper($this->getConnection());
    }

    /**
     * @param string $version
     * @return int
     */
    public function logMigrationStarted(string $version): int
    {
        if (!$this->getSchemaHelper()->tableExists(['ohrm_migration_log'])) {
            $this->getSchemaHelper()->createTable('ohrm_migration_log')
                ->addColumn('id', Types::INTEGER, ['Autoincrement' => true])
                ->addColumn('version', Types::STRING, ['Length' => 10])
                ->addColumn('db_version', Types::STRING, ['Length' => 255])
                ->addColumn('php_version', Types::STRING, ['Length' => 255])
                ->addColumn('started_at', Types::DATETIME_MUTABLE)
                ->addColumn('finished_at', Types::DATETIME_MUTABLE, ['Notnull' => false, 'Default' => null])
                ->setPrimaryKey(['id'])
                ->create();
        }

        $systemCheck = new SystemCheck($this->getConnection());

        return $this->getConnection()->createQueryBuilder()
            ->insert('ohrm_migration_log')
            ->setValue('version', ':migrationVersion')
            ->setValue('db_version', ':databaseVersion')
            ->setValue('php_version', ':phpVersion')
            ->setValue('started_at', ':startedAt')
            ->setParameter('migrationVersion', $version)
            ->setParameter('databaseVersion', substr($systemCheck->getMysqlServerVersion(), 0, 255))
            ->setParameter('phpVersion', substr($systemCheck->getPhpVersion(), 0, 255))
            ->setParameter(
                'startedAt',
                (new DateTime())->setTimezone(new DateTimeZone(DateTimeHelperService::TIMEZONE_UTC)),
                Types::DATETIME_MUTABLE
            )
            ->executeStatement();
    }

    /**
     * @param string $version
     * @return int
     */
    public function logMigrationFinished(string $version): int
    {
        // Fetching last record, rather than directly run update query
        $id = $this->getConnection()->createQueryBuilder()
            ->select('ohrm_migration_log.id')
            ->from('ohrm_migration_log')
            ->where('ohrm_migration_log.version = :version')
            ->setParameter('version', $version)
            ->orderBy('ohrm_migration_log.id', 'DESC')
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchOne();
        return $this->getConnection()->createQueryBuilder()
            ->update('ohrm_migration_log')
            ->set('ohrm_migration_log.finished_at', ':finishedAt')
            ->where('ohrm_migration_log.id = :id')
            ->setParameter('id', $id)
            ->setParameter(
                'finishedAt',
                (new DateTime())->setTimezone(new DateTimeZone(DateTimeHelperService::TIMEZONE_UTC)),
                Types::DATETIME_MUTABLE
            )
            ->executeStatement();
    }
}
