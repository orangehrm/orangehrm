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
 * Class SchemaIncrementTask68
 */
class SchemaIncrementTask68 extends SchemaIncrementTask
{
    /**
     *
     */
    public function loadSql()
    {
        $sql = array();

        $sql[] = 'ALTER TABLE hs_hr_employee add purged_at TIMESTAMP NULL DEFAULT NULL;';
        $sql[] = 'ALTER TABLE ohrm_job_candidate add consent_to_keep_data boolean not null default false;';

        $sql[] = 'INSERT INTO ohrm_module (name, status) VALUES (\'maintenance\', \'1\');';

        $sql[] = 'set @module_id := (SELECT id FROM ohrm_module WHERE name = \'maintenance\');';

        $sql[] = 'set @admin_role_id := (SELECT id FROM ohrm_user_role WHERE name = \'Admin\');';

        $sql[] = 'INSERT INTO ohrm_screen (name, module_id, action_url) VALUES
(\'Purge Employee Records\', @module_id , \'purgeEmployee\'),
(\'Purge Candidate Records\', @module_id , \'purgeCandidateData\'),
(\'Access Employee Records\', @module_id , \'accessEmployeeData\');';

        $sql[] = 'set @purge_employee_screen_id := (SELECT id FROM ohrm_screen WHERE action_url = \'purgeEmployee\');';

        $sql[] = 'set @purge_candidate_screen_id := (SELECT id FROM ohrm_screen WHERE action_url = \'purgeCandidateData\');';

        $sql[] = 'set @access_employee_records_screen_id := (SELECT id FROM ohrm_screen WHERE action_url = \'accessEmployeeData\');';

        $sql[] = 'INSERT INTO ohrm_menu_item (menu_title, screen_id, parent_id, level, order_hint, url_extras, status) VALUES
(\'Maintenance\', @purge_employee_screen_id , NULL, \'1\', \'1200\', NULL, \'1\');';

        $sql[] = 'set @parent_menu_id := (SELECT id FROM ohrm_menu_item WHERE menu_title = \'Maintenance\');';

        $sql[] = 'INSERT INTO ohrm_menu_item (menu_title, screen_id, parent_id, level, order_hint, url_extras, status) VALUES
(\'Purge Records\', NULL , @parent_menu_id, 2, \'100\', null, 1),
(\'Access Records\', @access_employee_records_screen_id, @parent_menu_id, 2, \'200\', null, 1);';

        $sql[] = 'set @parent_menu_id_level_2:= (SELECT id FROM ohrm_menu_item WHERE menu_title = \'Purge Records\');';
        $sql[] = 'INSERT INTO ohrm_menu_item (menu_title, screen_id, parent_id, level, order_hint, url_extras, status) VALUES
(\'Employee Records\', @purge_employee_screen_id, @parent_menu_id_level_2, 3, \'100\', null, 1),
(\'Candidate Records\', @purge_candidate_screen_id, @parent_menu_id_level_2, 3, \'200\', null, 1);';

        $sql[] = 'INSERT INTO ohrm_user_role_screen (user_role_id,screen_id, can_read) VALUES
(@admin_role_id, @purge_employee_screen_id, 1),
(@admin_role_id, @purge_candidate_screen_id, 1),
(@admin_role_id, @access_employee_records_screen_id, 1);';

        $sql[] = 'INSERT INTO ohrm_user_role_screen (user_role_id,screen_id, can_read, can_create, can_update, can_delete) VALUES
(@admin_role_id, @access_employee_records_screen_id, 1,1,1,1);';

        $sql[] = 'INSERT INTO ohrm_data_group (name, description, can_read, can_create, can_update, can_delete) VALUES
  (\'GDPR Employee\', \'Employee Records purge or Extract- Tasks\', 1, 1, 1, 1);';

        $sql[] = 'SET @data_group_id := (SELECT id FROM ohrm_data_group WHERE name = \'GDPR Employee\');';

        $sql[] = 'INSERT INTO ohrm_data_group_screen (data_group_id, screen_id, permission) VALUES
  (@data_group_id, @purge_employee_screen_id, 1),
  (@data_group_id, @access_employee_records_screen_id, 1);';

        $sql[] = 'INSERT INTO ohrm_user_role_data_group (user_role_id, data_group_id, can_read, can_create, can_update, can_delete, self) VALUES
  (@admin_role_id, @data_group_id, 1, 1, 1, 1, 1);';

        $this->sql = $sql;
    }

    /**
     *
     */
    public function getUserInputWidgets()
    {
    }

    /**
     *
     */
    public function setUserInputs()
    {
    }

    /**
     *
     */
    public function getNotes()
    {
    }

    /**
     *
     */
    public function execute()
    {
        $this->incrementNumber = 68;
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
