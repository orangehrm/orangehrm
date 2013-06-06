<?php
class Conf {

	var $smtphost;
	var $dbhost;
	var $dbport;
	var $dbname;
	var $dbuser;
	var $version;

	function Conf() {

		$this->dbhost	= 'localhost';
		$this->dbport 	= '3306';
		$this->dbname	= 'orangehrm_mysql';
		$this->dbuser	= 'root';
		$this->dbpass	= '';
		$this->version = '3.1';

		$this->emailConfiguration = dirname(__FILE__).'mailConf.php';
		$this->errorLog =  realpath(dirname(__FILE__).'/../logs/').'/';
	}
}
?>
