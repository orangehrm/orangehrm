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

namespace OrangeHRM\DevTools\Command;

use Conf;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MySQL57Platform;
use Doctrine\DBAL\Platforms\MySQL80Platform;
use Doctrine\DBAL\Types\Types;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateTestDatabaseCommand extends Command
{
    use EntityManagerHelperTrait;

    protected static $defaultName = 'instance:create-test-db';

    private SymfonyStyle $io;
    private ?Connection $testDBConnection = null;
    private ?AbstractPlatform $platform = null;
    private ?Conf $testConf = null;

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setDescription('Create test database');
    }

    /**
     * @inheritDoc
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $conn = $this->getEntityManager()->getConnection();
        $sm = $conn->createSchemaManager();
        if ($conn->getDatabasePlatform()->hasNativeJsonType()) {
            if ($conn->getDatabasePlatform() instanceof MySQL80Platform) {
                $this->platform = new Util\Platforms\MySQL80Platform();
            } elseif ($conn->getDatabasePlatform() instanceof MySQL57Platform) {
                $this->platform = new Util\Platforms\MySQL57Platform();
            }
        }

        define('ENVIRONMENT', 'test');
        $this->testConf = Config::getConf(true);

        try {
            $sm->dropDatabase($this->testConf->getDbName());
        } catch (\Exception $e) {
        }
        $sm->createDatabase($this->testConf->getDbName());

        $schema = $sm->createSchema();
        $this->getTestDBConnection()->createSchemaManager()->migrateSchema($schema);
        return Command::SUCCESS;

        $tables = [];
        $allTables = $sm->listTables();
        foreach ($allTables as $table) {
            $tableName = $table->getName();
            $results = $conn->fetchAllAssociative("SELECT * FROM " . $conn->quoteIdentifier($tableName));

            foreach ($results as $row) {
                $this->getTestDBConnection()->insert($tableName, $row);
            }
            $tables[] = [$table, count($results)];
        }
        $this->io->table(['Table', 'Records'], $tables);

        return Command::SUCCESS;
    }

    /**
     * @return Connection
     * @throws Exception
     */
    private function getTestDBConnection(): Connection
    {
        if (is_null($this->testDBConnection)) {
            $connectionParams = [
                'dbname' => $this->testConf->getDbName(),
                'user' => $this->testConf->getDbUser(),
                'password' => $this->testConf->getDbPass(),
                'host' => $this->testConf->getDbHost(),
                'port' => $this->testConf->getDbPort(),
                'driver' => 'pdo_mysql',
                'charset' => 'utf8mb4'
            ];
            is_null($this->platform) ?: $connectionParams['platform'] = $this->platform;
            $this->testDBConnection = DriverManager::getConnection($connectionParams);
            $this->testDBConnection->getDatabasePlatform()->registerDoctrineTypeMapping('enum', Types::STRING);
        }
        return $this->testDBConnection;
    }
}
