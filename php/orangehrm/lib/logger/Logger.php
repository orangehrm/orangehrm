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
 require_once 'Auditor.php';
 require_once 'AuditorFactory.php';


 class Logger {
 	private static $instance;
	private $propertyReader;
	private $auditors;

	const DEFAULT_PROPERTY_FILE = '/logger.properties';

	private function __construct($propertyFilePath = null) {
		if(!isset($propertyFilePath) || $propertyFilePath == '') {
			$propertyFilePath = dirname(__FILE__) . self::DEFAULT_PROPERTY_FILE;
		}
		$this->init($propertyFilePath);
	}

	public static function getInstance($propertyFilePath = null) {
//		if(!isset($this->instance)) {
//			$this->instance = new Logger($propertyFilePath);
//		}

		return new Logger();
	}

	private function init($propertyFilePath) {
		$this->auditors = array();

		$this->propertyReader = new PropertyReader($propertyFilePath);
		$auditorNames = $this->propertyReader->getPropertyArray(Auditor::LOG . '.' . Auditor::LOG_NAME);
		$this->initAuditors($auditorNames);
	}

	private function initAuditors($auditorNames) {
		if(isset($auditorNames) && is_array($auditorNames)) {

			$factory = AuditorFactory::getInstance($this->propertyReader);

			foreach($auditorNames as $name) {
				$auditor = $factory->getAuditor($name);
				if(isset($auditor)) {
					$this->auditors[] = $auditor;
				}
			}
		}
	}

	public function info($obj) {
		if(!isset($this->auditors)) {
			return null;
		}

		foreach($this->auditors as $auditor) {
			$auditor -> info($obj);
		}
	}

	public function debug($obj) {
		if(!isset($this->auditors)) {
			return null;
		}

		foreach($this->auditors as $auditor) {
			$auditor -> debug($obj);
		}
	}

	public function warn($obj) {
		if(!isset($this->auditors)) {
			return null;
		}

		foreach($this->auditors as $auditor) {
			$auditor -> warn($obj);
		}
	}

	public function error($obj) {
		if(!isset($this->auditors)) {
			return null;
		}

		foreach($this->auditors as $auditor) {
			$auditor -> error($obj);
		}
	}
 }
?>
