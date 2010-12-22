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
*/
class LeaveNotificationService extends BaseService
{
    // EmailService Object
    private $emailService ;
    private $userService;
    private $mailService;

    // Constance for email templates
    const EMAIL_TEMPLATE_LEAVE_ASSIGN = 'leave_assign';
    const EMAIL_TEMPLATE_LEAVE_ASSIGN_SUBJECT = 'leave_assign_subject';

    const EMAIL_TEMPLATE_LEAVE_APPLY = 'leave_apply';
    const EMAIL_TEMPLATE_LEAVE_APPLY_SUBJECT = 'leave_apply_subject';

    const EMAIL_TEMPLATE_LEAVE_APPROVED = 'leave_approval';
    const EMAIL_TEMPLATE_LEAVE_APPROVED_SUBJECT = 'leave_approval_subject';

    const EMAIL_TEMPLATE_LEAVE_CANCELLED = 'leave_cancelled';
    const EMAIL_TEMPLATE_LEAVE_CANCELLED_SUBJECT = 'leave_cancelled_subject';

    const EMAIL_TEMPLATE_LEAVE_CANCELLED_BY_EMPLOYEE = 'leave_cancelled_by_employee';
    const EMAIL_TEMPLATE_LEAVE_CANCELLED_BY_EMPLOYEE_SUBJECT = 'leave_cancelled_by_employee_subject';

    const EMAIL_TEMPLATE_LEAVE_REJECTED = 'leave_rejected';
    const EMAIL_TEMPLATE_LEAVE_REJECTED_SUBJECT = 'leave_rejected_subject';

    private $emails = array();
    private $mailSubject = "";
    private $mailBody = "";
    private $mailCC = null;


    protected function setEmailAdddress($email)
    {
        $this->emails[] = $email;
    }

    protected function getEmailAdddress()
    {
        return $this->emails;
    }

    protected function setEmailSubject($subject)
    {
        $this->mailSubject = $subject;
    }

    protected function getEmailSubject()
    {
        return $this->mailSubject;
    }

    protected function setEmailBody($body)
    {
        $this->mailBody = $body;
    }

    protected function getEmailBody()
    {
        return $this->mailBody;
    }

    protected function getEmailCC() {
       return $this->mailCC;
    }

    protected function setEmailCC($email) {
       $this->mailCC = $email;
    }

    public function setUserSettings($key, $value) {
        $_SESSION[$key] = $value;
    }

    private function ResetEmailSettings()
    {
        $this->emails = array();
        $this->mailBody = "";
        $this->mailSubject = "";
    }
    
    /**
     * Get Method for EmailService
     * @return EmailService $emailService
     */
    public function getEmailService()
    {
        if(is_null($this->emailService)) {
            $this->emailService = new EmailService();
        }
        return $this->emailService;
    }

    /**
     * Set Method for EmailService
     *
     */
    public function setEmailService(EmailService $emailService) {

        $this->emailService = $emailService;

    }

    /**
     * Get Method for MailService
     * @return MailService $mailService
     */
    public function getMailService()
    {
        if(is_null($this->mailService)) {
            $this->mailService = new MailService();
        }
        return $this->mailService;
    }

    /**
     * Set Method for EmailService
     *
     */
    public function setMailService(MailService $mailService) {

        $this->mailService = $mailService;

    }

    public function readEmailTemplate($name, $language = 'en-us')
    {
        return file_get_contents(sfConfig::get('sf_root_dir')."/apps/orangehrm/templates/mail/". $language."/". $name . ".txt");
    }

    /**
     * Leave Notification Service Constructor
     *
     */
    public function  __construct()
    {
        $this->getEmailService();
    }

    /**
     * Get UserService
     * @returns UserService
     */
    public function getUserService() {
        if(is_null($this->userService)) {
            $this->userService = new UserService();
        }
        return $this->userService;
    }

    /**
     * Set UserService
     * @param UserService $userService
     */
    public function setUserService(UserService $userService) {
        $this->userService = $userService;
    }

    /**
     * Send email notification for the assigned leave
     * @param LeaveRequest $leaveRequest
     * @param Leavelist $leaveList
     * @return bool
     */
    public function sendAssignLeaveNotification( LeaveRequest $leaveRequest, $leaveList )
    {
        try
        {
            $this->ResetEmailSettings();

            $employee = $leaveRequest->getEmployee();

            $bodyTextContent    =   $this->readEmailTemplate(self::EMAIL_TEMPLATE_LEAVE_ASSIGN);
            $bodyTextArr = preg_split('/#\{(.*)\}/', $bodyTextContent, null, PREG_SPLIT_DELIM_CAPTURE);

            // Process body text of teh email
            $leaveContentmanager = new LeaveNotificationContentManager($leaveRequest, $leaveList);
            $leaveContentmanager->setTemplateText($bodyTextArr[1]);
            $recordTxt = $leaveContentmanager->getEmailBody();

            // replace employee
            $bodyTextArr[0] = str_replace("#subordinate", $employee->getEmpFirstname() , $bodyTextArr[0]);

            // concat body text
            $mailBody = $bodyTextArr[0].$recordTxt.$bodyTextArr[2];

            //this is for CC ing admin
            $userService = $this->getUserService();
            $user = $userService->readUser($_SESSION['user']);
            $this->setEmailCC($user->getEmail1());

            $mailSubjectContent = $this->readEmailTemplate(self::EMAIL_TEMPLATE_LEAVE_ASSIGN_SUBJECT);
            $mailSubjectVaribles   =   array(
                    '#leavecount'=>$leaveContentmanager->getNoOfDays(),
            );
            $mailSubject = strtr($mailSubjectContent, $mailSubjectVaribles);

            $this->setEmailAdddress($employee->getEmpWorkEmail());
            $this->setEmailBody($mailBody);
            $this->setEmailSubject($mailSubject);
            return $this->sendMail(__FUNCTION__ . " - " . $employee->getEmpFirstname());

        } catch( Exception $e)
        {
            throw new LeaveNotificationException($e->getMessage());
        }



    }

    /**
     * Send email notification for the applied leave
     * @param LeaveRequest $leaveRequest
     * @param Leavelist $leaveList
     * @return bool
     */
    public function sendApplyLeaveNotification( LeaveRequest $leaveRequest, $leaveList) {
        try
        {

            $this->ResetEmailSettings();
            $employee = $leaveRequest->getEmployee();


            $bodyTextContent    =   $this->readEmailTemplate(self::EMAIL_TEMPLATE_LEAVE_APPLY);
            $bodyTextArr = preg_split('/#\{(.*)\}/', $bodyTextContent, null, PREG_SPLIT_DELIM_CAPTURE);

            // Process body text of teh email
            $leaveContentmanager = new LeaveNotificationContentManager($leaveRequest, $leaveList);
            $leaveContentmanager->setTemplateText($bodyTextArr[1]);
            $recordTxt = $leaveContentmanager->getEmailBody();

            // concat body text
            $mailBody = $bodyTextArr[0].$recordTxt.$bodyTextArr[2];
            //this is for CC ing if admin has subscribed
            $this->setEmailCC($this->getCCEmailAddress(MailService::EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_JOB_APPLIED));

            // replace employee
            $mailBody = str_replace("#subordinate", $employee->getEmpFirstname() , $mailBody);

            // get SUpervisors email list
            $supervisors = $employee->getSupervisors();
            foreach($supervisors as $supervisor)
            {
                $this->setEmailAdddress($supervisor->getEmpWorkEmail());
            }

            $mailSubjectContent = $this->readEmailTemplate(self::EMAIL_TEMPLATE_LEAVE_APPLY_SUBJECT);
            $mailSubjectVaribles   =   array(
                    '#leavecount'=>$leaveContentmanager->getNoOfDays(),
                    '#subordinate'=>$employee->getEmpFirstname(),
            );

            $mailSubject = strtr($mailSubjectContent, $mailSubjectVaribles);


            $this->setEmailBody($mailBody);
            $this->setEmailSubject($mailSubject);
            return $this->sendMail(__FUNCTION__. " - " . $employee->getEmpFirstname());

        } catch( Exception $e)
        {
            throw new LeaveNotificationException($e->getMessage());
        }
    }

    /**
     * Send email notification for the Approved leave
     * @param LeaveRequest $leaveRequest
     * @param Leavelist $leaveList
     * @return bool
     */
    public function sendApproveLeaveNotification( LeaveRequest $leaveRequest, $leaveList)
    {
        try
        {
            $this->ResetEmailSettings();
            $employee = $leaveRequest->getEmployee();


            $bodyTextContent    =   $this->readEmailTemplate(self::EMAIL_TEMPLATE_LEAVE_APPROVED);
            $bodyTextArr = preg_split('/#\{(.*)\}/', $bodyTextContent, null, PREG_SPLIT_DELIM_CAPTURE);

            // Process body text of teh email
            $leaveContentmanager = new LeaveNotificationContentManager($leaveRequest, $leaveList);
            $leaveContentmanager->setTemplateText($bodyTextArr[1]);
            $recordTxt = $leaveContentmanager->getEmailBody();

            // concat body text
            $mailBody = $bodyTextArr[0].$recordTxt.$bodyTextArr[2];

            // replace employee
            $mailBody = str_replace("#subordinate", $employee->getEmpFirstname() , $mailBody);

            //this is for CC ing if admin has subscribed
            $this->setEmailCC($this->getCCEmailAddress(MailService::EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_LEAVE_APPROVED));

            $mailSubjectContent = $this->readEmailTemplate(self::EMAIL_TEMPLATE_LEAVE_APPROVED_SUBJECT);
            $mailSubjectVaribles   =   array(
                    '#leavecount'=>$leaveContentmanager->getNoOfDays()
            );

            $mailSubject = strtr($mailSubjectContent, $mailSubjectVaribles);

            $this->setEmailAdddress($employee->getEmpWorkEmail());
            $this->setEmailSubject($mailSubject);
            $this->setEmailBody($mailBody);
            return $this->sendMail(__FUNCTION__. " - " . $employee->getEmpFirstname());

        } catch( Exception $e)
        {
            throw new LeaveNotificationException($e->getMessage());
        }
    }

    /**
     * Send email notification for the Canceled leave
     * @param LeaveRequest $leaveRequest
     * @param Leavelist $leaveList
     * @return bool
     */
    public function sendCanceledLeaveNotification( LeaveRequest $leaveRequest, $leaveList, $emptype)
    {
        try
        {
            $this->ResetEmailSettings();
            $employee = $leaveRequest->getEmployee();

            switch($emptype)
            {
                case Users::USER_TYPE_EMPLOYEE:
                    $template = self::EMAIL_TEMPLATE_LEAVE_CANCELLED_BY_EMPLOYEE;
                    $templateSubject = self::EMAIL_TEMPLATE_LEAVE_CANCELLED_BY_EMPLOYEE_SUBJECT;
                    
                    // get SUpervisors email list
                    $supervisors = $employee->getSupervisors();
                    foreach($supervisors as $supervisor)
                    {
                        $this->setEmailAdddress($supervisor->getEmpWorkEmail());
                    }

                    break;

                case Users::USER_TYPE_SUPERVISOR || Users::USER_TYPE_ADMIN:
                    $template = self::EMAIL_TEMPLATE_LEAVE_CANCELLED;
                    $templateSubject = self::EMAIL_TEMPLATE_LEAVE_CANCELLED_SUBJECT;
                    $this->setEmailAdddress($employee->getEmpWorkEmail());
                    break;

            }

            $this->setEmailCC($this->getCCEmailAddress(MailService::EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_LEAVE_CANCELLED));
            $bodyTextContent    =   $this->readEmailTemplate($template);
            $bodyTextArr = preg_split('/#\{(.*)\}/', $bodyTextContent, null, PREG_SPLIT_DELIM_CAPTURE);

            // Process body text of teh email
            $leaveContentmanager = new LeaveNotificationContentManager($leaveRequest, $leaveList);
            $leaveContentmanager->setTemplateText($bodyTextArr[1]);
            $recordTxt = $leaveContentmanager->getEmailBody();

            // concat body text
            $mailBody = $bodyTextArr[0].$recordTxt.$bodyTextArr[2];

            // replace employee
            $mailBody = str_replace("#subordinate", $employee->getEmpFirstname() , $mailBody);

            $mailSubjectContent = $this->readEmailTemplate($templateSubject);
            $mailSubjectVaribles   =   array(
                    '#leavecount'=>$leaveContentmanager->getNoOfDays(),
                    '#subordinate'=>$employee->getEmpFirstname(),
            );

            $mailSubject = strtr($mailSubjectContent, $mailSubjectVaribles);


            $this->setEmailSubject($mailSubject);
            $this->setEmailBody($mailBody);
            return $this->sendMail(__FUNCTION__. " - " . $employee->getEmpFirstname());

        } catch( Exception $e)
        {
            throw new LeaveNotificationException($e->getMessage());
        }
    }

    /**
     * Send email notification for the Rejected leave
     * @param LeaveRequest $leaveRequest
     * @param Leavelist $leaveList
     * @return bool
     */
    public function sendRejectedLeaveNotification( LeaveRequest $leaveRequest, $leaveList)
    {
        try
        {
            $email = array();
            $employee = $leaveRequest->getEmployee();


            $bodyTextContent    =   $this->readEmailTemplate(self::EMAIL_TEMPLATE_LEAVE_REJECTED);
            $bodyTextArr = preg_split('/#\{(.*)\}/', $bodyTextContent, null, PREG_SPLIT_DELIM_CAPTURE);

            // Process body text of teh email
            $leaveContentmanager = new LeaveNotificationContentManager($leaveRequest, $leaveList);
            $leaveContentmanager->setTemplateText($bodyTextArr[1]);
            $recordTxt = $leaveContentmanager->getEmailBody();

            // concat body text
            $mailBody = $bodyTextArr[0].$recordTxt.$bodyTextArr[2];

            // replace employee
            $mailBody = str_replace("#subordinate", $employee->getEmpFirstname() , $mailBody);



            $mailSubjectContent = $this->readEmailTemplate(self::EMAIL_TEMPLATE_LEAVE_REJECTED_SUBJECT);
            $mailSubjectVaribles   =   array(
                    '#leavecount'=>$leaveContentmanager->getNoOfDays()
            );

            $mailSubject = strtr($mailSubjectContent, $mailSubjectVaribles);

            //this is for CC ing if admin has subscribed
            $this->setEmailCC($this->getCCEmailAddress(MailService::EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_LEAVE_REJECTED));

            $this->setEmailAdddress( $employee->getEmpWorkEmail());
            $this->setEmailSubject($mailSubject);
            $this->setEmailBody($mailBody);
            return $this->sendMail(__FUNCTION__. " - " . $employee->getEmpFirstname());

        } catch( Exception $e)
        {
            throw new LeaveNotificationException($e->getMessage());
        }
    }

    private function sendMail($debugErrorMessage="") {

        $to = $this->getEmailAdddress();
        $cc = $this->getEmailCC();

        if (!empty($to) && !is_array($to)) {
            $to = array($to);
        }

        if (!empty($cc) && !is_array($cc)) {
            $cc = array($cc);
        }
        $emailService = $this->getEmailService();
        $emailService->setMessageSubject($this->getEmailSubject());
        $emailService->setMessageTo($to);
        $emailService->setMessageCc($cc);
        $emailService->setMessageBody($this->getEmailBody());
        $emailService->sendEmail();

        return true ;
        
    }

    /**
     * private function to get the email for a given notification
     * @param int $notificationTypeId
     * @returns String/null
     * @throws LeaveNotificationException
     */
    private function getCCEmailAddress($notificationTypeId) {
      try {
         $userService = $this->getUserService();
         $user = $userService->readUser($_SESSION['user']);
         $mailService = $this->getMailService();
         $subscriptions = $mailService->getMailNotificationList($_SESSION['user']);
        
         if(isset($subscriptions[$notificationTypeId])) {
            return $user->getEmail1();
         }
         return null;
      } catch(Exception $e) {
         throw new LeaveNotificationException($e->getMessage());
      }
    }
}
