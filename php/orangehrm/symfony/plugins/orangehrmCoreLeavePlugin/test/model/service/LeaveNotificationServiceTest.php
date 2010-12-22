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

/**
 * Testing of LeaveNotificationService
 *
 * @author Sujith T
 */
class LeaveNotificationServiceTest extends PHPUnit_Framework_TestCase {
    private $notificationService;
    private $fixture;

    /* set up */
    
    protected function setUp()
    {
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmCoreLeavePlugin/test/fixtures/LeaveNotificationService.yml';
        $this->notificationService = new LeaveNotificationService();
    }

    //public function testSendAssignLeaveNotification() {}

    /* test sendAssignLeaveNotification */
    
    public function testSendAssignLeaveNotification() {

        $userId = 1;
        $this->notificationService->setUserSettings('user', $userId);

        $employees      = TestDataService::loadObjectList('Employee', $this->fixture, 'Employee');
        $leaveRequests  = TestDataService::loadObjectList('LeaveRequest', $this->fixture, 'LeaveRequest');
        $leaves         = TestDataService::loadObjectList('Leave', $this->fixture, 'Leave');
        $users          = TestDataService::loadObjectList('Users', $this->fixture, 'Users');
        
        $leaveRequests[0]->setEmployee($employees[0]);

        $userService = $this->getMock('UserService', array('readUser'));
        $userService->expects($this->once())
                    ->method('readUser')
                    ->with($userId)
                    ->will($this->returnValue($users[0]));

        $emailService = $this->getMock('EmailService', array('sendEmail'));
        $emailService->expects($this->once())
                    ->method('sendEmail')
                    ->will($this->returnValue(true));
        
        $this->notificationService->setUserService($userService);
        $this->notificationService->setEmailService($emailService);
        $result = $this->notificationService->sendAssignLeaveNotification($leaveRequests[0], $leaves);

        $this->assertTrue($result);
    }

    /* test sendApplyLeaveNotification */
    
    public function testSendApplyLeaveNotification() {

        $userId = 1;
        $this->notificationService->setUserSettings('user', $userId);

        $employees      = TestDataService::loadObjectList('Employee', $this->fixture, 'Employee');
        $leaveRequests  = TestDataService::loadObjectList('LeaveRequest', $this->fixture, 'LeaveRequest');
        $leaves         = TestDataService::loadObjectList('Leave', $this->fixture, 'Leave');
        $users          = TestDataService::loadObjectList('Users', $this->fixture, 'Users');

        $leaveRequests[0]->setEmployee($employees[0]);

        $userService = $this->getMock('UserService', array('readUser'));
        $userService->expects($this->once())
                    ->method('readUser')
                    ->with($_SESSION['user'])
                    ->will($this->returnValue($users[0]));

        $notificationList = array(MailService::EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_JOB_APPLIED => 1, 2 => 1);
        $mailService = $this->getMock('MailService', array('getMailNotificationList'));
        $mailService->expects($this->once())
                    ->method('getMailNotificationList')
                    ->with($userId)
                    ->will($this->returnValue($notificationList));

        $emailService = $this->getMock('EmailService', array('sendEmail'));
        $emailService->expects($this->once())
                    ->method('sendEmail')
                    ->will($this->returnValue(true));

        $this->notificationService->setUserService($userService);
        $this->notificationService->setMailService($mailService);
        $this->notificationService->setEmailService($emailService);

        
        $this->assertTrue($this->notificationService->sendApplyLeaveNotification($leaveRequests[0], $leaves));
    }

    /* test sendApproveLeaveNotification */

    public function testSendApproveLeaveNotification() {

        $userId = 1;
        $this->notificationService->setUserSettings('user', $userId);
        
        $employees      = TestDataService::loadObjectList('Employee', $this->fixture, 'Employee');
        $leaveRequests  = TestDataService::loadObjectList('LeaveRequest', $this->fixture, 'LeaveRequest');
        $leaves         = TestDataService::loadObjectList('Leave', $this->fixture, 'Leave');
        $users          = TestDataService::loadObjectList('Users', $this->fixture, 'Users');
        
        $leaveRequests[0]->setEmployee($employees[0]);

        $userService = $this->getMock('UserService', array('readUser'));
        $userService->expects($this->once())
                    ->method('readUser')
                    ->with($userId)
                    ->will($this->returnValue($users[0]));
        
        $notificationList = array(MailService::EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_LEAVE_APPROVED => 1, 2 => 1);
        $mailService = $this->getMock('MailService', array('getMailNotificationList'));
        $mailService->expects($this->once())
                    ->method('getMailNotificationList')
                    ->with($userId)
                    ->will($this->returnValue($notificationList));

        $emailService = $this->getMock('EmailService', array('sendEmail'));
        $emailService->expects($this->once())
                    ->method('sendEmail')
                    ->will($this->returnValue(true));

        $this->notificationService->setUserService($userService);
        $this->notificationService->setMailService($mailService);
        $this->notificationService->setEmailService($emailService);

        $this->assertTrue($this->notificationService->sendApproveLeaveNotification($leaveRequests[0], $leaves));

    }

    /* test sendCanceledLeaveNotification for Employee Type */
    
    public function testSendCanceledLeaveNotificationForEmployeeType() {

        $userId = 1;
        $this->notificationService->setUserSettings('user', $userId);
        
        $employees      = TestDataService::loadObjectList('Employee', $this->fixture, 'Employee');
        $leaveRequests  = TestDataService::loadObjectList('LeaveRequest', $this->fixture, 'LeaveRequest');
        $leaves         = TestDataService::loadObjectList('Leave', $this->fixture, 'Leave');
        $users          = TestDataService::loadObjectList('Users', $this->fixture, 'Users');

        $leaveRequests[0]->setEmployee($employees[0]);

        $notificationList = array(MailService::EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_LEAVE_CANCELLED => 1, 2 => 1);
        $mailService = $this->getMock('MailService', array('getMailNotificationList'));
        $mailService->expects($this->once())
                    ->method('getMailNotificationList')
                    ->with($userId)
                    ->will($this->returnValue($notificationList));

        $userService = $this->getMock('UserService', array('readUser'));
        $userService->expects($this->once())
                    ->method('readUser')
                    ->with($userId)
                    ->will($this->returnValue($users[0]));

        $emailService = $this->getMock('EmailService', array('sendEmail'));
        $emailService->expects($this->once())
                    ->method('sendEmail')
                    ->will($this->returnValue(true));

        $this->notificationService->setMailService($mailService);
        $this->notificationService->setUserService($userService);
        $this->notificationService->setEmailService($emailService);
        
        $result = $this->notificationService->sendCanceledLeaveNotification($leaveRequests[0], $leaves, Users::USER_TYPE_EMPLOYEE);
        $this->assertTrue($result);
        
    }

    /* test sendCanceledLeaveNotification for Supervisor or AdminType */

    public function testSendCanceledLeaveNotificationForSupervisorOrAdminType() {

        $userId = 1;
        $this->notificationService->setUserSettings('user', $userId);

        $employees      = TestDataService::loadObjectList('Employee', $this->fixture, 'Employee');
        $leaveRequests  = TestDataService::loadObjectList('LeaveRequest', $this->fixture, 'LeaveRequest');
        $leaves         = TestDataService::loadObjectList('Leave', $this->fixture, 'Leave');
        $users          = TestDataService::loadObjectList('Users', $this->fixture, 'Users');

        $leaveRequests[0]->setEmployee($employees[0]);

        $notificationList = array(MailService::EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_LEAVE_CANCELLED => 1, 2 => 1);
        $mailService = $this->getMock('MailService', array('getMailNotificationList'));
        $mailService->expects($this->once())
                    ->method('getMailNotificationList')
                    ->with($userId)
                    ->will($this->returnValue($notificationList));

        $userService = $this->getMock('UserService', array('readUser'));
        $userService->expects($this->once())
                    ->method('readUser')
                    ->with($userId)
                    ->will($this->returnValue($users[0]));

        $emailService = $this->getMock('EmailService', array('sendEmail'));
        $emailService->expects($this->once())
                    ->method('sendEmail')
                    ->will($this->returnValue(true));

        $this->notificationService->setMailService($mailService);
        $this->notificationService->setUserService($userService);
        $this->notificationService->setEmailService($emailService);

        $result = $this->notificationService->sendCanceledLeaveNotification($leaveRequests[0], $leaves, Users::USER_TYPE_SUPERVISOR);
        $this->assertTrue($result);

    }

    /* test sendRejectedLeaveNotification */
    
    public function testSendRejectedLeaveNotification() {

        $userId = 1;
        $this->notificationService->setUserSettings('user', $userId);

        $employees      = TestDataService::loadObjectList('Employee', $this->fixture, 'Employee');
        $leaveRequests  = TestDataService::loadObjectList('LeaveRequest', $this->fixture, 'LeaveRequest');
        $leaves         = TestDataService::loadObjectList('Leave', $this->fixture, 'Leave');
        $users          = TestDataService::loadObjectList('Users', $this->fixture, 'Users');

        $leaveRequests[0]->setEmployee($employees[0]);

        $notificationList = array(MailService::EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_LEAVE_REJECTED => 1, 2 => 1);
        $mailService = $this->getMock('MailService', array('getMailNotificationList'));
        $mailService->expects($this->once())
                    ->method('getMailNotificationList')
                    ->with($userId)
                    ->will($this->returnValue($notificationList));

        $userService = $this->getMock('UserService', array('readUser'));
        $userService->expects($this->once())
                    ->method('readUser')
                    ->with($userId)
                    ->will($this->returnValue($users[0]));

        $emailService = $this->getMock('EmailService', array('sendEmail'));
        $emailService->expects($this->once())
                    ->method('sendEmail')
                    ->will($this->returnValue(true));
        
        $this->notificationService->setMailService($mailService);
        $this->notificationService->setUserService($userService);
        $this->notificationService->setEmailService($emailService);
        
        $result = $this->notificationService->sendRejectedLeaveNotification($leaveRequests[0], $leaves);

        $this->assertTrue($result);
    }
}
?>
