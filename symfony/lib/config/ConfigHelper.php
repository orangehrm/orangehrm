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

class ConfigHelper
{
    /**
     * @var array
     */
    protected array $configs = [];

    /**
     * @return array
     */
    private function getPathConfigs(): array
    {
        $pathToProjectBase = realpath(__DIR__ . '/../../../');
        $pathToSymfonyDir = realpath($pathToProjectBase . '/symfony/');
        return [
            Config::BASE_DIR => $pathToProjectBase,
            'ohrm_symfony_dir' => $pathToSymfonyDir,
            'ohrm_apps_dir' => realpath($pathToSymfonyDir . '/apps'),
            'ohrm_lib_dir' => realpath($pathToSymfonyDir . '/lib'),
            'ohrm_log_dir' => realpath($pathToSymfonyDir . '/log'),
            'ohrm_data_dir' => realpath($pathToSymfonyDir . '/data'),
            'ohrm_config_dir' => realpath($pathToSymfonyDir . '/config'),
            Config::PLUGINS_DIR => realpath($pathToSymfonyDir . '/plugins'),
            Config::PUBLIC_DIR => realpath($pathToProjectBase . '/web'),
            Config::CACHE_DIR => realpath($pathToSymfonyDir . '/cache'),
            'ohrm_app_dir' => realpath($pathToSymfonyDir . '/apps/orangehrm'),
            'ohrm_app_config_dir' => realpath($pathToSymfonyDir . '/apps/orangehrm/config'),
            'ohrm_app_lib_dir' => realpath($pathToSymfonyDir . '/apps/orangehrm/lib'),
            'ohrm_app_i18n_dir' => realpath($pathToSymfonyDir . '/apps/orangehrm/i18n'),
            Config::DOCTRINE_PROXY_DIR => realpath($pathToSymfonyDir . '/config/proxy'),
            Config::TEST_DIR => realpath($pathToSymfonyDir . '/test'),

            'ohrm_client_dir' => realpath($pathToSymfonyDir . '/client'),
            'ohrm_app_template_dir' => realpath($pathToSymfonyDir . '/apps/orangehrm/templates'),
            'ohrm_vue_build_dir' => realpath($pathToProjectBase . '/web/dist'),
        ];
    }

    /**
     * @return string[]
     */
    private function getModuleConfigs(): array
    {
        return [
            'sf_login_module' => 'auth',
            'sf_login_action' => 'login',
            'sf_secure_module' => 'default',
            'sf_secure_action' => 'secure',
            'sf_module_disabled_module' => 'default',
            'sf_module_disabled_action' => 'disabled',
            'sf_error_404_module' => 'default',
            'sf_error_404_action' => 'error404',
        ];
    }

    /**
     * This should call after call `getPathConfigs` and configure path configs
     * @return array[]
     */
    private function getPluginConfigs(): array
    {
        $pluginsDir = $this->get('ohrm_plugins_dir');

        $pluginAbsPaths = [];
        $plugins = [];
        $pluginConfigs = [];
        $dirs = scandir($pluginsDir);
        $dirSuffix = 'Plugin';
        foreach ($dirs as $dirName) {
            if (stripos($dirName, $dirSuffix) !== false) {
                $pluginDir = realpath($pluginsDir . DIRECTORY_SEPARATOR . $dirName);
                $pluginAbsPaths[] = $pluginDir;
                $plugins[] = $dirName;
                if ($pluginDir) {
                    $pluginConfig = $this->getPluginConfiguration($pluginDir);
                    if (!is_null($pluginConfig)) {
                        $pluginConfigs[$dirName] = $pluginConfig;
                    }
                }
            }
        }

        return [
            'ohrm_plugins' => $plugins,
            'ohrm_plugin_paths' => $pluginAbsPaths,
            'ohrm_plugin_configs' => $pluginConfigs,
        ];
    }

    /**
     * @param string $pluginDir
     * @return array|null
     */
    private function getPluginConfiguration(string $pluginDir): ?array
    {
        $pluginConfigDir = realpath($pluginDir . DIRECTORY_SEPARATOR . 'config');
        if ($pluginConfigDir) {
            $files = scandir($pluginConfigDir);
            $fileSuffix = 'PluginConfiguration.php';
            foreach ($files as $file) {
                if (stripos($file, $fileSuffix) !== false) {
                    $configFile = realpath($pluginConfigDir . DIRECTORY_SEPARATOR . $file);
                    return [
                        'filename' => $file,
                        'classname' => str_replace('.php', '', $file),
                        'filepath' => $configFile,
                        'dir' => $pluginConfigDir,
                    ];
                }
            }
        }
        return null;
    }

    public function getClientConfigs(): array
    {
        $pathToBuildTimestampFile = realpath($this->get('ohrm_vue_build_dir') . '/build');
        return [
            'ohrm_vue_build_timestamp' => $pathToBuildTimestampFile ? file_get_contents($pathToBuildTimestampFile) : '',
        ];
    }

    /**
     * @return array
     */
    public function getConfigs(): array
    {
        $this->add($this->getPathConfigs());
        $this->add($this->getModuleConfigs());
        $this->add($this->getPluginConfigs());
        $this->add($this->getClientConfigs());
        return $this->getAll();
    }

    /**
     * @param string $name
     * @param null $default
     * @return mixed|null
     */
    protected function get(string $name, $default = null)
    {
        return isset($this->configs[$name]) ? $this->configs[$name] : $default;
    }

    /**
     * @return array
     */
    protected function getAll(): array
    {
        return $this->configs;
    }

    /**
     * @param array $parameters
     */
    protected function add(array $parameters = [])
    {
        $this->configs = array_merge($this->configs, $parameters);
    }
}
