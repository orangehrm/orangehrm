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

require_once ROOT_PATH . '/lib/common/Config.php';

class KeyHandlerOld {

	private static $filePath = '/lib/confs/cryptokeys/key.ohrm';
	private static $key;
	private static $keySet = false;

    public static function createKey() {

		if (self::keyExists()) {
			throw new KeyHandlerOldException('Key already exists', KeyHandlerOldException::KEY_ALREADY_EXISTS);
		}

		// Creating the key
		try {

			$cryptKey = '';

			for($i = 0; $i < 4; $i++) {
				$cryptKey .= md5(rand(10000000, 99999999));
			}

			$cryptKey = str_shuffle($cryptKey);

			$handle = fopen(ROOT_PATH . self::$filePath, 'w');
			fwrite($handle, $cryptKey, 128) or die('error');
		    fclose($handle);

		} catch (Exception $e) {

			throw new KeyHandlerOldException('Failed to create the key file', KeyHandlerOldException::KEY_CREATION_FAILIURE);

		}

		if (self::keyExists()) {
			return true;
		} else {
		    return false;
		}

    }

    public static function readKey() {

		if (!self::keyExists()) {

			throw new KeyHandlerOldException('Key file does not exist', KeyHandlerOldException::KEY_DOES_NOT_EXIST);

		}

		if (!is_readable(ROOT_PATH . self::$filePath)) {

			throw new KeyHandlerOldException('Key is not readable', KeyHandlerOldException::KEY_NOT_READABLE);

		}

		if (!self::$keySet) {
	    	self::$key = trim(file_get_contents(ROOT_PATH . self::$filePath));
			self::$keySet = true;
		}

		return self::$key;

    }

    public static function deleteKey() {

		if (!self::keyExists()) {
			throw new KeyHandlerOldException('Key does not exist', KeyHandlerOldException::KEY_DOES_NOT_EXIST);
		}

		// Deleting
		try {
			@unlink(ROOT_PATH . self::$filePath);
		} catch (Exception $e) {
			throw new KeyHandlerOldException('Failed to delete the key file', KeyHandlerOldException::KEY_DELETION_FAILIURE);
		}

		if (!self::keyExists()) {
			return true;
		} else {
		    return false;
		}

    }

    public static function keyExists() {

		return (file_exists(ROOT_PATH . self::$filePath));

    }

}

class KeyHandlerOldException extends Exception {

	const KEY_DOES_NOT_EXIST		= 1;
	const KEY_NOT_READABLE			= 2;
	const KEY_ALREADY_EXISTS		= 3;
	const KEY_CREATION_FAILIURE	= 4;
	const KEY_DELETION_FAILIURE	= 5;

}

?>
