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
 * Boston, MA 02110-1301, USA
 */

class PublishAssetService
{
    const RESOURCE_DIR_PREFIX = 'webres_';
    const RESOURCE_DIR_INC_FILE = 'resource_dir_inc.php';

    protected $resourceDir = null;
    protected $fileSystem = null;

    public function publishAssets()
    {
        $this->logInfo("Start publishing assets.");
        $configuration = sfContext::getInstance()->getConfiguration();
        $plugins = $configuration->getAllPluginPaths();
        $symfonyWeb = sfConfig::get('sf_web_dir');

        // get old resource directory:
        $oldResourceDirs = sfFinder::type('directory')->name(self::RESOURCE_DIR_PREFIX . '*')
            ->maxdepth(0)->in($symfonyWeb);

        // create new assets directory
        $uniqueResourceDir = uniqid(self::RESOURCE_DIR_PREFIX, true);
        $this->resourceDir = $symfonyWeb . DIRECTORY_SEPARATOR . $uniqueResourceDir;
        $this->getFilesystem()->mkdirs($this->resourceDir);

        // check for enabled plugins
        foreach ($configuration->getPlugins() as $plugin) {
            if (stripos($plugin, 'orangehrm') !== FALSE) {
                $pluginPath = $plugins[$plugin];
                $this->logInfo('Publishing assets for plugin - ' . $pluginPath);
                $this->copyWebAssets($plugin, $pluginPath);
            }
        }

        // copy main resources
        $toCopy = array('images', 'js', 'themes');
        foreach ($toCopy as $dir) {
            $this->logInfo('Mirroring ' . $dir . ' to ' . $uniqueResourceDir . DIRECTORY_SEPARATOR . $dir);
            $this->mirrorDir($symfonyWeb . DIRECTORY_SEPARATOR . $dir,
                $this->resourceDir . DIRECTORY_SEPARATOR . $dir);
        }

        // update resource dir in php file
        $resourceIncFile = $symfonyWeb . DIRECTORY_SEPARATOR . self::RESOURCE_DIR_INC_FILE;
        $fp = @fopen($resourceIncFile, 'w');

        if ($fp === false) {
            $message = "Couldn't write resource inc file data. Failed to open $resourceIncFile";
            $this->getLogger()->error($message);
            throw new Exception($message);
        }

        $propertiesToSet = array(
            'sf_web_css_dir_name' => $uniqueResourceDir . '/css',
            'sf_web_js_dir_name' => $uniqueResourceDir . '/js',
            'sf_web_images_dir_name' => $uniqueResourceDir . '/images',
            'ohrm_resource_dir' => $uniqueResourceDir,
        );

        $content = "<?php \n";

        foreach ($propertiesToSet as $key => $value) {
            $content .= "sfConfig::set('$key','$value');\n";
            sfConfig::set($key, $value);
            $this->logInfo("sfConfig `$key` updated to `$value`.");
        }

        fwrite($fp, $content);
        fclose($fp);
        $this->logInfo("Assets published.");

        // delete old asset directories
        $this->logInfo("Start deleting old assets.");
        foreach ($oldResourceDirs as $oldResourceDir) {
            $this->directoryRecursiveDelete($oldResourceDir);
        }
        $this->logInfo("Finish publish assets.\n");
    }

    /**
     * Return array of errors and status
     * array("status"=>true,"errors"=>array("Error message"))
     * @return array
     */
    public function checkPrerequisites()
    {
        $result = array("errors" => array());
        if (is_writable(sfConfig::get('sf_web_dir'))) {
            $result['status'] = true;
        } else {
            $result['status'] = false;
            array_push($result['errors'], __("File write permission required to `symfony/web` directory."));
        }
        return $result;
    }

    /**
     * @param string $plugin
     * @param string $dir
     * @throws sfException
     */
    protected function copyWebAssets($plugin, $dir)
    {
        $webDir = $dir . DIRECTORY_SEPARATOR . 'web';
        if (is_dir($webDir)) {
            $this->mirrorDir($webDir, $this->resourceDir . DIRECTORY_SEPARATOR . $plugin);
        }
    }

    /**
     * @param string $src
     * @param string $dest
     * @throws sfException
     */
    protected function mirrorDir($src, $dest)
    {
        $finder = sfFinder::type('any');
        $this->getFilesystem()->mirror($src, $dest, $finder);
    }

    /**
     * @param string $dir
     */
    protected function directoryRecursiveDelete($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . DIRECTORY_SEPARATOR . $object) == "dir") $this->directoryRecursiveDelete($dir . DIRECTORY_SEPARATOR . $object); else unlink($dir . DIRECTORY_SEPARATOR . $object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    /**
     * @return sfFilesystem
     */
    protected function getFilesystem()
    {
        if (is_null($this->fileSystem)) {
            $this->fileSystem = new sfFilesystem();
        }
        return $this->fileSystem;
    }

    /**
     * @return Logger
     */
    protected function getLogger()
    {
        return Logger::getLogger("marketplace");
    }

    /**
     * @param string $message
     */
    protected function logInfo($message)
    {
        $this->getLogger()->forcedLog(Logger::class, null, LoggerLevel::getLevelInfo(), $message);
    }
}
