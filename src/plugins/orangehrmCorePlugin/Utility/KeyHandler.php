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

namespace OrangeHRM\Core\Utility;

use Exception;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Exception\KeyHandlerException;
use OrangeHRM\Framework\Filesystem\Filesystem;

class KeyHandler
{
    private static string $key;
    private static bool $keySet = false;

    /**
     * @throws KeyHandlerException
     */
    public static function createKey(): void
    {
        if (self::keyExists()) {
            throw KeyHandlerException::keyAlreadyExists();
        }

        try {
            $cryptKey = '';
            for ($i = 0; $i < 4; $i++) {
                $cryptKey .= md5(random_int(10000000, 99999999));
            }
            $cryptKey = str_shuffle($cryptKey);

            $fs = new Filesystem();
            $fs->dumpFile(self::getPathToKey(), $cryptKey);
            clearstatcache(true);
        } catch (Exception $e) {
            throw KeyHandlerException::failedToCreateKey();
        }
    }

    /**
     * @return string
     * @throws KeyHandlerException
     */
    public static function readKey(): string
    {
        if (!self::keyExists()) {
            throw KeyHandlerException::keyDoesNotExist();
        }

        if (!is_readable(self::getRealPathToKey())) {
            throw KeyHandlerException::keyIsNotReadable();
        }

        if (!self::$keySet) {
            self::$key = trim(file_get_contents(self::getRealPathToKey()));
            self::$keySet = true;
        }

        return self::$key;
    }

    /**
     * @return bool
     */
    public static function keyExists(): bool
    {
        return self::getRealPathToKey() !== null;
    }

    /**
     * @return string|null
     */
    public static function getRealPathToKey(): ?string
    {
        $path = realpath(self::getPathToKey());
        return $path === false ? null : $path;
    }

    /**
     * @return string
     */
    public static function getPathToKey(): string
    {
        return Config::get(Config::CRYPTO_KEY_DIR) . DIRECTORY_SEPARATOR . 'key.ohrm';
    }
}
