<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BeaconConfigurationDao
 *
 * @author chathura
 */
class BeaconConfigurationDao extends ConfigDao {

    public function setBeaconLock($value) {
        try {
            $q = 'UPDATE hs_hr_config SET `value`= CASE
                    WHEN `value`="unlocked" OR ?="unlocked" OR ?-`value`>600 THEN ? ELSE `value` END WHERE `key`= ?';
            
            $pdo = Doctrine_Manager::connection()->getDbh();
            $query = $pdo->prepare($q);
            
            $query->execute(array($value,$value,$value,  BeaconConfigurationService::KEY_BEACON_LOCK));
            return $query->rowCount();
//            $query = Doctrine_Query::create()
//                    ->update('Config')
//                    ->set('value', "'locked'")
//                    ->where('property = ?', BeaconConfigurationService::KEY_BEACON_LOCK)
//                    ->andWhere('value=?', "unlocked");
//            
//            return $query->execute();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

}
