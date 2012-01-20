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

/**
 * Contains utility functions that are used by the installer and upgrader.
 *
 * NOTE: This file is kept compatible with PHP4 to ensure that the installer
 *       does not crash when running on PHP4.
 */

/**
 * Constants
 */
define("INSTALLUTIL_MEMORY_NO_LIMIT", 0);
define("INSTALLUTIL_MEMORY_UNLIMITED", 1);
define("INSTALLUTIL_MEMORY_HARD_LIMIT_FAIL", 2);
define("INSTALLUTIL_MEMORY_SOFT_LIMIT_FAIL", 3);
define("INSTALLUTIL_MEMORY_OK", 4);

define("INSTALLUTIL_VERSION_INVALID", -1);
define("INSTALLUTIL_VERSION_UNSUPPORTED", 0);
define("INSTALLUTIL_VERSION_SUPPORTED", 1);

/**
 * Checks system PHP version or given php version against the given conditions.
 *
 * Versions below $minVersion are considered invalid unless included in the $supportedVersions.
 * If the version is greater than the highest in $supportedVersions and it is not included
 * in $invalidVersions, it is considered supported.
 *
 * @param string $minVersion Minimum PHP version to consider valid.
 * @param string array $supportedVersions Supported versions.
 * @param string array $invalidVersions Invalid versions. Should be mutually exclusive with supported versions.
 * @param string $systemPHPVersion PHP version to check. If null, system PHP version is used.
 *
 * @return one of INSTALLUTIL_VERSION_UNSUPPORTED, INSTALLUTIL_VERSION_INVALID
 * or INSTALLUTIL_VERSION_SUPPORTED
 */
function checkPHPVersion($minVersion, $supportedVersions, $invalidVersions, $systemPHPVersion = null) {

	$systemPHPVersion = empty($systemPHPVersion) ? constant('PHP_VERSION') : $systemPHPVersion;

	sort($supportedVersions);

	/* default to unsupported */
	$retval = INSTALLUTIL_VERSION_UNSUPPORTED;

	/* If below $minVersion = invalid */
	if(1 == version_compare($systemPHPVersion, $minVersion, '<')) {
		$retval = INSTALLUTIL_VERSION_INVALID;
	}

	/* Check if in supported list */
	foreach($supportedVersions as $ver) {
		if(1 == version_compare($systemPHPVersion, $ver, 'eq')) {
			$retval = INSTALLUTIL_VERSION_SUPPORTED;
			break;
		}
	}

	/* If not in supported list and version is greater than the highest amoung
	 * the supported versions, consider as supported.
	 */
	if (($retval != INSTALLUTIL_VERSION_SUPPORTED) && (1 == version_compare($systemPHPVersion, $ver, '>'))) {
		$retval = INSTALLUTIL_VERSION_SUPPORTED;
	}

	// invalid version check overrides default unsupported
	foreach($invalidVersions as $ver) {
		if(1 == version_compare($systemPHPVersion, $ver, 'eq')) {
			$retval = INSTALLUTIL_VERSION_INVALID;
			break;
		}
	}

	return $retval;
}

/**
 * Checks PHP memory limit (or the given $maxMemory value) against the given conditions.
 *
 *
 * @param int $hardLimit The hard limit.
 * @param int $softLimit The soft limit.
 * @param string $maxMemory Maximum memory. If null, the system memory_limit is returned in this variable.
 *
 * @return one of INSTALLUTIL_MEMORY_NO_LIMIT, INSTALLUTIL_MEMORY_UNLIMITED, INSTALLUTIL_MEMORY_HARD_LIMIT_FAIL
 * INSTALLUTIL_MEMORY_SOFT_LIMIT_FAIL or INSTALLUTIL_MEMORY_OK
 */
function checkPHPMemory($hardLimit, $softLimit, &$maxMemory = null) {

	$maxMemory = (is_null($maxMemory)) ? ini_get('memory_limit') : $maxMemory;

	$result = INSTALLUTIL_MEMORY_NO_LIMIT;

	if ($maxMemory == "") {
		$result = INSTALLUTIL_MEMORY_NO_LIMIT;
	} else if ($maxMemory === "-1") {
		$result = INSTALLUTIL_MEMORY_UNLIMITED;
	} else {

		$maxMemoryInt = (int) rtrim($maxMemory, "M");

		if ($maxMemoryInt < $hardLimit) {
			$result = INSTALLUTIL_MEMORY_HARD_LIMIT_FAIL;
		} else if ($maxMemoryInt < $softLimit) {
			$result = INSTALLUTIL_MEMORY_SOFT_LIMIT_FAIL;
		} else {
			$result = INSTALLUTIL_MEMORY_OK;
		}
	}

	return $result;
}

/**
 * Checks if the system is running PHP 4 or greater.
 *
 * @param string $phpVersion The php version to check. If empty the system php version is checked.
 *
 * @return boolean true or false
 */
function isAtleastPHP4($phpVersion = null) {
	$phpVersion = empty($phpVersion) ? constant('PHP_VERSION') : $phpVersion;

	if(1 == version_compare($phpVersion, '4.3.0', '>')) {
		return true;
	}

	return false;
}

/**
 * Create encryption key with given filename
 * @param  $fileName File name of key.
 * @return bool - true if successful, false if not.
 */
function createKeyFile($fileName) {

    $result = false;

    $keyDir = ROOT_PATH . '/lib/confs/cryptokeys';

    $filePath = $keyDir . '/' . $fileName;

    if (is_writable($keyDir)) {

        $cryptKey = '';
        for($i = 0; $i < 4; $i++) {
            $cryptKey .= md5(rand(10000000, 99999999));
        }
        $cryptKey = str_shuffle($cryptKey);
        $handle = fopen($filePath, 'w');
        if ($handle) {
            fwrite($handle, $cryptKey, 128);
            $result = true;
        }
        fclose($handle);
    }

    return $result;
}

?>
