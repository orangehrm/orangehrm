<?php

class BeaconNotificationDao extends BaseDao {

    public function deleteNotificationByName($name) {
        try {
            $query = Doctrine_Query::create()
                    ->delete()
                    ->from('BeaconNotification')
                    ->where('name = ?', $name);

            return $query->execute();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    public function getNotificationByName($name) {
        try {
            $query = Doctrine_Query::create()
                    ->from('BeaconNotification')
                    ->where('name = ?', $name);
            
            return $query->fetchOne();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }
    
    public function getRandomNotification() {
        try {
            $q = "SELECT * FROM ohrm_beacon_notification ORDER BY RAND() LIMIT 1";
            $pdo = Doctrine_Manager::connection()->getDbh();
            $query = $pdo->prepare($q);
            $query->execute();
            return $query->fetch();
            
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
        }

}
