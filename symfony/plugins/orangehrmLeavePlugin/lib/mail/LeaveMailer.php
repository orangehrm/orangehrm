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
 *
 */

/**
 * Leave Mail observer
 */
class LeaveMailer implements ohrmObserver {
    
    protected $emailService;
    
    /**
     * Get email service instance
     * @return EmailService
     */
    public function getEmailService() {
        if (empty($this->emailService)) {
            $this->emailService = new EmailService();
        }
        return $this->emailService;
    }

    public function setEmailService(EmailService $emailService) {
        $this->emailService = $emailService;
    }

    public function listen(sfEvent $event) {
                
        $logger = Logger::getLogger('leave.leavemailer');
        
        $eventData = $event->getParameters();
        
        if ($logger->isDebugEnabled()) {
            $logger->debug('Got event');
        }        
        
        if (isset($eventData['workFlow']) && 
                (($eventData['workFlow'] instanceof WorkflowStateMachine) 
                        || (is_array($eventData['workFlow']) && count($eventData['workFlow'])> 0))) {
            
            $recipientRoles = array();            
            $performerRole = null;
            $workFlow = $eventData['workFlow'];
            
            if (is_array($workFlow)) {
                
                // Multiple workflows
                if ($logger->isDebugEnabled()) {
                    $logger->debug("Multiple workflows in event");
                }                 
                
                $emailType = 'leave.change';
                
                $firstFlow = array_shift(array_values($workFlow));
                $performerRole = $firstFlow->getRole();
                
                foreach ($workFlow as $item) {
                    $roles = $item->getRolesToNotifyAsArray();
                    $recipientRoles = array_unique($recipientRoles + $roles);                    
                }
            } else {
                $recipientRoles = $workFlow->getRolesToNotifyAsArray();
                $performerRole = $workFlow->getRole();
                $emailType = 'leave.' . strtolower($workFlow->getAction());                                    
            }

            if ($logger->isDebugEnabled()) {
                $logger->debug("Email type: $emailType, Roles: " . print_r($recipientRoles, true));
            } 
            
            if (count($recipientRoles) > 0) {                 
                $this->getEmailService()->sendEmailNotifications($emailType, $recipientRoles, $eventData, 
                        strtolower($performerRole));                  
            } 
        } else {
            $logger->warn('event did not contain valid workFlow');
        }            
        
    }
}
