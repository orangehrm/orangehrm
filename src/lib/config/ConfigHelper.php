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
        $pathToSrcDir = realpath($pathToProjectBase . '/src/');
        return [
            Config::BASE_DIR => $pathToProjectBase,
            Config::SRC_DIR => $pathToSrcDir,
            'ohrm_lib_dir' => realpath($pathToSrcDir . '/lib'),
            'ohrm_log_dir' => realpath($pathToSrcDir . '/log'),
            'ohrm_data_dir' => realpath($pathToSrcDir . '/data'),
            'ohrm_config_dir' => realpath($pathToSrcDir . '/config'),
            Config::PLUGINS_DIR => realpath($pathToSrcDir . '/plugins'),
            Config::PUBLIC_DIR => realpath($pathToProjectBase . '/web'),
            Config::CACHE_DIR => realpath($pathToSrcDir . '/cache'),
            Config::DOCTRINE_PROXY_DIR => realpath($pathToSrcDir . '/config/proxy'),
            Config::TEST_DIR => realpath($pathToSrcDir . '/test'),
            Config::CONF_FILE_PATH => realpath($pathToProjectBase . '/lib/confs') . DIRECTORY_SEPARATOR . 'Conf.php',
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
            Config::PLUGINS => $plugins,
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

    /**
     * @return array
     */
    private function getClientConfigs(): array
    {
        $pathToProjectBase = $this->get(Config::BASE_DIR);
        $pathToSrcDir = $this->get(Config::SRC_DIR);
        $pathToVueBuildDir = realpath($pathToProjectBase . '/web/dist');
        $pathToBuildTimestampFile = realpath($pathToVueBuildDir . '/build');
        return [
            'ohrm_client_dir' => realpath($pathToSrcDir . '/client'),
            'ohrm_app_template_dir' => realpath($pathToSrcDir . '/plugins/orangehrmCorePlugin/templates'),
            'ohrm_vue_build_dir' => $pathToVueBuildDir,
            'ohrm_vue_build_timestamp' => $pathToBuildTimestampFile ? file_get_contents($pathToBuildTimestampFile) : '',
        ];
    }

    /**
     * @return array
     */
    private function getGlobalConfigs(): array
    {
        return [
            Config::I18N_ENABLED => true,
        ];
    }

    /**
     * @return array
     */
    public function getConfigs(): array
    {
        $this->add($this->getPathConfigs());
        $this->add($this->getPluginConfigs());
        $this->add($this->getClientConfigs());
        $this->add($this->getGlobalConfigs());
        return $this->getAll();
    }

    /**
     * @param string $name
     * @param null $default
     * @return mixed|null
     */
    protected function get(string $name, $default = null)
    {
        return $this->configs[$name] ?? $default;
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
    protected function add(array $parameters = []): void
    {
        $this->configs = array_merge($this->configs, $parameters);
    }
}
