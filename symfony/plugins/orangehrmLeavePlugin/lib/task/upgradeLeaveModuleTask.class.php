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
 *
 */

/**
 * Description of upgradeLeaveModuleTask
 */
class upgradeLeaveModuleTask extends sfBaseTask {

    protected function configure() {


        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
        ));

        $this->namespace = '';
        $this->name = 'UpgradeLeaveModule';
        $this->briefDescription = 'This task will upgrade leave related database tables';
        $this->detailedDescription = <<<EOF
This task will upgrade leave related database tables

  [php symfony UpgradeLeaveModule|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {

        $databaseManager = new sfDatabaseManager($this->configuration);
        $pdo = $databaseManager->getDatabase(isset($options['connection']) ? $options['connection'] : null)->getConnection();
        
        try {
            $sqlString = file_get_contents(sfConfig::get('sf_plugins_dir') . DIRECTORY_SEPARATOR . 'orangehrmLeavePlugin' . DIRECTORY_SEPARATOR .'install'. DIRECTORY_SEPARATOR . 'upgrade.sql');
            $queries = explode(';', $sqlString);
            
            foreach ($queries as $query) {
                $pdo->exec($query);
            }

        } catch (Exception $e) {

            return "<br>Exception: Tables already created or SQL error \n ";
        }
        
    }

}
