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

class SchemaIncrementTask67 extends SchemaIncrementTask {

    public function loadSql() {
        $sql = array();

        $sql[] = 'SELECT `id` FROM `ohrm_user_role` WHERE `name` = "Admin" INTO @AdminUserRoleId;';

        $sql[] = 'SELECT `id` FROM `ohrm_module` WHERE `name` = "time" INTO @timeModuleId;';

        $sql[] = 'UPDATE `ohrm_module_default_page` SET `priority` = 200 WHERE `module_id` = @timeModuleId AND `user_role_id` = @AdminUserRoleId AND `action` = "time/defineTimesheetPeriod";';

        $this->sql = $sql;
    }

    public function getUserInputWidgets() {
        // TODO: Implement getUserInputWidgets() method.
    }

    public function setUserInputs() {
        // TODO: Implement setUserInputs() method.
    }

    public function getNotes() {
        // TODO: Implement getNotes() method.
    }

    public function execute() {
        $this->incrementNumber = 67;
        parent::execute();

        $result = array();

        foreach ($this->sql as $sql) {
            $result[] = $this->upgradeUtility->executeSql($sql);
        }

        $this->checkTransactionComplete($result);
        $this->updateOhrmUpgradeInfo($this->transactionComplete, $this->incrementNumber);
        $this->upgradeUtility->finalizeTransaction($this->transactionComplete);
        $this->upgradeUtility->closeDbConnection();
    }
}