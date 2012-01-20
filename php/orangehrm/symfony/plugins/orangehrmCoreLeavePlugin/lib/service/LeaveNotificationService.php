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
    /**
     *
     * Send leave approval email.
     *
     * @param <type> $leaveList
     * @param <type> $performerType
     * @param <type> $performerId
     * @param <type> $requestType
     */
    public function approve($leaveList, $performerType, $performerId, $requestType) {
        $leaveApprovalMailer = new LeaveApprovalMailer($leaveList, $performerType, $performerId, $requestType);
        $leaveApprovalMailer->send();
    }

    /**
     * Send Leave rejection email.
     *
     * @param <type> $leaveList
     * @param <type> $performerType
     * @param <type> $performerId
     * @param <type> $requestType
     */
    public function reject($leaveList, $performerType, $performerId, $requestType) {
        $leaveRejectionMailer = new LeaveRejectionMailer($leaveList, $performerType, $performerId, $requestType);
        $leaveRejectionMailer->send();
    }

    /**
     * Send leave cancellation email.
     *
     * @param <type> $leaveList
     * @param <type> $performerType
     * @param <type> $performerId
     * @param <type> $requestType
     */
    public function cancel($leaveList, $performerType, $performerId, $requestType) {
        $leaveCancellationMailer = new LeaveCancellationMailer($leaveList, $performerType, $performerId, $requestType);
        $leaveCancellationMailer->send();
    }

    /**
     * Send leave cancellation email for employee.
     *
     * @param <type> $leaveList
     * @param <type> $performerType
     * @param <type> $performerId
     * @param <type> $requestType
     */
    public function cancelEmployee($leaveList, $performerType, $performerId, $requestType) {
        $leaveCancellationMailer = new LeaveEmployeeCancellationMailer($leaveList, $performerType, $performerId, $requestType);
        $leaveCancellationMailer->send();
    }

}
