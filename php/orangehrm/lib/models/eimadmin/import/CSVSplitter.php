<?php

class CSVSplitter {
	
	private $tempDir 		= '.';
	private $noOfRecords 	= null;
	private $header			= null;
	private $tempFileList	= null;

	private static $recordLimit 	= 100;
	
	private static function _discoverTempDir() {
		
		$tempDir = null;
	
		// Based on http://www.phpit.net/manual/en/function.sys-get-temp-dir.php
		// To add competability for PHP4/5
    	// by minghong at gmail dot com
		if (function_exists('sys_get_temp_dir')) {
			$tempDir =  sys_get_temp_dir();
		} else {
			if (!empty($_ENV['TMP'])) {
            	$tempDir =  realpath( $_ENV['TMP']);
			} elseif (!empty($_ENV['TMPDIR'])) {
            	$tempDir =  realpath( $_ENV['TMPDIR'] );
			} else if (!empty($_ENV['TEMP'])) {
            	$tempDir =  realpath( $_ENV['TEMP'] );
        	} else {
				// Try to use system's temporary directory as random name shouldn't exist
				$temp_file = tempnam( md5(uniqid(rand(), TRUE)), '' );
				
				if ($temp_file) {
					$temp_dir = realpath(dirname($temp_file));
					unlink($temp_file);
					$tempDir =  $temp_dir;
				} else {
                	return false;
				}
			}
		}
		
		return $tempDir;
	}
	
	public function __construct() {
		$this->tempDir 		= CSVSplitter::_discoverTempDir();
		$this->noOfRecords 	= null;
		$this->tempFileList	= array();
	}
		
	public function setTempDir($path) {
		$this->tempDir = $path;
	}
	
	public static function setRecordLimit($limit) {
		self::$recordLimit = $limit;
	}

	public function getTempDir() {
		return $this->tempDir;
	}

	public static function getRecordLimit() {
		return self::$recordLimit;
	}
	
	public function getNoOfRecords() {
		
		if (is_null($this->noOfRecords)) {
			throw new CSVSplitterException('Cannot get the number of rows before splitting is done.', CSVSplitterException::ROWS_NOT_CALCULATED_YET);
		}
		
		return $this->noOfRecords;
		
	}
	
	public function getHeader() {
		
		if (is_null($this->header)) {
			throw new CSVSplitterException('Cannot get the number of rows before splitting is done.', CSVSplitterException::ROWS_NOT_CALCULATED_YET);
		}
		
		return $this->header;
		
	}
	
	public function getTempFileList() {
		
		if (is_null($this->tempFileList)) {
			throw new CSVSplitterException('Cannot get the number of rows before splitting is done.', CSVSplitterException::ROWS_NOT_CALCULATED_YET);
		}
		
		return $this->tempFileList;
	}
	
	/**
	 * Splits the source file into temporory files considering the
	 * record limit. Inserts headers to each file if $withHeader is true
	 */
	public function split($fileName, $withHeader = true) {

		if (!file_exists($fileName)) {
			throw new CSVSplitterException('Souce file to be split cannot be found.', CSVSplitterException::SOURCE_FILE_NOT_FOUND);
		} else {
			if (!is_readable($fileName)) {
				throw new CSVSplitterException('Souce file to be split is not readable.', CSVSplitterException::SOURCE_FILE_NOT_READABLE);
			} elseif (filesize($fileName) == 0) {
				throw new CSVSplitterException('Souce file to be split is empty.', CSVSplitterException::SOURCE_FILE_EMPTY);
			} elseif (!is_writable($this->tempDir)) {
				throw new CSVSplitterException('Temperory directory is not writable.', CSVSplitterException::DIRECTORY_NOT_WRITABLE);
			}
		}
		
		$rowIndex = 1;
		
		$header = null;
		
		$fpRead = fopen($fileName, 'r');
		
		$tempFile = $this->tempDir . '/' . uniqid('_temp', true);
		$fpWrite = fopen($tempFile, 'w');
		$this->tempFileList[] = $tempFile;
		
		$this->noOfRecords = ($withHeader) ? -1 : 0;
		
		if ($withHeader) {
			$header = fgets($fpRead, 10240);
			fputs($fpWrite, $header, strlen($header));
		}

		while (!feof($fpRead)) {
			$line = fgets($fpRead, 10240);
			fputs($fpWrite, $line, strlen($line));
			$rowIndex++;
			$this->noOfRecords++;
			
			if ($rowIndex > self::$recordLimit && !feof($fpRead)) {
				$rowIndex = 1;
				
				fclose($fpWrite);
				
				$tempFile = $this->tempDir . '/' . uniqid('_temp', true);
				$fpWrite = fopen($tempFile, 'w');
				$this->tempFileList[] = $tempFile;
				
				if ($withHeader) {
					fputs($fpWrite, $header, strlen($header));
				}
			}
		}

		fclose($fpRead);
		
		$this->header = trim($header);
		
		return true;
	}
	
	/**
	 * Deletes the tempory files created by splitting the original
	 * source file
	 */
	public function cleanup() {
		
		foreach ($this->tempFileList as $file) {
			if (!is_writable($file)) {
				throw new CSVSplitterException('Cannot delete temperory files since they are read-only', CSVSplitterException::FILES_NOT_WRITABLE);
			}
				
			unlink($file);
		}
	
		return true;
		
	}
	
	/**
	 * Deleted a specific tempory file
	 */
	public function deleteTempFile($file) {
		
		if (!is_writable($file)) {
			throw new CSVSplitterException('Cannot delete temperory files since they are read-only', CSVSplitterException::FILES_NOT_WRITABLE);
		}
				
		unlink($file);
		
	}

}

class CSVSplitStatus {
	
	private $splitSuccess;
	private $noOfRecords;
	private $tempFileList;
	private $importType;
	
	public function __construct($success, $importType, $noOfRecords = null, $tempFileList = null) {
		$this->splitSuccess = $success;
		$this->noOfRecords = $noOfRecords;
		$this->tempFileList = $tempFileList;
		$this->importType = $importType;
	}
	
	public function getSplitStatus() {
		return $this->splitSuccess;
	}
	
	public function getNoOfRecords() {
		return $this->noOfRecords;
	} 
	
	public function getTempFileList() {
		return $this->tempFileList;
	}
	
	public function getImportType() {
		return $this->importType;
	}
}

class CSVSplitterException extends Exception {
	
	const UNKNOWN_ERROR 			= -1;
	const SOURCE_FILE_NOT_FOUND 	= 1;
	const SOURCE_FILE_EMPTY			= 2;
	const SOURCE_FILE_NOT_READABLE 	= 3;
	const DIRECTORY_NOT_WRITABLE 	= 4;
	const FILES_NOT_WRITABLE		= 5;
	const ROWS_NOT_CALCULATED_YET	= 6;
	
}
?>
