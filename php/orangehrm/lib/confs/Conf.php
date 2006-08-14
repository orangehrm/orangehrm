<?
class Conf {

	var $smtphost;
	var $dbhost;
	var $dbport;
	var $dbname;
	var $dbuser;
	var $dbpass;

	function Conf() {
		
	$this->dbhost	= 'localhost';
	$this->dbport 	= '3306';
	$this->dbname	= 'hr_mysql';
	$this->dbuser	= 'root';
	$this->dbpass	= 'moha';
	$this->smtphost = 'mail.beyondm.net';
	}
}
?>