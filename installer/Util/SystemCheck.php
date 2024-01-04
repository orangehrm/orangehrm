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
use OrangeHRM\Config\Config;
use OrangeHRM\Framework\Filesystem\Filesystem;
use PDO;
use Throwable;

class SystemCheck
{
    public const PASSED = 1;
    public const ACCEPTABLE = 2;
    public const BLOCKER = 3;

    public const ENGINE_INNODB = 'InnoDB';
    public const MARIADB = 'MariaDB';
    public const MYSQL = 'MySql';

    public const STATE_DISABLED = 'DISABLED';
    public const STATE_DEFAULT = 'DEFAULT';
    public const STATE_YES = 'YES';
    public const STATE_NO = 'NO';

    public const INSTALL_UTIL_MEMORY_NO_LIMIT = 0;
    public const INSTALL_UTIL_MEMORY_UNLIMITED = 1;
    public const INSTALL_UTIL_MEMORY_HARD_LIMIT_FAIL = 2;
    public const INSTALL_UTIL_MEMORY_SOFT_LIMIT_FAIL = 3;
    public const INSTALL_UTIL_MEMORY_OK = 4;

    private bool $interruptContinue = false;
    private Filesystem $filesystem;
    private array $systemRequirements = [];
    private ?Connection $connection;

    public function __construct(?Connection $connection = null)
    {
        $this->filesystem = new Filesystem();
        $this->systemRequirements = require realpath(__DIR__ . '/../config/system_requirements.php');
        $this->connection = $connection;
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
        $currentPHPVersion = phpversion();
        $allowedPHPConfigs = $this->systemRequirements['phpversion'];
        if ($this->isWithinRange(
            $currentPHPVersion,
            $allowedPHPConfigs['excludeRange'],
            $allowedPHPConfigs['min'],
            $allowedPHPConfigs['max']
        )) {
            return [
                'message' => Messages::STATUS_OK . ' (ver ' . $currentPHPVersion . ')',
                'status' => self::PASSED
            ];
        } else {
            $this->interruptContinue = true;
            $message = $this->getErrorMessage(
                'PHP',
                phpversion(),
                $allowedPHPConfigs['excludeRange'],
                $allowedPHPConfigs['min'],
                $allowedPHPConfigs['max']
            );

            return [
                'message' => $message,
                'status' => self::BLOCKER
            ];
        }
    }

    /**
     * MYSQL Client Check
     * @return array
     */
    public function isMySqlClientCompatible(): array
    {
        $mysqlClientVersion = $this->getMysqlClientVersion();
        if (!empty($mysqlClientVersion)) {
            $versionPattern = '/[0-9]+\.[0-9]+\.[0-9]+/';
            preg_match($versionPattern, $mysqlClientVersion, $matches);
            $mysqlClientVersion = $matches[0];
            if (version_compare($mysqlClientVersion, $this->systemRequirements['mysqlversion']['min']) < 0) {
                return [
                    'message' => Messages::MYSQL_CLIENT_RECOMMEND_MESSAGE . '(reported ver ' . $mysqlClientVersion . ')',
                    'status' => self::ACCEPTABLE
                ];
            } else {
                return [
                    'message' => Messages::STATUS_OK,
                    'status' => self::PASSED
                ];
            }
        } else {
            $this->interruptContinue = true;
            return [
                'message' => Messages::MYSQL_CLIENT_FAIL_MESSAGE,
                'status' => self::BLOCKER
            ];
        }
    }

    /**
     * MYSQL Server Check
     * @return array
     */
    public function isMySqlServerCompatible(): array
    {
        if ($this->getPDOConnection()) {
            $serverVersion = $this->getMysqlServerVersion();
            strpos($serverVersion, 'MariaDB') === false
                ? $allowedConfigs = $this->systemRequirements['mysqlversion'] :
                $allowedConfigs = $this->systemRequirements['mariadbversion'];
            if ($this->isWithinRange(
                $serverVersion,
                $allowedConfigs['excludeRange'],
                $allowedConfigs['min'],
                $allowedConfigs['max']
            )) {
                return [
                    'message' => Messages::STATUS_OK . " ($serverVersion)",
                    'status' => self::PASSED
                ];
            } else {
                $message = $this->getErrorMessage(
                    strpos($serverVersion, 'MariaDB') === false ? self::MYSQL : self::MARIADB,
                    $serverVersion,
                    $allowedConfigs['excludeRange'],
                    $allowedConfigs['min'],
                    $allowedConfigs['max']
                );
                $this->interruptContinue = true;
                return [
                    'message' => $message,
                    'status' => self::BLOCKER
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
     */
    public function isInnoDBSupport()
    {
        if ($this->getPDOConnection()) {
            $connection = $this->getPDOConnection();
            $engines = $connection->query('SHOW ENGINES')->fetchAll(PDO::FETCH_ASSOC);
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
                        'message' => Messages::DISABLED,
                        'status' => self::BLOCKER
                    ];
                } elseif ($innoDBEngine['Support'] === self::STATE_DEFAULT) {
                    return [
                        'message' => Messages::DEFAULT,
                        'status' => self::PASSED
                    ];
                } elseif ($innoDBEngine['Support'] === self::STATE_YES) {
                    return [
                        'message' => Messages::ENABLED,
                        'status' => self::PASSED
                    ];
                } elseif ($innoDBEngine['Support'] === self::STATE_NO) {
                    $this->interruptContinue = true;
                    return [
                        'message' => Messages::AVAILABLE,
                        'status' => self::BLOCKER
                    ];
                } else {
                    $this->interruptContinue = true;
                    return [
                        'message' => 'Unknown Error!',
                        'status' => self::BLOCKER
                    ];
                }
            }
        } else {
            $this->interruptContinue = true;
            return [
                'message' => 'Cannot connect to the database',
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
        $supportedWebServers = $this->systemRequirements['webserver'];
        $currentWebServer = $this->getWebServerDetails();
        if ($currentWebServer !== null) {
            foreach ($supportedWebServers as $supportedWebServer) {
                if (strpos($currentWebServer, $supportedWebServer) !== false) {
                    return [
                        'message' => Messages::STATUS_OK . "(ver $currentWebServer)",
                        'status' => self::PASSED
                    ];
                }
            }
        }
        return [
            'message' => 'FAILED',
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
    public function isWritableConfigDir(): array
    {
        if ($this->checkWritePermission(Config::get(Config::CONFIG_DIR))) {
            return [
                'message' => Messages::WRITEABLE,
                'status' => self::PASSED
            ];
        } else {
            $this->interruptContinue = true;
            return [
                'message' => Messages::NOT_WRITEABLE,
                'status' => self::BLOCKER
            ];
        }
    }

    /**
     * Write Permissions for “src/cache” Check
     * @return array
     */
    public function isWritableCacheDir(): array
    {
        if ($this->checkWritePermission(Config::get(Config::CACHE_DIR))) {
            return [
                'message' => Messages::WRITEABLE,
                'status' => self::PASSED
            ];
        } else {
            $this->interruptContinue = true;
            return [
                'message' => Messages::NOT_WRITEABLE,
                'status' => self::BLOCKER
            ];
        }
    }

    /**
     * Write Permissions for “src/log” Check
     * @return array
     */
    public function isWritableLogDir(): array
    {
        if ($this->checkWritePermission(Config::get(Config::LOG_DIR))) {
            return [
                'message' => Messages::WRITEABLE,
                'status' => self::PASSED
            ];
        } else {
            $this->interruptContinue = true;
            return [
                'message' => Messages::NOT_WRITEABLE,
                'status' => self::BLOCKER
            ];
        }
    }

    /**
     * Write Permissions for “lib/confs/cryptokeys” Check
     * @return array
     */
    public function isWritableCryptoKeyDir(): array
    {
        if ($this->checkWritePermission(Config::get(Config::CRYPTO_KEY_DIR))) {
            return [
                'message' => Messages::WRITEABLE,
                'status' => self::PASSED
            ];
        } else {
            $this->interruptContinue = true;
            return [
                'message' => Messages::NOT_WRITEABLE,
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
        $gcMaxLifeTimeMinutes = floor(ini_get('session.gc_maxlifetime') / 60);
        $gcMaxLifeTimeSeconds = ini_get(' session.gc_maxlifetime') % 60;
        $timeSpan = "($gcMaxLifeTimeMinutes minutes and $gcMaxLifeTimeSeconds seconds)";
        if ($gcMaxLifeTimeMinutes > 15) {
            return [
                'message' => Messages::MAXIMUM_SESSION_IDLE_OK_MESSAGE . $timeSpan,
                'status' => self::PASSED
            ];
        } elseif ($gcMaxLifeTimeMinutes > 2) {
            return [
                'message' => Messages::MAXIMUM_SESSION_IDLE_SHORT_MESSAGE . $timeSpan,
                'status' => self::ACCEPTABLE
            ];
        } else {
            $this->interruptContinue = true;
            return [
                'message' => Messages::MAXIMUM_SESSION_IDLE_TOO_SHORT_MESSAGE . $timeSpan,
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
        $registerGlobalsValue = (bool)ini_get('register_globals');
        if ($registerGlobalsValue) {
            $this->interruptContinue = true;
            return [
                'message' => Messages::REGISTER_GLOBALS_OFF_FAIL_MESSAGE,
                'status' => self::BLOCKER
            ];
        } else {
            return [
                'message' => Messages::STATUS_OK,
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
                $message = 'OK (No Limit)';
                break;

            case self::INSTALL_UTIL_MEMORY_UNLIMITED:
                $message = 'OK (Unlimited)';
                break;

            case self::INSTALL_UTIL_MEMORY_HARD_LIMIT_FAIL:
                $this->interruptContinue = true;
                $message = "Warning at least {$hardLimit}M required ($maxMemory available, Recommended {$softLimit}M)";
                $status = self::BLOCKER;
                break;

            case self::INSTALL_UTIL_MEMORY_SOFT_LIMIT_FAIL:
                $message = "OK (Recommended {$softLimit}M)";
                break;

            case self::INSTALL_UTIL_MEMORY_OK:
                $message = 'OK';
                break;
        }
        return [
            'message' => $message,
            'status' => $status
        ];
    }

    /**
     * cURL Status Check
     * @return array
     */
    public function isCurlEnabled(): array
    {
        if (extension_loaded('curl')) {
            return [
                'message' => Messages::ENABLED,
                'status' => self::PASSED
            ];
        } else {
            $this->interruptContinue = true;
            return [
                'message' => Messages::DISABLED,
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
                'message' => Messages::ENABLED,
                'status' => self::PASSED
            ];
        } else {
            $this->interruptContinue = true;
            return [
                'message' => Messages::DISABLED,
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
                'message' => Messages::ENABLED,
                'status' => self::PASSED
            ];
        } else {
            $this->interruptContinue = true;
            return [
                'message' => Messages::DISABLED,
                'status' => self::BLOCKER
            ];
        }
    }

    /**
     * @return array
     */
    public function isGdExtensionEnabled(): array
    {
        if (extension_loaded('gd')) {
            return [
                'message' => Messages::ENABLED,
                'status' => self::PASSED
            ];
        } else {
            return [
                'message' => Messages::DISABLED,
                'status' => self::ACCEPTABLE
            ];
        }
    }

    /**
     * @return array
     */
    public function isIntlExtensionEnabled(): array
    {
        if (extension_loaded('intl')) {
            return [
                'message' => Messages::ENABLED,
                'status' => self::PASSED
            ];
        } else {
            return [
                'message' => Messages::DISABLED,
                'status' => self::ACCEPTABLE
            ];
        }
    }

    /**
     * @return bool
     */
    public function checkPDOExtensionEnabled(): bool
    {
        if (extension_loaded('pdo')) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function checkPDOMySqlExtensionEnabled(): bool
    {
        if (extension_loaded('pdo_mysql')) {
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function isLDAPExtensionEnabled(): array
    {
        if (extension_loaded('ldap')) {
            return [
                'message' => Messages::ENABLED,
                'status' => self::PASSED
            ];
        } else {
            return [
                'message' => Messages::DISABLED,
                'status' => self::BLOCKER
            ];
        }
    }

    /**
     * @return array
     */
    public function isOpenSSLExtensionEnabled(): array
    {
        if (extension_loaded('openssl')) {
            return [
                'message' => Messages::ENABLED,
                'status' => self::PASSED
            ];
        } else {
            return [
                'message' => Messages::DISABLED,
                'status' => self::BLOCKER
            ];
        }
    }

    /**
     * @param string $path
     * @return bool
     */
    public function checkWritePermission(string $path): bool
    {
        try {
            $this->filesystem->dumpFile($path . DIRECTORY_SEPARATOR . '_temp.txt', $path);
            $this->filesystem->remove($path . DIRECTORY_SEPARATOR . '_temp.txt');
            return true;
        } catch (Throwable $e) {
            Logger::getLogger()->error($e->getMessage());
            Logger::getLogger()->error($e->getTraceAsString());
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

    /**
     * @param string $currentVersion
     * @param array $excludeRange
     * @param string $min
     * @param string $max
     * @return bool
     */
    private function isWithinRange(string $currentVersion, array $excludeRange, string $min, string $max): bool
    {
        $points = max(substr_count($max, '.'), substr_count($min, '.'));
        $pattern = '/^(\d+)';
        for ($i = 0; $i < $points; $i++) {
            $pattern = $pattern . '\.(\d+)';
        }
        $pattern = $pattern . '/';
        preg_match($pattern, $currentVersion, $matches);
        $trimmedValue = $matches[0];

        if (!(version_compare($trimmedValue, $min) >= 0 && version_compare($max, $trimmedValue) >= 0)) {
            return false;
        }
        if ($this->isExcluded($currentVersion, $excludeRange)) {
            return false;
        }
        return true;
    }

    /**
     * @param string $component
     * @param string $currentVersion
     * @param array $excludeRange
     * @param string $min
     * @param string $max
     * @return string
     */
    private function getErrorMessage(
        string $component,
        string $currentVersion,
        array $excludeRange,
        string $min,
        string $max
    ): string {
        $message = '';

        if ($this->isExcluded($currentVersion, $excludeRange)) {
            $message = $message . $component . ' Version ' . $currentVersion . ' is not supported';
        } else {
            $message = $component . ' Version should be higher than ' . $min . ' and lower than ' . $max;
        }

        return $message . '. Installed version is ' . $currentVersion;
    }

    /**
     * @param string $currentVersion
     * @param array $excludedRange
     * @return bool
     */
    private function isExcluded(string $currentVersion, array $excludedRange = []): bool
    {
        if (in_array($currentVersion, $excludedRange)) {
            return true;
        }
        return false;
    }

    /**
     * Return PHP version
     * @return string
     */
    public function getPhpVersion(): string
    {
        return phpversion();
    }

    /**
     * Return web server details
     * @return string|null
     */
    public function getWebServerDetails(): ?string
    {
        return $_SERVER['SERVER_SOFTWARE'] ?? null;
    }

    /**
     * Return MySql client version
     * @return string
     */
    public function getMysqlClientVersion(): string
    {
        return $this->getPDOConnection()->getAttribute(PDO::ATTR_CLIENT_VERSION) ?: '';
    }

    /**
     * Return MySql server version
     * @return string
     */
    public function getMysqlServerVersion(): string
    {
        return $this->getPDOConnection()->getAttribute(PDO::ATTR_SERVER_VERSION) ?: '';
    }

    /**
     * Return MySql host info
     * @return string
     */
    public function getMySqlHostInfo(): string
    {
        return $this->getPDOConnection()->getAttribute(PDO::ATTR_CONNECTION_STATUS) ?: '';
    }

    /**
     * Return running operating system details
     * @return array
     */
    public function getOSDetails(): array
    {
        return [
            'os' => php_uname('s'),
            'release_name' => php_uname('r'),
            'version_info' => php_uname('v'),
        ];
    }

    /**
     * @return PDO|null
     */
    private function getPDOConnection(): ?PDO
    {
        try {
            if ($this->connection instanceof Connection) {
                return $this->connection->getNativeConnection();
            }
            return DatabaseServerConnection::getConnection()->getNativeConnection();
        } catch (Throwable $e) {
        }
        return null;
    }

    /**
     * @return array
     */
    public function getSystemDetails(): array
    {
        return [
            'os' => $this->getOSDetails(),
            'php' => [
                'version' => $this->getPhpVersion()
            ],
            'mysql' => [
                'client_version' => $this->getMysqlClientVersion(),
                'server_version' => $this->getMysqlServerVersion(),
                'conn_type' => $this->getMySqlHostInfo()
            ],
            'server' => $this->getWebServerDetails(),
            'ohrm' => [
                'version' => Config::PRODUCT_VERSION
            ]
        ];
    }

    /**
     * @param bool $isCli
     * @return array[]
     */
    public function getSystemCheckResults(bool $isCli = false): array
    {
        return [
            [
                'category' => 'Environment',
                'checks' => [
                    [
                        'label' => 'PHP version',
                        'value' => $this->isPHPVersionCompatible()
                    ],
                    [
                        'label' => 'MYSQL Client',
                        'value' => $this->isMySqlClientCompatible()
                    ],
                    [
                        'label' => 'MYSQL Server',
                        'value' => $this->isMySqlServerCompatible()
                    ],
                    [
                        'label' => 'MYSQL InnoDB Support',
                        'value' => $this->isInnoDBSupport()
                    ],
                    [
                        'label' => 'Web Server',
                        'value' => $isCli
                            ? ['message' => 'N/A', 'status' => self::ACCEPTABLE]
                            : $this->isWebServerCompatible()
                    ]
                ]
            ],
            [
                'category' => 'Permissions',
                'checks' => [
                    [
                        'label' => 'Write Permissions for “lib/confs”',
                        'value' => $this->isWritableConfigDir()
                    ],
                    [
                        'label' => 'Write Permissions for “src/cache”',
                        'value' => $this->isWritableCacheDir()
                    ],
                    [
                        'label' => 'Write Permissions for “src/log”',
                        'value' => $this->isWritableLogDir()
                    ],
                ]
            ],
            [
                'category' => 'Extensions',
                'checks' => [
                    [
                        'label' => 'Maximum Session idle time before timeout',
                        'value' => $this->isMaximumSessionIdle()
                    ],
                    [
                        'label' => 'Register Global turned-off',
                        'value' => $this->isRegisterGlobalsOff()
                    ],
                    [
                        'label' => 'Memory Allocated for PHP script',
                        'value' => $this->getAllocatedMemoryStatus()
                    ],
                    [
                        'label' => 'cURL Status',
                        'value' => $this->isCurlEnabled()
                    ],
                    [
                        'label' => 'SimpleXML status',
                        'value' => $this->isSimpleXMLEnabled()
                    ],
                    [
                        'label' => 'Zip extension status',
                        'value' => $this->isZipExtensionEnabled()
                    ],
                    [
                        'label' => 'Intl extension status',
                        'value' => $this->isIntlExtensionEnabled()
                    ],
                    [
                        'label' => 'GD extension status',
                        'value' => $this->isGdExtensionEnabled()
                    ],
                    [
                        'label' => 'LDAP extension status',
                        'value' => $this->isLDAPExtensionEnabled()
                    ],
                    [
                        'label' => 'OpenSSL extension status',
                        'value' => $this->isOpenSSLExtensionEnabled()
                    ],
                ]
            ]
        ];
    }
}
