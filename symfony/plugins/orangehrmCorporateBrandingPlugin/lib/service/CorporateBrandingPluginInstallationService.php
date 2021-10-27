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

use Orangehrm\Rest\Api\Integration\IntegrationAPI;
use Orangehrm\Rest\http\Request;

class CorporateBrandingPluginInstallationService implements \AddonInstallationService
{
    protected $installationQueries = array();
    protected $unInstallationQueries = array();
    protected $installationQueriesIgnoreIfFails = array();
    protected $unInstallationQueriesIgnoreIfFails = array();

    /**
     * This method will run all necessary scripts and commands to install the
     * add-on and return an array with the relevant status and message.
     * @return array Array will consist of two keys : the status & message.
     * @example array("status" => true, "message" => "Successfully Installed.");
     */
    public function install()
    {
        $this->prepareInstallation();
        $response = array("status" => true, "message" => __('Successfully Installed.'));

        $this->prepareInstallationIgnoreIfFails();
        $this->executeQueriesIgnoreIfFails($this->installationQueriesIgnoreIfFails);
        try {
            $this->executeQueries($this->installationQueries);
            // @codeCoverageIgnoreStart
        } catch (\Exception $ex) {
            throw $ex;
        }
        // @codeCoverageIgnoreEnd
        return $response;
    }

    /**
     * This method will run all necessary scripts and commands to uninstall the
     * add-on and return an array with the relevant status and message.
     * @return array Array will consist of two keys : the status & message.
     * @example array("status" => true, "message" => "Successfully Uninstalled.");
     */
    public function uninstall()
    {
        $this->prepareUnInstallation();
        OrangeConfig::getInstance()->setAppConfValue(ConfigService::KEY_THEME_NAME, "default");
        sfContext::getInstance()->getUser()->setAttribute('meta.themeName', 'default');
        $response = array("status" => true, "message" => __('Successfully Uninstalled.'));

        $this->prepareUnInstallationIgnoreIfFails();
        $this->executeQueriesIgnoreIfFails($this->unInstallationQueriesIgnoreIfFails);
        try {
            $this->executeQueries($this->unInstallationQueries);
            // @codeCoverageIgnoreStart
        } catch (\Exception $ex) {
            throw $ex;
        }
        // @codeCoverageIgnoreEnd
        return $response;
    }

    /**
     * @param $queries
     * @return array
     */
    protected function executeQueries($queries)
    {
        $pdo = \Doctrine_Manager::connection()->getDbh();
        $results = array();
        try {
            foreach ($queries as $query) {
                $results[] = $this->executeQuery($pdo, $query);
            }
            // @codeCoverageIgnoreStart
        } catch (\Exception $ex) {
            throw $ex;
        }
        // @codeCoverageIgnoreEnd
        return $results;
    }

    /**
     * @param $pdo
     * @param $query
     * @return mixed
     */
    protected function executeQuery($pdo, $query)
    {
        try {
            $preparedQuery = $pdo->prepare($query);
            $result = $preparedQuery->execute();
            return $result;
            // @codeCoverageIgnoreStart
        } catch (\Exception $ex) {
            throw $ex;
        }
        // @codeCoverageIgnoreEnd
    }

    private function prepareInstallation()
    {
        $this->installationQueries[] = "CREATE TABLE `ohrm_theme` (
                                            `theme_id` INT(11) AUTO_INCREMENT,
                                            `theme_name` VARCHAR(100),
                                            `main_logo` BLOB,
                                            `variables` TEXT,
                                            PRIMARY KEY(`theme_id`)
                                        ) engine=innodb default charset=utf8;";
        $this->installationQueries[] = "SET @module_id := (SELECT id FROM ohrm_module WHERE `name` = 'admin' );";

        $this->installationQueries[] = "INSERT INTO ohrm_screen (`name`, `module_id`, `action_url`) VALUES('Add Theme', @module_id, 'addTheme');";
        $this->installationQueries[] = "SET @add_theme_screen_id := (SELECT LAST_INSERT_ID());";
        $this->installationQueries[] = "INSERT INTO `ohrm_user_role_screen` (`user_role_id`, `screen_id`, `can_read`, `can_create`, `can_update`, `can_delete`) VALUES ('1', @add_theme_screen_id, '1', '1', '1', '1');";
        $this->installationQueries[] = "INSERT INTO ohrm_menu_item (`menu_title`, `screen_id`, `parent_id`, `level`, `order_hint`, `url_extras`, `status`) VALUES('Corporate Branding', @add_theme_screen_id, 1, 2, 700, '', 1);";
        $this->installationQueries[] = "INSERT INTO ohrm_theme (`theme_id`,`theme_name`, `variables`) VALUES ('1','default', '{\"primaryColor\":\"#f28b38\",\"secondaryColor\":\"#f3f3f3\",\"buttonSuccessColor\":\"#56ac40\",\"buttonCancelColor\":\"#848484\"}');";
        $this->installationQueries[] = "ALTER TABLE ohrm_theme ADD social_media_icons VARCHAR(100) DEFAULT 'inline' NOT NULL ;";
        $this->installationQueries[] = "ALTER TABLE ohrm_theme ADD login_banner BLOB ;";
    }

    private function prepareUnInstallation()
    {
        $this->unInstallationQueries[] = "DROP TABLE IF EXISTS `ohrm_theme`; ";
        $this->unInstallationQueries[] = "DELETE FROM ohrm_menu_item WHERE menu_title ='Corporate Branding'; ";
        $this->unInstallationQueries[] = "DELETE FROM ohrm_screen WHERE name = 'Add Theme'; ";
    }

    /**
     * @param $queries
     * @return array
     */
    protected function executeQueriesIgnoreIfFails($queries)
    {
        $pdo = \Doctrine_Manager::connection()->getDbh();
        $results = [];
        foreach ($queries as $query) {
            $results[] = $this->executeQueryIgnoreIfFails($pdo, $query);
        }
        return $results;
    }

    /**
     * @param $pdo
     * @param $query
     * @return mixed
     */
    protected function executeQueryIgnoreIfFails($pdo, $query)
    {
        try {
            $preparedQuery = $pdo->prepare($query);
            $result = $preparedQuery->execute();
        } catch (\Exception $e) {
            $result = $e->getMessage();
        }
        return $result;
    }

    private function prepareInstallationIgnoreIfFails()
    {
        $this->installationQueriesIgnoreIfFails[] = "INSERT INTO `ohrm_i18n_group` (`name`, `title`) VALUES ('branding', 'Corporate Branding');";
    }

    private function prepareUnInstallationIgnoreIfFails()
    {
        $this->unInstallationQueriesIgnoreIfFails[] = "DELETE FROM `ohrm_i18n_group` WHERE name ='branding';";
    }
}
