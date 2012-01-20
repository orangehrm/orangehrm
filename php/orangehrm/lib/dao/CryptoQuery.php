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

require_once ROOT_PATH . '/lib/models/eimadmin/encryption/KeyHandlerOld.php';

class CryptoQuery {

	public static function isEncTable($table) {

		if (strtolower($table) == "hs_hr_employee" || strtolower($table) == "hs_hr_emp_basicsalary") {
		    return true;
		} else {
		    return false;
		}

	}

	public static function prepareDecryptFields($decryptFieldsArray) {
		$encOn = KeyHandlerOld::KeyExists();
		foreach ($decryptFieldsArray as $field) { 
			if ($encOn && self::isEncField($field)) {
				$key = KeyHandlerOld::readKey();
			    $fieldsArray[] = "AES_DECRYPT(`$field`, '$key')";
			} else {
			    $fieldsArray[] = $field;
			}
		}
		return $fieldsArray;
	}
	
	public static function prepareEncryptFields($encryptFieldsArray, $encryptValuesArray) {
		$encOn = KeyHandlerOld::KeyExists();
		
		$valuesArray = array();
		
		$encryptFieldsArrayCount = count($encryptFieldsArray);
		
		for ($i = 0; $i < $encryptFieldsArrayCount; $i++) { 
			if ($encOn && self::isEncField($encryptFieldsArray[$i])) {
				
				$key = KeyHandlerOld::readKey();
				
				if ($encryptValuesArray[$i] == null)
					$valuesArray[$i] = null;
				else
				    $valuesArray[$i] = "AES_ENCRYPT($encryptValuesArray[$i], '$key')";
			    
			} else {
			    $valuesArray[$i] = $encryptValuesArray[$i];
			}
		}
		return $valuesArray;
	}	

	public static function isEncField($field) {
	    if (strtolower($field) == "emp_ssn_num" || strtolower($field) == "ebsal_basic_salary") {
			return true;
	    } else {
			return false;
	    }
	}

}

?>
