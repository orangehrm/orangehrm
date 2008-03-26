<?php
/**
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
 * @copyright 2006 OrangeHRM Inc., http://www.orangehrm.com
 */

require_once ROOT_PATH . '/lib/common/htmlMimeMail5/htmlMimeMail5.php';
require_once ROOT_PATH . '/lib/models/eimadmin/EmailConfiguration.php';
require_once ROOT_PATH . '/lib/models/eimadmin/EmailNotificationConfiguration.php';

require_once ROOT_PATH . '/lib/models/hrfunct/EmpInfo.php';

require_once ROOT_PATH . '/lib/models/benefits/HspPaymentRequest.php';


class HspMailNotification {
	const HSP_PAYMENT_REQUEST_NOTIFICATION_TEMPLATE_SUBJECT = '/templates/benefits/mail/payment_request_subject.txt';
	const HSP_PAYMENT_ACCEPT_NOTIFICATION_TEMPLATE_SUBJECT = '/templates/benefits/mail/payment_accept_subject.txt';
	const HSP_PAYMENT_DENY_NOTIFICATION_TEMPLATE_SUBJECT = '/templates/benefits/mail/payment_deny_subject.txt';
	const HSP_PAYMENT_REQUEST_DELETE_NOTIFICATION_TEMPLATE_SUBJECT = '/templates/benefits/mail/payment_request_delete_subject.txt';
	const HSP_ADMIN_HALTE_PALN_NOTIFICATION_TEMPLATE_SUBJECT = '/templates/benefits/mail/admin_halt_plan_subject.txt';
	const HSP_ESS_HALTE_PALN_NOTIFICATION_TEMPLATE_SUBJECT = '/templates/benefits/mail/ess_halts_plan_subject.txt';

	const HSP_PAYMENT_REQUEST_NOTIFICATION_TEMPLATE_MESSAGE = '/templates/benefits/mail/payment_request.txt';
	const HSP_PAYMENT_ACCEPT_NOTIFICATION_TEMPLATE_MESSAGE = '/templates/benefits/mail/payment_accept.txt';
	const HSP_PAYMENT_DENY_NOTIFICATION_TEMPLATE_MESSAGE = '/templates/benefits/mail/payment_deny.txt';
	const HSP_PAYMENT_REQUEST_DELETE_NOTIFICATION_TEMPLATE_MESSAGE = '/templates/benefits/mail/payment_request_delete.txt';
	const HSP_ADMIN_HALT_PLAN_NOTIFICATION_TEMPLATE_MESSAGE = '/templates/benefits/mail/admin_halt_plan.txt';
	const HSP_ESS_HALT_PLAN_NOTIFICATION_TEMPLATE_MESSAGE = '/templates/benefits/mail/ess_halts_plan.txt';

	const HSP_PAYMENT_REQUEST_NOTIFICATION_VARIABLE_EMPLOYEE = 'employee';
	const HSP_PAYMENT_REQUEST_NOTIFICATION_VARIABLE_LINK = 'link';
	const HSP_PAYMENT_REQUEST_NOTIFICATION_VARIABLE_DATEINQ = 'dateInquired';
	const HSP_PAYMENT_REQUEST_NOTIFICATION_VARIABLE_DESC = 'expenseDesc';

	const HSP_PAYMENT_ACCPTE_NOTIFICATION_VARIABLE_FIRSTNAME = 'firstName';
	const HSP_PAYMENT_ACCPTE_NOTIFICATION_VARIABLE_DATEINQUIRED = 'dateInquired';
	const HSP_PAYMENT_ACCPTE_NOTIFICATION_VARIABLE_EMPLOYEE = 'employee';
	const HSP_PAYMENT_ACCPTE_NOTIFICATION_VARIABLE_DESC = 'expenseDesc';
	const HSP_PAYMENT_ACCPTE_NOTIFICATION_VARIABLE_PERSONINQ = 'personInquiring';
	const HSP_PAYMENT_ACCPTE_NOTIFICATION_VARIABLE_AMOUNT = 'amount';
	const HSP_PAYMENT_ACCPTE_NOTIFICATION_VARIABLE_PAIDDATE = 'paidDate';
	const HSP_PAYMENT_ACCPTE_NOTIFICATION_VARIABLE_LINK = 'link';

	const HSP_ADMIN_HALT_PLAN_NOTIFICATION_EMPLOYEE = 'employee';
	const HSP_ADMIN_HALT_PLAN_NOTIFICATION_HALTED_DATE = 'haltedDate';
	const HSP_ESS_HALT_PLAN_NOTIFICATION_EMPLOYEE = 'employee';
	const HSP_ESS_HALT_PLAN_NOTIFICATION_HALTED_DATE = 'haltedDate';

	private $mailer;
	private $emailConfig;
	private $emailNotificationConfig;
	private $mailType;

	/**
	* Init htmlMimeMail5, EmailConfiguration, EmailNotificationConfiguration.
	* Set smtp params, sendmailpath, from.
	**/
	public function __construct() {
		$this -> mailer = new htmlMimeMail5();
		$this -> emailConfig = new EmailConfiguration();
		$this -> emailNotificationConfig = new EmailNotificationConfiguration();
		$this -> mailType = $this -> emailConfig -> getMailType();

		$auth = true;
		if ($this -> emailConfig -> getSmtpUSer() == '') {
			$auth = false;
		}
		$this -> mailer -> setSmtpParams($this -> emailConfig -> getSmtpHost(), $this -> emailConfig -> getSmtpPort(), null, $auth, $this -> emailConfig -> getSmtpUser(), $this -> emailConfig -> getSmtpPass());

		$this->mailer->setSendmailPath($this -> emailConfig -> getSendmailPath());

		$this->mailer->setFrom("OrangeHRM <{$this -> emailConfig -> getMailAddress()}>");
	}

	/**
	* send email to hr admin group notifying hsp payment request made by a employee.
	* @param HspPaymentRequest $hspPaymentRequest model/HspPaymentRequest bean
	* @return boolean $success true if successfuly send or null otherwise
	**/
	public function sendHspPaymentRequestNotifications($hspPaymentRequest, $link) {
		$toAddress = null;
		$subject = null;
		$msg = null;
		$empId = null;
		$empName = null;
		$logMsg = '';

		$empId = $hspPaymentRequest -> getEmployeeId();
		$empName = $this -> _getEmployeeName($empId);

		$emailNotificationTypeId = EmailNotificationConfiguration::EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_HSP;
		$toAddress = $this -> _getNotificationAddress($emailNotificationTypeId);

		$subject = $this -> _getPaymentRequestSubject($empName);
		$msg = $this -> _getPaymentRequestMsg($empName, $link);

		$success = $this -> _sendEmail($msg, $subject, $toAddress);

		return $success;
	}

	/**
	 * Send Hsp payment notification to the user.
	 * @param HspPaymentRequest
	 * @return boolean true if success
	 */
	 public function sendHspPaymentAcceptNotification($hspPaymentRequest, $link) {
		$toAddress = null;
		$ccAddress = null;
		$subject = null;
		$msg = null;
		$empId = null;
		$empName = null;
		$dateInquired = null;
		$expenseDescription = null;
		$personInquiring = null;
		$amount = null;
		$paidDate = null;
		$logMsg = '';

		$empId = $hspPaymentRequest -> getEmployeeId();
		$empName = $this -> _getEmployeeName($empId);

		$toAddress = $this-> getEmployeeAddress($empId);

		$empDetail = $hspPaymentRequest -> fetchHspRequestDetails($hspPaymentRequest->getId());
		if(isset($empDetail) && is_array($empDetail)){
		$dateInquired = $empDetail[0];
		$expenseDescription = $empDetail[1];
		$personInquiring = $empDetail[2];
		$amount = $empDetail[3];
		}

		$paidDate = $hspPaymentRequest -> getDatePaid();

		$emailNotificationTypeId = EmailNotificationConfiguration::EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_HSP;
		$ccAddress = $this -> _getNotificationAddress($emailNotificationTypeId);

		$subject = $this -> _getPaymentAcceptSubject();
		$msg = $this -> _getPaymentAcceptMsg($empName, $dateInquired, $expenseDescription, $personInquiring, $amount, $paidDate, $link);

		$success = $this -> _sendEmail($msg, $subject, $toAddress, $ccAddress);

		return $success;
	 }

	 /**
	 * Send Hsp payment notification to the user.
	 * @param HspPaymentRequest
	 * @return boolean true if success
	 */
	 public function sendHspPaymentDenyNotification($hspPaymentRequest) {
		$toAddress = null;
		$ccAddress = null;
		$subject = null;
		$msg = null;
		$empId = null;
		$empName = null;
		$dateInquired = null;
		$expenseDescription = null;
		$personInquiring = null;
		$amount = null;
		$paidDate = null;
		$logMsg = '';

		$empId = $hspPaymentRequest -> getEmployeeId();
		$empName = $this -> _getEmployeeName($empId);

		$toAddress = $this-> getEmployeeAddress($empId);

		$empDetail = $hspPaymentRequest -> fetchHspRequestDetails($hspPaymentRequest->getId());
		if(isset($empDetail) && is_array($empDetail)){
		$dateInquired = $empDetail[0];
		$expenseDescription = $empDetail[1];
		$personInquiring = $empDetail[2];
		$amount = $empDetail[3];
		}

		$emailNotificationTypeId = EmailNotificationConfiguration::EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_HSP;
		$ccAddress = $this -> _getNotificationAddress($emailNotificationTypeId);

		$subject = $this -> _getPaymentDenySubject();
		$msg = $this -> _getPaymentDenyMsg($empName, $dateInquired, $expenseDescription, $personInquiring, $amount);

		$success = $this -> _sendEmail($msg, $subject, $toAddress, $ccAddress);

		return $success;
	 }

	 /**
	 * Send Hsp payment notification to the user.
	 * @param HspPaymentRequest
	 * @return boolean true if success
	 */
	 public function sendHspPaymentRequestDeleteNotification($hspPaymentRequest) {
		$toAddress = null;
		$ccAddress = null;
		$subject = null;
		$msg = null;
		$empId = null;
		$empName = null;
		$dateInquired = null;
		$expenseDescription = null;
		$personInquiring = null;
		$amount = null;
		$paidDate = null;
		$logMsg = '';

		$empId = $hspPaymentRequest -> getEmployeeId();
		$empName = $this -> _getEmployeeName($empId);

		$toAddress = $this-> getEmployeeAddress($empId);

		$empDetail = $hspPaymentRequest -> fetchHspRequestDetails($hspPaymentRequest->getId());
		if(isset($empDetail) && is_array($empDetail)){
		$dateInquired = $empDetail[0];
		$expenseDescription = $empDetail[1];
		$personInquiring = $empDetail[2];
		$amount = $empDetail[3];
		}

		$paidDate = $hspPaymentRequest -> getDatePaid();

		$emailNotificationTypeId = EmailNotificationConfiguration::EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_HSP;
		$ccAddress = $this -> _getNotificationAddress($emailNotificationTypeId);

		$subject = $this -> _getPaymentRequestDeleteSubject();
		$msg = $this -> _getPaymentDeleteRequestMsg($empName, $dateInquired, $expenseDescription, $personInquiring, $amount, $paidDate);

		$success = $this -> _sendEmail($msg, $subject, $toAddress, $ccAddress);

		return $success;
	 }

	 public function sendHspPlanHaltedByHRAdminNotification($hsp) {
	 	$empId = $hsp->getEmployeeId();
	 	$empName = $this -> _getEmployeeName($empId);
	 	$toAdd = $this->getEmployeeAddress($empId);
	 	$haltedDate = date('Y-m-d');

	 	$emailNotificationTypeId = EmailNotificationConfiguration::EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_HSP;
		$ccAddress = $this -> _getNotificationAddress($emailNotificationTypeId);

		$subject = $this -> _getAdminHaltePlanSubject();
		$msg = $this -> _getAdminHaltedPlanMsg($empName, $haltedDate);

		$success = $this -> _sendEmail($msg, $subject, $toAdd[0], $ccAddress[0]);

		return $success;
	 }

	 public function sendHspPlanHaltedByHRAdminOnRequestNotification($hsp) {
	 	$empId = $hsp->getEmployeeId();
	 	$empName = $this -> _getEmployeeName($empId);
	 	$toAdd = $this->getEmployeeAddress($empId);
	 	$haltedDate = date('Y-m-d');

	 	$emailNotificationTypeId = EmailNotificationConfiguration::EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_HSP;
		$ccAddress = $this -> _getNotificationAddress($emailNotificationTypeId);

		$subject = $this -> _getAdminHaltePlanSubject();
		$msg = $this -> _getAdminHaltedPlanMsg($empName, $haltedDate);

		$success = $this -> _sendEmail($msg, $subject, $toAdd[0], $ccAddress[0]);

		return $success;
	}

	 public function sendHspPlanHaltRequestedByESSNotification($hsp) {
	 	$empId = $hsp -> getEmployeeId();
	 	$empName = $this -> _getEmployeeName($empId);
	 	$toCC = $this -> getEmployeeAddress($empId);
	 	$requestedDate = date('Y-m-d');
	 	$haltedDate = date('Y-m-d');

	 	$emailNotificationTypeId = EmailNotificationConfiguration::EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_HSP;
		$toAdd = $this -> _getNotificationAddress($emailNotificationTypeId);

		$subject = $this -> _getEssHaltePlanSubject();
		$msg = $this -> _getEssHaltedPlanMsg($empName, $haltedDate);

		$success = $this -> _sendEmail($msg, $subject, $toAdd, $toCC);

		return $success;
	 }

	/**
	* Get employee name using firstname and lastName
	* @param int $empId
	* @return string name first and last names of the employee
	*/
	public function _getEmployeeName($empId) {
		$empName = '';
		$empInfoObj = new EmpInfo();
		$empInfo = $empInfoObj -> filterEmpMain($empId);

		if(isset($empInfo[0][2])) {
			$empName = $empInfo[0][2];
		}
		if(isset($empInfo[0][1])) {
			$empName .= " " . $empInfo[0][1];
		}
		return $empName;
	}

	/**
	 * Get employee email address
	 * @return string array empEmail if not available return null
	 */
	 public function getEmployeeAddress($empId) {
	 	$empEmail = null;
	 	$empInfo = new EmpInfo();
	 	$empDetial = $empInfo -> filterEmpContact($empId);
	 	if(isset($empDetial[0][10])) {
	 		$empEmail = array($empDetial[0][10]);
	 	}
	 	if(isset($empDetial[0][11])) {
	 		$empEmail[] = $empDetial[0][11];
	 	}

	 	return $empEmail;
	 }

	/**
	* Get email notification address from EmailNotificationConfiguration using notification type
	*
	* @param int $emailNotificationTypeId
	* @return string array of email addresses
	*/
	public function _getNotificationAddress($emailNotificationTypeId) {
		$notificationAddresses = $this -> emailNotificationConfig -> fetchMailNotifications($emailNotificationTypeId);
		if (is_array($notificationAddresses)) {
			return $notificationAddresses; //implode(', ', $notificationAddresses);
		}else {
			return $notificationAddresses;
		}
	}

	/**
	* Get subject for hsp payment request. Get template from template path replace employee name
	* @param string $employee name of the employee
	* @return string $subject
	*/
	public function _getPaymentRequestSubject($employee) {
		$subjectTemp = file_get_contents(ROOT_PATH . self::HSP_PAYMENT_REQUEST_NOTIFICATION_TEMPLATE_SUBJECT);
		return $this -> _getMailSubject($subjectTemp, $employee);
	}

	/**
	 *
	 */
	 public function _getPaymentAcceptSubject() {
		$subject = file_get_contents(ROOT_PATH . self::HSP_PAYMENT_ACCEPT_NOTIFICATION_TEMPLATE_SUBJECT);
		$subject = preg_replace('/\n/', '', $subject);
		return trim($subject);
	 }


	/**
	  *
	  */
	  public function _getPaymentDenySubject() {
		$subject = file_get_contents(ROOT_PATH . self::HSP_PAYMENT_DENY_NOTIFICATION_TEMPLATE_SUBJECT);
		$subject = preg_replace('/\n/', '', $subject);
		return trim($subject);
	  }

	 /**
	  *
	  */
	  public function _getPaymentRequestDeleteSubject() {
		$subject = file_get_contents(ROOT_PATH . self::HSP_PAYMENT_REQUEST_DELETE_NOTIFICATION_TEMPLATE_SUBJECT);
		$subject = preg_replace('/\n/', '', $subject);
		return trim($subject);
	  }

	  private function _getAdminHaltePlanSubject() {
	  	$subject = file_get_contents(ROOT_PATH . self::HSP_ADMIN_HALTE_PALN_NOTIFICATION_TEMPLATE_SUBJECT);
		$subject = preg_replace('/\n/', '', $subject);
		return trim($subject);
	  }

	private function _getEssHaltePlanSubject() {
	  	$subject = file_get_contents(ROOT_PATH . self::HSP_ESS_HALTE_PALN_NOTIFICATION_TEMPLATE_SUBJECT);
		$subject = preg_replace('/\n/', '', $subject);
		return trim($subject);
	  }
	/**
	 * Get the mail subject from given template
	 *
	 * @param string $template Mail subject template file
	 * @param string $employee The name of the employee
	 *
	 * @return string Mail subject from the given file, with parameters replaced
	 */
	private function _getMailSubject($template, $employee) {

		$pattern = array('/#'. self::HSP_PAYMENT_REQUEST_NOTIFICATION_VARIABLE_EMPLOYEE.'/', '/\n/');

		if (!isset($employee) || !strcmp($employee, '')) {
			$employee = 'Employee';
		}
		$replace = array($employee, "");
		$subject = preg_replace($pattern, $replace, $template);

		return trim($subject);
	}

	/**
	* get email message body for the hsp payment request.
	* @param string $empName combine of first and last name
	* @param string $dateInquired
	* @param string $expenceDescription
	* @return string $msg body of the email msg
	*/
	public function _getPaymentRequestMsg($empName, $link) {
		$msgTemp = file_get_contents(ROOT_PATH . self::HSP_PAYMENT_REQUEST_NOTIFICATION_TEMPLATE_MESSAGE);

		$pattern = array('/#'.self::HSP_PAYMENT_REQUEST_NOTIFICATION_VARIABLE_EMPLOYEE.'/', '/#'.self::HSP_PAYMENT_REQUEST_NOTIFICATION_VARIABLE_LINK.'/');

		$replace = array($empName, $link);

		$msg = preg_replace($pattern, $replace, $msgTemp);

	return trim($msg, "\n \t\r");
	}

	/**
	* get email message body for the hsp payment accpected by hr admin.
	* @param string $empName combine of first and last name
	* @param string $dateInquired
	* @param string $expenceDescription
	* @param string $personInquring
	* @param int amount
	* @param string paidDate
	* @return string $msg body of the email msg
	*/
	public function _getPaymentAcceptMsg($empName, $dateInquired, $expenseDescription, $personInquring, $amount, $paidDate, $link) {
		$msgTemp = file_get_contents(ROOT_PATH . self::HSP_PAYMENT_ACCEPT_NOTIFICATION_TEMPLATE_MESSAGE);

		$pattern = array('/#'.self::HSP_PAYMENT_ACCPTE_NOTIFICATION_VARIABLE_FIRSTNAME.'/',
		 '/#'.self::HSP_PAYMENT_ACCPTE_NOTIFICATION_VARIABLE_DATEINQUIRED.'/',
		  '/#'.self::HSP_PAYMENT_ACCPTE_NOTIFICATION_VARIABLE_EMPLOYEE.'/',
		  '/#'.self::HSP_PAYMENT_ACCPTE_NOTIFICATION_VARIABLE_DESC.'/',
		  '/#'.self::HSP_PAYMENT_ACCPTE_NOTIFICATION_VARIABLE_PERSONINQ.'/',
		  '/#'.self::HSP_PAYMENT_ACCPTE_NOTIFICATION_VARIABLE_AMOUNT.'/',
		  '/#'.self::HSP_PAYMENT_ACCPTE_NOTIFICATION_VARIABLE_PAIDDATE.'/',
		  '/#'.self::HSP_PAYMENT_ACCPTE_NOTIFICATION_VARIABLE_LINK.'/');

		$empNameAry = explode(' ', $empName);
		if(isset($empNameAry) && is_array($empNameAry)) {
			$firstName = $empNameAry[0];
		}else {
			$firstName = $empName;
		}

		$replace = array($firstName, $dateInquired, $empName, $expenseDescription,
		$personInquring, $amount, $paidDate, $link);

		$msg = preg_replace($pattern, $replace, $msgTemp);

	return trim($msg, "\n \t\r");
	}

	/**
	* get email message body for the hsp payment accpected by hr admin.
	* @param string $empName combine of first and last name
	* @param string $dateInquired
	* @param string $expenceDescription
	* @param string $personInquring
	* @param int amount
	* @param string paidDate
	* @return string $msg body of the email msg
	*/
	public function _getPaymentDenyMsg($empName, $dateInquired, $expenseDescription, $personInquring, $amount) {
		$msgTemp = file_get_contents(ROOT_PATH . self::HSP_PAYMENT_DENY_NOTIFICATION_TEMPLATE_MESSAGE);

		$pattern = array('/#'.self::HSP_PAYMENT_ACCPTE_NOTIFICATION_VARIABLE_FIRSTNAME.'/',
		 '/#'.self::HSP_PAYMENT_ACCPTE_NOTIFICATION_VARIABLE_DATEINQUIRED.'/',
		  '/#'.self::HSP_PAYMENT_ACCPTE_NOTIFICATION_VARIABLE_EMPLOYEE.'/',
		  '/#'.self::HSP_PAYMENT_ACCPTE_NOTIFICATION_VARIABLE_DESC.'/',
		  '/#'.self::HSP_PAYMENT_ACCPTE_NOTIFICATION_VARIABLE_PERSONINQ.'/',
		  '/#'.self::HSP_PAYMENT_ACCPTE_NOTIFICATION_VARIABLE_AMOUNT.'/');

		$empNameAry = explode(' ', $empName);
		if(isset($empNameAry) && is_array($empNameAry)) {
			$firstName = $empNameAry[0];
		}else {
			$firstName = $empName;
		}

		$replace = array($firstName, $dateInquired, $empName, $expenseDescription,
		$personInquring, $amount);

		$msg = preg_replace($pattern, $replace, $msgTemp);

	return trim($msg, "\n \t\r");
	}

	/**
	* get email message body for the hsp payment accpected by hr admin.
	* @param string $empName combine of first and last name
	* @param string $dateInquired
	* @param string $expenceDescription
	* @param string $personInquring
	* @param int amount
	* @param string paidDate
	* @return string $msg body of the email msg
	*/
	public function _getPaymentDeleteRequestMsg($empName, $dateInquired, $expenseDescription, $personInquring, $amount) {
		$msgTemp = file_get_contents(ROOT_PATH . self::HSP_PAYMENT_REQUEST_DELETE_NOTIFICATION_TEMPLATE_MESSAGE);

		$pattern = array('/#'.self::HSP_PAYMENT_ACCPTE_NOTIFICATION_VARIABLE_FIRSTNAME.'/',
		 '/#'.self::HSP_PAYMENT_ACCPTE_NOTIFICATION_VARIABLE_DATEINQUIRED.'/',
		  '/#'.self::HSP_PAYMENT_ACCPTE_NOTIFICATION_VARIABLE_EMPLOYEE.'/',
		  '/#'.self::HSP_PAYMENT_ACCPTE_NOTIFICATION_VARIABLE_DESC.'/',
		  '/#'.self::HSP_PAYMENT_ACCPTE_NOTIFICATION_VARIABLE_PERSONINQ.'/',
		  '/#'.self::HSP_PAYMENT_ACCPTE_NOTIFICATION_VARIABLE_AMOUNT.'/');

		$empNameAry = explode(' ', $empName);
		if(isset($empNameAry) && is_array($empNameAry)) {
			$firstName = $empNameAry[0];
		}else {
			$firstName = $empName;
		}

		$replace = array($firstName, $dateInquired, $empName, $expenseDescription,
		$personInquring, $amount);

		$msg = preg_replace($pattern, $replace, $msgTemp);

		return trim($msg, "\n \t\r");
	}

	private function _getAdminHaltedPlanMsg($empName, $haltedDate) {
		$msgTemp = file_get_contents(ROOT_PATH . self::HSP_ADMIN_HALT_PLAN_NOTIFICATION_TEMPLATE_MESSAGE);

 		$pattern = array('/#'.self::HSP_ADMIN_HALT_PLAN_NOTIFICATION_EMPLOYEE.'/',
		 '/#'.self::HSP_ADMIN_HALT_PLAN_NOTIFICATION_HALTED_DATE.'/');

		$empNameAry = explode(' ', $empName);
		if(isset($empNameAry) && is_array($empNameAry)) {
			$firstName = $empNameAry[0];
		}else {
			$firstName = $empName;
		}

		$replace = array($firstName, $haltedDate);

		$msg = preg_replace($pattern, $replace, $msgTemp);

		return trim($msg, "\n \t\r");
	}

	private function _getEssHaltedPlanMsg($empName, $haltedDate) {
		$msgTemp = file_get_contents(ROOT_PATH . self::HSP_ESS_HALT_PLAN_NOTIFICATION_TEMPLATE_MESSAGE);

 		$pattern = array('/#'.self::HSP_ESS_HALT_PLAN_NOTIFICATION_EMPLOYEE.'/',
		 '/#'.self::HSP_ESS_HALT_PLAN_NOTIFICATION_HALTED_DATE.'/');

		$empNameAry = explode(' ', $empName);
		if(isset($empNameAry) && is_array($empNameAry)) {
			$firstName = $empNameAry[0];
		}else {
			$firstName = $empName;
		}

		$replace = array($firstName, $haltedDate);

		$msg = preg_replace($pattern, $replace, $msgTemp);

		return trim($msg, "\n \t\r");
	}

	/**
	* Send email
	* @param string $msg message body
	* @param string $subject
	* @param string array $to
	* @param String array $cc
	* @return boolean $success
	*/
	private function _sendEmail($msg, $subject, $to, $cc = null) {
		$mailer = $this->mailer;
		$mailType = $this -> mailType;
		$mailer -> setText($msg);
		$mailer -> setSubject($subject);
		$success = true;

		$logMessage = date('r')." Sending {$subject} to";
		if (isset($to) && is_array($to)) {
			foreach($to as $toAdd) {
				$logMessage .= "\r\n".$toAdd;
			}
		}else if(isset($to) && !is_array($to)) {
			$to = array($to);
			$logMessage .= "\r\n".$to[0];
		}

		if(isset($cc) && is_array($cc)) {
			$mailer -> setCc(implode(', ', $cc));
		}

		if ((!is_array($to)) || (!@$mailer->send($to, $mailType))) {
			$logMessage .= " - FAILED \r\nReason(s):";
			$success = false;
			if (isset($mailer->errors)) {
				$logMessage .= "\r\n\t*\t".implode("\r\n\t*\t",$mailer->errors);
			}
		} else {
			$logMessage .= " - SUCCEEDED";
		}

		//$logPath = $this -> _getLogPath();

		//error_log($logMessage."\r\n", 3, $logPath."notification_mails.log");

		return $success;
	}

	/**
	* Get email log file path from email configuration
	* If not set return default log path
	* @return String $logPaath
	*/
	/*public function _getLogPath() {
		$logPath = $this -> emailConfig ->getLogPath();
		if (!empty($logPath) && isset($logPath)) {
			return $logPath;
		} else {
			return ROOT_PATH.'/lib/logs/';
		}
	}*/

}
?>
