<?php

namespace OrangeHRM\Leave\Listener;

use OrangeHRM\Entity\Leave;
use OrangeHRM\Leave\Event\LeaveApprove;
use OrangeHRM\Core\Email\Message;
use OrangeHRM\Core\Email\EmailService;

class SickLeaveApprovalListener
{
    public function __invoke(LeaveApprove $event): void
    {
        $leaves = $event->getLeaves();

        if (!$leaves || count($leaves) == 0) {
            return;
        }

        // Get employee
        $employee = $leaves[0]->getEmployee();

        if (!$employee || !$employee->getWorkEmail()) {
            return;
        }

        // Check Sick Leave
        if (strtolower($leaves[0]->getLeaveType()->getName()) !== 'sick leave') {
            return;
        }

        // Get consecutive count
        $dates = [];

        foreach ($leaves as $leave) {

            if ((int)$leave->getStatus() === Leave::LEAVE_STATUS_LEAVE_APPROVED) {
                $dates[] = $leave->getDate();
            }
        }

        sort($dates);

        $count = count($dates);

        if ($count <= 2) {
            return;
        }

        error_log(
            "Sick Leave Listener Triggered: Employee=" .
            $employee->getFirstName() .
            " Count=" . $count
        );

        // Send email
        $email = $employee->getWorkEmail();

        $subject = "Medical Certificate Required";

        $body = "Dear " . $employee->getFirstName() . ",\n\n"
              . "You have taken Sick Leave for more than 2 consecutive days.\n"
              . "Please submit medical certificate.\n\n"
              . "Regards,\nHR Team";

        $message = new Message();
        $message->setSubject($subject);
        $message->setBody($body);
        $message->addTo($email);

        $emailService = new EmailService();
        $emailService->send($message);
    }
}