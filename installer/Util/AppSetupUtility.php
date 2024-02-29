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
use Doctrine\DBAL\Types\Types;
use Exception;
use InvalidArgumentException;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Exception\KeyHandlerException;
use OrangeHRM\Core\Utility\KeyHandler;
use OrangeHRM\Core\Utility\PasswordHash;
use OrangeHRM\Framework\Filesystem\Filesystem;
use OrangeHRM\Installer\Exception\MigrationException;
use OrangeHRM\Installer\Migration\V3_3_3\Migration;
use OrangeHRM\Installer\Util\Dto\DatabaseConnectionWrapper;
use OrangeHRM\Installer\Util\SystemConfig\SystemConfiguration;
use OrangeHRM\Installer\Util\V1\AbstractMigration;

class AppSetupUtility
{
    public const MIGRATIONS_MAP = [
        '3.3.3' => Migration::class, // From beginning to `3.3.3`
        '4.0' => \OrangeHRM\Installer\Migration\V4_0\Migration::class,
        '4.1' => \OrangeHRM\Installer\Migration\V4_1\Migration::class,
        '4.1.1' => \OrangeHRM\Installer\Migration\V4_1_1\Migration::class,
        '4.1.2' => \OrangeHRM\Installer\Migration\V4_1_2\Migration::class,
        '4.1.2.1' => \OrangeHRM\Installer\Migration\V4_1_2_1\Migration::class,
        '4.2' => \OrangeHRM\Installer\Migration\V4_2\Migration::class,
        '4.2.0.1' => \OrangeHRM\Installer\Migration\V4_2_0_1\Migration::class,
        '4.3' => \OrangeHRM\Installer\Migration\V4_3\Migration::class,
        '4.3.1' => \OrangeHRM\Installer\Migration\V4_3_1\Migration::class,
        '4.3.2' => \OrangeHRM\Installer\Migration\V4_3_2\Migration::class,
        '4.3.3' => \OrangeHRM\Installer\Migration\V4_3_3\Migration::class,
        '4.3.4' => \OrangeHRM\Installer\Migration\V4_3_4\Migration::class,
        '4.3.5' => \OrangeHRM\Installer\Migration\V4_3_5\Migration::class,
        '4.4' => \OrangeHRM\Installer\Migration\V4_4_0\Migration::class,
        '4.5' => \OrangeHRM\Installer\Migration\V4_5_0\Migration::class,
        '4.6' => \OrangeHRM\Installer\Migration\V4_6_0\Migration::class,
        '4.6.0.1' => \OrangeHRM\Installer\Migration\V4_6_0_1\Migration::class,
        '4.7' => \OrangeHRM\Installer\Migration\V4_7_0\Migration::class,
        '4.8' => \OrangeHRM\Installer\Migration\V4_8_0\Migration::class,
        '4.9' => \OrangeHRM\Installer\Migration\V4_9_0\Migration::class,
        '4.10' => \OrangeHRM\Installer\Migration\V4_10_0\Migration::class,
        '4.10.1' => \OrangeHRM\Installer\Migration\V4_10_1\Migration::class,
        '5.0' => [
            \OrangeHRM\Installer\Migration\V5_0_0_beta\Migration::class,
            \OrangeHRM\Installer\Migration\V5_0_0\Migration::class,
        ],
        '5.1' => \OrangeHRM\Installer\Migration\V5_1_0\Migration::class,
        '5.2' => \OrangeHRM\Installer\Migration\V5_2_0\Migration::class,
        '5.3' => \OrangeHRM\Installer\Migration\V5_3_0\Migration::class,
        '5.4' => \OrangeHRM\Installer\Migration\V5_4_0\Migration::class,
        '5.5' => \OrangeHRM\Installer\Migration\V5_5_0\Migration::class,
        '5.6' => \OrangeHRM\Installer\Migration\V5_6_0\Migration::class,
        '5.6.1' => \OrangeHRM\Installer\Migration\V5_6_1\Migration::class,
        '5.7' => \OrangeHRM\Installer\Migration\V5_7_0\Migration::class,
    ];

    public const INSTALLATION_DB_TYPE_NEW = 'new';
    public const INSTALLATION_DB_TYPE_EXISTING = 'existing';

    private ConfigHelper $configHelper;
    private SystemConfiguration $systemConfiguration;
    private MigrationHelper $migrationHelper;

    /**
     * @return ConfigHelper
     */
    protected function getConfigHelper(): ConfigHelper
    {
        return $this->configHelper ??= new ConfigHelper();
    }

    /**
     * @return SystemConfiguration
     */
    protected function getSystemConfiguration(): SystemConfiguration
    {
        return $this->systemConfiguration ??= new SystemConfiguration();
    }

    /**
     * @return MigrationHelper
     */
    protected function getMigrationHelper(): MigrationHelper
    {
        return $this->migrationHelper ??= new MigrationHelper();
    }

    /**
     * @param string $dbName
     */
    public function createNewDatabase(string $dbName)
    {
        try {
            DatabaseServerConnection::getConnection()->createSchemaManager()->createDatabase($dbName);
        } catch (Exception $e) {
            Logger::getLogger()->error($e->getMessage());
            Logger::getLogger()->error($e->getTraceAsString());
        }
    }

    /**
     * Trying to connect database server without selecting a database
     * @return DatabaseConnectionWrapper
     */
    public function connectToDatabaseServer(): DatabaseConnectionWrapper
    {
        return DatabaseConnectionWrapper::establishConnection(fn () => DatabaseServerConnection::getConnection());
    }

    /**
     * @param string $dbName
     * @return bool
     */
    public function isDatabaseExist(string $dbName): bool
    {
        try {
            return in_array(
                $dbName,
                DatabaseServerConnection::getConnection()->createSchemaManager()->listDatabases()
            );
        } catch (Exception $e) {
            Logger::getLogger()->error($e->getMessage());
            Logger::getLogger()->error($e->getTraceAsString());
            return false;
        }
    }

    /**
     * @param string $dbUser
     * @return bool
     */
    public function isDatabaseUserExist(string $dbUser): bool
    {
        try {
            $dbUser = DatabaseServerConnection::getConnection()->quote($dbUser);
            $result = DatabaseServerConnection::getConnection()->executeQuery(
                "SELECT USER FROM mysql.user WHERE USER = $dbUser"
            );
            return $result->rowCount() > 0;
        } catch (Exception $e) {
            Logger::getLogger()->error($e->getMessage());
            Logger::getLogger()->error($e->getTraceAsString());
            return false;
        }
    }

    /**
     * Trying to connect existing database
     * @return DatabaseConnectionWrapper
     */
    public function connectToDatabase(): DatabaseConnectionWrapper
    {
        return DatabaseConnectionWrapper::establishConnection(fn () => Connection::getConnection());
    }

    /**
     * Connect existing database & check is empty
     * @return bool
     */
    public function isExistingDatabaseEmpty(): bool
    {
        return !(count(Connection::getConnection()->createSchemaManager()->listTables()) > 0);
    }

    /**
     * Create database at the installation
     */
    public function createDatabase(): void
    {
        if (StateContainer::getInstance()->getDbType() === AppSetupUtility::INSTALLATION_DB_TYPE_NEW) {
            $dbName = StateContainer::getInstance()->getDbInfo()[StateContainer::DB_NAME];
            if ($this->isDatabaseExist($dbName)) {
                throw new InvalidArgumentException("Cannot create database `$dbName`, already exist");
            }
            $this->createNewDatabase($dbName);
            return;
        } elseif (StateContainer::getInstance()->getDbType() === AppSetupUtility::INSTALLATION_DB_TYPE_EXISTING) {
            if (!$this->isExistingDatabaseEmpty()) {
                $dbName = StateContainer::getInstance()->getDbInfo()[StateContainer::DB_NAME];
                throw new InvalidArgumentException("Database `$dbName`, not empty");
            }
            return;
        }
        throw new InvalidArgumentException(
            'Invalid installation database type ' . StateContainer::getInstance()->getDbType()
        );
    }

    public function insertSystemConfiguration(): void
    {
        $this->insertCsrfKey();
        $this->insertOrganizationInfo();
        $this->insertSubunitOrganizationName();
        $this->setDefaultLanguage();
        $this->insertAdminEmployee();
        $this->insertAdminUser();
        !StateContainer::getInstance()->getRegConsent() ?: $this->insertInstanceIdentifier();
        $this->getConfigHelper()->setConfigValue(
            'instance.reg_consent',
            (int)StateContainer::getInstance()->getRegConsent()
        );
    }

    protected function insertCsrfKey(): void
    {
        $bytes = random_bytes(64); // 512/8
        $csrfSecret = rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
        $this->getConfigHelper()->setConfigValue('csrf_secret', $csrfSecret);
    }

    protected function insertOrganizationInfo(): void
    {
        $instanceData = StateContainer::getInstance()->getInstanceData();
        Connection::getConnection()->createQueryBuilder()
            ->insert('ohrm_organization_gen_info')
            ->values([
                'name' => ':name',
                'country' => ':countryCode',
            ])
            ->setParameter('name', $instanceData[StateContainer::INSTANCE_ORG_NAME])
            ->setParameter('countryCode', $instanceData[StateContainer::INSTANCE_COUNTRY_CODE])
            ->executeQuery();
    }

    protected function insertSubunitOrganizationName(): void
    {
        $instanceData = StateContainer::getInstance()->getInstanceData();
        Connection::getConnection()->createQueryBuilder()
            ->update('ohrm_subunit', 'subunit')
            ->set('subunit.name', ':organizationName')
            ->setParameter('organizationName', $instanceData[StateContainer::INSTANCE_ORG_NAME])
            ->andWhere('subunit.level = :topLevel')
            ->setParameter('topLevel', 0)
            ->executeQuery();
    }

    protected function setDefaultLanguage(): void
    {
        $instanceData = StateContainer::getInstance()->getInstanceData();
        $this->getConfigHelper()->setConfigValue(
            'admin.localization.default_language',
            $instanceData[StateContainer::INSTANCE_LANG_CODE] ?? 'en_US'
        );
    }

    /**
     * @param string $employeeId
     */
    protected function insertAdminEmployee(string $employeeId = '0001'): void
    {
        $adminUserData = StateContainer::getInstance()->getAdminUserData();
        Connection::getConnection()->createQueryBuilder()
            ->insert('hs_hr_employee')
            ->values([
                'employee_id' => ':employeeId',
                'emp_lastname' => ':lastName',
                'emp_firstname' => ':firstName',
                'emp_work_email' => ':workEmail',
                'emp_work_telephone' => ':contact',
            ])
            ->setParameter('employeeId', $employeeId)
            ->setParameter('firstName', $adminUserData[StateContainer::ADMIN_FIRST_NAME])
            ->setParameter('lastName', $adminUserData[StateContainer::ADMIN_LAST_NAME])
            ->setParameter('workEmail', $adminUserData[StateContainer::ADMIN_EMAIL])
            ->setParameter('contact', $adminUserData[StateContainer::ADMIN_CONTACT])
            ->executeQuery();
    }

    /**
     * @param string $employeeId
     */
    protected function insertAdminUser(string $employeeId = '0001'): void
    {
        $empNumber = Connection::getConnection()->createQueryBuilder()
            ->select('employee.emp_number')
            ->from('hs_hr_employee', 'employee')
            ->where('employee.employee_id = :employeeId')
            ->setParameter('employeeId', $employeeId)
            ->setMaxResults(1)
            ->fetchOne();

        $adminUserRoleId = Connection::getConnection()->createQueryBuilder()
            ->select('userRole.id')
            ->from('ohrm_user_role', 'userRole')
            ->where('userRole.name = :userRoleName')
            ->setParameter('userRoleName', 'Admin')
            ->setMaxResults(1)
            ->fetchOne();

        $adminUserData = StateContainer::getInstance()->getAdminUserData();
        $passwordHasher = new PasswordHash();
        $hashedPassword = $passwordHasher->hash($adminUserData[StateContainer::ADMIN_PASSWORD]);
        Connection::getConnection()->createQueryBuilder()
            ->insert('ohrm_user')
            ->values([
                'user_role_id' => ':userRoleId',
                'emp_number' => ':empNumber',
                'user_name' => ':username',
                'user_password' => ':hashedPassword',
                'date_entered' => ':created',
            ])
            ->setParameter('userRoleId', $adminUserRoleId)
            ->setParameter('empNumber', $empNumber)
            ->setParameter('username', $adminUserData[StateContainer::ADMIN_USERNAME])
            ->setParameter('hashedPassword', $hashedPassword)
            ->setParameter('created', new DateTime(), Types::DATETIME_MUTABLE)
            ->executeQuery();

        $this->updateUniqueIdForAdminEmployeeInsertion();
    }

    private function updateUniqueIdForAdminEmployeeInsertion(): void
    {
        Connection::getConnection()->createQueryBuilder()
            ->update('hs_hr_unique_id', 'uniqueId')
            ->set('uniqueId.last_id', ':id')
            ->where('uniqueId.table_name = :table')
            ->setParameter('id', 1)
            ->setParameter('table', 'hs_hr_employee')
            ->executeQuery();
    }

    /**
     * When installing via the application installer, it will get the
     * unique identifiers from the session.
     * When installing via the CLI installer, it will create new
     * unique identifiers since no unique identifiers stored in the session.
     */
    protected function insertInstanceIdentifier(): void
    {
        $instanceIdentifierData = StateContainer::getInstance()->getInstanceIdentifierData();

        if (isset($instanceIdentifierData[StateContainer::INSTANCE_IDENTIFIER])) {
            $this->getConfigHelper()->setConfigValue(
                SystemConfiguration::INSTANCE_IDENTIFIER,
                $instanceIdentifierData[StateContainer::INSTANCE_IDENTIFIER]
            );
        } else {
            $instanceIdentifier = $this->getInstanceIdentifier();
            $this->getConfigHelper()->setConfigValue(
                SystemConfiguration::INSTANCE_IDENTIFIER,
                $instanceIdentifier
            );
        }
    }

    /**
     * @return string
     */
    public function getInstanceIdentifier(): string
    {
        $instanceIdentifierData = $this->getInstanceIdentifierData();
        return $this->getSystemConfiguration()
            ->createInstanceIdentifier(...$instanceIdentifierData);
    }

    /**
     * @return array
     */
    private function getInstanceIdentifierData(): array
    {
        $adminUserData = StateContainer::getInstance()->getAdminUserData();
        $instanceData = StateContainer::getInstance()->getInstanceData();
        $dateTime = new DateTime();
        return [
            $instanceData[StateContainer::INSTANCE_ORG_NAME],
            $adminUserData[StateContainer::ADMIN_EMAIL],
            $adminUserData[StateContainer::ADMIN_FIRST_NAME],
            $adminUserData[StateContainer::ADMIN_LAST_NAME],
            $_SERVER['HTTP_HOST'] ?? null,
            $instanceData[StateContainer::INSTANCE_COUNTRY_CODE] ?? null,
            Config::PRODUCT_VERSION,
            $dateTime->getTimestamp()
        ];
    }

    public function createDBUser(): void
    {
        if (StateContainer::getInstance()->getDbType() === AppSetupUtility::INSTALLATION_DB_TYPE_NEW) {
            $dbInfo = StateContainer::getInstance()->getDbInfo();
            $dbName = $dbInfo[StateContainer::DB_NAME];
            $dbUser = $dbInfo[StateContainer::DB_USER];
            $ohrmDbUser = $dbInfo[StateContainer::ORANGEHRM_DB_USER];
            if ($ohrmDbUser === null || $dbUser === $ohrmDbUser) {
                return;
            }
            $ohrmDbPassword = $dbInfo[StateContainer::ORANGEHRM_DB_PASSWORD];
            $queries = [
                ...$this->getUserCreationQueries($dbName, $ohrmDbUser, $ohrmDbPassword, 'localhost'),
                ...$this->getUserCreationQueries($dbName, $ohrmDbUser, $ohrmDbPassword, '%'),
                'FLUSH PRIVILEGES;'
            ];
            foreach ($queries as $query) {
                Connection::getConnection()->executeStatement($query);
            }
        }
    }

    protected function getUserCreationQueries(
        string $dbName,
        string $ohrmDbUser,
        ?string $ohrmDbPassword,
        string $grantHost
    ): array {
        $conn = Connection::getConnection();
        $dbName = $conn->quoteIdentifier($dbName);
        $ohrmDbUser = $conn->quote($ohrmDbUser);

        $queryIdentifiedBy = is_null($ohrmDbPassword) ? '' : 'IDENTIFIED BY ' . $conn->quote($ohrmDbPassword);
        $createQuery = "CREATE USER $ohrmDbUser@'$grantHost' $queryIdentifiedBy;";
        $grantQuery = 'GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, DROP, INDEX, REFERENCES, CREATE ROUTINE, ALTER ROUTINE, CREATE TEMPORARY TABLES, CREATE VIEW, EXECUTE ' .
            "ON $dbName.* TO $ohrmDbUser@'$grantHost';";
        return [$createQuery, $grantQuery];
    }

    public function writeConfFile(): void
    {
        $template = file_get_contents(realpath(__DIR__ . '/../config/Conf.tpl.php'));
        $search = ['{{dbHost}}', '{{dbPort}}', '{{dbName}}', '{{dbUser}}', '{{dbPass}}'];
        $dbInfo = StateContainer::getInstance()->getDbInfo();
        $replace = [
            $dbInfo[StateContainer::DB_HOST],
            $dbInfo[StateContainer::DB_PORT],
            $dbInfo[StateContainer::DB_NAME],
            $dbInfo[StateContainer::ORANGEHRM_DB_USER] ?? $dbInfo[StateContainer::DB_USER],
            $dbInfo[StateContainer::ORANGEHRM_DB_PASSWORD] ?? $dbInfo[StateContainer::DB_PASSWORD],
        ];

        $fs = new Filesystem();
        $fs->dumpFile(Config::get(Config::CONF_FILE_PATH), str_replace($search, $replace, $template));
        clearstatcache(true);
    }

    /**
     * @throws KeyHandlerException
     */
    public function writeKeyFile(): void
    {
        $keyHandler = new KeyHandler();
        $keyHandler::createKey();
    }

    /**
     * @param string $fromVersion
     * @param string|null $toVersion null for latest version
     * @param bool $includeFromVersion
     * @return string[]
     */
    public function getVersionsInRange(
        string $fromVersion,
        ?string $toVersion = null,
        bool $includeFromVersion = true
    ): array {
        $isIn = false;
        $versions = [];
        foreach (self::MIGRATIONS_MAP as $version => $migration) {
            if ($version === $toVersion) {
                $isIn = false;
                $versions[] = $version;
            } elseif ($isIn) {
                $versions[] = $version;
            } elseif ($version === $fromVersion) {
                $isIn = true;
                !$includeFromVersion ?: $versions[] = $version;
            }
        }
        return $versions;
    }

    /**
     * @param string $fromVersion
     * @param string|null $toVersion null for latest version
     */
    public function runMigrations(string $fromVersion, ?string $toVersion = null): void
    {
        StateContainer::getInstance()->clearMigrationCompleted();
        foreach ($this->getVersionsInRange($fromVersion, $toVersion) as $version) {
            $this->runMigrationFor($version);
        }
    }

    /**
     * @param string $version
     */
    public function runMigrationFor(string $version): void
    {
        if (!isset(self::MIGRATIONS_MAP[$version])) {
            throw new InvalidArgumentException("Invalid migration version `$version`");
        }

        $this->throwMigrationErrorIfPreviousIncomplete();

        if (is_array(self::MIGRATIONS_MAP[$version])) {
            foreach (self::MIGRATIONS_MAP[$version] as $migration) {
                $this->_runMigration($migration);
            }
            return;
        }

        $this->_runMigration(self::MIGRATIONS_MAP[$version]);
    }

    /**
     * @throws MigrationException
     */
    private function throwMigrationErrorIfPreviousIncomplete()
    {
        if (StateContainer::getInstance()->isMigrationCompleted() === false) {
            throw MigrationException::previousMigrationIncomplete();
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
            StateContainer::getInstance()->setMigrationCompleted(false);
            $this->disableExecutionTimeLimit();
            $migration->up();
            $this->getConfigHelper()->setConfigValue('instance.version', $version);
            StateContainer::getInstance()->setMigrationCompleted(true);
            $this->getMigrationHelper()->logMigrationFinished($version);
            return;
        }
        throw new InvalidArgumentException("Invalid migration class `$migrationClass`");
    }

    /**
     * Disable execution time limit to prevent migration failure
     */
    private function disableExecutionTimeLimit(): void
    {
        if (function_exists('set_time_limit')) {
            $success = set_time_limit(0);
            Logger::getLogger()->info('set_time_limit: ' . ($success ? 'success' : 'fail'));
        }
    }

    /**
     * @return string|null
     */
    public function getCurrentProductVersionFromDatabase(): ?string
    {
        $instanceVersion = $this->getConfigHelper()->getConfigValue('instance.version');
        if ($instanceVersion == null) {
            return null;
        }

        $migrationMap = self::MIGRATIONS_MAP;
        for (end($migrationMap); ($version = key($migrationMap)) !== null; prev($migrationMap)) {
            $migrationClasses = current($migrationMap);
            if (!is_array($migrationClasses)) {
                $migrationClasses = [$migrationClasses];
            }
            foreach ($migrationClasses as $migrationClass) {
                $migration = new $migrationClass();
                if ($migration instanceof AbstractMigration && $migration->getVersion() == $instanceVersion) {
                    return $version;
                }
            }
        }

        return null;
    }

    public function cleanUpInstallOnFailure(): void
    {
        Connection::getConnection()->executeStatement('SET FOREIGN_KEY_CHECKS=0;');
        foreach (Connection::getConnection()->createSchemaManager()->listTableNames() as $table) {
            Connection::getConnection()->createSchemaManager()->dropTable($table);
        }
        Connection::getConnection()->executeStatement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function dropDatabase(): void
    {
        $dbName = StateContainer::getInstance()->getDbInfo()[StateContainer::DB_NAME];
        Connection::getConnection()->createSchemaManager()->dropDatabase($dbName);
    }
}
