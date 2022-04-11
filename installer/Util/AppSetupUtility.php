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

use InvalidArgumentException;
use OrangeHRM\Framework\Http\Session\Session;
use OrangeHRM\Framework\ServiceContainer;
use OrangeHRM\Framework\Services;
use OrangeHRM\Installer\Migration\V4_3_3\Migration;
use OrangeHRM\Installer\Util\V1\AbstractMigration;

class AppSetupUtility
{
    public const MIGRATIONS_MAP = [
        '4.3.3' => Migration::class, // From previous version to `4.3.3`
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

    public function createDB()
    {
        // $_SESSION['dbCreateMethod'] == 'existing'
        // $_SESSION['dbCreateMethod'] == 'new'
    }

    public function insertCsrfKey()
    {
    }

    public function insertSystemConfiguration()
    {
    }

    public function initUniqueIDs()
    {
    }

    public function createDBUser()
    {
    }

    public function writeConfFile(): void
    {
        /** @var Session $session */
        $session = ServiceContainer::getContainer()->get(Services::SESSION);
        $dbHost = $session->get(Constant::DB_HOST);
        $dbPort = $session->get(Constant::DB_PORT);
        $dbUser = $session->get(Constant::DB_USER);
        $dbPassword = $session->get(Constant::DB_PASSWORD);
        $dbName = $session->get(Constant::DB_NAME);

        $template = file_get_contents(realpath(__DIR__ . '/../config/Conf.tpl.php'));
        $search = ['{{dbHost}}', '{{dbPort}}', '{{dbName}}', '{{dbUser}}', '{{dbPass}}'];
        $replace = [$dbHost, $dbPort, $dbName, $dbUser, $dbPassword];

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
            return;
        }
        throw new InvalidArgumentException("Invalid migration class `$migrationClass`");
    }
}
