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

class MailService extends BaseService {
	
	const EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_LEAVE_REJECTED = -1;
	const EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_LEAVE_CANCELLED = 0;
	const EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_LEAVE_PENDING_APPROVAL = 1;
	const EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_LEAVE_APPROVED = 2;
	const EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_HSP = 3;
	const EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_JOB_APPLIED = 4;
    const EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_SEEK_HIRE_APPROVAL = 5;
    const EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_HIRE_TASKS = 6;
    const EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_HIRE_APPROVED = 7;
    
    
	/**
     * Save MembershipType
     * @param MembershipType 
     * @return void
     */
    public function saveMailNotification(MailNotifications $mailNotifications)
    {
    	try
        {

        	$mailNotifications->save();
			
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
	/**
     * Get Mail Notification
     * @param MembershipType 
     * @return void
     */
    public function getMailNotificationList( $userId)
    {
    	$mailNotificationArr	=	array();
    	try
        {

        	$q = Doctrine_Query::create()
			    ->from('MailNotification m')
			    ->where("m.user_id='$userId'");
			    
			$mailNotificationList = $q->execute();

			foreach( $mailNotificationList as $mailNotification)
			{
				
				$mailNotificationArr[$mailNotification->getNotificationTypeId()] = $mailNotification->getStatus();
			}
			
			return  $mailNotificationArr ;
			
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
    
    /**
     * Remove Mail notification
     * @param MembershipType 
     * @return void
     */
    public function removeMailNotification( $userId )
    {
    	try
        {
	    	
	        	$q = Doctrine_Query::create()
					   ->delete('MailNotification m')
					   ->where("m.user_id='$userId'");
	
					   
				$numDeleted = $q->execute();
	    
        }catch( Exception $e)
        {
            throw new AdminServiceException($e->getMessage());
        }
    }
}