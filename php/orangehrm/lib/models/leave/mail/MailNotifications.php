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

set_include_path(get_include_path() . PATH_SEPARATOR . ROOT_PATH . '/lib/common');

require_once ROOT_PATH . '/lib/common/Zend/Mail.php';
require_once ROOT_PATH . '/lib/common/Zend/Mail/Transport/Smtp.php';
require_once ROOT_PATH . '/lib/common/Zend/Mail/Transport/Sendmail.php';

require_once ROOT_PATH . '/lib/models/eimadmin/EmailConfiguration.php';
require_once ROOT_PATH . '/lib/models/eimadmin/EmailNotificationConfiguration.php';
require_once ROOT_PATH . '/lib/confs/sysConf.php';

require_once ROOT_PATH . '/lib/models/leave/Leave.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpInfo.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpRepTo.php';

require_once ROOT_PATH . '/lib/common/Language.php';

/**
 * Manages sending of mail notifications
 *
 */
class MailNotifications {

	/**
	 * Action constants
	 *
	 */
	const MAILNOTIFICATIONS_ACTION_APPLY = "apply";
	const MAILNOTIFICATIONS_ACTION_CANCEL = "cancel";
	const MAILNOTIFICATIONS_ACTION_REJECT = "reject";
	const MAILNOTIFICATIONS_ACTION_APPROVE = "approve";
	const MAILNOTIFICATIONS_ACTION_ASSIGN = "assign";
	const MAILNOTIFICATIONS_ACTION_SUPERVISOR_CANCEL = "supervisorCancel";

	/**
	 * Template file name constants
	 *
	 */
	const MAILNOTIFICATIONS_TEMPLATE_APPLY = 'supervisor/applied.txt';
	const MAILNOTIFICATIONS_TEMPLATE_CANCEL = "supervisor/cancelled.txt";
	const MAILNOTIFICATIONS_TEMPLATE_REJECT = "subordinate/rejected.txt";
	const MAILNOTIFICATIONS_TEMPLATE_APPROVE = "subordinate/approval.txt";
	const MAILNOTIFICATIONS_TEMPLATE_ASSIGN = "subordinate/assign.txt";
	const MAILNOTIFICATIONS_TEMPLATE_SUPERVISOR_CANCEL = "subordinate/cancelled.txt";

	/**
	 * Mail subject templates
	 */
	const MAILNOTIFICATIONS_TEMPLATE_APPLY_SUBJECT = 'supervisor/applied-subject.txt';
	const MAILNOTIFICATIONS_TEMPLATE_CANCEL_SUBJECT = 'supervisor/cancelled-subject.txt';
	const MAILNOTIFICATIONS_TEMPLATE_REJECT_SUBJECT = 'subordinate/rejected-subject.txt';
	const MAILNOTIFICATIONS_TEMPLATE_APPROVE_SUBJECT = 'subordinate/approval-subject.txt';
	const MAILNOTIFICATIONS_TEMPLATE_ASSIGN_SUBJECT = 'subordinate/assign-subject.txt';
	const MAILNOTIFICATIONS_TEMPLATE_SUPERVISOR_CANCEL_SUBJECT = 'subordinate/cancelled-subject.txt';

	/**
	 * Template variable identifier constants
	 *
	 */
	const MAILNOTIFICATIONS_IDENTIFIER = "#";
	const MAILNOTIFICATIONS_IDENTIFIER_GROUP = "#{.*}";

	/**
	 * Template variable constants
	 *
	 */
	const MAILNOTIFICATIONS_VARIABLE_SUPERVISOR = "supervisor";
	const MAILNOTIFICATIONS_VARIABLE_SUBORDINATE = "subordinate";
	const MAILNOTIFICATIONS_VARIABLE_LEAVECOUNT = "leavecount";


	/*
	 * Class atributes
	 **/
	private $leaveObjs;
	private $leaveRequestObj;
	private $action;

	private $templateFile;
	private $to;
	private $mail;
	private $subject;

	private $supervisorMail;
	private $subordinateMail;

	private $mailer;
	private $mailType;

	private $employeeIdLength;

	private $notificationTypeId;

	public function setLeaveObjs ($leaveObjs) {
		$this->leaveObjs = $leaveObjs;
	}

	public function getLeaveObjs () {
		return $this->leaveObjs;
	}

	public function setLeaveRequestObj ($leaveRequestObj) {
		$this->leaveRequestObj = $leaveRequestObj;
	}

	public function getLeaveRequestObj () {
		return $this->leaveRequestObj;
	}

	public function setAction($action) {
		$this->action = $action;
	}

	public function getAction() {
		return $this->action;
	}

	/**
	 * Constructor
	 *
	 * Serializes the object
	 *
	 */
		public function __construct() {
		$confObj = new EmailConfiguration();

		$this->mailType = $confObj->getMailType();
		if ($this->mailType == 'smtp') {

			$config = array();

			$authType = $confObj->getSmtpAuth();
			if ($authType != EmailConfiguration::EMAILCONFIGURATION_SMTP_AUTH_NONE) {
				$config['auth'] = strtolower($authType);
    			$config['username'] = trim($confObj->getSmtpUser());
    			$config['password'] = trim($confObj->getSmtpPass());
			}

			$security = $confObj->getSmtpSecurity();
			if ($security != EmailConfiguration::EMAILCONFIGURATION_SMTP_SECURITY_NONE) {
				$config['ssl'] = strtolower($security);
			}

			$config['port'] = trim($confObj->getSmtpPort());

			$transport = new Zend_Mail_Transport_Smtp($confObj->getSmtpHost(), $config);

		} else if ($this->mailType = 'sendmail') {
			$transport = new Zend_Mail_Transport_Sendmail();
		}

		Zend_Mail::setDefaultTransport($transport);
		$this->mailer = new Zend_Mail();
		$this->mailer->setFrom($confObj->getMailAddress(), "OrangeHRM");

		$sysConfObj = new sysConf();

		$this->employeeIdLength = $sysConfObj->getEmployeeIdLength();

	}

	public function __destruct() {
		//nothing to do
	}

	/**
	 * Sends the mail notification.
	 *
	 * If leaves are not filled it will try to fetch the leaves
	 * related to the leave request. All work is delegated to
	 * private functions.
	 *
	 * @return boolean Success
	 */
	public function send() {
		if (isset($this->leaveRequestObj)) {
			if (!isset($this->leaveObjs)) {
				$this->_preFetchLeaves();
			}
			return $this->_sendMail();
		} else if (isset($this->leaveObjs)) {
			$this->_refreshLeaves();
			return $this->_sendMail();
		}
		return false;
	}

	/**
	 * Mail sending method. This is the workhorse function
	 *
	 * @return unknown
	 */
	private function _sendMail() {
		$this->_buildMail();

		$mailer = $this->mailer;
		$mailer->setBodyText($this->mail);

		$mailer->setSubject($this->subject);
		$mailNotificationObj = new EmailNotificationConfiguration();
		$notificationAddresses = $mailNotificationObj->fetchMailNotifications($this->notificationTypeId, true);

		$logMessage = date('r')." Sending {$this->subject} to";

		if (isset($this->to) && is_array($this->to)) {
			foreach ($this->to as $to) {
				if ($to != "") {
                                    $mailer->addTo($to);
                                }
				$logMessage .= "\r\n".$to;
			}
		}

		if (isset($notificationAddresses) && is_array($notificationAddresses)) {

			if (is_array($this->to)) {
				$logMessage .= "\r\nCC to";
			}

			foreach ($notificationAddresses as $cc) {
					if (is_array($this->to)) {
						if ($cc != "") {
                                                    $mailer->addCc($cc);
                                                }
					} else {
						if ($cc != "") {
                                                    $mailer->addTo($cc);
                                                    $this->to = array();
                                                }
					}
					$logMessage .= "\r\n".$cc;
			}

		}

		if (!is_array($this->to)) {
			$logMessage .= " - FAILED \r\nReason: No to address set";
		} else {

			try {
				$mailer->send();
				$logMessage .= " - SUCCEEDED";
			} catch (Exception $e) {
				$errorMsg = $e->getMessage();
				if (isset($errorMsg)) {
					$logMessage .= " - FAILED \r\nReason: $errorMsg";
				}
			}
		}

		if (isset($confObj->logPath) && !empty($confObj->logPath)) {
			$logPath = $confObj->logPath;
		} else {
			$logPath = ROOT_PATH.'/lib/logs/';
		}

		error_log($logMessage."\r\n", 3, $logPath."notification_mails.log");

		return true;
	}

	/**
	 * Calls the correct mail builder according to the action
	 *
	 */
	private function _buildMail() {
		switch ($this->getAction()) {
			case self::MAILNOTIFICATIONS_ACTION_APPLY : $this->_applyMail();
														break;

			case self::MAILNOTIFICATIONS_ACTION_SUPERVISOR_CANCEL: // fall through to next case
			case self::MAILNOTIFICATIONS_ACTION_CANCEL : $this->_cancelMail();
														break;
			case self::MAILNOTIFICATIONS_ACTION_APPROVE : $this->_approveMail();
														break;
			case self::MAILNOTIFICATIONS_ACTION_REJECT : $this->_rejectMail();
														break;
			case self::MAILNOTIFICATIONS_ACTION_ASSIGN : $this->_assignMail();
														break;
		}
	}

	/**
	 * Builds leave approved notice
	 *
	 */
	private function _approveMail() {
		$this->notificationTypeId = EmailNotificationConfiguration::EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_LEAVE_APPROVED;

		$this->templateFile = file_get_contents(ROOT_PATH."/templates/leave/mails/".self::MAILNOTIFICATIONS_TEMPLATE_APPROVE);
		$txt = $this->templateFile;

		$leaveObjs = $this->getLeaveObjs();

		$txtArr = preg_split('/#\{(.*)\}/', $txt, null, PREG_SPLIT_DELIM_CAPTURE);

		$recordTxt = $txtArr[1];
		$recordArr = null;

		$fulldays = 0;

		foreach ($leaveObjs as $leaveObj) {
			if ($leaveObj->getLeaveStatus() == Leave::LEAVE_STATUS_LEAVE_APPROVED) {

				$leaveLength = $leaveObj->getLeaveLengthHours();

				$fulldays+=$leaveObj->getLeaveLengthDays();

				$duration = $leaveObj->getLeaveLengthHours();

				$date = $leaveObj->getLeaveDate();
				$type = $leaveObj->getLeaveTypeName();
				$comments = $leaveObj->getLeaveComments();

				$recordArr[] = preg_replace(array('/#date/', '/#type/', '/#duration/', '/#comments/'), array($date, $type, $duration, $comments), $recordTxt);
			}
		}

		$recordTxt = "";
		if (isset($recordArr)) {
			$recordTxt = join("\r\n", $recordArr);
		}

		$txt = $txtArr[0].$recordTxt.$txtArr[2];

		if (isset($leaveObjs[0])) {
			$employeeName = $leaveObjs[0]->getEmployeeName();
			$employeeId = $leaveObjs[0]->getEmployeeId();

			$this->_getAddresses($employeeId);

			$txt = preg_replace('/#'.self::MAILNOTIFICATIONS_VARIABLE_SUBORDINATE.'/', $employeeName, $txt);

			$this->subject = $this->_getMailSubject(self::MAILNOTIFICATIONS_TEMPLATE_APPROVE_SUBJECT,
													$employeeName, $fulldays);

			$this->to = $this->subordinateMail;
		}

		$this->mail = $txt;
	}

	/**
	 * Builds leave approved notice
	 *
	 */
	private function _rejectMail() {
		$this->notificationTypeId = EmailNotificationConfiguration::EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_LEAVE_REJECTED;

		$this->templateFile = file_get_contents(ROOT_PATH."/templates/leave/mails/".self::MAILNOTIFICATIONS_TEMPLATE_REJECT);
		$txt = $this->templateFile;

		$leaveObjs = $this->getLeaveObjs();

		$txtArr = preg_split('/#\{(.*)\}/', $txt, null, PREG_SPLIT_DELIM_CAPTURE);

		$recordTxt = $txtArr[1];
		$recordArr = null;

		$fulldays = 0;

		foreach ($leaveObjs as $leaveObj) {
			if ($leaveObj->getLeaveStatus() == Leave::LEAVE_STATUS_LEAVE_REJECTED) {

				$leaveLength = $leaveObj->getLeaveLengthHours();

				$fulldays+=$leaveObj->getLeaveLengthDays();

				$duration = $leaveObj->getLeaveLengthHours();

				$date = $leaveObj->getLeaveDate();
				$type = $leaveObj->getLeaveTypeName();
				$comments = $leaveObj->getLeaveComments();

				$recordArr[] = preg_replace(array('/#date/', '/#type/', '/#duration/', '/#comments/'), array($date, $type, $duration, $comments), $recordTxt);
			}
		}

		$recordTxt = "";
		if (isset($recordArr)) {
			$recordTxt = join("\r\n", $recordArr);
		}

		$txt = $txtArr[0].$recordTxt.$txtArr[2];

		if (isset($leaveObjs[0])) {
			$employeeName = $leaveObjs[0]->getEmployeeName();
			$employeeId = $leaveObjs[0]->getEmployeeId();

			$this->_getAddresses($employeeId);

			$txt = preg_replace('/#'.self::MAILNOTIFICATIONS_VARIABLE_SUBORDINATE.'/', $employeeName, $txt);

			$this->subject = $this->_getMailSubject(self::MAILNOTIFICATIONS_TEMPLATE_REJECT_SUBJECT,
													$employeeName, $fulldays);

			$this->to = $this->subordinateMail;
		}

		$this->mail = $txt;
	}

	/**
	 * builds leave applied notification
	 *
	 */
	private function _applyMail() {
		$this->notificationTypeId = EmailNotificationConfiguration::EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_LEAVE_PENDING_APPROVAL;

		$this->templateFile = file_get_contents(ROOT_PATH."/templates/leave/mails/".self::MAILNOTIFICATIONS_TEMPLATE_APPLY);
		$txt = $this->templateFile;

		$leaveObjs = $this->getLeaveObjs();

		$txtArr = preg_split('/#\{(.*)\}/', $txt, null, PREG_SPLIT_DELIM_CAPTURE);

		$recordTxt = $txtArr[1];
		$recordArr = null;

		$fulldays = 0;

		if (is_array($leaveObjs)) {
			foreach ($leaveObjs as $leaveObj) {
				if ($leaveObj->getLeaveStatus() == Leave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL) {

					$leaveLength = $leaveObj->getLeaveLengthHours();

					$fulldays+=$leaveObj->getLeaveLengthDays();

					$duration = $leaveObj->getLeaveLengthHours();

					$date = $leaveObj->getLeaveDate();
					$type = $leaveObj->getLeaveTypeName();
					$comments = $leaveObj->getLeaveComments();

					$recordArr[] = preg_replace(array('/#date/', '/#type/', '/#duration/', '/#comments/'), array($date, $type, $duration, $comments), $recordTxt);
				}
			}
		}

		$recordTxt = "";
		if (isset($recordArr)) {
			$recordTxt = join("\r\n", $recordArr);
		}

		$txt = $txtArr[0].$recordTxt.$txtArr[2];

		if (isset($leaveObjs[0])) {
			$employeeName = $leaveObjs[0]->getEmployeeName();
			$employeeId = $leaveObjs[0]->getEmployeeId();

			$this->_getAddresses($employeeId);

			$txt = preg_replace('/#'.self::MAILNOTIFICATIONS_VARIABLE_SUBORDINATE.'/', $employeeName, $txt);

			$this->subject = $this->_getMailSubject(self::MAILNOTIFICATIONS_TEMPLATE_APPLY_SUBJECT,
													$employeeName, $fulldays);

			$this->to = $this->supervisorMail;

			if (empty($this->supervisorMail)) {
				$mailNotificationObj = new EmailNotificationConfiguration();
				$this->to = $mailNotificationObj->fetchMailNotifications($this->notificationTypeId);
			}

		}

		$this->mail = $txt;
	}

	private function _assignMail() {
		$this->notificationTypeId = EmailNotificationConfiguration::EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_LEAVE_PENDING_APPROVAL;

		$this->templateFile = file_get_contents(ROOT_PATH."/templates/leave/mails/".self::MAILNOTIFICATIONS_TEMPLATE_ASSIGN);
		$txt = $this->templateFile;

		$leaveObjs = $this->getLeaveObjs();

 		$txtArr = preg_split('/#\{(.*)\}/', $txt, null, PREG_SPLIT_DELIM_CAPTURE);

		$recordTxt = $txtArr[1];
		$recordArr = null;

		$fulldays = 0;

		if (is_array($leaveObjs)) {

			foreach ($leaveObjs as $leaveObj) {
				if ($leaveObj->getLeaveStatus() == Leave::LEAVE_STATUS_LEAVE_APPROVED) {

					$leaveLength = $leaveObj->getLeaveLengthHours();

					$fulldays+=$leaveObj->getLeaveLengthDays();

					$duration = $leaveObj->getLeaveLengthHours();

					$date = $leaveObj->getLeaveDate();
					$type = $leaveObj->getLeaveTypeName();
					$comments = $leaveObj->getLeaveComments();

					$recordArr[] = preg_replace(array('/#date/', '/#type/', '/#duration/', '/#comments/'), array($date, $type, $duration, $comments), $recordTxt);
				}
			}
		}

		$recordTxt = "";
		if (isset($recordArr)) {
			$recordTxt = join("\r\n", $recordArr);
		}

		$txt = $txtArr[0].$recordTxt.$txtArr[2];

		if (isset($leaveObjs[0])) {
			$employeeName = $leaveObjs[0]->getEmployeeName();
			$employeeId = $leaveObjs[0]->getEmployeeId();

			$this->_getAddresses($employeeId);

			$txt = preg_replace('/#'.self::MAILNOTIFICATIONS_VARIABLE_SUBORDINATE.'/', $employeeName, $txt);

			$this->subject = $this->_getMailSubject(self::MAILNOTIFICATIONS_TEMPLATE_ASSIGN_SUBJECT,
													$employeeName, $fulldays);

			$this->to = $this->subordinateMail;
		}

		$this->mail = $txt;
	}

	/**
	 * Builds leave cancellation notice
	 *
	 */
	private function _cancelMail() {

		$this->notificationTypeId = EmailNotificationConfiguration::EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_LEAVE_CANCELLED;
		if ($this->action == self::MAILNOTIFICATIONS_ACTION_SUPERVISOR_CANCEL) {
			$this->templateFile = file_get_contents(ROOT_PATH."/templates/leave/mails/".self::MAILNOTIFICATIONS_TEMPLATE_SUPERVISOR_CANCEL);
		} else {
			$this->templateFile = file_get_contents(ROOT_PATH."/templates/leave/mails/".self::MAILNOTIFICATIONS_TEMPLATE_CANCEL);
		}

		$txt = $this->templateFile;

		$leaveObjs = $this->getLeaveObjs();

		$txtArr = preg_split('/#\{(.*)\}/', $txt, null, PREG_SPLIT_DELIM_CAPTURE);

		$recordTxt = $txtArr[1];
		$recordArr = null;

		$fulldays = 0;

		foreach ($leaveObjs as $leaveObj) {
			if ($leaveObj->getLeaveStatus() == Leave::LEAVE_STATUS_LEAVE_CANCELLED) {

				$leaveLength = $leaveObj->getLeaveLengthHours();

				$fulldays+=$leaveObj->getLeaveLengthDays();

				$duration = $leaveObj->getLeaveLengthHours();

				$date = $leaveObj->getLeaveDate();
				$type = $leaveObj->getLeaveTypeName();
				$comments = $leaveObj->getLeaveComments();

				$recordArr[] = preg_replace(array('/#date/', '/#type/', '/#duration/', '/#comments/'), array($date, $type, $duration, $comments), $recordTxt);
			}
		}

		$recordTxt = "";
		if (isset($recordArr)) {
			$recordTxt = join("\r\n", $recordArr);
		}

		$txt = $txtArr[0].$recordTxt.$txtArr[2];

		if (isset($leaveObjs[0])) {

			$employeeId = $leaveObjs[0]->getEmployeeId();

			$empInfoObj = new EmpInfo();
            $employeeName = $empInfoObj->getFullName($employeeId);

			$this->_getAddresses($employeeId);

			$txt = preg_replace('/#'.self::MAILNOTIFICATIONS_VARIABLE_SUBORDINATE.'/', $employeeName, $txt);

			if ($this->action == self::MAILNOTIFICATIONS_ACTION_SUPERVISOR_CANCEL) {
				$subjectTemplate = self::MAILNOTIFICATIONS_TEMPLATE_SUPERVISOR_CANCEL_SUBJECT;
				$this->to = $this->subordinateMail;
			} else {
				$subjectTemplate = self::MAILNOTIFICATIONS_TEMPLATE_CANCEL_SUBJECT;
				$this->to = $this->supervisorMail;
			}
			$this->subject = $this->_getMailSubject($subjectTemplate,
													$employeeName, $fulldays);
		}

		$this->mail = $txt;
	}

	/**
	 * Fetches the leaves of the leave request in the object
	 *
	 */
	private function _preFetchLeaves() {
		$leaveObj = new Leave();
		$leaveRequestObj = $this->getLeaveRequestObj();

		$leaveObjs = $leaveObj->retrieveLeave($leaveRequestObj->getLeaveRequestId());

		$this->setLeaveObjs($leaveObjs);
	}

	/**
	 * Refresh leave objects from the database, since leave objects extracted from request are
	 * missing some information like dates and leave lengths.
	 */
	private function _refreshLeaves() {

		$leaveObjs = array();
		foreach ($this->leaveObjs as $leaveObj) {
			$leave = $leaveObj->retrieveIndividualLeave($leaveObj->getLeaveId());
			$leaveObjs[] = $leave[0];
		}
		$this->leaveObjs = $leaveObjs;
	}

	/**
	 * Fetch required mail addresses related to the employee
	 *
	 * @param integer $employeeId - Employee ID
	 */
	private function _getAddresses($employeeId) {
		$this->subordinateMail = array($this->_getEmpAddress(str_pad($employeeId, $this->employeeIdLength)));

		$empRepToObj1 = new EmpRepTo();

		$supInfo = $empRepToObj1->getEmpSup(str_pad($employeeId, $this->employeeIdLength, "0", STR_PAD_LEFT));

		$supAddr = null;

		if (isset($supInfo) && is_array($supInfo)) {
			foreach ($supInfo as $supervisor) {
				$supAddr[] = $this->_getEmpAddress($supervisor[1]);
			}
		}

		$this->supervisorMail = $supAddr;
	}

	/**
	 * Fetch the mail address of the employee
	 *
	 * @param integer $employeeId - Employee ID
	 * @return String E-Mail
	 */
	private function _getEmpAddress($employeeId) {
		$empInfoObj = new EmpInfo();

		$empInfo = $empInfoObj->filterEmpContact($employeeId);

		if (isset($empInfo[0][10])) {
			return $empInfo[0][10];
		}

		return null;
	}

	/**
	 * Get the leave count as a string considering half days.
	 *
	 * @param interger $fulldays - Number of full days
	 * @param integer $halfdays - Number of half days
	 * @return String Number of leave days as a string (eg: 2 1/2 or 1/2 )
	 */
	private function _getLeaveCountStr($fulldays, $halfdays) {

		$fulldays += floor($halfdays / 2);
		$halfdayStr = ($halfdays % 2 == 1) ? " 1/2" : "";
		$fulldayStr = ($fulldays > 0) ? sprintf("%d", $fulldays) : "";

		$leaveStr = sprintf("%s%s", $fulldayStr, $halfdayStr);
		return $leaveStr;
	}

	/**
	 * Get the description string for the leave length
	 * (Half day / Morning etc. )
	 *
	 * @param integer $leaveLength - The leave length constant
	 *
	 * @return String Leave length description
	 */
	private function _getLeaveLengthDesc($leaveLength) {

		$lan = new Language();
		require ROOT_PATH . '/language/default/lang_default_full.php';
		require ($lan->getLangPath("full.php"));

		$desc = "";
		if ($leaveLength == Leave::LEAVE_LENGTH_FULL_DAY) {
			$desc = $lang_Leave_Common_FullDay;
		} else if ($leaveLength == Leave::LEAVE_LENGTH_HALF_DAY_MORNING) {
			$desc = $lang_Leave_Common_HalfDayMorning;
		} else if ($leaveLength == Leave::LEAVE_LENGTH_HALF_DAY_AFTERNOON) {
			$desc = $lang_Leave_Common_HalfDayAfternoon;
		}
		return $desc;
	}

	/**
	 * Get the mail subject from given template
	 *
	 * @param string $template Mail subject template file
	 * @param string $subordinate The name of the employee applying for leave
	 * @param int    $leaveCount The number of days of leave
	 *
	 * @return string Mail subject from the given file, with parameters replaced
	 */
	private function _getMailSubject($template, $subordinate, $leaveCount) {

		$subject = file_get_contents(ROOT_PATH."/templates/leave/mails/".$template);

		$pattern = array('/#'.self::MAILNOTIFICATIONS_VARIABLE_LEAVECOUNT.'/',
                          '/#'.self::MAILNOTIFICATIONS_VARIABLE_SUBORDINATE.'/',
                          "/\n/");
		$replace = array($leaveCount, $subordinate, "");
		$subject = preg_replace($pattern, $replace, $subject);

		return $subject;
	}
}

?>
