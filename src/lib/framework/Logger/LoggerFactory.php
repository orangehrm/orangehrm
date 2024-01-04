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

namespace OrangeHRM\Framework\Logger;

use Monolog\Handler\StreamHandler;
use OrangeHRM\Config\Config;
use OrangeHRM\Framework\Framework;
use OrangeHRM\Framework\ServiceContainer;
use OrangeHRM\Framework\Services;

class LoggerFactory
{
    /**
     * @var array<string, Logger>
     */
    private static array $loggers = [];

    /**
     * @param string $name
     * @param string|null $path
     * @return Logger
     */
    public static function getLogger(string $name, ?string $path = null): Logger
    {
        if (!isset(self::$loggers[$name])) {
            $logger = new Logger($name);
            /** @var Framework $kernel */
            $kernel = ServiceContainer::getContainer()->get(Services::HTTP_KERNEL);
            self::$loggers[$name] = $logger->pushHandler(
                new StreamHandler(
                    Config::get(Config::LOG_DIR) . DIRECTORY_SEPARATOR . ($path ?? "$name.log"),
                    $kernel->isDebug() ? Logger::DEBUG : Logger::WARNING
                )
            );
        }
        return self::$loggers[$name];
    }
}
