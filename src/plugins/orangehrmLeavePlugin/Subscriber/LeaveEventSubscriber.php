<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Leave\Subscriber;

use InvalidArgumentException;
use OrangeHRM\Core\Service\EmailService;
use OrangeHRM\Framework\Event\AbstractEventSubscriber;
use OrangeHRM\Leave\Event\LeaveAllocate;
use OrangeHRM\Leave\Event\LeaveApply;
use OrangeHRM\Leave\Event\LeaveApprove;
use OrangeHRM\Leave\Event\LeaveAssign;
use OrangeHRM\Leave\Event\LeaveCancel;
use OrangeHRM\Leave\Event\LeaveEvent;
use OrangeHRM\Leave\Event\LeaveReject;
use OrangeHRM\Leave\Event\LeaveStatusChange;

class LeaveEventSubscriber extends AbstractEventSubscriber
{
    private ?EmailService $emailService = null;

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            LeaveEvent::APPLY => [['onAllocateEvent', 0]],
            LeaveEvent::ASSIGN => [['onAllocateEvent', 0]],
            LeaveEvent::APPROVE => [['onStatusChangeEvent', 0]],
            LeaveEvent::CANCEL => [['onStatusChangeEvent', 0]],
            LeaveEvent::REJECT => [['onStatusChangeEvent', 0]],
        ];
    }

    /**
     * @return EmailService
     */
    public function getEmailService(): EmailService
    {
        if (!$this->emailService instanceof EmailService) {
            $this->emailService = new EmailService();
        }
        return $this->emailService;
    }

    /**
     * @param LeaveAllocate $allocateEvent
     */
    public function onAllocateEvent(LeaveAllocate $allocateEvent): void
    {
        $leaveRequest = $allocateEvent->getDetailedLeaveRequest();
        $leaveRequest->getLeaves();
        if ($allocateEvent instanceof LeaveApply) {
            $emailName = 'leave.apply';
        } elseif ($allocateEvent instanceof LeaveAssign) {
            $emailName = 'leave.assign';
        } else {
            throw new InvalidArgumentException('Invalid instance of `' . LeaveAllocate::class . '` provided');
        }

        $workflow = $allocateEvent->getWorkflow();
        $recipientRoles = $workflow->getDecorator()->getRolesToNotify();
        $performerRole = strtolower($workflow->getRole());

        $this->getEmailService()->queueEmailNotifications($emailName, $recipientRoles, $performerRole, $allocateEvent);
    }

    /**
     * @param LeaveStatusChange $statusChangeEvent
     */
    public function onStatusChangeEvent(LeaveStatusChange $statusChangeEvent): void
    {
        if ($statusChangeEvent instanceof LeaveApprove) {
            $emailName = 'leave.approve';
        } elseif ($statusChangeEvent instanceof LeaveCancel) {
            $emailName = 'leave.cancel';
        } elseif ($statusChangeEvent instanceof LeaveReject) {
            $emailName = 'leave.reject';
        } else {
            throw new InvalidArgumentException('Invalid instance of `' . LeaveAllocate::class . '` provided');
        }

        $workflow = $statusChangeEvent->getWorkflow();
        $recipientRoles = $workflow->getDecorator()->getRolesToNotify();
        $performerRole = strtolower($workflow->getRole());

        $this->getEmailService()->queueEmailNotifications(
            $emailName,
            $recipientRoles,
            $performerRole,
            $statusChangeEvent
        );
    }
}
