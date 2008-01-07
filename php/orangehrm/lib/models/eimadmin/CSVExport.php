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
 *
 */

require_once ROOT_PATH.'/lib/dao/DMLFunctions.php';
require_once ROOT_PATH.'/lib/dao/SQLQBuilder.php';
require_once ROOT_PATH.'/lib/confs/sysConf.php';
require_once ROOT_PATH.'/lib/common/CommonFunctions.php';
require_once ROOT_PATH.'/lib/models/eimadmin/export/CSVExportPlugin.php';

class CSVExport {

	/**
	 * Class Attributes
	 */
	private $exportPlugins;
	private $pluginDir;

	/**
	 * Constructor
	 *
	 */
	public function __construct() {
		$this->pluginDir = ROOT_PATH . '/lib/models/eimadmin/export/plugins';
		$this->exportPlugins = $this->_getListOfAvailablePlugins();
	}

	/**
	 * Get defined export types
	 *
	 */
	public function getDefinedExportTypes() {
		return $this->exportPlugins;
	}

	/**
	 * Do the data export
	 *
	 * @param string $type Export type
	 */
	 public function exportData($type) {

		$exportPlugin = $this->_getPlugin($type);

		$csvContents = $exportPlugin->getHeader() . "\n" . $exportPlugin->getCSVData();

		ob_end_clean();
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);
		header("Content-Type: text/csv");
		header('Content-Disposition: attachment; filename="' . $type . '.csv";');
		header("Content-Transfer-Encoding: none");
		//header("Content-Length: " .strlen($csvContents));

		echo $csvContents;

	 }

	 /**
	  * Get list of available csv export plugins
	  */
	 protected function _getListOfAvailablePlugins() {

		$plugins = array();

		if (is_dir($this->pluginDir)) {

			$handle = @opendir($this->pluginDir);
			if ($handle) {

				$oldDir = getcwd();
				chdir($this->pluginDir);

				while (false !== ($file = readdir($handle))) {

					if (is_file($file)) {
						$fileInfo = pathinfo($file);
						$className = $fileInfo['basename'];
						$extension = $fileInfo['extension'];
						if (!empty($extension)) {
							$className = str_replace("." . $extension, "", $className);
						}

						/* Skip any unit test classes (ending with Test) */
						if (!(strrpos($className, "Test") === strlen($className) - 4)) {

							require_once $this->pluginDir . "/" . $file;
							$object = new $className;

							if ($object instanceof CSVExportPlugin) {
								$pluginName = $object->getName();
								$plugins[$className] = $pluginName;
							}
						}
					}
    			}
				closedir($handle);
				chdir($oldDir);
			}
		}
		return $plugins;
	 }

	 private function _getPlugin($name) {

		require_once $this->pluginDir . "/" . $name . ".php";
		$object = new $name;
		return $object;
	 }
}
?>
