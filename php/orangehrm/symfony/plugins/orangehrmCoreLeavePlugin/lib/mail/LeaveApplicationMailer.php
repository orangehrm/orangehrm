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

class LeaveApplicationMailer extends orangehrmLeaveMailer {

    public function  __construct($performer, $leaveRequest, $leaveList) {

        parent::__construct();

        $this->performer = $performer;
        $this->leaveRequest = $leaveRequest;
        $this->leaveList = $leaveList;

    }

    public function sendToSupervisors() {

        $supervisors = $this->performer->getSupervisors();

        if (count($supervisors) > 0) {

            foreach ($supervisors as $supervisor) {

                $to = $supervisor->getEmpWorkEmail();

                if (!empty($to)) {

                    try {

                        $this->message->setFrom($this->getSystemFrom());
                        $this->message->addTo($to);

                        $message = new LeaveApplicationMailContent($this->performer, $supervisor, $this->leaveRequest, $this->leaveList);

                        $this->message->setSubject($message->generateSubject());
                        $this->message->setBody($message->generateBody());

                        $this->mailer->send($this->message);

                        $logMessage = "Leave application email was sent to $to";
                        $this->logResult('Success', $logMessage);

                    } catch (Exception $e) {

                        $logMessage = "Couldn't send leave application email to $to";
                        $logMessage .= '. Reason: '.$e->getMessage();
                        $this->logResult('Failure', $logMessage);

                    }

                }

            }

        }

    }

    public function sendToSubscribers() {

        $mailNotificationService = new MailService();
        $subscription = $mailNotificationService->getSubscription(MailNotification::LEAVE_APPLICATION);

        if ($subscription instanceof MailNotification) {

            if ($subscription->getStatus() == MailNotification::STATUS_SUBSCRIBED) {

                $to = $subscription->getEmail();

                try {

                    $this->message->setFrom($this->getSystemFrom());
                    $this->message->setTo($to);

                    $message = new LeaveApplicationMailContent($this->performer, NULL, $this->leaveRequest, $this->leaveList);

                    $this->message->setSubject($message->generateSubject());
                    $this->message->setBody($message->generateSubscriberBody());

                    $this->mailer->send($this->message);

                    $logMessage = "Leave application subscription email was sent to $to";
                    $this->logResult('Success', $logMessage);

                } catch (Exception $e) {

                    $logMessage = "Couldn't send leave application subscription email to $to";
                    $logMessage .= '. Reason: '.$e->getMessage();
                    $this->logResult('Failure', $logMessage);

                }

            }

        }

    }

    public function send() {

        if (!empty($this->mailer)) {

            $this->sendToSupervisors();
            $this->sendToSubscribers();

        }

    }
    
}

