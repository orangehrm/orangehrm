<?php

/*
 *
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
 *
 *
 */

class Restore {
	
	/*
	 *	Class Attributes
	 *
	 **/
	 public $connection;
	 public $file;
	 
	 
	 
	 /*
	 *
	 *	Class Constructor
	 *
	 **/
	
	public function __construct() {
		// nothing to do		
	}
	
	/*
	 *	Setter method followed by getter method for each
	 *	attribute
	 *
	 **/
	
	public function setConnection($connection){
		
		$this->connection = $connection;
	}
	
	public function getConnection (){
		
		return $this->connection;
	}
	
	public function setFileSource($file) {
		
		$this->file = $file;
	}
	
	public function getFileSource() {
		
		return $this->file;
	}
	public function setDatabase($database) {
		
		$this->database = $database;
	}
	
	public function getDatabase() {
		
		return $this->database;
	}
	
	
	
	
	public function fillDatabase() {		
		return  $this-> _restoreDatabase();		
	}
	
	public function _restoreDatabase() {
		
		//echo $this->getConnection();
		$_arrSQL = explode(";", $this->getfileSource());
		
		$noofrec = count($_arrSQL);
		
		mysql_select_db($this->getDatabase());
		for($i=0; $i<($noofrec-1); $i++)
		{			
			$result = mysql_query($_arrSQL[$i], $this->getConnection());
			if(!$result) {				
				return false;
			}
			
			if (($i%50) == 0) {
				set_time_limit(30);
				error_log($_arrSQL[$i], 3, 'log.txt');
			}
			
		}
		return true;		 
	}
}
?>