<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'HspMailNotificationTest::main');
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once "testConf.php";
require_once ROOT_PATH."/lib/confs/Conf.php";

require_once ROOT_PATH . '/lib/models/benefits/mail/HspMailNotification.php';
require_once ROOT_PATH . '/lib/models/benefits/HspPaymentRequest.php';
require_once ROOT_PATH . '/lib/models/eimadmin/EmailNotificationConfiguration.php';


class HspMailNotificationTest extends PHPUnit_Framework_TestCase {

	private $hspMailNotification;
	private $hspPaymentRequest;
	private $connection;

	protected function setUp() {
		$this -> hspMailNotification = new HspMailNotification();
		$this -> hspPaymentRequest = new HspPaymentRequest();

		$conf = new Conf();
    		$this->connection = mysql_connect($conf->dbhost.":".$conf->dbport, $conf->dbuser, $conf->dbpass);
        	mysql_select_db($conf->dbname);

		mysql_query("INSERT INTO `hs_hr_employee` VALUES (11, '011', 'Subasinghe', 'Arnold', '', 'Arnold', 0, NULL, '0000-00-00 00:00:00', NULL, NULL, NULL, '', '', '', '', '0000-00-00', '', NULL, NULL, NULL, NULL, '', '', '', 'AF', '', '', '', '', '', 'dimuthu@orangehrm.com', NULL, '0000-00-00', 'dsamarasekara@gmail.com')");

		mysql_query("INSERT INTO `hs_hr_users` (`id`, `email1`) VALUES ('USR011', 'dimuthu@beyondm.net')");

		mysql_query("TRUNCATE TABLE `hs_hr_mailnotifications`");
    	mysql_query("INSERT INTO `hs_hr_mailnotifications` (`user_id`, `notification_type_id`, `status`) VALUES ('USR011', -1, 1)");
    	mysql_query("INSERT INTO `hs_hr_mailnotifications` (`user_id`, `notification_type_id`, `status`) VALUES ('USR010', 0, 1)");
    	mysql_query("INSERT INTO `hs_hr_mailnotifications` (`user_id`, `notification_type_id`, `status`) VALUES ('USR010', 1, 1)");
    	mysql_query("INSERT INTO `hs_hr_mailnotifications` (`user_id`, `notification_type_id`, `status`) VALUES ('USR010', 2, 1)");
    	mysql_query("INSERT INTO `hs_hr_mailnotifications` (`user_id`, `notification_type_id`, `status`) VALUES ('USR010', 3, 1)");
	}

	protected function tearDown() {
		mysql_query("DELETE FROM `hs_hr_employee` WHERE `emp_no` = 11");
		mysql_query("DELETE FROM `hs_hr_user` WHERE (`id` = 'USR011' OR `id` = 'USR012')");
		mysql_query("TRUNCATE TABLE `hs_hr_mailnotifications`");
	}

	/**
	*
	*/
	public function testGetEmployeeName() {
		$this -> assertEquals($this -> hspMailNotification -> _getEmployeeName('011'), 'Arnold Subasinghe');

		$this -> assertEquals($this ->hspMailNotification -> _getEmployeeName('999'), '');
	}

	/**
	*
	*/
	public function testGetNotificationAddress() {

		$type1 = EmailNotificationConfiguration :: EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_LEAVE_REJECTED;
		$type2 = EmailNotificationConfiguration :: EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_LEAVE_CANCELLED;
		$type3 = EmailNotificationConfiguration :: EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_LEAVE_PENDING_APPROVAL;
		$type4 = EmailNotificationConfiguration :: EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_LEAVE_APPROVED;
		$type5 = EmailNotificationConfiguration :: EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_HSP;

		$email = $this -> hspMailNotification -> _getNotificationAddress($type1);
		//$this -> assertTrue(is_array($email));
		$this -> assertEquals('dimuthu@beyondm.net', is_array($email));

		$email = $this -> hspMailNotification -> _getNotificationAddress($type2);
		$this -> assertTrue(is_array($email));
		$this -> assertEquals('dimuthu@beyondm.net', is_array($email));

		$email = $this -> hspMailNotification -> _getNotificationAddress($type3);
		$this -> assertTrue(is_array($email));
		$this -> assertEquals('dimuthu@beyondm.net', is_array($email));

		$email = $this -> hspMailNotification -> _getNotificationAddress($type4);
		$this -> assertTrue(is_array($email));
		$this -> assertEquals('dimuthu@beyondm.net', is_array($email));

		$email = $this -> hspMailNotification -> _getNotificationAddress($type5);
		$this -> assertTrue(is_array($email));
		$this -> assertEquals('dimuthu@beyondm.net', is_array($email));

	}

	/**
	*
	*/
	public function testGetPaymentRequestSubject() {
		$expected = 'HSP Notification - dimuthu has submitted a request';
		$this -> assertEquals($expected, $this -> hspMailNotification -> _getPaymentRequestSubject("dimuthu"));

		$expected = 'HSP Notification - dimuthu samarasekara has submitted a request';
		$this -> assertEquals($expected, $this -> hspMailNotification -> _getPaymentRequestSubject("dimuthu samarasekara"));

		$expected = 'HSP Notification - Employee has submitted a request';
		$this -> assertEquals($expected, $this -> hspMailNotification -> _getPaymentRequestSubject(''));
	}

	/**
	*
	*/
	public function testGetPaymentRequestMsg() {
		$result = $this -> hspMailNotification -> _getPaymentRequestMsg("dimuthu", '2007-01-01', 'flu');
		$this->assertEquals(preg_match("/#employee/", $result), 0);
		$this->assertEquals(preg_match("/##dateInquired/", $result), 0);
		$this->assertEquals(preg_match("/#empName/", $result), 0);
		$this->assertEquals(preg_match("/#expenseDesc/", $result), 0);
	}

	/**
	 *
	 */
	public function testGetEmployeeAddress() {
		$address = $this -> hspMailNotification -> getEmployeeAddress('011');

		$this -> assertTrue(is_array($address));
		$this -> assertEquals(count($address), 2);
		$this -> assertEquals("dimuthu@orangehrm.com", $address[0]);
		$this -> assertEquals("dsamarasekara@gmail.com", $address[1]);

		$address = $this -> hspMailNotification -> getEmployeeAddress('777');

		$this -> assertFalse(isset($address));
	}

	/**
	*
	*/
	public function testGetPaymentAcceptSubject() {
		$expected = 'HSP Notification - Payment Request Approved';
		$this -> assertEquals($expected, $this -> hspMailNotification -> _getPaymentAcceptSubject());
	 }

	/**
	*
	*/
	public function testGetPaymentAcceptMsg() {
		$result = $this -> hspMailNotification -> _getPaymentAcceptMsg("dimuthu", '2007-01-01', 'flu', 'person inquire', 150, '2007-02-01');
		$this->assertEquals(preg_match("/#firstName/", $result), 0);
		$this->assertEquals(preg_match("/##dateInquired/", $result), 0);
		$this->assertEquals(preg_match("/#employee/", $result), 0);
		$this->assertEquals(preg_match("/#expenseDesc/", $result), 0);
		$this->assertEquals(preg_match("/#personInquiring/", $result), 0);
		$this->assertEquals(preg_match("/#amount/", $result), 0);
		$this->assertEquals(preg_match("/#paidDate/", $result), 0);
	 }

	/**
	*
	*/
	public function testGetPaymentDenySubject() {
		$expected = 'HSP Notification - Payment Request Denied';
		$this -> assertEquals($expected, $this -> hspMailNotification -> _getPaymentDenySubject());
	 }

	/**
	*
	*/
	public function testGetPaymentDeleteSubject() {
		$expected = 'HSP Notification - Payment Request Deleted';
		$this -> assertEquals($expected, $this -> hspMailNotification -> _getPaymentRequestDeleteSubject());
	}

	public static function main() {
        	require_once 'PHPUnit/TextUI/TestRunner.php';

        	$suite  = new PHPUnit_Framework_TestSuite('HspMailNotficationTest');
        	$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


}
?>
