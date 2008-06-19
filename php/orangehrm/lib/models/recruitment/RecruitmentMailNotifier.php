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
require_once ROOT_PATH . '/lib/models/eimadmin/CountryInfo.php';
require_once ROOT_PATH . '/lib/models/eimadmin/EmailConfiguration.php';
require_once ROOT_PATH . '/lib/models/eimadmin/EmailNotificationConfiguration.php';
require_once ROOT_PATH . '/lib/models/maintenance/Users.php';
require_once ROOT_PATH . '/lib/models/recruitment/JobVacancy.php';
require_once ROOT_PATH . '/lib/models/recruitment/JobApplication.php';
require_once ROOT_PATH . '/lib/confs/sysConf.php';
require_once ROOT_PATH . '/lib/models/hrfunct/EmpInfo.php';
require_once ROOT_PATH . '/lib/common/Language.php';

/**
 * Manages sending of mail notifications
 *
 */
class RecruitmentMailNotifier {

	const MAILNOTIFICATIONS_TEMPLATE_APPLY_SUBJECT = 'applied-subject.txt';

	/**
	 * Template file name constants
	 *
	 */
	const TEMPLATE_RECEIVED_APPLICANT = 'applicant-received.txt';
	const TEMPLATE_RECEIVED_HIRING_MANAGER = 'hiringmanager-received.txt';
    const TEMPLATE_REJECTED_APPLICANT = 'applicant-rejected.txt';
    const TEMPLATE_INTERVIEW_MANAGER_TASK = 'interview-manager-task.txt';
    const TEMPLATE_SEEK_APPROVAL_DIRECTOR = 'seek-approval-director.txt';

	/**
	 * Mail subject templates
	 */
	const SUBJECT_RECEIVED_APPLICANT = 'applicant-received-subject.txt';
	const SUBJECT_RECEIVED_HIRING_MANAGER = 'hiringmanager-received-subject.txt';
    const SUBJECT_REJECTED_APPLICANT = 'applicant-rejected-subject.txt';
    const SUBJECT_INTERVIEW_MANAGER_TASK = 'interview-manager-task-subject.txt';
    const SUBJECT_SEEK_APPROVAL_DIRECTOR = 'seek-approval-director-subject.txt';

	/**
	 * Template variable constants
	 *
	 */
	const VARIABLE_JOB_TITLE = '#jobtitle#';
	const VARIABLE_TO = '#to#';
	const VARIABLE_APPLICANT_FIRSTNAME = '#firstname#';
	const VARIABLE_APPLICANT_MIDDLENAME = '#middlename#';
	const VARIABLE_APPLICANT_LASTNAME = '#lastname#';
	const VARIABLE_APPLICANT_STREET1 = '#street1#';
	const VARIABLE_APPLICANT_STREET2 = '#street2#';
	const VARIABLE_APPLICANT_CITY = '#city#';
	const VARIABLE_APPLICANT_PROVINCE = '#province#';
	const VARIABLE_APPLICANT_ZIP = '#zip#';
	const VARIABLE_APPLICANT_COUNTRY = '#country#';
	const VARIABLE_APPLICANT_PHONE = '#phone#';
	const VARIABLE_APPLICANT_MOBILE = '#mobile#';
	const VARIABLE_APPLICANT_EMAIL = '#email#';
	const VARIABLE_APPLICANT_QUALIFICATIONS = '#qualifications#';
    const VARIABLE_INTERVIEW_NOTES = '#interview-notes#';
    const VARIABLE_SEEK_NOTES = '#seek-approval-notes#';
    const VARIABLE_FROM = '#from#';

    const VCALENDAR_DATETIME_FORMAT = 'Ymd\\THis\\Z';

	/*
	 * Class atributes
	 **/
	private $mailType;
	private $logFile;
	private $emailConf;

	/* Mailer instance. used only for testing */
	private $mailer;

	/**
	 * Constructor
	 *
	 * Constructs the object
	 *
	 */
	public function __construct() {
		$this->emailConf = new EmailConfiguration();

		if (isset($this->emailConf->logPath) && !empty($this->emailConf->logPath)) {
			$logPath = $this->emailConf->logPath;
		} else {
			$logPath = ROOT_PATH.'/lib/logs/';
		}

		$this->mailType = $this->emailConf->getMailType();
		$this->logFile = $logPath . "notification_mails.log";
	}

	/**
	 * Return a mailer object based on email configuration
	 *
	 * @return htmlMimeMail5 Mail object
	 */
	private function _getMailer() {

		if (!empty($this->mailer)) {
		    return $this->mailer;
		}

		$auth = true;
		if ($this->emailConf->getSmtpUser() == '') {
			$auth=false;
		}

		$mailer = new htmlMimeMail5();
		$mailer->setSMTPParams($this->emailConf->getSmtpHost(), $this->emailConf->getSmtpPort(), null, $auth, $this->emailConf->getSmtpUser(), $this->emailConf->getSmtpPass());
		$mailer->setSendmailPath($this->emailConf->getSendmailPath());
		$mailer->setFrom($this->emailConf->getMailAddress());

	    return $mailer;
	}

	/**
	 * Set mailer instance. Normally used for testing. If set, will override the
	 * internally used mailer
	 */
	public function setMailer($mailer) {
	    $this->mailer = $mailer;
	}

	/**
	 * Send application received email to Applicant
	 *
	 * @param JobApplication $jobApplication Job Application object
	 *
	 * @return boolean True if mail sent, false otherwise
	 */
	 public function sendApplicationReceivedEmailToApplicant($jobApplication) {

	     $email = $jobApplication->getEmail();
	     $name = $jobApplication->getFirstName() . ' ' . $jobApplication->getLastName();
	     $vacancy = JobVacancy::getJobVacancy($jobApplication->getVacancyId());

	     $subject = $this->_getTemplate(self::SUBJECT_RECEIVED_APPLICANT);
	     $body = $this->_getTemplate(self::TEMPLATE_RECEIVED_APPLICANT);

		 $search = array(self::VARIABLE_JOB_TITLE, self::VARIABLE_TO);
		 $replace = array($vacancy->getJobTitleName(), $name);

		 $subject = str_replace($search, $replace, $subject);
		 $body = str_replace($search, $replace, $body);

		 $notificationType = null;

		 return $this->_sendMail($email, $subject, $body, $notificationType);
	 }

    /**
     * Send application rejected email to Applicant
     *
     * @param JobApplication $jobApplication Job Application object
     *
     * @return boolean True if mail sent, false otherwise
     */
     public function sendApplicationRejectedEmailToApplicant($jobApplication) {

         $email = $jobApplication->getEmail();
         $name = $jobApplication->getFirstName() . ' ' . $jobApplication->getLastName();
         $vacancy = JobVacancy::getJobVacancy($jobApplication->getVacancyId());

         $subject = $this->_getTemplate(self::SUBJECT_REJECTED_APPLICANT);
         $body = $this->_getTemplate(self::TEMPLATE_REJECTED_APPLICANT);

         $search = array(self::VARIABLE_JOB_TITLE, self::VARIABLE_TO);
         $replace = array($vacancy->getJobTitleName(), $name);

         $subject = str_replace($search, $replace, $subject);
         $body = str_replace($search, $replace, $body);

         $notificationType = null;

         return $this->_sendMail($email, $subject, $body, $notificationType);
     }

	/**
	 * Send application received email to Manager
	 *
	 * @param JobApplication $jobApplication Job Application object
	 *
	 * @return boolean True if mail sent, false otherwise
	 */
	 public function sendApplicationReceivedEmailToManager($jobApplication) {

	     $vacancy = JobVacancy::getJobVacancy($jobApplication->getVacancyId());
		 $managerId = $vacancy->getManagerId();
		 $email = $this->_getEmpAddress($managerId);
	     $empName = $this->_getEmpName($managerId);

	     $subject = $this->_getTemplate(self::SUBJECT_RECEIVED_HIRING_MANAGER);
	     $body = $this->_getTemplate(self::TEMPLATE_RECEIVED_HIRING_MANAGER);

         // Replace placeholders in subject and body
		 $search = array(self::VARIABLE_JOB_TITLE, self::VARIABLE_TO,
			self::VARIABLE_APPLICANT_FIRSTNAME,	self::VARIABLE_APPLICANT_MIDDLENAME,
			self::VARIABLE_APPLICANT_LASTNAME, self::VARIABLE_APPLICANT_STREET1,
			self::VARIABLE_APPLICANT_STREET2, self::VARIABLE_APPLICANT_CITY,
			self::VARIABLE_APPLICANT_PROVINCE, self::VARIABLE_APPLICANT_ZIP,
			self::VARIABLE_APPLICANT_COUNTRY, self::VARIABLE_APPLICANT_PHONE,
			self::VARIABLE_APPLICANT_MOBILE, self::VARIABLE_APPLICANT_EMAIL,
			self::VARIABLE_APPLICANT_QUALIFICATIONS);

         $country = $this->_getCountryName($jobApplication->getCountry());
		 $replace = array($vacancy->getJobTitleName(), $empName['first'],
		 $jobApplication->getFirstName(), $jobApplication->getMiddleName(),
		 $jobApplication->getLastName(), $jobApplication->getStreet1(),
		 $jobApplication->getStreet2(), $jobApplication->getCity(),
		 $jobApplication->getProvince(), $jobApplication->getZip(),
		 $country, $jobApplication->getPhone(),
		 $jobApplication->getMobile(), $jobApplication->getEmail(),
		 $jobApplication->getQualifications());

		 $subject = str_replace($search, $replace, $subject);
		 $body = str_replace($search, $replace, $body);

		 $notificationType = EmailNotificationConfiguration::EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_JOB_APPLIED;

		 return $this->_sendMail($email, $subject, $body, $notificationType);
	 }

    /**
     * Send an email to the director seeking for approval for hiring the applicant.
     *
     * @param JobApplication $jobApplication Job Application object
     * @param JobApplicationEvent $jobApplicationEvent Job Application Event object
     *
     * @return boolean True if mail sent, false otherwise
     */
     public function sendSeekApprovalToDirector($jobApplication, $jobApplicationEvent) {

         $vacancy = JobVacancy::getJobVacancy($jobApplication->getVacancyId());
         $directorId = $jobApplicationEvent->getOwner();
         $email = $this->_getEmpAddress($directorId);
         $empName = $this->_getEmpName($directorId);

         $fromName = $jobApplicationEvent->getCreatorName();

         $subject = $this->_getTemplate(self::SUBJECT_SEEK_APPROVAL_DIRECTOR);
         $body = $this->_getTemplate(self::TEMPLATE_SEEK_APPROVAL_DIRECTOR);

         // Replace placeholders in subject and body
         $search = array(self::VARIABLE_JOB_TITLE, self::VARIABLE_TO,
            self::VARIABLE_APPLICANT_FIRSTNAME, self::VARIABLE_APPLICANT_MIDDLENAME,
            self::VARIABLE_APPLICANT_LASTNAME, self::VARIABLE_APPLICANT_STREET1,
            self::VARIABLE_APPLICANT_STREET2, self::VARIABLE_APPLICANT_CITY,
            self::VARIABLE_APPLICANT_PROVINCE, self::VARIABLE_APPLICANT_ZIP,
            self::VARIABLE_APPLICANT_COUNTRY, self::VARIABLE_APPLICANT_PHONE,
            self::VARIABLE_APPLICANT_MOBILE, self::VARIABLE_APPLICANT_EMAIL,
            self::VARIABLE_APPLICANT_QUALIFICATIONS, self::VARIABLE_SEEK_NOTES,
            self::VARIABLE_FROM);

         $country = $this->_getCountryName($jobApplication->getCountry());

         $replace = array($vacancy->getJobTitleName(), $empName['first'],
         $jobApplication->getFirstName(), $jobApplication->getMiddleName(),
         $jobApplication->getLastName(), $jobApplication->getStreet1(),
         $jobApplication->getStreet2(), $jobApplication->getCity(),
         $jobApplication->getProvince(), $jobApplication->getZip(),
         $country, $jobApplication->getPhone(),
         $jobApplication->getMobile(), $jobApplication->getEmail(),
         $jobApplication->getQualifications(), $jobApplicationEvent->getNotes(),
         $fromName);

         $subject = str_replace($search, $replace, $subject);
         $body = str_replace($search, $replace, $body);

         $notificationType = EmailNotificationConfiguration::EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_SEEK_HIRE_APPROVAL;

         return $this->_sendMail($email, $subject, $body, $notificationType);
     }

    /**
     * Send a task to the interviewing manager, giving details of scheduled interview
     *
     * @param JobApplicationEvent $jobApplicationEvent Job Application Event object
     *
     * @return boolean True if mail sent, false otherwise
     */
     public function sendInterviewTaskToManager($jobApplicationEvent) {

        /* Get interview details */
        $intManagerId = $jobApplicationEvent->getOwner();
        if (empty($intManagerId)) {
            throw new RecruitmentMailNotifierException("Invalid parameters",RecruitmentMailNotifierException::INVALID_PARAMETER);
        }
        $intManagerEmail = $this->_getEmpAddress($intManagerId);
        if (empty($intManagerEmail)) {
            $this->_log("Interviewing manager {$intManagerId} does not have email address.");
            return false;
        }

        $intManagerName = $this->_getEmpName($intManagerId);

        $jobApplication = JobApplication::getJobApplication($jobApplicationEvent->getApplicationId());
        $applicantName = $jobApplication->getFirstName() . ' ' . $jobApplication->getLastName();
        $applicantEmail = $jobApplication->getEmail();

        $creatorName = $jobApplicationEvent->getCreatorName();
        $creatorEmail = $jobApplicationEvent->getCreatorEmail();

        $interviewTime = $jobApplicationEvent->getEventTime();

        $vacancy = JobVacancy::getJobVacancy($jobApplication->getVacancyId());

        /* Get summary and description from templates */
        $subject = $this->_getTemplate(self::SUBJECT_INTERVIEW_MANAGER_TASK);
        $body = $this->_getTemplate(self::TEMPLATE_INTERVIEW_MANAGER_TASK);

        // Replace placeholders in subject and body
        $search = array(self::VARIABLE_JOB_TITLE, self::VARIABLE_TO,
            self::VARIABLE_APPLICANT_FIRSTNAME, self::VARIABLE_APPLICANT_MIDDLENAME,
            self::VARIABLE_APPLICANT_LASTNAME, self::VARIABLE_APPLICANT_STREET1,
            self::VARIABLE_APPLICANT_STREET2, self::VARIABLE_APPLICANT_CITY,
            self::VARIABLE_APPLICANT_PROVINCE, self::VARIABLE_APPLICANT_ZIP,
            self::VARIABLE_APPLICANT_COUNTRY, self::VARIABLE_APPLICANT_PHONE,
            self::VARIABLE_APPLICANT_MOBILE, self::VARIABLE_APPLICANT_EMAIL,
            self::VARIABLE_APPLICANT_QUALIFICATIONS, self::VARIABLE_INTERVIEW_NOTES);

         $country = $this->_getCountryName($jobApplication->getCountry());
         $replace = array($vacancy->getJobTitleName(), $intManagerName['first'],
         $jobApplication->getFirstName(), $jobApplication->getMiddleName(),
         $jobApplication->getLastName(), $jobApplication->getStreet1(),
         $jobApplication->getStreet2(), $jobApplication->getCity(),
         $jobApplication->getProvince(), $jobApplication->getZip(),
         $country, $jobApplication->getPhone(),
         $jobApplication->getMobile(), $jobApplication->getEmail(),
         $jobApplication->getQualifications(), $jobApplicationEvent->getNotes());

         $summary = str_replace($search, $replace, $subject);
         $description = str_replace($search, $replace, $body);

        /* Create task */
        $message = $this->_getTask($summary, $description, $creatorName, $creatorEmail, $applicantName, $applicantEmail, $interviewTime);
        $attachment = new stringAttachment($message, 'interview.ics', 'text/calendar');

        /* Send Email with task attached */
        $to =  "{$intManagerName['first']} {$intManagerName['last']}<{$intManagerEmail}>";
        $body = '';
        $notificationType = null;
        $attachments = array($attachment);
        $subject = $summary;
        $this->_sendMail($to, $subject, $body, $notificationType, $attachments);
     }

    /**
     * Get an ICalendar task according to the given format.
     * Format is defined in: http://tools.ietf.org/html/rfc2445
     *
     * @param String $summary
     * @param String $description
     * @param String $organizerName
     * @param String $organizerEmail
     * @param String $attendeeName
     * @param String $attendeeEmail
     * @param String $startTime
     *
     * @return String String formatted as an ICalendar TODO task
     */
    private function _getTask($summary, $description, $organizerName, $organizerEmail, $attendeeName, $attendeeEmail, $startTime = '') {

        if (!empty($startTime)) {
            $startTime = gmdate(self::VCALENDAR_DATETIME_FORMAT, strtotime($startTime));
        }

        $sentTime = gmdate(self::VCALENDAR_DATETIME_FORMAT);
        $uid = $this->_generateUID();

        // Trim newlines, carriage returns from subject.
        $summary = $this->_removeNewLines($summary);

        // convert newlines to \n in description
        $description = $this->_escapeNewLines($description);

        $message = <<<EOT
BEGIN:VCALENDAR
PRODID:-//OrangeHRM/Recruitment//EN
METHOD:REQUEST
VERSION:2.0
BEGIN:VEVENT
UID:$uid
DTSTAMP:$sentTime
DTSTART:$startTime
DUE:$startTime
SUMMARY:$summary
DESCRIPTION:$description
ORGANIZER;CN="$organizerName";RSVP=TRUE;ROLE=NON-PARTICIPANT:mailto:$organizerEmail
ATTENDEE;CN="$attendeeName";ROLE=REQ-PARTICIPANT:mailto:$attendeeEmail
CLASS:CONFIDENTIAL
CATEGORIES:INTERVIEW
PRIORITY:1
STATUS:NEEDS-ACTION
END:VEVENT
END:VCALENDAR
EOT;

        return $message;
    }

    /**
     * Generate UID for use with TASKS
     */
    private function _generateUID() {
        $host = 'orangehrm';
        $uid = $host . '-' . time();
        return $uid;
    }

	/**
	 * Send email with given parameters
	 *
	 * @param mixed $to Array of email address, or single email address
	 * @param String $subject Email subject
	 * @param String $body Email body
	 * @param int $notificationType Notification type, used to fetch other emails subscribed to this type
	 *
	 * @return boolean True if mail sent, false otherwise
	 */
	private function _sendMail($to, $subject, $body, $notificationType, $attachments = null) {

		$mailer = $this->_getMailer();

        if (!empty($body)) {
		  $mailer->setText($body);
        }

        if (!empty($attachments) && is_array($attachments)) {
            foreach ($attachments as $attachment) {
                $mailer->addAttachment($attachment);
            }
        }

		// Trim newlines, carriage returns from subject.
		$subject = $this->_removeNewLines($subject);
		$mailer->setSubject($subject);

		if (empty($notificationType)) {
		    $notificationAddresses = null;
		} else {
			$mailNotificationObj = new EmailNotificationConfiguration();
			$notificationAddresses = $mailNotificationObj->fetchMailNotifications($notificationType);
		}

		$logMessage = date('r')." Sending {$subject} ";

		/*
		 * Check if at least one receipient available.
		 * If no 'to' receipients are available, one of the cc emails is used as the to address.
		 */
		if (empty($to)) {
		    if (empty($notificationAddresses)) {

		    	$logMessage .= " - FAILED \r\nReason: No receipients";
				$this->_log($logMessage);
		    	return false;
		    } else {
		        $to = array(array_shift($notificationAddresses));
		    }
		} else {
		    if (!is_array($to)) {
		        $to = array($to);
		    }
		}

		if (is_array($notificationAddresses)) {
			$cc = implode(', ', $notificationAddresses);
			$mailer->setCc($cc);
		}

		$logMessage .= "to " . implode(', ', $to) . "\r\n";
		if (isset($cc)) {
		    $logMessage .= "CC to {$cc}\r\n";
		}

		if (@$mailer->send($to, $this->mailType)) {
			$logMessage .= " - SUCCEEDED";
		} else {
			$logMessage .= " - FAILED \r\nReason(s):";
			if (isset($mailer->errors)) {
				$logMessage .= "\r\n\t*\t".implode("\r\n\t*\t",$mailer->errors);
			}
			$this->_log($logMessage);
			return false;
		}

		$this->_log($logMessage);
		return true;
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
	 * Fetch employee name
	 *
	 * @param integer $employeeId - Employee ID
	 * @return Array Array with employee first, middle and last names
	 */
	private function _getEmpName($employeeId) {
		$empInfoObj = new EmpInfo();
		$empInfo = $empInfoObj->filterEmpMain($employeeId);

		if (isset($empInfo[0])) {
			$last = $empInfo[0][1];
			$first =  $empInfo[0][2];
			$middle = $empInfo[0][3];

			return array('first'=>$first, 'middle'=>$middle, 'last'=>$last);
		}

		return null;
	}

	/**
	 * Get the mail template from given template file
	 *
	 * @param string $template Mail template file
	 *
	 * @return string Contents of template file
	 */
	private function _getTemplate($template) {
		$text = file_get_contents(ROOT_PATH."/templates/recruitment/mails/".$template);
		return $text;
	}

	/**
	 * Logs the given message to email notification log file
	 *
	 * @param String $message Message to log
	 */
	 private function _log($message) {
		error_log($message . "\r\n", 3, $this->logFile);
	 }

     /**
      * Remove new lines in given text
      * @param String $text Text in which to remove new lines
      * @return String String with newlines removed
      */
     private function _removeNewLines($text) {
        if (!empty($text)) {
            $text = str_replace(array("\r", "\n"), array('', ''), $text);
        }
        return $text;
     }

     /**
      * Remove new lines in \n
      * @param String $text Text in which to escape new lines
      * @return String String with newlines escaped
      */
     private function _escapeNewLines($text) {
        if (!empty($text)) {
            $text = str_replace("\r\n", '\\n', $text);
            $text = str_replace("\r", '\\n', $text);
            $text = str_replace("\n", '\\n', $text);
        }
        return $text;
     }

     /**
      * Get the country name when given the country code
      *
      * @param String $countryCode The country code
      * @return String The country name
      */
     private function _getCountryName($countryCode) {

         $country = '';
         if (!empty($countryCode)) {
             $countryInfo = new CountryInfo();
             $countryInfo = $countryInfo->filterCountryInfo($countryCode);
             if (is_array($countryInfo) && is_array($countryInfo[0])) {
                 $country = $countryInfo[0][1];
             } else {
                 $country = $countryCode;
             }
         }
         return $country;
     }
}

class RecruitmentMailNotifierException extends Exception {
    const INVALID_PARAMETER = 0;
}

?>
