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

require_once ROOT_PATH.'/lib/confs/Conf.php';

class ModuleManager {
    
    private $dbConnection;
    
    public function setDbConnection($dbConnection) {
        $this->dbConnection = $dbConnection;
    }

    public function getDbConnection() {
        
        if (empty($this->dbConnection)) {
            $conf = new Conf();
            $this->dbConnection = mysqli_connect($conf->dbhost, $conf->dbuser, $conf->dbpass, $conf->dbname, $conf->dbport);
        }
        
        return $this->dbConnection;
    }
    
    public function getDisabledModuleList() {
        
        $q = "SELECT `name` FROM `ohrm_module` WHERE `status` = 0";
        
        $result = mysqli_query($this->getDbConnection(), $q);
        
        $disabledModules = array();
        
        while ($row = mysqli_fetch_assoc($result)) {
            $disabledModules[] = $row['name'];
        }
        
        return $disabledModules;
        
    }

}

