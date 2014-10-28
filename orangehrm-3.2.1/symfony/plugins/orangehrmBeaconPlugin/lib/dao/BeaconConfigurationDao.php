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

    public function setBeaconLock() {
        try {
            $query = Doctrine_Query::create()
                    ->update('Config')
                    ->set('value', "'locked'")
                    ->where('key = ?', BeaconConfigurationService::KEY_BEACON_LOCK)
                    ->andWhere('value=?', "unlocked");
            
            return $query->execute();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

}
