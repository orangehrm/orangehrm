<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements. See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 *
 *	   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @package log4php
 */

/**
 * LoggerAppenderRollingFile extends LoggerAppenderFile to backup the log files 
 * when they reach a certain size.
 *
 * Parameters are:
 *
 * - layout            - Sets the layout class for this appender
 * - file              - The target file to write to
 * - filename          - The target file to write to
 * - append            - Sets if the appender should append to the end of the file or overwrite content ("true" or "false")
 * - maxBackupIndex    - Set the maximum number of backup files to keep around (int)
 * - maxFileSize       - Set the maximum size that the output file is allowed to
 *                       reach before being rolled over to backup files.
 *                       Suffixes like "KB", "MB" or "GB" are allowed, f. e. "10KB" is interpreted as 10240
 * - maximumFileSize   - Alias to MaxFileSize
 *
 * <p>Contributors: Sergio Strampelli.</p>
 *
 * An example:
 *
 * {@example ../../examples/php/appender_socket.php 19}
 *
 * {@example ../../examples/resources/appender_socket.properties 18}
 *
 * @version $Revision: 883108 $
 * @package log4php
 * @subpackage appenders
 */
class LoggerAppenderRollingFile extends LoggerAppenderFile {

	/**
	 * Set the maximum size that the output file is allowed to reach
	 * before being rolled over to backup files.
	 *
	 * <p>In configuration files, the <var>MaxFileSize</var> option takes a
	 * long integer in the range 0 - 2^63. You can specify the value
	 * with the suffixes "KB", "MB" or "GB" so that the integer is
	 * interpreted being expressed respectively in kilobytes, megabytes
	 * or gigabytes. For example, the value "10KB" will be interpreted
	 * as 10240.</p>
	 * <p>The default maximum file size is 10MB.</p>
	 *
	 * <p>Note that MaxFileSize cannot exceed <b>2 GB</b>.</p>
	 *
	 * @var integer
	 */
	private $maxFileSize = 10485760;
	
	/**
	 * Set the maximum number of backup files to keep around.
	 * 
	 * <p>The <var>MaxBackupIndex</var> option determines how many backup
	 * files are kept before the oldest is erased. This option takes
	 * a positive integer value. If set to zero, then there will be no
	 * backup files and the log file will be truncated when it reaches
	 * MaxFileSize.</p>
	 * <p>There is one backup file by default.</p>
	 *
	 * @var integer 
	 */
	private $maxBackupIndex	 = 1;
	
	/**
	 * @var string the filename expanded
	 * @access private
	 */
	private $expandedFileName = null;

	public function __destruct() {
       parent::__destruct();
   	}
   	
	/**
	 * Returns the value of the MaxBackupIndex option.
	 * @return integer 
	 */
	private function getExpandedFileName() {
		return $this->expandedFileName;
	}

	/**
	 * Get the maximum size that the output file is allowed to reach
	 * before being rolled over to backup files.
	 * @return integer
	 */
	private function getMaximumFileSize() {
		return $this->maxFileSize;
	}

	/**
	 * Implements the usual roll over behaviour.
	 *
	 * <p>If MaxBackupIndex is positive, then files File.1, ..., File.MaxBackupIndex -1 are renamed to File.2, ..., File.MaxBackupIndex. 
	 * Moreover, File is renamed File.1 and closed. A new File is created to receive further log output.
	 * 
	 * <p>If MaxBackupIndex is equal to zero, then the File is truncated with no backup files created.
	 */
	private function rollOver() {
		// If maxBackups <= 0, then there is no file renaming to be done.
		if($this->maxBackupIndex > 0) {
			$fileName = $this->getExpandedFileName();
			// Delete the oldest file, to keep Windows happy.
			$file = $fileName . '.' . $this->maxBackupIndex;
			if(is_writable($file))
				unlink($file);
			// Map {(maxBackupIndex - 1), ..., 2, 1} to {maxBackupIndex, ..., 3, 2}
			for($i = $this->maxBackupIndex - 1; $i >= 1; $i--) {
				$file = $fileName . "." . $i;
				if(is_readable($file)) {
					$target = $fileName . '.' . ($i + 1);
					rename($file, $target);
				}
			}
	
			$this->close();
	
			// Rename fileName to fileName.1
			$target = $fileName . ".1";
			$file = $fileName;
			rename($file, $target);
		}
		
		//unset($this->fp);
		$this->activateOptions();
		$this->setFile($fileName, false);
	}
	
	public function setFileName($fileName) {
		$this->fileName = $fileName;
		// As LoggerAppenderFile does not create the directory, it has to exist.
		// realpath() fails if the argument does not exist so the filename is separated.
		$this->expandedFileName = realpath(dirname($fileName));
		if ($this->expandedFileName === false) throw new Exception("Directory of $fileName does not exist!");
		$this->expandedFileName .= '/'.basename($fileName);
	}


	/**
	 * Set the maximum number of backup files to keep around.
	 * 
	 * <p>The <b>MaxBackupIndex</b> option determines how many backup
	 * files are kept before the oldest is erased. This option takes
	 * a positive integer value. If set to zero, then there will be no
	 * backup files and the log file will be truncated when it reaches
	 * MaxFileSize.
	 *
	 * @param mixed $maxBackups
	 */
	public function setMaxBackupIndex($maxBackups) {
		if(is_numeric($maxBackups)) {
			$this->maxBackupIndex = abs((int)$maxBackups);
		}
	}

	/**
	 * Set the maximum size that the output file is allowed to reach
	 * before being rolled over to backup files.
	 *
	 * @param mixed $maxFileSize
	 * @see setMaxFileSize()
	 * @deprecated
	 */
	public function setMaximumFileSize($maxFileSize) {
		return $this->setMaxFileSize($maxFileSize);
	}

	/**
	 * Set the maximum size that the output file is allowed to reach
	 * before being rolled over to backup files.
	 * <p>In configuration files, the <b>MaxFileSize</b> option takes an
	 * long integer in the range 0 - 2^63. You can specify the value
	 * with the suffixes "KB", "MB" or "GB" so that the integer is
	 * interpreted being expressed respectively in kilobytes, megabytes
	 * or gigabytes. For example, the value "10KB" will be interpreted
	 * as 10240.
	 *
	 * @param mixed $value
	 * @return the actual file size set
	 */
	public function setMaxFileSize($value) {
		$maxFileSize = null;
		$numpart = substr($value,0, strlen($value) -2);
		$suffix = strtoupper(substr($value, -2));

		switch($suffix) {
			case 'KB': $maxFileSize = (int)((int)$numpart * 1024); break;
			case 'MB': $maxFileSize = (int)((int)$numpart * 1024 * 1024); break;
			case 'GB': $maxFileSize = (int)((int)$numpart * 1024 * 1024 * 1024); break;
			default:
				if(is_numeric($value)) {
					$maxFileSize = (int)$value;
				}
		}
		
		if($maxFileSize !== null) {
			$this->maxFileSize = abs($maxFileSize);
		}
		return $this->maxFileSize;
	}

	/**
	 * @param LoggerLoggingEvent $event
	 */
	public function append(LoggerLoggingEvent $event) {
		parent::append($event);
		if(ftell($this->fp) > $this->getMaximumFileSize()) {
			$this->rollOver();
		}
	}
}
