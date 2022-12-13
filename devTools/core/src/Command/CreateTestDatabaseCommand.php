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
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Types;
use Exception;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Tests\Util\CoreFixtureService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

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
        $this->setDescription('Create test database')
            ->addOption('user', 'u', InputOption::VALUE_REQUIRED, 'Privileged database user', 'root')
            ->addOption('password', 'p', InputOption::VALUE_REQUIRED)
            ->addOption('dump-options', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, '', []);
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
        $conf = Config::getConf();
        $conn = $this->getEntityManager()->getConnection();
        $sm = $conn->createSchemaManager();

        define('ENVIRONMENT', 'test');
        $this->testConf = Config::getConf(true);

        try {
            $sm->dropDatabase($this->testConf->getDbName());
        } catch (Exception $e) {
        }
        $sm->createDatabase($this->testConf->getDbName());

        $command = [
            'mysqldump',
            '--host=' . $conf->getDbHost(),
            '--port=' . $conf->getDbPort(),
            '--user=' . $input->getOption('user'),
            '--add-drop-table',
            '--routines',
            '--skip-triggers',
            ...$input->getOption('dump-options'), // --column-statistics=0
        ];
        if (!is_null($input->getOption('password'))) {
            $command[] = '--password=' . $input->getOption('password');
        }
        $command[] = $conf->getDbName();
        $process = new Process($command);

        try {
            $process->mustRun();
            $dbScriptStatements = preg_split('/;\s*$/m', $process->getOutput());
            $testDBConnection = $this->createTestDBConnection($input->getOption('user'), $input->getOption('password'));
            foreach ($dbScriptStatements as $statement) {
                if (empty(trim($statement))) {
                    continue;
                }
                $testDBConnection->executeStatement($statement);
            }
        } catch (ProcessFailedException $exception) {
            $this->io->error($exception->getMessage());
            return Command::FAILURE;
        }

        $this->io->success("Test db {$this->testConf->getDbName()} created");

        $coreFixtureService = new CoreFixtureService();
        $coreFixtureService->saveToFixtures();

        $this->io->success('Core fixtures generated.');

        return Command::SUCCESS;
    }

    /**
     * @param string $user
     * @param string|null $password
     * @return Connection
     */
    private function createTestDBConnection(string $user, ?string $password): Connection
    {
        $connectionParams = [
            'dbname' => $this->testConf->getDbName(),
            'user' => $user,
            'password' => $password,
            'host' => $this->testConf->getDbHost(),
            'port' => $this->testConf->getDbPort(),
            'driver' => 'pdo_mysql',
            'charset' => 'utf8mb4'
        ];
        is_null($this->platform) ?: $connectionParams['platform'] = $this->platform;
        $testDBConnection = DriverManager::getConnection($connectionParams);
        $testDBConnection->getDatabasePlatform()->registerDoctrineTypeMapping('enum', Types::STRING);
        return $testDBConnection;
    }
}
