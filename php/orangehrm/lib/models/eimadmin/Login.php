<?php
/*
// OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures 
// all the essential functionalities required for any enterprise. 
// Copyright (C) 2006 hSenid Software International Pvt. Ltd, http://www.hsenid.com

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

require_once ROOT_PATH . '/lib/confs/Conf.php';
require_once ROOT_PATH . '/lib/dao/DMLFunctions.php';
require_once ROOT_PATH . '/lib/dao/SQLQBuilder.php';

class Login {

		var $username;
		var $password;
		
		function Login() {
		}

function filterUser($userName) {
			$this->username=$userName;
			$tableName = 'HS_HR_USERS a LEFT JOIN HS_HR_EMPLOYEE b ON (a.EMP_NUMBER = b.EMP_NUMBER)';
			$arrFieldList[0] = 'a.USER_NAME';
			$arrFieldList[1] = 'a.USER_PASSWORD';
			$arrFieldList[2] = 'IFNULL(b.EMP_FIRSTNAME, a.USER_NAME)';
			$arrFieldList[3] = 'a.ID';
			$arrFieldList[4] = 'a.USERG_ID';
			$arrFieldList[5] = 'a.STATUS';
			$arrFieldList[6] = 'a.EMP_NUMBER';
			$arrFieldList[7] = 'a.IS_ADMIN';
			
	
			$sql_builder = new SQLQBuilder();
			
			$sql_builder->table_name = $tableName;
			$sql_builder->flg_select = 'true';
			$sql_builder->arr_select = $arrFieldList;		
				
			$sqlQString = $sql_builder->selectOneRecordFiltered($this->username);
			
			//echo $sqlQString;		
			$dbConnection = new DMLFunctions();
			$message2 = $dbConnection -> executeQuery($sqlQString); //Calling the addData() function
			
			
			if ( ($message2) && (mysql_num_rows($message2)!=0) ) {
				$i=0;
				while ($line = mysql_fetch_array($message2, MYSQL_NUM)) {
					
					$arrayDispList[$i][0] = $line[0];
					$arrayDispList[$i][1] = $line[1];
					$arrayDispList[$i][2] = $line[2];
					$arrayDispList[$i][3] = $line[3];
					$arrayDispList[$i][4] = $line[4];
					$arrayDispList[$i][5] = $line[5];
					$arrayDispList[$i][6] = $line[6];
					$arrayDispList[$i][7] = $line[7];
					$i++;
				}
			return $arrayDispList;
				
			 } else return NULL;
			}
}
?>