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
 * Class SchemaIncrementTask72
 */
class SchemaIncrementTask72  extends SchemaIncrementTask
{

    public function loadSql()
    {
        $sql = array (
            0 => 'ALTER TABLE `hs_hr_employee` MODIFY `emp_number` int(7) not null AUTO_INCREMENT;',
            1 => 'ALTER TABLE `ohrm_timesheet` MODIFY `timesheet_id` BIGINT(20) NOT NULL AUTO_INCREMENT;',
            2 => 'ALTER TABLE `ohrm_timesheet_item` MODIFY `timesheet_item_id` BIGINT(20) NOT NULL AUTO_INCREMENT;',
            3 => 'ALTER TABLE `ohrm_timesheet_action_log` MODIFY `timesheet_action_log_id` BIGINT(20) NOT NULL AUTO_INCREMENT;',
            4 => 'ALTER TABLE `ohrm_attendance_record` MODIFY `id` BIGINT(20) NOT NULL AUTO_INCREMENT;',
            5 => 'ALTER TABLE `ohrm_job_candidate` MODIFY `id` int(13) not null AUTO_INCREMENT;',
            6 => 'ALTER TABLE `ohrm_job_candidate_vacancy` MODIFY `id` int(13) default null unique AUTO_INCREMENT;',
            7 => 'ALTER TABLE `ohrm_job_vacancy` MODIFY `id` int(13) not null auto_increment;',
        );
        array_unshift($sql, 'SET FOREIGN_KEY_CHECKS = 0;');
        array_push($sql, 'SET FOREIGN_KEY_CHECKS = 1;');
        $this->sql = $sql;
    }

    public function getUserInputWidgets()
    {
    }

    public function setUserInputs()
    {
    }

    public function getNotes()
    {
    }

    public function execute()
    {
        $this->incrementNumber = 71;
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
