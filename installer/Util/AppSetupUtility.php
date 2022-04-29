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

namespace OrangeHRM\Installer\Util;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Exception;
use InvalidArgumentException;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Utility\PasswordHash;
use OrangeHRM\Installer\Migration\V3_3_3\Migration;
use OrangeHRM\Installer\Util\SystemConfig\SystemConfiguration;
use OrangeHRM\Installer\Util\V1\AbstractMigration;
use Symfony\Component\Filesystem\Filesystem;

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
    ];

    public const INSTALLATION_DB_TYPE_NEW = 'new';
    public const INSTALLATION_DB_TYPE_EXISTING = 'existing';

    public const ERROR_CODE_ACCESS_DENIED = 1045;
    public const ERROR_CODE_INVALID_HOST_PORT = 2002;
    public const ERROR_CODE_DATABASE_NOT_EXISTS = 1049;

    private ?ConfigHelper $configHelper = null;
    private ?SystemConfiguration $systemConfiguration = null;

    /**
     * @return ConfigHelper
     */
    protected function getConfigHelper(): ConfigHelper
    {
        if (!$this->configHelper instanceof ConfigHelper) {
            $this->configHelper = new ConfigHelper();
        }
        return $this->configHelper;
    }

    protected function getSystemConfiguration(): SystemConfiguration
    {
        if (!$this->systemConfiguration instanceof SystemConfiguration) {
            $this->systemConfiguration = new SystemConfiguration();
        }
        return $this->systemConfiguration;
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
     * @return bool|Exception
     */
    public function connectToDatabaseServer()
    {
        try {
            DatabaseServerConnection::getConnection()->connect();
            return true;
        } catch (Exception $e) {
            Logger::getLogger()->error($e->getMessage());
            Logger::getLogger()->error($e->getTraceAsString());
            return $e;
        }
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
     * @return bool|Exception
     */
    public function connectToDatabase()
    {
        try {
            Connection::getConnection()->connect();
            return true;
        } catch (Exception $e) {
            Logger::getLogger()->error($e->getMessage());
            Logger::getLogger()->error($e->getTraceAsString());
            return $e;
        }
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
        $this->setDefaultLanguage();
        $this->insertAdminEmployee();
        $this->insertAdminUser();
        $this->insertInstanceIdentifierAndChecksum();
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
    protected function insertInstanceIdentifierAndChecksum(): void
    {
        $instanceIdentifierData = StateContainer::getInstance()->getInstanceIdentifierData();

        if (!is_null($instanceIdentifierData)) {
            $this->getConfigHelper()->setConfigValue(
                SystemConfiguration::INSTANCE_IDENTIFIER,
                $instanceIdentifierData[StateContainer::INSTANCE_IDENTIFIER]
            );
            $this->getConfigHelper()->setConfigValue(
                SystemConfiguration::INSTANCE_IDENTIFIER_CHECKSUM,
                $instanceIdentifierData[StateContainer::INSTANCE_IDENTIFIER_CHECKSUM]
            );
        } else {
            list(
                $instanceIdentifier,
                $instanceIdentifierChecksum
                ) = $this->getInstanceUniqueIdentifyingData();

            $this->getConfigHelper()->setConfigValue(
                SystemConfiguration::INSTANCE_IDENTIFIER,
                $instanceIdentifier
            );
            $this->getConfigHelper()->setConfigValue(
                SystemConfiguration::INSTANCE_IDENTIFIER_CHECKSUM,
                $instanceIdentifierChecksum
            );
        }
    }

    /**
     * @return array
     */
    public function getInstanceUniqueIdentifyingData(): array
    {
        $instanceIdentifierData = $this->getInstanceIdentifierData();

        $instanceIdentifier = $this->getSystemConfiguration()
            ->createInstanceIdentifier(...$instanceIdentifierData);
        $instanceIdentifierChecksum = $this->getSystemConfiguration()
            ->createInstanceIdentifierChecksum(...$instanceIdentifierData);

        return [
            $instanceIdentifier,
            $instanceIdentifierChecksum
        ];
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
            if ($dbUser === $ohrmDbUser) {
                return;
            }
            $ohrmDbPassword = $dbInfo[StateContainer::ORANGEHRM_DB_PASSWORD];
            Connection::getConnection()->executeStatement(
                $this->getUserCreationQuery($dbName, $ohrmDbUser, $ohrmDbPassword, 'localhost')
            );
            Connection::getConnection()->executeStatement(
                $this->getUserCreationQuery($dbName, $ohrmDbUser, $ohrmDbPassword, '%')
            );
        }
    }

    protected function getUserCreationQuery(
        string $dbName,
        string $ohrmDbUser,
        ?string $ohrmDbPassword,
        string $grantHost
    ) {
        $conn = Connection::getConnection();
        $dbName = $conn->quoteIdentifier($dbName);
        $ohrmDbUser = $conn->quote($ohrmDbUser);
        $queryIdentifiedBy = empty($ohrmDbPassword) ? '' : "IDENTIFIED BY " . $conn->quote($ohrmDbPassword);
        return "GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, DROP, INDEX, CREATE ROUTINE, ALTER ROUTINE, CREATE TEMPORARY TABLES, CREATE VIEW, EXECUTE " .
            "ON $dbName.* TO $ohrmDbUser@'$grantHost' $queryIdentifiedBy;";
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
            $dbInfo[StateContainer::DB_USER],
            $dbInfo[StateContainer::DB_PASSWORD]
        ];

        $fs = new Filesystem();
        $fs->dumpFile(Config::get(Config::CONF_FILE_PATH), str_replace($search, $replace, $template));
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
            if ($version == $toVersion) {
                $isIn = false;
                $versions[] = $version;
            } elseif ($isIn) {
                $versions[] = $version;
            } elseif ($version == $fromVersion) {
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
        foreach ($this->getVersionsInRange($fromVersion, $toVersion) as $version) {
            $this->runMigrationFor($version);
        }
    }

    /**
     * @param string $version
     * @return void
     */
    public function runMigrationFor(string $version): void
    {
        if (!isset(self::MIGRATIONS_MAP[$version])) {
            throw new InvalidArgumentException("Invalid migration version `$version`");
        }

        if (is_array(self::MIGRATIONS_MAP[$version])) {
            foreach (self::MIGRATIONS_MAP[$version] as $migration) {
                $this->_runMigration($migration);
            }
            return;
        }

        $this->_runMigration(self::MIGRATIONS_MAP[$version]);
    }

    /**
     * @param string $migrationClass
     */
    private function _runMigration(string $migrationClass): void
    {
        $migration = new $migrationClass();
        if ($migration instanceof AbstractMigration) {
            $migration->up();
            $this->getConfigHelper()->setConfigValue('instance.version', $migration->getVersion());
            return;
        }
        throw new InvalidArgumentException("Invalid migration class `$migrationClass`");
    }

    /**
     * @return string|null
     */
    public function getCurrentProductVersionFromDatabase(): ?string
    {
        return $this->getConfigHelper()->getConfigValue('instance.version');
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

    /**
     * get possible errors based on DBAL exception when connecting to server with database
     * @param \Doctrine\DBAL\Exception $exception
     * @param string $dbHost
     * @param string $dbPort
     * @return string
     */
    public function getExistingDBConnectionErrorMessage(
        \Doctrine\DBAL\Exception $exception,
        string $dbHost,
        string $dbPort
    ): string {
        $errorMessage = $exception->getMessage();
        $errorCode = $exception->getCode();

        if ($errorCode === self::ERROR_CODE_INVALID_HOST_PORT) {
            $message = "The MySQL server isn't running on `$dbHost:$dbPort`. " . Messages::ERROR_MESSAGE_INVALID_HOST_PORT;
        } elseif ($errorCode === self::ERROR_CODE_ACCESS_DENIED) {
            $message = Messages::ERROR_MESSAGE_ACCESS_DENIED;
        } elseif ($errorCode === self::ERROR_CODE_DATABASE_NOT_EXISTS) {
            $message = 'Database Not Exist';
        } else {
            $message = $errorMessage . ' ' . Messages::ERROR_MESSAGE_REFER_LOG_FOR_MORE;
        }
        return $message;
    }

    /**
     * get possible errors based on DBAL exception when connecting to server without database
     * @param \Doctrine\DBAL\Exception $exception
     * @param string $dbHost
     * @param string $dbPort
     * @return string
     */
    public function getNewDBConnectionErrorMessage(
        \Doctrine\DBAL\Exception $exception,
        string $dbHost,
        string $dbPort
    ): string {
        $errormessage = $exception->getMessage();
        $errorCode = $exception->getCode();

        if ($errorCode === self::ERROR_CODE_INVALID_HOST_PORT) {
            $message = "The MySQL server isn't running on `$dbHost:$dbPort`. " . Messages::ERROR_MESSAGE_INVALID_HOST_PORT;
        } elseif ($errorCode === self::ERROR_CODE_ACCESS_DENIED) {
            $message = Messages::ERROR_MESSAGE_ACCESS_DENIED;
        } else {
            $message = $errormessage . ' ' . Messages::ERROR_MESSAGE_REFER_LOG_FOR_MORE;
        }
        return $message;
    }
}
