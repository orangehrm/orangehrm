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

namespace OrangeHRM\Leave\Mail\Processor;

use OrangeHRM\Admin\Dto\EmailSubscriberSearchFilterParams;
use OrangeHRM\Admin\Service\EmailSubscriberService;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\EmailNotification;
use OrangeHRM\Entity\EmailSubscriber;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Leave;
use OrangeHRM\Leave\Dto\LeaveDuration;
use OrangeHRM\Leave\Dto\LeaveRequest\DetailedLeave;
use OrangeHRM\Leave\Dto\LeaveRequestCommentSearchFilterParams;
use OrangeHRM\Leave\Mail\Recipient;
use OrangeHRM\Leave\Service\LeaveRequestCommentService;

abstract class AbstractLeaveEmailProcessor
{
    use UserRoleManagerTrait;
    use DateTimeHelperTrait;

    protected ?LeaveRequestCommentService $leaveRequestCommentService = null;

    /**
     * @return LeaveRequestCommentService
     */
    public function getLeaveRequestCommentService(): LeaveRequestCommentService
    {
        if (!$this->leaveRequestCommentService instanceof LeaveRequestCommentService) {
            $this->leaveRequestCommentService = new LeaveRequestCommentService();
        }
        return $this->leaveRequestCommentService;
    }

    /**
     * @param $notificationId
     * @return EmailSubscriber[]
     */
    protected function getSubscribersForEvent($notificationId): array
    {
        $mailNotificationService = new EmailSubscriberService();
        $emailSubscriberSearchFilterParams = new EmailSubscriberSearchFilterParams();
        $emailSubscriberSearchFilterParams->setEnabled(true);
        return $mailNotificationService->getEmailSubscriberDao()->getEmailSubscribersByEmailSubscriptionId(
            $notificationId,
            $emailSubscriberSearchFilterParams
        );
    }

    /**
     * @param string $role
     * @param Employee $employee
     * @return Recipient[]
     */
    protected function getEmployeesWithRole(string $role, Employee $employee): array
    {
        $recipients = [];
        $employees = $this->getUserRoleManager()->getEmployeesWithRole(
            $role,
            [Employee::class => $employee->getEmpNumber()]
        );
        foreach ($employees as $employee) {
            if (!is_null($employee->getWorkEmail())) {
                $recipients[] = new Recipient(
                    $employee->getWorkEmail(),
                    $employee->getDecorator()->getFirstAndLastNames(),
                    $employee->getFirstName()
                );
            }
        }
        return $recipients;
    }

    /**
     * @param Employee $employee
     * @return Recipient[]
     */
    protected function getSelf(Employee $employee): array
    {
        $recipients = [];
        if (!is_null($employee->getWorkEmail())) {
            $recipients[] = new Recipient(
                $employee->getWorkEmail(),
                $employee->getDecorator()->getFirstAndLastNames(),
                $employee->getFirstName()
            );
        }

        return $recipients;
    }

    /**
     * @param string $emailName
     * @return Recipient[]
     */
    protected function getSubscribers(string $emailName): array
    {
        $recipients = [];
        $notification = null;
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
            foreach ($subscribers as $subscriber) {
                $recipients[] = new Recipient(
                    $subscriber->getEmail(),
                    $subscriber->getName(),
                    $subscriber->getName()
                );
            }
        }

        return $recipients;
    }

    /**
     * @param Employee $performer
     * @return Recipient[]
     */
    protected function getSupervisors(Employee $performer): array
    {
        $recipients = [];
        $supervisors = $performer->getSupervisors();

        foreach ($supervisors as $supervisor) {
            if (!is_null($supervisor->getWorkEmail())) {
                $recipients[] = new Recipient(
                    $supervisor->getWorkEmail(),
                    $supervisor->getDecorator()->getFirstAndLastNames(),
                    $supervisor->getFirstName()
                );
            }
        }

        return $recipients;
    }

    /**
     * @param DetailedLeave[] $detailedLeaves
     * @return array
     */
    protected function getLeaveDetailsByDetailedLeaves(array $detailedLeaves): array
    {
        $leaveDetails = [];
        foreach ($detailedLeaves as $detailedLeave) {
            $status = $detailedLeave->getLeave()->getStatus();
            if (in_array($status, [Leave::LEAVE_STATUS_LEAVE_WEEKEND, Leave::LEAVE_STATUS_LEAVE_HOLIDAY])) {
                continue;
            }
            $dates = $detailedLeave->getDatesDetail();
            $leaveDatePeriod = $this->getDateTimeHelper()->formatDateTimeToYmd($dates->getFromDate());
            $startTime = $this->getDateTimeHelper()->formatDateTimeToTimeString($dates->getStartTime());
            $endTime = $this->getDateTimeHelper()->formatDateTimeToTimeString($dates->getEndTime());
            if (!is_null($startTime) && !is_null($endTime)) {
                $leaveDatePeriod .= " ($startTime - $endTime)";
            }

            if ($dates->getDurationType() === LeaveDuration::HALF_DAY_MORNING ||
                $dates->getDurationType() === LeaveDuration::HALF_DAY_AFTERNOON) {
                $leaveDatePeriod .= ' Half Day';
            }
            $leaveDetails[] = [
                'date' => $leaveDatePeriod,
                'duration' => number_format($detailedLeave->getLeave()->getLengthHours(), 2),
            ];
        }
        return $leaveDetails;
    }

    /**
     * @param int $leaveRequestId
     * @return array
     */
    protected function getLeaveRequestComments(int $leaveRequestId): array
    {
        $leaveRequestCommentSearchFilterParams = new LeaveRequestCommentSearchFilterParams();
        $leaveRequestCommentSearchFilterParams->setLeaveRequestId($leaveRequestId);
        $leaveRequestComments = $this->getLeaveRequestCommentService()
            ->getLeaveRequestCommentDao()
            ->searchLeaveRequestComments($leaveRequestCommentSearchFilterParams);
        $comments = [];
        if (count($leaveRequestComments) > 0) {
            foreach ($leaveRequestComments as $leaveRequestComment) {
                $comments[] = [
                    'name' => $leaveRequestComment->getCreatedByEmployee()->getDecorator()->getFirstAndLastNames(),
                    'date' => $leaveRequestComment->getDecorator()->getCreatedAtDate(),
                    'time' => $leaveRequestComment->getDecorator()->getCreatedAtTime(),
                    'comment' => $leaveRequestComment->getComment(),
                ];
            }
        }
        return $comments;
    }
}
