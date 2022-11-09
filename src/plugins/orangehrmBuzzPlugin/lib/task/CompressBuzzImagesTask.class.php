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
 * Description of orangehrmUpgradeAdvancedLeaveTask
 *
 */
class CompressBuzzImagesTask extends sfBaseTask {

    protected function configure() {

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
        ));

        $this->namespace = 'orangehrm';
        $this->name = 'compress-buzz-images';
        $this->briefDescription = 'Compresses Buzz images to be within max configured size';
        $this->detailedDescription = <<<EOF
Compresses Buzz images to be within max configured size:

Usage:

[./symfony orangehrm:compress-buzz-images|INFO]
                
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {

        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        define('SF_ROOT_DIR', realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..'));
        define('SF_APP', 'orangehrm');
        define('SF_ENVIRONMENT', 'prod');
        define('SF_DEBUG', true);

        require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'ProjectConfiguration.class.php');
        $configuration = ProjectConfiguration::getApplicationConfiguration('orangehrm', 'prod', true);
        sfContext::createInstance($configuration);

        $utility = new BuzzImageCompressUtility();
        $utility->compressImages();
    }

}
