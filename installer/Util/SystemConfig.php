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
use OrangeHRM\Installer\Controller\Upgrader\Traits\UpgraderUtilityTrait;
use Symfony\Component\Filesystem\Filesystem;

class SystemConfig
{
    use UpgraderUtilityTrait;

    public const PASSED = 1;
    public const ACCEPTABLE = 2;
    public const BLOCKER = 3;

    public const ENGINE_INNODB = 'InnoDB';

    public const STATE_DISABLED = 'DISABLED';
    public const STATE_DEFAULT = 'DEFAULT';
    public const STATE_YES = 'YES';
    public const STATE_NO = 'NO';
    public const STATE_ON = 'ON';

    public const INSTALL_UTIL_MEMORY_NO_LIMIT = 0;
    public const INSTALL_UTIL_MEMORY_UNLIMITED = 1;
    public const INSTALL_UTIL_MEMORY_HARD_LIMIT_FAIL = 2;
    public const INSTALL_UTIL_MEMORY_SOFT_LIMIT_FAIL = 3;
    public const INSTALL_UTIL_MEMORY_OK = 4;

    private bool $interruptContinue = false;
    private ?Filesystem $filesystem = null;

    public function __construct()
    {
        $this->filesystem = new Filesystem();
    }

    /**
     * @return bool
     */
    public function isInterruptContinue(): bool
    {
        return $this->interruptContinue;
    }

    /**
     * Category => Environment Check
     */

    /**
     * PHP Version Check
     * @return array
     */
    public function isPHPVersionCompatible(): array
    {
        if (version_compare(PHP_VERSION, Messages::PHP_MIN_VERSION) < 0) {
            $this->interruptContinue = true;
            return [
                'message' => Messages::PHP_FAIL_MESSAGE . " Installed version is " . PHP_VERSION,
                'status' => self::BLOCKER
            ];
        } else {
            return [
                'message' => Messages::PHP_OK_MESSAGE . " (ver " . PHP_VERSION . ")",
                'status' => self::PASSED
            ];
        }
    }

    /**
     * MYSQL Client Check
     * @return array
     */
    public function isMySqlClientCompatible(): array
    {
        if (function_exists('mysqli_get_client_info')) {
            $mysqlClient = mysqli_get_client_info();
            $versionPattern = '/[0-9]+\.[0-9]+\.[0-9]+/';

            preg_match($versionPattern, $mysqlClient, $matches);
            $mysql_client_version = $matches[0];

            if (version_compare($mysql_client_version, Messages::MYSQL_MIN_VERSION) < 0) {
                return [
                    'message' => Messages::MYSQL_CLIENT_RECOMMEND_MESSAGE . "(reported ver " . $mysqlClient . ")",
                    'status' => self::ACCEPTABLE
                ];
            } else {
                return [
                    'message' => Messages::MYSQL_CLIENT_OK_MESSAGE,
                    'status' => self::PASSED
                ];
            }
        } else {
            $this->interruptContinue = true;
            return [
                'message' => Messages::MYSQL_CLIENT_FAIL_MESSAGE,
                'status' => 3
            ];
        }
    }


    /**
     * MYSQL Server Check
     * @return array
     * @throws \Doctrine\DBAL\Exception
     */
    public function isMySqlServerCompatible(): array
    {
        if ($this->checkConnection()) {
            $connection = $this->getConnection();
            $result = $connection->executeQuery("SELECT VERSION()")->fetchAssociative();
            $mysqlServerVersion = $result['VERSION()'];
            if (version_compare($mysqlServerVersion, "5.1.6") >= 0) {
                return [
                    'message' => Messages::MYSQL_SERVER_OK_MESSAGE . " ($mysqlServerVersion)",
                    'status' => self::PASSED
                ];
            } else {
                return [
                    'message' => Messages::MYSQL_SERVER_RECOMMEND_MESSAGE . " (reported ver " . $mysqlServerVersion . ")",
                    'status' => self::ACCEPTABLE
                ];
            }
        } else {
            $this->interruptContinue = true;
            return [
                'message' => Messages::MYSQL_SERVER_FAIL_MESSAGE,
                'status' => self::BLOCKER
            ];
        }
    }

    /**
     * MYSQL InnoDB Support Check
     * @return array|void
     * @throws \Doctrine\DBAL\Exception
     */
    public function isInnoDBSupport()
    {
        if ($this->checkConnection()) {
            $connection = $this->getConnection();
            $mysqlServer = $connection->executeQuery("SHOW ENGINES");
            $engines = $mysqlServer->fetchAllAssociative();
            $innoDBEngine = array_values(
                array_filter($engines, function ($engine) {
                    return $engine['Engine'] === self::ENGINE_INNODB;
                })
            );

            $innoDBEngine = $innoDBEngine[0];

            if (!empty($innoDBEngine)) {
                if ($innoDBEngine['Support'] === self::STATE_DISABLED) {
                    $this->interruptContinue = true;
                    return [
                        'message' => "MySQL InnoDB Support - Disabled!",
                        'status' => self::BLOCKER
                    ];
                } elseif ($innoDBEngine['Support'] === self::STATE_DEFAULT) {
                    return [
                        'message' => "MySQL InnoDB Support - Default",
                        'status' => self::PASSED
                    ];
                } elseif ($innoDBEngine['Support'] === self::STATE_YES) {
                    return [
                        'message' => "MySQL InnoDB Support - Enabled",
                        'status' => self::PASSED
                    ];
                } elseif ($innoDBEngine['Support'] === self::STATE_NO) {
                    $this->interruptContinue = true;
                    return [
                        'message' => "MySQL InnoDB Support - available!",
                        'status' => self::BLOCKER
                    ];
                } else {
                    $this->interruptContinue = true;
                    return [
                        'message' => "MySQL InnoDB Support - Unknown Error!",
                        'status' => self::BLOCKER
                    ];
                }
            }
        } else {
            $this->interruptContinue = true;
            return [
                'message' => "MySQL InnoDB Support - Cannot connect to the database",
                'status' => self::BLOCKER
            ];
        }
    }

    /**
     * Web Server Check
     * @return array
     */
    public function isWebServerCompatible(): array
    {
        $supportedWebServers = ['Apache', 'nginx', 'IIS'];
        $currentWebServer = $_SERVER['SERVER_SOFTWARE'];
        foreach ($supportedWebServers as $supportedWebServer) {
            if (strpos($currentWebServer, $supportedWebServer) !== false) {
                return [
                    'message' => Messages::WEB_SERVER_OK_MESSAGE . "(ver ${currentWebServer})",
                    'status' => self::PASSED
                ];
            }
        }
        $this->interruptContinue = true;
        return [
            'message' => "FAILED",
            'status' => self::BLOCKER
        ];
    }

    /**
     * Category => Permissions Check
     */

    /**
     * Write Permissions for “lib/confs” Check
     * @return array
     */
    public function isWritableLibConfs(): array
    {
        if ($this->checkWritePermission(realpath(__DIR__ . '/../../lib/confs'))) {
            return [
                'message' => Messages::WritableLibConfs_OK_MESSAGE,
                'status' => self::PASSED
            ];
        } else {
            $this->interruptContinue = true;
            return [
                'message' => Messages::WritableLibConfs_FAIL_MESSAGE,
                'status' => self::BLOCKER
            ];
        }
    }

    /**
     * Write Permissions for “lib/logs” Check
     * @return array
     */
    public function isWritableLibLogs(): array
    {
        if ($this->checkWritePermission(realpath(__DIR__ . '/../../lib/logs'))) {
            return [
                'message' => Messages::WritableLibConfs_OK_MESSAGE,
                'status' => self::PASSED
            ];
        } else {
            $this->interruptContinue = true;
            return [
                'message' => Messages::WritableLibConfs_FAIL_MESSAGE,
                'status' => self::BLOCKER
            ];
        }
    }

    /**
     * Write Permissions for “symfony/configs” Check
     * @return array
     */
    public function isWritableSymfonyConfig(): array
    {
        if ($this->checkWritePermission(realpath(__DIR__ . '/../../symfony/config'))) {
            return [
                'message' => Messages::WritableSymfonyConfig_OK_MESSAGE,
                'status' => self::PASSED
            ];
        } else {
            $this->interruptContinue = true;
            return [
                'message' => Messages::WritableSymfonyConfig_FAIL_MESSAGE,
                'status' => self::BLOCKER
            ];
        }
    }

    /**
     * Write Permissions for “symfony/cache” Check
     * @return array
     */
    public function isWritableSymfonyCache(): array
    {
        if ($this->checkWritePermission(realpath(__DIR__ . '/../../symfony/cache'))) {
            return [
                'message' => Messages::WritableSymfonyCache_OK_MESSAGE,
                'status' => self::PASSED
            ];
        } else {
            $this->interruptContinue = true;
            return [
                'message' => Messages::WritableSymfonyCache_FAIL_MESSAGE,
                'status' => self::BLOCKER
            ];
        }
    }

    /**
     * Write Permissions for “symfony/log” Check
     * @return array
     */
    public function isWritableSymfonyLog(): array
    {
        if ($this->checkWritePermission(realpath(__DIR__ . '/../../symfony/log'))) {
            return [
                'message' => Messages::WritableSymfonyLog_OK_MESSAGE,
                'status' => self::PASSED
            ];
        } else {
            $this->interruptContinue = true;
            return [
                'message' => Messages::WritableSymfonyLog_FAIL_MESSAGE,
                'status' => self::BLOCKER
            ];
        }
    }

    /**
     * Category => Extensions
     */
    /**
     * Maximum Session idle time before timeout Check
     * @return array
     */
    public function isMaximumSessionIdle(): array
    {
        $gcMaxLifeTimeMinutes = floor(ini_get("session.gc_maxlifetime") / 60);
        $gcMaxLifeTimeSeconds = ini_get(" session.gc_maxlifetime") % 60;
        $timeSpan = "($gcMaxLifeTimeMinutes minutes and $gcMaxLifeTimeSeconds seconds)";
        if ($gcMaxLifeTimeMinutes > 15) {
            return [
                'message' => Messages::MaximumSessionIdle_OK_MESSAGE . $timeSpan,
                'status' => self::PASSED
            ];
        } elseif ($gcMaxLifeTimeMinutes > 2) {
            return [
                'message' => Messages::MaximumSessionIdle_SHORT_MESSAGE . $timeSpan,
                'status' => self::ACCEPTABLE
            ];
        } else {
            $this->interruptContinue = true;
            return [
                'message' => Messages::MaximumSessionIdle_TOO_SHORT_MESSAGE . $timeSpan,
                'status' => self::BLOCKER
            ];
        }
    }

    /**
     * Register Global turned-off Check
     * @return array
     */
    public function isRegisterGlobalsOff(): array
    {
        $registerGlobalsValue = (bool)ini_get("register_globals");
        if ($registerGlobalsValue) {
            $this->interruptContinue = true;
            return [
                'message' => Messages::RegisterGlobalsOff_FAIL_MESSAGE,
                'status' => self::BLOCKER
            ];
        } else {
            return [
                'message' => Messages::RegisterGlobalsOff_OK_MESSAGE,
                'status' => self::PASSED
            ];
        }
    }

    /**
     * Memory Allocated for php script Check
     * @return array
     */
    public function getAllocatedMemoryStatus(): array
    {
        $hardLimit = 9;
        $softLimit = 16;
        $maxMemory = null;
        $message = '';
        $status = self::PASSED;

        $result = $this->checkPhpMemory($hardLimit, $softLimit, $maxMemory);
        switch ($result) {
            case self::INSTALL_UTIL_MEMORY_NO_LIMIT:
                $message = "OK (No Limit)";
                break;

            case self::INSTALL_UTIL_MEMORY_UNLIMITED:
                $message = "OK (Unlimited)";
                break;

            case self::INSTALL_UTIL_MEMORY_HARD_LIMIT_FAIL:
                $this->interruptContinue = true;
                $message = "Warning at least ${hardLimit}M required (${maxMemory} available, Recommended ${softLimit}M)";
                $status = self::BLOCKER;
                break;

            case self::INSTALL_UTIL_MEMORY_SOFT_LIMIT_FAIL:
                $message = "OK (Recommended ${softLimit}M)";
                break;

            case self::INSTALL_UTIL_MEMORY_OK:
                $message = "OK";
                break;
        }
        return [
            'message' => "Memory allocated for PHP script - ${message}",
            'status' => $status
        ];
    }

    /**
     * @return array
     */
    public function IsGgExtensionEnable(): array
    {
        if (extension_loaded('gd') && function_exists('gd_info')) {
            return [
                'message' => Messages::GgExtensionEnable_OK_MESSAGE,
                'status' => self::PASSED
            ];
        } else {
            $this->interruptContinue = true;
            return [
                'message' => Messages::GgExtensionEnable_FAIL_MESSAGE,
                'status' => self::PASSED
            ];
        }
    }

    /**
     * MySQL Event Scheduler Status Check
     * This is not useful in opensource 5X
     * @return array
     * @throws \Doctrine\DBAL\Exception
     */
    public function mySqlEventSchedulerStatus(): array
    {
        if ($this->checkConnection()) {
            $connection = $this->getConnection();
            $result = $connection->executeQuery("SHOW VARIABLES LIKE 'EVENT_SCHEDULER'");
            $eventScheduler = $result->fetchAssociative();
            if ($eventScheduler['Value'] === self::STATE_ON) {
                return [
                    'message' => Messages::MySQLEventStatus_OK_MESSAGE,
                    'status' => self::PASSED
                ];
            } else {
                return [
                    'message' => Messages::MySQLEventStatus_DISABLE_MESSAGE,
                    'status' => self::ACCEPTABLE
                ];
            }
        } else {
            $this->interruptContinue = true;
            return [
                'message' => Messages::MySQLEventStatus_FAIL_MESSAGE,
                'status' => self::BLOCKER
            ];
        }
    }

    /**
     * cURL Status Check
     * @return array
     */
    public function isCurlEnabled(): array
    {
        if (extension_loaded('curl')) {
            return [
                'message' => Messages::CURLStatus_OK_MESSAGE,
                'status' => self::PASSED
            ];
        } else {
            $this->interruptContinue = true;
            return [
                'message' => Messages::CURLStatus_DISABLE_MESSAGE,
                'status' => self::BLOCKER
            ];
        }
    }

    /**
     * SimpleXML status Check
     * @return array
     */
    public function isSimpleXMLEnabled(): array
    {
        if (extension_loaded('SimpleXML') && extension_loaded('libxml') && extension_loaded('xml')) {
            return [
                'message' => Messages::SimpleXMLStatus_OK_MESSAGE,
                'status' => self::PASSED
            ];
        } else {
            $this->interruptContinue = true;
            return [
                'message' => Messages::SimpleXMLStatus_DISABLE_MESSAGE,
                'status' => self::BLOCKER
            ];
        }
    }

    /**
     * Zip extension status Check
     * @return array
     */
    public function isZipExtensionEnabled(): array
    {
        if (extension_loaded('zip')) {
            return [
                'message' => Messages::ZIP_Status_OK_MESSAGE,
                'status' => self::PASSED
            ];
        } else {
            $this->interruptContinue = true;
            return [
                'message' => Messages::ZIP_Status_DISABLE_MESSAGE,
                'status' => self::BLOCKER
            ];
        }
    }

    /**
     * @return bool
     */
    private function checkConnection(): bool
    {
        try {
            $connection = $this->getConnection();
            if ($connection->isConnected()) {
                $connection->connect();
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param string $path
     * @return bool
     */
    private function checkWritePermission(string $path): bool
    {
        try {
            $this->filesystem->dumpFile($path . '/_temp.txt', $path);
            $this->filesystem->remove($path . '/_temp.txt');
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param int $hardLimit
     * @param int $softLimit
     * @param string|null $maxMemory
     * @return int
     */
    private function checkPhpMemory(int $hardLimit, int $softLimit, string &$maxMemory = null): int
    {
        $maxMemory = is_null($maxMemory) ? ini_get('memory_limit') : $maxMemory;
        if (empty($maxMemory)) {
            $memory = self::INSTALL_UTIL_MEMORY_NO_LIMIT;
        } elseif ($maxMemory === '-1') {
            $memory = self::INSTALL_UTIL_MEMORY_UNLIMITED;
        } else {
            $maxMemoryBytes = $this->getSizeInBytes($maxMemory);
            $maxMemoryMB = $maxMemoryBytes / (1024 * 1024);

            if ($maxMemoryMB < $hardLimit) {
                $memory = self::INSTALL_UTIL_MEMORY_HARD_LIMIT_FAIL;
            } elseif ($maxMemoryMB < $softLimit) {
                $memory = self::INSTALL_UTIL_MEMORY_SOFT_LIMIT_FAIL;
            } else {
                $memory = self::INSTALL_UTIL_MEMORY_OK;
            }
        }
        return $memory;
    }

    /**
     * @param string $size
     * @return int
     */
    private function getSizeInBytes(string $size): int
    {
        $suffix = strtoupper(substr($size, -1));
        $value = (int)$size;

        if ($suffix === 'G') {
            $value = $value * pow(1024, 3);
        } elseif ($suffix === 'M') {
            $value = $value * pow(1024, 2);
        } else {
            $value = $value * 1024;
        }
        return $value;
    }
}
