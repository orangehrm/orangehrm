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

use Exception;
use InvalidArgumentException;
use OrangeHRM\Installer\Migration\V4_0\Migration;
use OrangeHRM\Installer\Util\V1\AbstractMigration;

class AppSetupUtility
{
    public const MIGRATIONS_MAP = [
        '4.0' => Migration::class, // From previous version to `4.0`
        '4.1' => \OrangeHRM\Installer\Migration\V4_1\Migration::class,
        '4.1.1' => \OrangeHRM\Installer\Migration\V4_1_1\Migration::class,
        '4.1.2' => \OrangeHRM\Installer\Migration\V4_1_2\Migration::class,
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

    private ?ConfigHelper $configHelper = null;

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

    /**
     * @param string $dbName
     */
    public function createNewDatabase(string $dbName)
    {
        try {
            Connection::getConnection()->createSchemaManager()->createDatabase($dbName);
        } catch (Exception $e) {
            Logger::getLogger()->error($e->getMessage());
            Logger::getLogger()->error($e->getTraceAsString());
        }
    }

    /**
     * @return bool
     */
    public function isExistingDatabaseEmpty(): bool
    {
        return !(Connection::getConnection()->createSchemaManager()->listTables() > 0);
    }

    public function insertCsrfKey()
    {
        // TODO:: Develop with installer
    }

    public function insertSystemConfiguration()
    {
        // TODO:: Develop with installer
    }

    public function initUniqueIDs()
    {
        // TODO:: Develop with installer
    }

    public function createDBUser()
    {
        // TODO:: Develop with installer
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

        file_put_contents(
            realpath(__DIR__ . '/../../lib/confs') . DIRECTORY_SEPARATOR . 'Conf.php',
            str_replace($search, $replace, $template)
        );
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
}
