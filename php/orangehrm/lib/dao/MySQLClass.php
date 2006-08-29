<?
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

require_once ROOT_PATH . '/lib/exception/ExceptionHandler.php';

class MySQLClass {

	var $myHost; // server name
	var $myHostPort;
	var $userName; //db user
	var $userPassword; // db user password
	var $db_name; // database name
	var $conn; // database connection
	var $result;


/* Class Constructor for MySQLClass*/	
	function MySQLClass($conf) {
		$this->myHost 		= $conf ->dbhost; //reference for the Host
		$this->myHostPort	= $conf ->dbport; 
		$this->userName 	= $conf ->dbuser; //reference for the Username
		$this->userPassword = $conf ->dbpass; //reference for the Password
		$this->db_name 		= $conf ->dbname; //reference for the DatabaseName
		
	}

/* 	DBConnection Function 
	uses mysql_connect function to connect the Database
	gets myhost, username, userpassword
	Returns true if Connected to the Database
	
*/
	function dbConnect() {
			
		//$this -> conn = mysql_connect($this->myHost, $this->userName, $this->userPassword);
	  	
	  	if (!@$this -> conn = mysql_connect($this->myHost .':'.$this->myHostPort, $this->userName, $this->userPassword)) {
	  		
	  		$exception_handler = new ExceptionHandler();
	  	 	$exception_handler->dbexNoConnection();
	  	   	exit;
    	
	  	} else {
	  		
	  	 if ($this -> conn) {
	  	 	mysql_query("SET NAMES 'utf8'");	  	 	
	  	 	if (mysql_select_db ($this->db_name)) {
	  	 		
		 	    return true;
		 	} else {
		 		
			 	$exception_handler = new ExceptionHandler();
		  	 	$exception_handler->dbexNoDatabase();
		  	 	exit;
			 		
		 	}
			
		 } else {
		   	
		   return false;
		}
	}	  	
	  		
	  	
	  	
	  	
	  	 if ($this -> conn) {
	  	 		  	 	
	  	 	mysql_select_db ($this->db_name);
	 	    return true;
			
		 } else {
		   	
		   	$exception_handler = new ExceptionHandler();
	  	 	$exception_handler->dbexNoDatabase();
	  	 	//exit;
		   return false;
		}
	}

/* 	dbDisconnect Function 
	uses mysql_close function to disconnect 
	from the Database
	
*/	
	
	function dbDisconnect($result = NULL)
	{
		if ($this-> conn != null)
		{
			if($result) 
			{
				$this-> conn -> freeMem($result);
			}
			
			return $this->conn->dbClose();
		}
	}

/* 	
	SQLQUERY Function 
	Input Parameter is the Query String
	Uses mysql_query(), mysql_error() functions
	returns the result set $this->result
	
*/

	function sqlQuery($sql)	{
		
		if( (isset($this -> conn)) && ($sql != '') ){
			$this->result = mysql_query($sql);
			 return $this->result;
	 		 echo mysql_error();
	
	 	} else {	
	 		 	
	 		$exception_handler = new ExceptionHandler();
	  	 	$exception_handler->dbexNoQueryFound();
	 		return false;
	 		
		}
	
	}

	
	function getArray($result) {
		return mysql_fetch_array($result);
	}

/*
	function fetch_result($result,$id = '') {
		return mysql_result($result,$id);
	}


	function fetch_row($result) {
		return mysql_fetch_row($result);
	}


	function getObject($result) {
		return @mysql_fetch_object ($result) ;
	}


	function rowCount($result) {
	// Right
		return @mysql_num_rows($result);
	}


	function getFieldsName($num,$conf) {
	// Right
		$table = $conf['Website']['params']['table'][$num];
//		echo $table ."<br>";
		if (isset($this->conn)){
			return $fields = @mysql_list_fields($this->db_name, $table  , $this->conn);
		}	else return 'false';
	}


	function getCountFields($fields) {
		if ($fields !='false')
			return $columns = mysql_num_fields($fields);
		else return 'false';
	}


	function freeMem($result)	{
		@mysql_free_result($result);
	}


	function dbClose()	{

		if($this->conn)
			@mysql_close ($this->conn);
	}
*/
}
?>
