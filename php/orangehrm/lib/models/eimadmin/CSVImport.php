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
require_once ROOT_PATH.'/lib/models/eimadmin/import/CSVImportPlugin.php';
require_once ROOT_PATH.'/lib/models/eimadmin/import/CustomizableCSVImport.php';
require_once ROOT_PATH.'/lib/models/eimadmin/import/CSVSplitter.php';

class CSVImport {

	/* Import statuses */
	const IMPORTED = "IMPORTED";
	const IMPORT_ERROR = "IMPORT_ERROR";
	const INCORRECT_COLUMN_NUMBER = "INCORRECT_COLUMN_NUMBER";
	const SKIPPED_HEADER = "SKIPPED_HEADER";
	const MISSING_WORKSTATION = "MISSING_WORKSTATION";
	const COMPULSARY_FIELDS_MISSING_DATA = "COMPULSARY_FIELDS_MISSING_DATA";
	const DD_DATA_INCOMPLETE = "DD_DATA_INCOMPLETE";
	const INVALID_TYPE = "INVALID_TYPE";
	const DUPLICATE_EMPLOYEE_ID = "DUPLICATE_EMPLOYEE_ID";
	const DUPLICATE_EMPLOYEE_NAME = "DUPLICATE_EMPLOYEE_NAME";
	const FIELD_TOO_LONG = "FIELD_TOO_LONG";

	/**
	 * Class Attributes
	 */
	private $fileName;
	private $importType;
	private $importPlugins;
	private $pluginDir;

	public function setFileName($fileName) {
		$this->fileName = $fileName;
	}

	public function getFileName() {
		return $this->fileName;
	}

	public function setImportType($importType) {
		$this->importType = $importType;
	}

	public function getImportType() {
		return $this->importType;
	}

	/**
	 * Constructor
	 *
	 */
	public function __construct() {
		$this->pluginDir = ROOT_PATH . '/lib/models/eimadmin/import/plugins';
		$this->importPlugins = $this->_getListOfAvailablePlugins();

		/* Get user defined imports - defined via the UI.*/
		$customImports = CustomImport::getCustomImportList();
		foreach ($customImports as $import) {

			/* We don't check for any conflicts in key since, plugins have the class name as key*/
			$this->importPlugins[$import->getId()] = $import->getName();
		}
	}

	/**
	 * Get defined import types
	 *
	 */
	public function getDefinedImportTypes() {
		return $this->importPlugins;
	}

	/**
	 * Do the data import
	 *
	 * @return array Array containing results of import
	 */
	 
	 public function handleUpload() {
	 	
	 	$csvSplitter = new CSVSplitter();
	 	$success = $csvSplitter->split($this->fileName);
	 	
	 	if ($success) {
	 		
	 		$noOfRecords = $csvSplitter->getNoOfRecords();
	 		$tempFileList = $csvSplitter->getTempFileList();
	 		
	 		$result = new CSVSplitStatus('success', $this->importType, $noOfRecords, $tempFileList);
	 	
	 	} else {
	 		$result = new CSVSplitStatus('failure', $this->importType);
	 	}
	 	
	 	return $result;
	 }
	 
	 public function importData($fileName) {

		set_time_limit(0); // For handling time out

/*		$ir = array();
		$ir[] = new ImportResult(self::IMPORT_ERROR, "A comment");
		$ir[] = new ImportResult(self::INCORRECT_COLUMN_NUMBER, "Another comment");

		$xx = new CSVImportStatus($ir, 3, 1, 1);
		return $xx;
*/
		if (empty($this->importType) || empty($fileName)) {
			throw new CSVImportException("Import data not received", CSVImportException::IMPORT_DATA_NOT_RECEIVED);
		}
		$importPlugin = $this->_getPlugin($this->importType);

		// Open CSV file and read the data
		$row = 0;
		$rowsToSkip = $importPlugin->getNumHeaderRows();
		$numColumns = $importPlugin->getNumColumns();
		$rowsImported = 0;
		$rowsWithErrors = 0;
		$rowsSkipped = 0;
		$importResults = array();

		$handle = fopen($fileName, "r");

		while (($data = fgetcsv($handle)) !== FALSE) {

			if ($row >= $rowsToSkip) {
				if (count($data) == $numColumns) {
					array_walk($data, array('CSVImport', 'trimValue'));
					try {
						$importPlugin->importCSVData($data);
						$importResults[$row] = new ImportResult(self::IMPORTED);
						$rowsImported++;
					} catch (CSVImportException $e) {

						switch ($e->getCode()) {
							case CSVImportException::MISSING_WORKSTATION:
								$error = self::MISSING_WORKSTATION;
								break;
							case CSVImportException::DD_DATA_INCOMPLETE:
								$error = self::DD_DATA_INCOMPLETE;
								break;
							case CSVImportException::INVALID_TYPE:
								$error = self::INVALID_TYPE;
								break;
							case CSVImportException::DUPLICATE_EMPLOYEE_ID:
								$error = self::DUPLICATE_EMPLOYEE_ID;
								break;
							case CSVImportException::DUPLICATE_EMPLOYEE_NAME:
								$error = self::DUPLICATE_EMPLOYEE_NAME;
								break;
							case CSVImportException::FIELD_TOO_LONG:
								$error = self::FIELD_TOO_LONG;
								break;

							default:
								$error = self::IMPORT_ERROR;
						}

						$importResults[$row] = new ImportResult($error, $e->getMessage());
						$rowsWithErrors++;
					} catch (Exception $ee) {
						$importResults[$row] = new ImportResult(self::IMPORT_ERROR, $ee->getMessage());
						$rowsWithErrors++;
					}
				} else {
					$importResults[$row] = new ImportResult(self::INCORRECT_COLUMN_NUMBER);
					$rowsWithErrors++;
				}
			} else {
				$importResults[$row] = new ImportResult(self::SKIPPED_HEADER);
				$rowsSkipped++;
			}
			$row++;
		}
		fclose($handle);

		$result = new CSVImportStatus($importResults, $rowsImported, $rowsWithErrors, $rowsSkipped);

		return $result;
	 }


	 /**
	  * Get list of available csv import plugins
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

							if ($object instanceof CSVImportPlugin) {
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

	/* Used to trim array */
	public static function trimValue(& $value) {
		$value = trim($value);
	}

	 private function _getPlugin($type) {

		/* If the type is an ID, get the customizable CSV Import class */
		if (CommonFunctions::isValidId($type)) {
			$object = new CustomizableCSVImport($type);
		} else {
			require_once $this->pluginDir . "/" . $type . ".php";
			$object = new $type;
		}
		return $object;
	 }
}

class ImportResult {
	private $status;
	private $comments;

	public function __construct($status, $comments = "") {
		$this->status = $status;
		$this->comments = $comments;
	}

	public function getStatus() {
		return $this->status;
	}

	public function getComments() {
		return $this->comments;
	}
}

class CSVImportStatus {

	private $importResults;
	private $numImported;
	private $numFailed;
	private $numSkipped;

	public function __construct($importResults, $numImported, $numFailed, $numSkipped) {
		$this->importResults = $importResults;
		$this->numImported = $numImported;
		$this->numFailed = $numFailed;
		$this->numSkipped = $numSkipped;
	}

	public function getImportResults() {
		return $this->importResults;
	}

	public function getNumImported() {
		return $this->numImported;
	}

	public function getNumFailed() {
		return $this->numFailed;
	}

	public function getNumSkipped() {
		return $this->numSkipped;
	}

}
?>
