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

 class PropertyReader {
 	private $path;
 	private $properties;
 	private $keys;

 	public function __construct($path) {
 		$this -> path = $path;
 		$this->init();
 	}

 	private function init() {
 		if(!isset($this->path)) {
 			return;
 		}
 		if(!is_file($this->path)) {
				throw new PropertyReaderException($this->path, PropertyReaderException::PROPERTY_FILE_NOT_FOUND);
		}

		$this->initProperties();
 	}

	private function initProperties() {
		$this->properties = array();
		$this->keys = array();

		try {
			$handle = fopen($this->path, "r");

			if($handle == false) {
				throw new PropertyReaderException('', PropertyReaderException::PROPERTY_FILE_NOT_FOUND);
			}

			while(!feof($handle)) {
				$line = fgets($handle);
				$isLine = preg_match('/^#/', $line);

				if($isLine == 0 ) {
					$temp = explode("=", $line);
					if(is_array($temp) && count($temp) == 2) {
						$this -> keys[] = trim($temp[0]);
						$this -> properties[] = trim($temp[1]);
					}
				}
			}
		}catch(Exception $e) {
			throw $e;
		}
	}

	public function getProperty($key) {
		if(!isset($this->properties)) {
			return null;
		}
		try {
			for($i = 0; $i < count($this->keys); $i = $i + 1) {
				if ($this -> keys[$i] == $key) {
					return $this->properties[$i];
				}
			}
		}catch(Exception $e) {

		}
		return null;
	}

	/**
	 * return value array with all the values where key is equal to param
	 */
	public function getPropertyArray($key) {
		if(!isset($this->properties)) {
			return null;
		}
		$values = array();
		try {
			for($i = 0; $i < count($this->keys); $i = $i + 1) {
				if ($this -> keys[$i] == $key) {
					$values[] = $this->properties[$i];
				}
			}
		}catch(Exception $e) {

		}
		return $values;

	}


 }

 class PropertyReaderException extends Exception {
 	const PROPERTY_FILE_NOT_FOUND = 0;
 }
?>
