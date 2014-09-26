<?php
/**
 * MailDao for CRUD operation of MailNotification objects
 *
 */
class MailDao extends BaseDao {
   
   /**
    * Save MailNotification
    * @param MailNotification $mailNotification
    * @returns boolean
    * @throws DaoException
    */
   public function saveMailNotification(MailNotification $mailNotification) {
      try{
         $mailNotification->save();
         return true;
      }catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   /**
    * Get Email Notification List
    * @param int $userId
    * @returns array()
    * @throws DaoException
    * @todo need to change to return the collection not array
    */
   public function getMailNotificationList($userId) {
      $mailNotificationArr	=	array();
      try {
         $q = Doctrine_Query::create()
             ->from('MailNotification m')
             ->where("m.user_id= ?", $userId);

         $mailNotificationList = $q->execute();
         foreach($mailNotificationList as $mailNotification) {
            $mailNotificationArr[$mailNotification->getNotificationTypeId()] = $mailNotification->getStatus();
         }
         
         return $mailNotificationArr;
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

   public function getMailNotificationFullList() {

      try {
         $q = Doctrine_Query::create()->from('MailNotification m');

         return$q->execute();
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }
   
   public function getAllMailNotifications() {

        try {
            return Doctrine :: getTable('MailNotification')->findAll();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

   /**
    * Delete MailNotification
    * @param int $userId
    * @returns boolean
    * @throws DaoException
    */
   public function deleteMailNotification($userId) {
      try {
         $q = Doctrine_Query::create()
            ->delete('MailNotification m')
            ->where("m.user_id=?", $userId);

         $numDeleted = $q->execute();
         if($numDeleted > 0) {
            return true;
         }
         return false;
      } catch(Exception $e) {
         throw new DaoException($e->getMessage());
      }
   }

    public function getSubscription($type) {

        try {

            $q = Doctrine_Query::create()
                 ->from('MailNotification m')
                 ->where("m.notification_type_id= ?", $type);

            return $q->fetchOne();

        } catch(Exception $e) {

            throw new DaoException($e->getMessage());
            
        }

    }

}
?>
