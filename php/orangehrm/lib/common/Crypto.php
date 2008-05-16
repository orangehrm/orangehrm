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
 */

class Crypto {

    private static $instance;
    private static $key;
    private static $iv;
    private static $keyFilesDontExist = false;

    private function __construct() {

        $keyLocation = ROOT_PATH.'/lib/cryptoinc/key.ohrm';
        $ivLocation = ROOT_PATH.'/lib/cryptoinc/iv.ohrm';

        if (file_exists($keyLocation) && file_exists($ivLocation)) {

			$this->_readKey($keyLocation);
       		$this->_readIv($ivLocation);

        } else {
            self::$keyFilesDontExist = true;
        }


    }

    public static function getInstance() {

		if (!is_a(self::$instance, 'Crypto')) {
			self::$instance = new Crypto();
		}

		return self::$instance;

	}


    private function _readKey($keyFile) {
        // read $keyFile and set self::$key
    }

    private function _readIv($ivFile) {
        // read $ivFile and set self::$iv
    }

    public function encode($input) {
        // encode $input using self::$key, self::$iv and return output
        // if needed validate input
        // Do MySQL escaping so that output is ready to be saved in the database
        // if $keyFilesDontExist is set to 'true', return $input without any encoding
        // return null on failure
    }

    public function decode($input) {
        // decode $input using self::$key, self::$iv and return output
        // if needed validate input
        // if $keyFilesDontExist is set to 'true', return $input without any decoding
        // return null on failure
    }

}

?>
