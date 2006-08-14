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
	$this->dbname	= 'root';
	$this->dbuser	= 'orangehrm';
	$this->dbpass	= 'orangehrm';
	$this->smtphost = 'mail.beyondm.net';
	}
}
?>