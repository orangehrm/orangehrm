<?php

/*
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

include_once 'SchemaIncrementTask.php';

class SchemaIncrementTask53 extends SchemaIncrementTask {
    
    public $userInputs;
    
    public function execute() {
        $this->incrementNumber = 53;
        parent::execute();
        
        $result = array();        
        
        $result[] = $this->createLeaveTakenStatusChangingMysqlEvent();
        
        $this->checkTransactionComplete($result);
        $this->updateOhrmUpgradeInfo($this->transactionComplete, $this->incrementNumber);
        $this->upgradeUtility->finalizeTransaction($this->transactionComplete);
        $this->upgradeUtility->closeDbConnection();
    }
    
    public function getUserInputWidgets() {        
    }
    
    public function setUserInputs() {        
    }
    
    public function loadSql() {
    }
    
    public function getNotes() {        
        return array();
    }
    
    public function createLeaveTakenStatusChangingMysqlEvent() {
        
        $eventTime = date('Y-m-d') . " 00:00:00";
        $query = "CREATE EVENT leave_taken_status_change
                    ON SCHEDULE EVERY 1 HOUR STARTS '$eventTime'
                    DO
                      BEGIN
                        UPDATE hs_hr_leave SET leave_status = 3 WHERE leave_status = 2 AND leave_date < DATE(NOW());
                      END";
        
        return $this->upgradeUtility->executeSql($query);
        
    }
    
}