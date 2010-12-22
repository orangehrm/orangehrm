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

class MailService extends BaseService {
   
   //preserving old functionality while on refactoring
   const EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_LEAVE_REJECTED         = -1;
   const EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_LEAVE_CANCELLED        = 0;
   const EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_LEAVE_PENDING_APPROVAL = 1;
   const EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_LEAVE_APPROVED         = 2;
   const EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_HSP                    = 3;
   const EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_JOB_APPLIED            = 4;
   const EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_SEEK_HIRE_APPROVAL     = 5;
   const EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_HIRE_TASKS             = 6;
   const EMAILNOTIFICATIONCONFIGURATION_NOTIFICATION_TYPE_HIRE_APPROVED          = 7;
   const EMAILCONFIGURATION_FILE_CONFIG = '/lib/confs/mailConf.php';

   private $mailDao;

   /**
    * Constructor
    */
   public function __construct() {
      $this->mailDao = new MailDao();
   }

   /**
    * Set MailDao
    * @param MailDao $mailDao
    */
   public function setMailDao(MailDao $mailDao) {
      $this->mailDao = $mailDao;
   }

   /**
    * Returns MailDao
    * @returns MailDao
    */
   public function getMailDao() {
      return $this->mailDao;
   }

   /**
    * Save MailNotification
    * @param MailNotification $mailNotification
    * @returns boolean
    * @throws AdminServiceException
    */
   public function saveMailNotification(MailNotification $mailNotification) {
      try{
         return $this->mailDao->saveMailNotification($mailNotification);
      }catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Get Email Notification List
    * @param int $userId
    * @returns array()
    * @throws AdminServiceException
    */
   public function getMailNotificationList($userId) {
      try{
         return $this->mailDao->getMailNotificationList($userId);
      }catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Delete MailNotification. The name not consistant with standard naming convention, but just added for backward compatibility
    * @param int $userId
    * @returns boolean
    * @throws DaoException
    */
   public function removeMailNotification($userId) {
      try{
         return $this->mailDao->deleteMailNotification($userId);
      }catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   /**
    * Read Mail Configeration
    * @returns String
    * @throws FileNotFoundException, AdminServiceException
    */
   public function readMailConfigeration() {
      try {
         if(!file_exists(self::EMAILCONFIGURATION_FILE_CONFIG)) {
            throw new FileNotFoundException("Mail configuration file pointed in " . self::EMAILCONFIGURATION_FILE_CONFIG . " doesn't exists");
         }
         return file_get_contents(self::EMAILCONFIGURATION_FILE_CONFIG);
      } catch(Exception $e) {
         throw new AdminServiceException($e->getMessage());
      }
   }

   public function getSubscription($type) {
       return $this->mailDao->getSubscription($type);
   }

}
?>