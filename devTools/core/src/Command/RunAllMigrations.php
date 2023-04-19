<?php

namespace OrangeHRM\DevTools\Command;

use Doctrine\DBAL\Connection;
use InvalidArgumentException;
use OrangeHRM\Installer\Util\AppSetupUtility;
use OrangeHRM\Installer\Util\ConfigHelper;
use OrangeHRM\Installer\Util\MigrationHelper;
use OrangeHRM\Installer\Util\V1\AbstractMigration;
use OrangeHRM\Installer\Util\V1\SchemaHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunAllMigrations extends Command
{
    protected static $defaultName = 'migration:all';
    private MigrationHelper $migrationHelper;
    private ConfigHelper $configHelper;
    private ?Connection $connection = null;
    private SchemaHelper $schemaHelper;

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setDescription('Run all migrations for all versions');
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
     * @return MigrationHelper
     */
    protected function getMigrationHelper(): MigrationHelper
    {
        return $this->migrationHelper ??= new MigrationHelper();
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $keys = array_keys(AppSetupUtility::MIGRATIONS_MAP);
        foreach ($keys as $version) {
            if ($this->migrationExist($version)) {
                continue;
            }

            try {
                $this->runMigrationFor($version);
            }catch (\Throwable $t) {
                echo "\n ". $version . "\n";
                echo $t->getMessage();
            }
        }
        return Command::SUCCESS;
    }

    /**
     * @return ConfigHelper
     */
    protected function getConfigHelper(): ConfigHelper
    {
        return $this->configHelper ??= new ConfigHelper();
    }

    /**
     * @param string $version
     * @return void
     */
    public function runMigrationFor(string $version): void
    {
        if (!isset(AppSetupUtility::MIGRATIONS_MAP[$version])) {
            throw new InvalidArgumentException("Invalid migration version `$version`");
        }
        if (is_array(AppSetupUtility::MIGRATIONS_MAP[$version])) {
            foreach (AppSetupUtility::MIGRATIONS_MAP[$version] as $migration) {
                $this->_runMigration($migration);
            }
            return;
        }

        $this->_runMigration(AppSetupUtility::MIGRATIONS_MAP[$version]);
    }

    private function migrationExist(string $version): bool
    {
        try {
            return $this->getConnection()->createQueryBuilder()
                    ->select('ohrm_migration_log.id')
                    ->from('ohrm_migration_log')
                    ->where('ohrm_migration_log.version = :version')
                    ->setParameter('version', $version)
                    ->orderBy('ohrm_migration_log.id', 'DESC')
                    ->setMaxResults(1)
                    ->executeQuery()
                    ->rowCount() > 0;
        } catch (\Throwable) {
            return false;
        }

    }

    /**
     * @param string $migrationClass
     */
    private function _runMigration(string $migrationClass): void
    {
        $migration = new $migrationClass();
        if ($migration instanceof AbstractMigration) {
            $version = $migration->getVersion();
            $this->getMigrationHelper()->logMigrationStarted($version);
            $migration->up();
            $this->getConfigHelper()->setConfigValue('instance.version', $version);
            $this->getMigrationHelper()->logMigrationFinished($version);
            return;
        }
        throw new InvalidArgumentException("Invalid migration class `$migrationClass`");
    }
}