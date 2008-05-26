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

$confPHP = ROOT_PATH . '/lib/confs/Conf.php';
if(file_exists($confPHP)) {
	@require_once ROOT_PATH . '/lib/dao/DMLFunctions.php';
}

class DbBackup {


	public static function backup($path, $table, $fields = '*', $encrypted = false, $encryptingFields = null) {
	
		try {
			$dbConnection = new DMLFunctions();

			if ($encrypted) {
			
				if (!is_array($fields)) {
				
					throw new DbBackupException('Encryption fields has to be defined in a array if you are encrypting the backup files', DbBackupException::ENCRYPTION_FIELDS_NOT_DEFINED);
				
				}
				
				$fields = '`' . implode('`, `', $fields) . '`';
				
				$success =  $dbConnection->executeQuery("SELECT $fields INTO OUTFILE '$path' FROM `$table`");
				
			} else {
			
				if (is_array($fields)) {
				
					$fields = '`' . implode('`, `', $fields) . '`';
				
				}

				$success =  $dbConnection->executeQuery("SELECT $fields INTO OUTFILE '$path' FROM `$table`");
			
			}
			
		} catch (Exception $e) {
		
			throw new DbBackupException(mysql_error(), DbBackupException::DATABASE_ERROR);
		
		}
	
		return $success;
	
	}
	
	public static function restore($path = null, $table, $fields = '*', $decrypted = false, $clearnup = false) {
	
	
	
	}

}

class DbBackupException extends Exception {

	const UNKNOWN_ERROR 								= -1;
	const ENCRYPTION_FIELDS_NOT_DEFINED 	= 1;
	const DATABASE_ERROR								= 2;

}

?>