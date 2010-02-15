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
require_once ROOT_PATH . '/lib/dao/DMLFunctions.php';
require_once ROOT_PATH . '/lib/dao/SQLQBuilder.php';

class Plugin {
		
	private static $PluginArray = array('CSVREPORT' => 'plugins/csv/installer.xml', 'LDAP' => 'plugins/ldap/installer.xml', 'LEAVEREPORT' => 'plugins/leave-csv/installer.xml');
	
	public static function fetchPlugin($plugInName){
	
		if(array_key_exists($plugInName , self::$PluginArray)){
		
			return self::$PluginArray[$plugInName];
		}else{
		
			return false;
		}
		
		 
	
	}
	 
}
?>