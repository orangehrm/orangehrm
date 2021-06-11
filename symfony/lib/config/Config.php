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

namespace OrangeHRM\Config;

class Config
{
    public const BASE_DIR = 'ohrm_base_dir';
    public const PLUGINS_DIR = 'ohrm_plugins_dir';
    public const PUBLIC_DIR = 'ohrm_public_dir';
    public const LOG_DIR = 'ohrm_log_dir';
    public const DOCTRINE_PROXY_DIR = 'ohrm_doctrine_proxy_dir';

    /**
     * @var array
     */
    protected static array $configs = [];
    /**
     * @var bool
     */
    protected static bool $initialized = false;

    private function __construct()
    {
    }

    private static function init(): void
    {
        if (!self::$initialized) {
            $configHelper = new ConfigHelper();
            self::add($configHelper->getConfigs());

            self::$initialized = true;
        }
    }

    /**
     * @param string $name
     * @param null $default
     * @return mixed|null
     */
    public static function get(string $name, $default = null)
    {
        self::init();
        return self::$configs[$name] ?? $default;
    }

    /**
     * @param string $name
     * @return bool
     */
    public static function has(string $name): bool
    {
        self::init();
        return array_key_exists($name, self::$configs);
    }

    /**
     * @param string $name
     * @param $value
     */
    public static function set(string $name, $value)
    {
        self::$configs[$name] = $value;
    }

    /**
     * @param array $parameters
     */
    public static function add(array $parameters = [])
    {
        self::$configs = array_merge(self::$configs, $parameters);
    }

    /**
     * @return array
     */
    public static function getAll(): array
    {
        self::init();
        return self::$configs;
    }

    public static function clear(): void
    {
        self::$configs = [];
    }
}
