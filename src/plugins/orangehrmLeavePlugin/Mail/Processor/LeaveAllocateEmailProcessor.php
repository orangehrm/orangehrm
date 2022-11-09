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

namespace OrangeHRM\Leave\Mail\Processor;

use InvalidArgumentException;
use OrangeHRM\Core\Mail\AbstractRecipient;
use OrangeHRM\Core\Mail\MailProcessor;
use OrangeHRM\Core\Traits\Service\NumberHelperTrait;
use OrangeHRM\Framework\Event\Event;
use OrangeHRM\Leave\Event\LeaveAllocate;
use OrangeHRM\Leave\Mail\Recipient;
use OrangeHRM\Leave\Traits\Service\LeaveRequestServiceTrait;
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;

class LeaveAllocateEmailProcessor extends AbstractLeaveEmailProcessor implements MailProcessor
{
    use EmployeeServiceTrait;
    use LeaveRequestServiceTrait;
    use NumberHelperTrait;

    public function getReplacements(
        string $emailName,
        AbstractRecipient $recipient,
        string $recipientRole,
        string $performerRole,
        Event $event
    ): array {
        if (!$event instanceof LeaveAllocate) {
            throw new InvalidArgumentException(
                'Expected instance of `' . LeaveAllocate::class . '` got `' . get_class($event) . '`'
            );
        }
        $replacements = [];
        $performer = $event->getPerformer()->getEmployee();
        $replacements['performerFirstName'] = $performer->getFirstName();
        $replacements['performerFullName'] = $performer->getDecorator()->getFirstAndLastNames();

        $replacements['recipientFirstName'] = $recipient->getName();
        $replacements['recipientFullName'] = $recipient->getName();
        if ($recipient instanceof Recipient) {
            $replacements['recipientFirstName'] = $recipient->getFirstName();
        }

        $applicant = $event->getDetailedLeaveRequest()->getLeaveRequest()->getEmployee();
        $replacements['applicantFullName'] = $applicant->getDecorator()->getFirstAndLastNames();
        $replacements['assigneeFullName'] = $applicant->getDecorator()->getFirstAndLastNames();

        $event->getDetailedLeaveRequest()->fetchLeaves();
        $leaves = $event->getDetailedLeaveRequest()->getLeaves();
        $detailedLeaves = $this->getLeaveRequestService()->getDetailedLeaves($leaves, $leaves);

        $replacements['leaveType'] = $event->getDetailedLeaveRequest()->getLeaveRequest()->getLeaveType()->getName();
        $replacements['leaveDetails'] = [];
        $replacements['numberOfDays'] = $this->getNumberHelper()->numberFormat(
            $event->getDetailedLeaveRequest()->getNoOfDays(),
            2
        );
        $replacements['leaveDetails'] = $this->getLeaveDetailsByDetailedLeaves($detailedLeaves);
        $leaveRequestId = $event->getDetailedLeaveRequest()->getLeaveRequest()->getId();
        $replacements['leaveRequestComments'] = $this->getLeaveRequestComments($leaveRequestId);

        return $replacements;
    }

    /**
     * @inheritDoc
     */
    public function getRecipients(
        string $emailName,
        string $recipientRole,
        string $performerRole,
        Event $event
    ): array {
        if (!$event instanceof LeaveAllocate) {
            throw new InvalidArgumentException(
                'Expected instance of `' . LeaveAllocate::class . '` got `' . get_class($event) . '`'
            );
        }

        $recipients = [];
        switch ($recipientRole) {
            case 'subscriber' :
                $recipients = $this->getSubscribers($emailName);
                break;
            case 'supervisor':
                $recipients = $this->getSupervisors(
                    $event->getDetailedLeaveRequest()->getLeaveRequest()->getEmployee()
                );
                break;
            case 'ess':
                $recipients = $this->getSelf($event->getDetailedLeaveRequest()->getLeaveRequest()->getEmployee());
                break;
            default:
                $recipients = $this->getEmployeesWithRole(
                    $recipientRole,
                    $event->getDetailedLeaveRequest()->getLeaveRequest()->getEmployee()
                );
                break;
        }

        return $recipients;
    }
}
