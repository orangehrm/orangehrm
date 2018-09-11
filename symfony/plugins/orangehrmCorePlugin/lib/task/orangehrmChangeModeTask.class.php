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

class orangehrmChangeModeTask extends sfBaseTask {

    protected function configure() {

        $this->addOptions(array(
            new sfCommandOption('mode', null, sfCommandOption::PARAMETER_REQUIRED, 'The mode', 'dev')
        ));

        $this->namespace = 'orangehrm';
        $this->name = 'change-mode';
        $this->briefDescription = 'This task will change the environment mode';

        $this->detailedDescription = <<<EOF
The [orangehrm:change-mode|INFO] Task will change the environment mode. Available modes are 'dev', 'prod', 'test' and 'uat'.

  [php symfony orangehrm:change-mode|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        $mode = $options['mode'];
        $allowedModes = $this->getModes();

        if (in_array($mode, $allowedModes)) {
            $sysConfPath = sfConfig::get('sf_root_dir') . "/../lib/confs/sysConf.php";
            $contents = file_get_contents($sysConfPath);
            $contents = preg_replace('/this->mode = "(\w+)";/', 'this->mode = "' . $mode . '";', $contents);
            file_put_contents($sysConfPath, $contents);
            $this->logSection('orangehrm', "Mode changed to " . $mode);
        } else {
            throw new sfCommandException('Mode must be valid. Available modes are \'dev\', \'prod\', \'test\' and \'uat\'.');
        }
    }

    /**
     * Return array with available modes.
     * @return array
     */
    private function getModes() {
        require_once(sfConfig::get('sf_root_dir') . "/../lib/confs/sysConf.php");

        return array(sysConf::DEV_MODE, sysConf::PROD_MODE, sysConf::TEST_MODE, sysConf::UAT_MODE);
    }
}
