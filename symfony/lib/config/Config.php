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
    protected static $configs = [];
    protected static $initialized = false;

    private function __construct()
    {
    }

    private static function init()
    {
        if (!self::$initialized) {
            $pathToProjectBase = realpath(__DIR__ . '/../../../');
            $pathToSymfonyDir = realpath($pathToProjectBase . '/symfony/');
            self::add(
                [
                    'sf_root_dir' => $pathToSymfonyDir,
                    'sf_apps_dir' => realpath($pathToSymfonyDir . '/apps'),
                    'sf_lib_dir' => realpath($pathToSymfonyDir . '/lib'),
                    'sf_log_dir' => realpath($pathToSymfonyDir . '/log'),
                    'sf_data_dir' => realpath($pathToSymfonyDir . '/data'),
                    'sf_config_dir' => realpath($pathToSymfonyDir . '/config'),
                    'sf_plugins_dir' => realpath($pathToSymfonyDir . '/plugins'),
                    'sf_web_dir' => realpath($pathToSymfonyDir . '/web'),
                    'sf_cache_dir' => realpath($pathToSymfonyDir . '/cache'),
                    'sf_app_dir' => realpath($pathToSymfonyDir . '/apps/orangehrm'),
                    'sf_app_config_dir' => realpath($pathToSymfonyDir . '/apps/orangehrm/config'),
                    'sf_app_lib_dir' => realpath($pathToSymfonyDir . '/apps/orangehrm/lib'),
                    'sf_app_i18n_dir' => realpath($pathToSymfonyDir . '/apps/orangehrm/i18n'),

                    'sf_test_dir' => realpath($pathToProjectBase . '/tests'),

                    'client_dir' => realpath($pathToSymfonyDir . '/client'),
                    'app_template_dir' => realpath($pathToSymfonyDir . '/apps/orangehrm/templates'),
                ]
            );

            self::$initialized = true;
        }
    }

    public static function get(string $name, $default = null)
    {
        self::init();
        return isset(self::$configs[$name]) ? self::$configs[$name] : $default;
    }

    public static function has(string $name): bool
    {
        self::init();
        return array_key_exists($name, self::$configs);
    }

    public static function set(string $name, $value)
    {
        self::$configs[$name] = $value;
    }

    public static function add(array $parameters = [])
    {
        self::$configs = array_merge(self::$configs, $parameters);
    }

    public static function getAll(): array
    {
        self::init();
        return self::$configs;
    }

    public static function clear()
    {
        self::$configs = [];
    }
}
