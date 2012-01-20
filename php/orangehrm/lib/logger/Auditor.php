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

require_once 'PropertyReader.php';

class Auditor{
	private $name;
	private $level;

	const LOG = 'logger';
	const LOG_NAME = 'name';
	const LOG_TYPE = 'type';
	const LOG_LEVEL ='level';

	const DEFAULT_LEVEL = 1;

	const LEVE_INFO ='info';
	const LEVEL_DEBUG = 'debug';
	const LEVEL_WARNING ='warn';
	const LEVEL_ERROR = 'error';

	const INFO = 0;
	const DEBUG = 1;
	const WARNING = 2;
	const ERROR = 3;

	const TYPE_FILELOGGER = 'fileLogger';

	public function __construct($name, $propertyReader) {
		$this -> name = $name;
		$this -> initProperties($propertyReader);
	}

	private function initProperties($propertyReader) {
		//set level
		if(isset($propertyReader)) {
			$level = $propertyReader->getProperty(self::LOG . '.' . $this->name . '.' . self::LOG_LEVEL);

			if(isset($level) && $level != '') {
				$this->setLevel($level);
			}else {
				$this -> level = self::DEFAULT_LEVEL;
			}
		}else {
			$this -> level = self::DEFAULT_LEVEL;
		}
	}

	private function setLevel($level) {
		switch($level) {
			case self::LEVE_INFO :
				$this->level = self::INFO;
				break;
			case self::LEVEL_DEBUG :
				$this->level = self::DEBUG;
				break;
			case self::LEVEL_WARNING :
				$this->level = self::WARNING;
				break;
			case self::LEVEL_ERROR :
				$this->level = self::ERROR;
				break;
			default :
				$this->level = self::DEBUG;
				break;
		}
	}

	public static function getType($name, $propertyReader) {
 		try {
			$type = $propertyReader-> getProperty(self::LOG . '.' . $name . '.' . self::LOG_TYPE);
			return $type;
 		}catch(Exception $e) {

 		}
 		return null;
 	}

	public function getName() {
		return $this->name;
	}

	public function getLevel() {
		return $this->level;
	}

	public function info($obj) {
		return false;
	}

	public function debug($obj) {
		return false;
	}

	public function warning($obj) {
		return false;
	}

	public function error($obj) {
		return false;
	}
}
?>
