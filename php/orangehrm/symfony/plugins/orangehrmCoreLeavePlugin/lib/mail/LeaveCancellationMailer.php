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

class LeaveCancellationMailer extends orangehrmLeaveMailer {

    public function  __construct($leaveList, $performerType, $performerId, $requestType) {

        parent::__construct();

        $this->employeeService = new EmployeeService();
        $this->leaveList = $leaveList;
        $this->performerType = $performerType;
        $this->_populatePerformer($performerId);
        $this->leaveRequest = $leaveList[0]->getLeaveRequest();
        $this->_populateRecipient();
        $this->requestType = $requestType;

    }

    private function _populatePerformer($performerId) {

        if (!empty($performerId)) {
            $this->performer = $this->employeeService->getEmployee($performerId);
        }

    }

    private function _populateRecipient() {

        $this->recipient = $this->leaveRequest->getEmployee();

    }

    public function sendToApplicant() {

        $to = $this->recipient->getEmpWorkEmail();

        if (!empty($to)) {

            try {

                $this->message->setFrom($this->getSystemFrom());
                $this->message->setTo($to);

                $message = new LeaveCancellationMailContent($this->performer, $this->recipient, $this->leaveRequest, $this->leaveList, $this->requestType);

                $this->message->setSubject($message->generateSubject());
                $this->message->setBody($message->generateBody());

                $this->mailer->send($this->message);

                $logMessage = "Leave cancellation email was sent to $to";
                $this->logResult('Success', $logMessage);

            } catch (Exception $e) {

                $logMessage = "Couldn't send leave cancellation email to $to";
                $logMessage .= '. Reason: '.$e->getMessage();
                $this->logResult('Failure', $logMessage);

            }

        }

    }

    public function sendToSubscribers() {

        $mailNotificationService = new MailService();
        $subscription = $mailNotificationService->getSubscription(MailNotification::LEAVE_CANCELLATION);

        if ($subscription instanceof MailNotification) {

            if ($subscription->getStatus() == MailNotification::STATUS_SUBSCRIBED) {

                $to = $subscription->getEmail();

                try {

                    $this->message->setFrom($this->getSystemFrom());
                    $this->message->setTo($to);

                    $message = new LeaveCancellationMailContent($this->performer, $this->recipient, $this->leaveRequest, $this->leaveList, $this->requestType);

                    $this->message->setSubject($message->generateSubscriberSubject());
                    $this->message->setBody($message->generateSubscriberBody());

                    $this->mailer->send($this->message);

                    $logMessage = "Leave cancellation subscription email was sent to $to";
                    $this->logResult('Success', $logMessage);

                } catch (Exception $e) {

                    $logMessage = "Couldn't send leave cancellation subscription email to $to";
                    $logMessage .= '. Reason: '.$e->getMessage();
                    $this->logResult('Failure', $logMessage);

                }

            }

        }

    }

    public function send() {

        if (!empty($this->mailer)) {

            $this->sendToApplicant();
            $this->sendToSubscribers();

        }

    }
    
}

