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
        $pathToConfigDir = $pathToProjectBase . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'confs';
        return [
            Config::BASE_DIR => $pathToProjectBase,
            Config::SRC_DIR => $pathToSrcDir,
            Config::PLUGINS_DIR => realpath($pathToSrcDir . '/plugins'),
            Config::PUBLIC_DIR => realpath($pathToProjectBase . '/web'),
            Config::DOCTRINE_PROXY_DIR => realpath($pathToSrcDir . '/config/proxy'),
            Config::TEST_DIR => realpath($pathToSrcDir . '/test'),
            Config::LOG_DIR => $pathToSrcDir . DIRECTORY_SEPARATOR . 'log',
            Config::CACHE_DIR => $pathToSrcDir . DIRECTORY_SEPARATOR . 'cache',
            Config::CONFIG_DIR => $pathToConfigDir,
            Config::CRYPTO_KEY_DIR => $pathToConfigDir . DIRECTORY_SEPARATOR . 'cryptokeys',
            Config::SESSION_DIR => null,
            Config::CONF_FILE_PATH => $pathToConfigDir . DIRECTORY_SEPARATOR . 'Conf.php',
        ];
    }

    /**
     * This should call after call `getPathConfigs` and configure path configs
     * @return array[]
     */
    private function getPluginConfigs(): array
    {
        $pluginsDir = $this->get(Config::PLUGINS_DIR);

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
            Config::PLUGIN_PATHS => $pluginAbsPaths,
            Config::PLUGIN_CONFIGS => $pluginConfigs,
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
            Config::APP_TEMPLATE_DIR => realpath($pathToSrcDir . '/plugins/orangehrmCorePlugin/templates'),
            'ohrm_vue_build_dir' => $pathToVueBuildDir,
            Config::VUE_BUILD_TIMESTAMP => $pathToBuildTimestampFile
                ? file_get_contents($pathToBuildTimestampFile) : '',
        ];
    }

    /**
     * @return array
     */
    private function getGlobalConfigs(): array
    {
        return [
            Config::I18N_ENABLED => true,
            Config::DATE_FORMATTING_ENABLED => false,
            Config::MAX_SESSION_IDLE_TIME => Config::DEFAULT_MAX_SESSION_IDLE_TIME,
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
