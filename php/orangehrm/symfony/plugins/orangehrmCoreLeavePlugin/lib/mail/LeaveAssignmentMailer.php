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
*/

class LeaveAssignmentMailer extends orangehrmLeaveMailer {

    public function  __construct($leaveRequest, $leaveList, $performerId) {

        parent::__construct();

        $this->employeeService = new EmployeeService();
        $this->leaveList = $leaveList;
        $this->_populatePerformer($performerId);
        $this->leaveRequest = $leaveRequest;
        $this->_populateRecipient();
    }

    private function _populatePerformer($performerId) {

        if (!empty($performerId)) {
            $this->performer = $this->employeeService->getEmployee($performerId);
        }
    }

    private function _populateRecipient() {

        $this->recipient = $this->leaveRequest->getEmployee();
    }

    public function sendToAssignee() {

        $to = $this->recipient->getEmpWorkEmail();

        if (!empty($to)) {

            try {

                $this->message->setFrom($this->getSystemFrom());
                $this->message->setTo($to);

                $message = $this->getMailContent($this->performer, $this->recipient, $this->leaveRequest, $this->leaveList);

                $this->message->setSubject($message->generateSubject());
                $this->message->setBody($message->generateBody());

                $this->mailer->send($this->message);

                $logMessage = "Leave assignment email was sent to $to";
                $this->logResult('Success', $logMessage);
            } catch (Exception $e) {

                $logMessage = "Couldn't send leave assignment email to $to";
                $logMessage .= '. Reason: ' . $e->getMessage();
                $this->logResult('Failure', $logMessage);
            }
        }
    }

    /*
     * Send mail notifications to supervisors of the assignee
     */

    public function sendToSupervisors() {

        $supervisors = $this->recipient->getSupervisors();

        if (count($supervisors) > 0) {

            foreach ($supervisors as $supervisor) {

                $to = $supervisor->getEmpWorkEmail();

                if (!empty($to) && ((empty($this->performer) || (($this->performer instanceof Employee) && ($to != $this->performer->getEmpWorkEmail()))))) {

                    try {

                        $this->message->setFrom($this->getSystemFrom());
                        $this->message->setTo($to);

                        $message = $this->getMailContent($this->performer, $supervisor, $this->leaveRequest, $this->leaveList);

                        $this->message->setSubject($message->generateSubjectForSupervisors());
                        $this->message->setBody($message->generateBodyForSupervisors());

                        $this->mailer->send($this->message);

                        $logMessage = "Leave assignment email was sent to $to";
                        $this->logResult('Success', $logMessage);
                    } catch (Exception $e) {

                        $logMessage = "Couldn't send leave assignment email to $to";
                        $logMessage .= '. Reason: ' . $e->getMessage();
                        $this->logResult('Failure', $logMessage);
                    }
                }
            }
        }
    }

    /*
     * Send mail notifications to subscribers
     */

    public function sendToSubscribers() {

        $mailNotificationService = new EmailNotificationService();
        $subscriptions = $mailNotificationService->getSubscribersByNotificationId(EmailNotification::LEAVE_ASSIGNMENT);

        foreach ($subscriptions as $subscription) {

            if ($subscription instanceof EmailSubscriber) {

                if ($subscription->getEmailNotification()->getIsEnable() == EmailNotification::ENABLED) {

                    $to = $subscription->getEmail();

                    try {

                        $this->message->setFrom($this->getSystemFrom());
                        $this->message->setTo($to);

                        $message = $this->getMailContent($this->performer, NULL, $this->leaveRequest, $this->leaveList);

                        $this->message->setSubject($message->generateSubscriberSubject());
                        $this->message->setBody($message->generateSubscriberBody());

                        $this->mailer->send($this->message);

                        $logMessage = "Leave assignment subscription email was sent to $to";
                        $this->logResult('Success', $logMessage);
                    } catch (Exception $e) {

                        $logMessage = "Couldn't send leave assignment subscription email to $to";
                        $logMessage .= '. Reason: ' . $e->getMessage();
                        $this->logResult('Failure', $logMessage);
                    }
                }
            }
        }
    }

    public function send() {

        if (!empty($this->mailer)) {

            $this->sendToAssignee();
            $this->sendToSupervisors();
            $this->sendToSubscribers();
        }
    }

    protected function getMailContent($performer, $recipient, $leaveRequest, $leaveList) {

        return new LeaveAssignmentMailContent($performer, $recipient, $leaveRequest, $leaveList);
    }

}