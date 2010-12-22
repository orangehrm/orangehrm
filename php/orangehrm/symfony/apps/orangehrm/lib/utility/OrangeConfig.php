<?php
/*
 // OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 // all the essential functionalities required for any enterprise.
 // Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com

 // OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 // the GNU General Public License as published by the Free Software Foundation; either
 // version 2 of the License, or (at your option) any later version.

 // OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 // without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 // See the GNU General Public License for more details.

 // You should have received a copy of the GNU General Public License along with this program;
 // if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 // Boston, MA  02110-1301, USA
 */

/**
 * Gives access to OrangeHRM config files sysConf.php and conf.php
 */
class OrangeConfig {

	private $sysConf = null;

	private $conf = null;

	private $appConf = null;

	private static $instance = null;

	/**
	 * Private constructor. Use the getInstance() method to get object instance
	 */
	private function __construct() {

	}

	/**
	 * Returns an instance of this class
	 *
	 * @return OrangeConfig
	 */
	public static function getInstance() {

		if ( ! isset(self::$instance) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Get orangehrm's sysConf configuration object
	 *
	 * @return sysConf object
	 */
	public function getSysConf() {
		if (is_null($this->sysConf)) {

			require_once sfConfig::get('sf_root_dir') . '/../lib/confs/sysConf.php';
			$this->sysConf = new sysConf();
		}

		return $this->sysConf;
	}

	/**
	 * Get orangehrm's Conf configuration object
	 *
	 * @return Conf object
	 */
	public function getConf() {
		if (is_null($this->conf)) {

			require_once sfConfig::get('sf_root_dir') . '/../lib/confs/Conf.php';
			$this->conf = new Conf();
		}

		return $this->conf;
	}

	/*
	 * TODO: Create new class for getting and setting app configs
	 */
	public function loadAppConf() {
		require_once sfConfig::get('sf_root_dir') . '/../lib/common/Config.php';
	}
	
	public function getAppConfValue($key) {
		
		$this->loadAppConf();
		
		switch ($key) {
			case Config :: KEY_LEAVE_PERIOD_DEFINED:
				return Config::isLeavePeriodDefined();
				break;
			default:
				throw new Exception("Getting {$key} is not implemented yet");
				break;
		}
	}

	public function setAppConfValue($key, $value) {
		
		$this->loadAppConf();
		
		switch ($key) {
			case Config :: KEY_LEAVE_PERIOD_DEFINED:
				Config::setIsLeavePriodDefined($value);
				break;
			default:
				throw new Exception("Setting {$key} is not implemented yet");
				break;
		}
	}

}