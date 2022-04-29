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
    public const PLUGINS = 'ohrm_plugins';
    public const BASE_DIR = 'ohrm_base_dir';
    public const SRC_DIR = 'ohrm_src_dir';
    public const PLUGINS_DIR = 'ohrm_plugins_dir';
    public const PUBLIC_DIR = 'ohrm_public_dir';
    public const LOG_DIR = 'ohrm_log_dir';
    public const CACHE_DIR = 'ohrm_cache_dir';
    public const DOCTRINE_PROXY_DIR = 'ohrm_doctrine_proxy_dir';
    public const APP_TEMPLATE_DIR = 'ohrm_app_template_dir';
    public const TEST_DIR = 'ohrm_test_dir';
    public const CONF_FILE_PATH = 'ohrm_conf_file_path';
    public const I18N_ENABLED = 'ohrm_i18n_enabled';

    public const MODE_DEV = 'dev';
    public const MODE_PROD = 'prod';
    public const MODE_TEST = 'test';

    public const PRODUCT_NAME = 'OrangeHRM OS';
    public const PRODUCT_VERSION = '5.0';
    public const PRODUCT_MODE = self::MODE_PROD;
    public const REGISTRATION_URL = 'https://ospenguin.orangehrm.com';

    public const MAX_SESSION_IDLE_TIME = 1800;

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

    /**
     * @return bool
     */
    public static function isInstalled(): bool
    {
        return realpath(self::get(self::CONF_FILE_PATH)) !== false;
    }
}
