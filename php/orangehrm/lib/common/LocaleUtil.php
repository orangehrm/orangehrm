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

require_once ROOT_PATH . '/lib/confs/sysConf.php';

/**
 * Class to handle locale specific formatting tasks
 * Implemented as a singleton.
 */
 class LocaleUtil {

	/** This singleton instance */
	private static $instance;

	private $sysConf;

	/**
	 * Set the sysConf instance used by this object.
	 *
	 * @param sysConf $sysConf sysConf object
	 */
	public function setSysConf($sysConf) {
		$this->sysConf = $sysConf;
	}

	/**
	 * Private constructor
	 */
	private function __constructor() {
		$this->setSysConf(new sysConf());
	}

	/**
	 * Get the singleton instance of this class
	 */
	 public static function getInstance() {

	 	if (!is_object(self::$instance)) {
	 		self::$instance = new LocaleUtil();
	 	}
		return self::$instance;
	 }

	 /**
	  * Converts the date format to the format configured in the system
	  *
	  * @param string date
	  * @return date formatted according to configured format
	  */
	 public function formatDate($date) {

	 }

	 /**
	  * Converts a date and time value to the format configured in the system
	  *
	  * @param string date and time to be converted
	  * @return date and time formatted according to configured format
	  */
	 public function formatDateTime($dateTime) {

	 }

}

class LocaleException extends Exception {
}

?>
