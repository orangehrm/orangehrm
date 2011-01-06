<?php
/*
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

class orangehrmPublishAssetsTask extends sfBaseTask {
    protected function configure() {
        $this->namespace = 'orangehrm';
        $this->name = 'publish-assets';

        $this->briefDescription = 'Publishes web assets of OrangeHRM plugins (copy -> no symlinks)';

        $this->detailedDescription = <<<EOF
The [plugin:publish-assets|INFO] Task will publish web assets of OrangeHRM plugins.

  [./symfony orangehrm:publish-assets|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        //only enabled plugins are here
        $plugins    = $this->configuration->getAllPluginPaths();

        //check for enabled plugins
        foreach ($this->configuration->getPlugins() as $plugin)
        if (stripos($plugin, 'orangehrm') !== FALSE) {
            $pluginPath = $plugins[$plugin];
            $this->logSection('plugin', 'Configuring plugin - ' . $pluginPath);
            $this->copyWebAssets($plugin, $pluginPath);
        }
    }

    private function copyWebAssets($plugin, $dir) {
        $webDir = $dir.DIRECTORY_SEPARATOR.'web';
        $filesystem = new sfFilesystem();
        
        if (is_dir($webDir)) {
            $finder = sfFinder::type('any');
            $this->dirctoryRecusiveDelete(sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.$plugin);
            $filesystem->mirror($webDir, sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.$plugin, $finder);
        }
        return;
    }

    private function dirctoryRecusiveDelete($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                 if (filetype($dir."/".$object) == "dir") $this->dirctoryRecusiveDelete($dir."/".$object); else unlink($dir."/".$object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }
}
?>
