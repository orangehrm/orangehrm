<?php
/**
 *
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures 
 * all the essential functionalities required for any enterprise. 
 * Copyright (C) 2006 hSenid Software International Pvt. Ltd, http://www.hsenid.com
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
 * @copyright 2006 hSenid Software International Pvt. Ltd, http://www.hsenid.com
 */

require_once ROOT_PATH . '/lib/common/htmlMimeMail5/htmlMimeMail5.php';
require_once ROOT_PATH . '/lib/models/eimadmin/EmailConfiguration.php';
require_once ROOT_PATH . '/lib/confs/sysConf.php';

require_once ROOT_PATH . '/lib/models/leave/Leave.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpInfo.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpRepTo.php';

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
	
	/**
	 * Template file name constants
	 *
	 */
	const MAILNOTIFICATIONS_TEMPLATE_APPLY = 'supervisor/applied.txt';
	const MAILNOTIFICATIONS_TEMPLATE_CANCEL = "supervisor/cancelled.txt";
	const MAILNOTIFICATIONS_TEMPLATE_REJECT = "subordinate/rejected.txt";
	const MAILNOTIFICATIONS_TEMPLATE_APPROVE = "subordinate/approval.txt";
	
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
		
		$this->mailer = new htmlMimeMail5();
		
		$this->mailer->setSMTPParams($confObj->getSmtpHost(), $confObj->getSmtpPort(), null, true, $confObj->getSmtpUser(), $confObj->getSmtpPass());
				
		$this->mailer->setSendmailPath($confObj->getSendmailPath());
		
		$this->mailer->setFrom("OrangeHRM <{$confObj->getMailAddress()}>");
		
		$sysConfObj = new sysConf();
		
		$this->employeeIdLength = $sysConfObj->getEmployeeIdLength();
		
		$this->mailType = $confObj->getMailType();
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
		$mailer->setText($this->mail);
		
		$mailer->setSubject($this->subject);
		
		$logMessage = date('r')." Sending {$this->subject} to";
		
		if (isset($this->to) && is_array($this->to)) {
			foreach ($this->to as $to) {
				$logMessage .= "\r\n".$to;
			}
		}
		
		if ((!is_array($this->to)) || (!@$mailer->send($this->to, $this->mailType))) {			
			$logMessage .= " - FAILED \r\nReason(s):";
			if (isset($mailer->errors)) {
				$logMessage .= "\r\n\t*\t".implode("\r\n\t*\t",$mailer->errors);
			}
		} else {
			$logMessage .= " - SUCCEEDED";
		}
		
		error_log($logMessage."\r\n", 3, ROOT_PATH."/lib/logs/notification_mails.log");

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
			case self::MAILNOTIFICATIONS_ACTION_CANCEL : $this->_cancelMail();
														break;
			case self::MAILNOTIFICATIONS_ACTION_APPROVE : $this->_approveMail();
														break;
			case self::MAILNOTIFICATIONS_ACTION_REJECT : $this->_rejectMail();
														break;
		}		
	}
	
	/**
	 * Builds leave approved notice
	 *
	 */
	private function _approveMail() {		
		$this->templateFile = file_get_contents(ROOT_PATH."/templates/leave/mails/".self::MAILNOTIFICATIONS_TEMPLATE_APPROVE);
		$txt = $this->templateFile;
		
		$leaveObjs = $this->getLeaveObjs();		
		
		$txtArr = preg_split('/#\{(.*)\}/', $txt, null, PREG_SPLIT_DELIM_CAPTURE);
		
		$recordTxt = $txtArr[1];
		$recordArr = null;	
		
		$leaveCount = 0;	
		
		foreach ($leaveObjs as $leaveObj) {
			if ($leaveObj->getLeaveStatus() == Leave::LEAVE_STATUS_LEAVE_APPROVED) {
				
				$leaveCount++;
				
				$date = $leaveObj->getLeaveDate();
				$type = $leaveObj->getLeaveTypeName();
				$comments = $leaveObj->getLeaveComments();
				
				$recordArr[] = preg_replace(array('/#date/', '/#type/', '/#comments/'), array($date, $type, $comments), $recordTxt);
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
			
			$this->subject = "Leave Notification - Approved $leaveCount day(s)";
			
			$this->to = $this->subordinateMail;
		}	
		
		$this->mail = $txt;	
	}
	
	/**
	 * Builds leave approved notice
	 *
	 */
	private function _rejectMail() {		
		$this->templateFile = file_get_contents(ROOT_PATH."/templates/leave/mails/".self::MAILNOTIFICATIONS_TEMPLATE_REJECT);
		$txt = $this->templateFile;
		
		$leaveObjs = $this->getLeaveObjs();		
		
		$txtArr = preg_split('/#\{(.*)\}/', $txt, null, PREG_SPLIT_DELIM_CAPTURE);
		
		$recordTxt = $txtArr[1];
		$recordArr = null;	
		
		$leaveCount = 0;	
		
		foreach ($leaveObjs as $leaveObj) {
			if ($leaveObj->getLeaveStatus() == Leave::LEAVE_STATUS_LEAVE_REJECTED) {
				
				$leaveCount++;
				
				$date = $leaveObj->getLeaveDate();
				$type = $leaveObj->getLeaveTypeName();
				$comments = $leaveObj->getLeaveComments();
				
				$recordArr[] = preg_replace(array('/#date/', '/#type/', '/#comments/'), array($date, $type, $comments), $recordTxt);
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
			
			$this->subject = "Leave Notification - Rejected $leaveCount day(s)";
			
			$this->to = $this->subordinateMail;
		}	
		
		$this->mail = $txt;	
	}
	
	/**
	 * builds leave applied notification
	 *
	 */
	private function _applyMail() {		
		$this->templateFile = file_get_contents(ROOT_PATH."/templates/leave/mails/".self::MAILNOTIFICATIONS_TEMPLATE_APPLY);
		$txt = $this->templateFile;
		
		$leaveObjs = $this->getLeaveObjs();		
		
		$txtArr = preg_split('/#\{(.*)\}/', $txt, null, PREG_SPLIT_DELIM_CAPTURE);
		
		$recordTxt = $txtArr[1];
		$recordArr = null;	
		
		$leaveCount = 0;	
		
		foreach ($leaveObjs as $leaveObj) {
			if ($leaveObj->getLeaveStatus() == Leave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL) {
				
				$leaveCount++;
				
				$date = $leaveObj->getLeaveDate();
				$type = $leaveObj->getLeaveTypeName();
				$comments = $leaveObj->getLeaveComments();
				
				$recordArr[] = preg_replace(array('/#date/', '/#type/', '/#comments/'), array($date, $type, $comments), $recordTxt);
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
			
			$this->subject = "Leave Notification - $employeeName applied for $leaveCount day(s)";
			
			$this->to = $this->supervisorMail;
		}	
		
		$this->mail = $txt;	
	}
	
	/**
	 * Builds leave cancellation notice
	 *
	 */
	private function _cancelMail() {		
		$this->templateFile = file_get_contents(ROOT_PATH."/templates/leave/mails/".self::MAILNOTIFICATIONS_TEMPLATE_CANCEL);
		$txt = $this->templateFile;
		
		$leaveObjs = $this->getLeaveObjs();		
		
		$txtArr = preg_split('/#\{(.*)\}/', $txt, null, PREG_SPLIT_DELIM_CAPTURE);
		
		$recordTxt = $txtArr[1];
		$recordArr = null;	
		
		$leaveCount = 0;	
		
		foreach ($leaveObjs as $leaveObj) {
			if ($leaveObj->getLeaveStatus() == Leave::LEAVE_STATUS_LEAVE_CANCELLED) {
				
				$leaveCount++;
				
				$date = $leaveObj->getLeaveDate();
				$type = $leaveObj->getLeaveTypeName();
				$comments = $leaveObj->getLeaveComments();
				
				$recordArr[] = preg_replace(array('/#date/', '/#type/', '/#comments/'), array($date, $type, $comments), $recordTxt);
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
			
			$this->subject = "Leave Notification - $employeeName cancelled leave for $leaveCount day(s)";
			
			$this->to = $this->supervisorMail;
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
	 * Fetch required mail addresses related to the employee
	 *
	 * @param integer $employeeId - Employee ID
	 */
	private function _getAddresses($employeeId) {				
		$this->subordinateMail = array($this->_getEmpAddress(str_pad($employeeId, $this->employeeIdLength)));	
		
		$empRepToObj1 = new EmpRepTo();
		
		$supInfo = $empRepToObj1->getEmpSub(str_pad($employeeId, $this->employeeIdLength, "0", STR_PAD_LEFT));

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
	
}

?>