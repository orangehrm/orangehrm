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
 */

/**
 * Config Dao: Manages configuration entries in hs_hr_config
 *
 */
class ConfigDao extends BaseDao {

    private $logger;
    
    /**
     * Get Logger instance. Creates if not already created.
     *
     * @return Logger
     */
    protected function getLogger() {
        if (is_null($this->logger)) {
            $this->logger = Logger::getLogger('core.ConfigDao');
        }

        return($this->logger);
    }
    
    /**
     * Set $key to given $value
     * @param type $key Key
     * @param type $value Value
     */
    public function setValue($key, $value) {
        try {
            $config = new Config();
            $config->key = $key;
            $config->value = $value;
            $config->replace();

        } catch (Exception $e) {
            $this->getLogger()->error("Exception in setValue:" . $e);
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }        
    }
    
    /**
     * Get value corresponding to given $key
     * @param type $key Key
     * @return String value
     */
    public function getValue($key) {
        try {
            $q = Doctrine_Query::create()
                 ->select('c.value')
                 ->from('Config c')
                 ->where('c.key = ?', $key);
            $value = $q->execute(array(), Doctrine::HYDRATE_SINGLE_SCALAR);
      
            return $value;
        } catch (Exception $e) {
            $this->getLogger()->error("Exception in getValue:" . $e);
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
       
    }
    
    public function getAllValues() {
        try {
            $values = array();
            
            $q = Doctrine_Query::create()
                 ->select('c.key as keyVal, c.value as Val')
                 ->from('Config c');
            $results = $q->execute(array(), Doctrine::HYDRATE_SCALAR);

            foreach ($results as $row) {
                $values[$row['c_keyVal']] = $row['c_Val'];
            }
            return $values;
            
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }        
    }
    
    
}