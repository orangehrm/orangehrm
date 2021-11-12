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
 * Class SchemaIncrementTask81
 */
class SchemaIncrementTask81 extends SchemaIncrementTask
{

    public function loadSql()
    {
        $sql[] = 'SELECT * FROM `ohrm_marketplace_addon` WHERE `plugin_name` = \'orangehrmCorporateBrandingPlugin\';';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `ohrm_theme` (
    `theme_id`   INT(11) AUTO_INCREMENT,
    `theme_name` VARCHAR(100),
    `main_logo`  BLOB,
    `variables`  TEXT,
    PRIMARY KEY (`theme_id`)
) engine = innodb
  default charset = utf8;';

        $sql[] = 'SET @module_id := (SELECT id
                   FROM ohrm_module
                   WHERE `name` = \'admin\');';

        $sql[] = 'INSERT INTO ohrm_screen (`name`, `module_id`, `action_url`)
VALUES (\'Add Theme\', @module_id, \'addTheme\');';

        $sql[] = 'SET @add_theme_screen_id := (SELECT LAST_INSERT_ID());';

        $sql[] = 'INSERT INTO `ohrm_user_role_screen` (`user_role_id`, `screen_id`, `can_read`, `can_create`, `can_update`, `can_delete`)
VALUES (\'1\', @add_theme_screen_id, \'1\', \'1\', \'1\', \'1\');';

        $sql[] = 'INSERT INTO ohrm_menu_item (`menu_title`, `screen_id`, `parent_id`, `level`, `order_hint`, `url_extras`, `status`)
VALUES (\'Corporate Branding\', @add_theme_screen_id, 1, 2, 700, \'\', 1);';

        $sql[] = 'INSERT INTO ohrm_theme (`theme_id`, `theme_name`, `variables`)
VALUES (\'1\', \'default\',
        \'{"primaryColor":"#f28b38","secondaryColor":"#f3f3f3","buttonSuccessColor":"#56ac40","buttonCancelColor":"#848484"}\');';

        $sql[] = 'ALTER TABLE ohrm_theme
    ADD social_media_icons VARCHAR(100) DEFAULT \'inline\' NOT NULL;';

        $sql[] = 'ALTER TABLE ohrm_theme
    ADD login_banner BLOB;';

        $sql[] = 'INSERT INTO `ohrm_i18n_group` (`name`, `title`)
VALUES (\'branding\', \'Corporate Branding\');';

        $sql[] = 'DELETE FROM `ohrm_marketplace_addon` WHERE `plugin_name`=\'orangehrmCorporateBrandingPlugin\';';

        $sql[] = "UPDATE `hs_hr_config` SET `value` = '4.9' WHERE `hs_hr_config`.`key` = 'instance.version';";

        $sql[] = "UPDATE `hs_hr_config` SET `value` = '" . $this->incrementNumber . "' WHERE `hs_hr_config`.`key` = 'instance.increment_number';";

        $sql[] = "CREATE TABLE IF NOT EXISTS `ohrm_registration_event_queue`
(
    `id`         INT NOT NULL AUTO_INCREMENT,
    `event_type`   INT NOT NULL,
    `published`  TINYINT UNSIGNED NOT NULL DEFAULT 0,
    `event_time` DATETIME DEFAULT NULL,
    `publish_time` DATETIME DEFAULT NULL,
    `data` TEXT DEFAULT NULL,
    primary key (`id`)
) engine = innodb
  default charset = utf8;";

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
        $this->incrementNumber = 81;
        parent::execute();
        $result = [];
        $queryResponse = $this->upgradeUtility->executeSql($this->sql[0]);
        if (mysqli_num_rows($queryResponse) == 0) {
            foreach ($this->sql as $sql) {
                $result[] = $this->upgradeUtility->executeSql($sql);
            }
        }
        $result[] = $this->upgradeUtility->executeSql($this->sql[14]);
        $this->checkTransactionComplete($result);
        $this->updateOhrmUpgradeInfo($this->transactionComplete, $this->incrementNumber);
        $this->upgradeUtility->finalizeTransaction($this->transactionComplete);
        $this->upgradeUtility->closeDbConnection();
    }


}
