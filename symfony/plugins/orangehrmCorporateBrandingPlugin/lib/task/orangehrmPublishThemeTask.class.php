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
 * Boston, MA 02110-1301, USA
 */

class orangehrmPublishThemeTask extends sfBaseTask {

    protected function configure() {
        $this->namespace = 'orangehrm';
        $this->name = 'publish-themes';

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'orangehrm'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod')
        ));

        $this->briefDescription = 'Create theme folders with Database values.';

        $this->detailedDescription = <<<EOF
The [plugin:restore-themes|INFO] Task will create theme folders with values stored in database.

  [./symfony orangehrm:restore-themes|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        sfContext::createInstance($this->configuration);
        $themeService = new ThemeService();
        $customTheme = $themeService->getThemeDao()->getThemeByThemeName('custom');
        $success = true;
        if ($customTheme && $customTheme->getId()) {
            $success = $themeService->publishTheme($customTheme);
        }
        if ($success) {
            $this->log('Successfully published custom theme');
        } else {
            $this->log('Publishing theme failed.');
        }
    }

}