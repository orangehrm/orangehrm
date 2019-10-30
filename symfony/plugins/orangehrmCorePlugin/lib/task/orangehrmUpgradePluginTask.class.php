<?php

class orangehrmUpgradePluginTask extends sfBaseTask
{
    protected function configure()
    {
        $this->namespace = 'orangehrm';
        $this->name = 'upgrade-plugin';
        $this->briefDescription = 'upgrades a given plugin to latest version.';
        $this->addArgument('plugin', sfCommandArgument::REQUIRED, 'plugin name');
        $this->addArgument('previousVersion', sfCommandArgument::REQUIRED, 'previous version of the plugin');
    }

    /**
     * @inheritDoc
     */
    protected function execute($arguments = array(), $options = array())
    {
        $pluginName = $arguments['plugin'];
        $pluginsDir = sfConfig::get('sf_plugins_dir');
        if (!is_dir("$pluginsDir/$pluginName") || !in_array($pluginName, $this->configuration->getPlugins())) {
            throw new InvalidArgumentException('plugin name is invalid.');
        }
        $filePath = "$pluginsDir/$pluginName/config/app.yml";
        $content = sfYaml::load($filePath);
        $currentVersion = $content['all'][$pluginName]['version'];
        if (version_compare($arguments['previousVersion'], $currentVersion) !== -1) {
            throw new InvalidArgumentException('nothing to upgrade.');
        }
        chdir(ROOT_PATH . '/upgrader');
        exec(sprintf('php symfony upgrader:upgrade-plugin %s %s', $pluginName, $arguments['previousVersion']), $out, $status);
        $this->log($out);
        if ($status !== 0) {
            throw new sfCommandException(sprintf('Error upgrading plugin: %s', $pluginName));
        }
        $filePath = "$pluginsDir/$pluginName/install/installer.yml";
        $content = sfYaml::load($filePath);
        if (isset($content['post_upgrade_commands'])) {
            chdir(sfConfig::get('sf_root_dir'));
            if (is_string($content['post_upgrade_commands'])) {
                $content['post_upgrade_commands'] = [$content['post_upgrade_commands']];
            }
            foreach ($content['post_upgrade_commands'] as $command) {
                $out = [];
                exec($command, $out, $status);
                $this->log($out);
                if ($status !== 0) {
                    throw new sfCommandException('Error running post upgrade command');
                }
            }
        }
    }
}