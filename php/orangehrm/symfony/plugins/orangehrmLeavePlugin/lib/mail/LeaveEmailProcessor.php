<?php

/**
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
 */

/**
 * Description of LeaveEmailProcessor
 *
 */
class LeaveEmailProcessor implements orangehrmMailProcessor {
    
    protected $employeeService;
    protected $userRoleManager;
    protected $logger;
    
    /**
     * Get Logger instance
     * @return Logger
     */
    public function getLogger() {
        if (empty($this->logger)) {
            $this->logger = Logger::getLogger('leave.leavemailer');
        }
        return $this->logger;
    }    
    
    public function getEmployeeService() {
        if (!($this->employeeService instanceof EmployeeService)) {
            $this->employeeService = new EmployeeService();
        }
        return $this->employeeService;
    }

    public function setEmployeeService($employeeService) {
        $this->employeeService = $employeeService;
    }
    
    /**
     * Get User role manager instance
     * @return AbstractUserRoleManager
     */
    public function getUserRoleManager() {
        if (!($this->userRoleManager instanceof AbstractUserRoleManager)) {
            $this->userRoleManager = UserRoleManagerFactory::getUserRoleManager();
        }
        return $this->userRoleManager;
    }

    /**
     * Set user role manager instance
     * @param AbstractUserRoleManager $userRoleManager
     */
    public function setUserRoleManager(AbstractUserRoleManager $userRoleManager) {
        $this->userRoleManager = $userRoleManager;
    }   
    
    public function getReplacements($data) {

        $replacements = array();
        
        $performer = $this->getEmployeeService()->getEmployee($data['empNumber']);
        
        if ($performer instanceof Employee) {
            $replacements['performerFirstName'] = $performer->getFirstName();
            $replacements['performerFullName'] = $performer->getFullName();
        } else {
            $name = sfContext::getInstance()->getUser()->getAttribute('auth.firstName');
            
            $replacements['performerFirstName'] = $name;
            $replacements['performerFullName'] = $name;
            
        }        

        if ($data['recipient'] instanceof Employee) {
            $replacements['recipientFirstName'] = $data['recipient']->getFirstName();
            $replacements['recipientFullName'] = $data['recipient']->getFullName();
        } else if ($data['recipient'] instanceof EmailSubscriber) {
            $replacements['recipientFirstName'] = $data['recipient']->getName();
            $replacements['recipientFullName'] = $data['recipient']->getName();            
        }

        $applicantNo = $data['days'][0]->getEmpNumber();
        
        $applicant = $this->getEmployeeService()->getEmployee($applicantNo);
        if ($applicant instanceof Employee) {
            $replacements['applicantFirstName'] = $applicant->getFirstName();
            $replacements['applicantFullName'] = $applicant->getFullName();
        }                
        
        $replacements = $this->_populateLeaveReplacements($data, $replacements);
        
        return $replacements;

    }   

    protected function getSubscribersForEvent($event) {
        $recipients = array();
        
        $mailNotificationService = new EmailNotificationService();
        $subscriptions = $mailNotificationService->getSubscribersByNotificationId($event);

        foreach ($subscriptions as $subscription) {

            if ($subscription instanceof EmailSubscriber) {

                if ($subscription->getEmailNotification()->getIsEnable() == EmailNotification::ENABLED) {

                    $recipients[] = $subscription;
                }
            }
        }
        
        return $recipients;        
    }
    
    protected function _populateLeaveReplacements($data, $replacements) {

        
        if ($data['request'] instanceof LeaveRequest) {
            $replacements['leaveType'] = $data['request']->getLeaveType()->getName();
            $replacements['assigneeFullName'] = $data['request']->getEmployee()->getFirstAndLastNames();
        } else {
            $replacements['leaveType'] = $data['days'][0]->getLeaveType()->getName();
            $replacements['assigneeFullName'] = $data['days'][0]->getLeaveRequest()->getEmployee()->getFirstAndLastNames();
            
        }

        $numberOfDays = 0;

        foreach ($data['days'] as $leave) {
            $numberOfDays += $leave->getLengthDays();
        }

        $replacements['numberOfDays'] = round($numberOfDays, 2);

        $replacements['leaveDetails'] = $this->_generateLeaveDetailsTable($data, $replacements);

        return $replacements;
    }

    protected function _generateLeaveDetailsTable($data, $replacements) {

        $requestType = isset($data['requestType']) ? $data['requestType'] : 'request';

        // Show individual comments in table if there are any leave dates with comments
        $displayIndividualComments = false;
        if ($requestType == 'multiple' && count($data['days']) > 1) {
            
            foreach ($data['days'] as $leave) {
                $thisLeaveComment = $leave->getLatestCommentAsText();
                if (!empty($thisLeaveComment)) {
                    $displayIndividualComments = true;
                    break;
                }
            }
        }
        
        // Format:
        // Date(s)                                     Duration            Comments  
        // ========================================================================
        // 2013-06-04 (09:00 - 12:00) Half Day         8.00        
        
        // Length of tab (4 spaces) : "    "

        $details = "Date(s)                                     Duration (Hours)";
        if ($displayIndividualComments) {
            $details .= "            Comments";
        }
        $details .= "\n";
        $details .= "====================================================";
        if ($displayIndividualComments) {
            $details .= "====================";
        }        
        
        $details .= "\n";
        $dateLengthWithTimeAndHalfTime = 27;
        $dateLengthWithTime = 13;

        foreach ($data['days'] as $leave) {
            
            $leaveDate = $leave->getFormattedLeaveDateToView();
            $dateLength = strlen($leaveDate);
            
            $durationPad = 0;
            
            if ($dateLength < $dateLengthWithTime) {
                // These padding lengths are based on gmail. They can vary based on the font used to view the email
                $durationPad = 10;
            } else if ($dateLength < $dateLengthWithTimeAndHalfTime) {
                // These padding lengths are based on gmail. They can vary based on the font used to view the email
                $durationPad = 4;
            }
            
            $durationPadSpaces = str_repeat(' ', $durationPad);
             
            $leaveDate = str_pad($leaveDate, 35, " ", STR_PAD_RIGHT);
            
            $leaveDuration = round($leave->getLengthHours(), 2);

            if ($leaveDuration > 0) {

                $leaveDuration = $this->_fromatDuration($leaveDuration);
                $details .= "{$leaveDate}         {$durationPadSpaces}{$leaveDuration}";
                if ($displayIndividualComments) {
                    $details .= "        " . $this->trimComment($leave->getLatestCommentAsText());
                }
                $details .= "\n";

            }

        }

        $details .= "\n";
        $details .= "Leave type : " . $replacements['leaveType'];
        $details .= "\n";

        $leaveComment = '';
        
        if ($requestType == 'request') {
            $leaveComment = $data['request']->getCommentsAsText();
        } elseif ($requestType == 'single') {
            $leaveComment = $data['days'][0]->getCommentsAsText();
        }

        if (!empty($leaveComment)) {
            $details .= "\n\nComments:\n=========\n$leaveComment";
            $details .= "\n";
        }

        return $details;

    }

    protected function _fromatDuration($duration) {

        $formattedDuration = number_format($duration, 2);

        return $formattedDuration;

    }
    
    protected function trimComment($comment) {
        if (strlen($comment) > 30) {
            $comment = substr($comment, 0, 30) . '...';
        }
        return $comment;
    }

    public function getRecipients($emailName, $role, $data) {
        
        $recipients = array();
        
        switch ($role) {
            case 'subscriber' :
                $recipients = $this->getSubscribers($emailName, $data);
                break;
            case 'supervisor':                
                if (isset($data['days'][0])) {
                    $recipients = $this->getSupervisors($data['days'][0]->getEmpNumber(), $data);
                }
                break;
            case 'ess':                
                if (isset($data['days'][0])) {
                    $recipients = $this->getSelf($data['days'][0]->getEmpNumber());
                }
                break;            
            default:
                if (isset($data['days'][0])) {
                    $recipients = $this->getEmployeesWithRole($role, $data['days'][0]->getEmpNumber());
                }
                break;
        }

        return $recipients;
    }
    
    protected function getEmployeesWithRole($role, $empNumber) {
        
        $entities = array('Employee' => $empNumber);
        $employees = $this->getUserRoleManager()->getEmployeesWithRole($role, $entities);
        
        return $employees;
            
    }
    
    protected function getSelf($empNumber) {
        $recipients = array();
        $performer = $this->getEmployeeService()->getEmployee($empNumber); 
        
        $to = $performer->getEmpWorkEmail();

        if (!empty($to)) {
            $recipients[] = $performer;
        }            
        
        return $recipients;
    }
    
    protected function getSubscribers($emailName, $data) {
        $subscribers = array();
        
        $notification = NULL;
        
        switch ($emailName) {
            case 'leave.apply':
                $notification = EmailNotification::LEAVE_APPLICATION;
                break;
            case 'leave.assign':
                $notification = EmailNotification::LEAVE_ASSIGNMENT;
                break;
            case 'leave.approve':
                $notification = EmailNotification::LEAVE_APPROVAL;
                break;
            case 'leave.cancel':
                $notification = EmailNotification::LEAVE_CANCELLATION;
                break;
            case 'leave.reject':
                $notification = EmailNotification::LEAVE_REJECTION;
                break;                
        }
        
        if (!is_null($notification)) {
            $subscribers = $this->getSubscribersForEvent($notification);
        }
        
        return $subscribers;
    }  
    
    public function getSupervisors($empNumber) {
        
        $recipients = array();
        
        $performer = $this->getEmployeeService()->getEmployee($empNumber);    
        
        // TODO: Do we need to send to supervisor chain?
        $supervisors = $performer->getSupervisors();

        if (count($supervisors) > 0) {

            foreach ($supervisors as $supervisor) {

                $to = $supervisor->getEmpWorkEmail();

                if (!empty($to)) {
                    $recipients[] = $supervisor;
                }
            }
        }
        
        return $recipients;
    }    
    
}

