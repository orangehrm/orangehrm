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

/**
 * Class ThemeServiceTest
 */
class ThemeServiceTest extends PHPUnit_Framework_TestCase
{
    private $themeService;
    private $themeName = '_test_';
    private $webPath;
    private $webResPath;

    public function setUp()
    {
        $this->themeService = new ThemeService();
        $this->webPath = sfConfig::get('sf_web_dir'). DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . $this->themeName;
        $this->resourceIncFile = sfConfig::get('sf_web_dir') . '/resource_dir_inc.php';
    }

    /**
     * This method is called after a test is executed.
     */
    protected function tearDown() {


    }

    public function testPublishTheme() {
        $primaryColor = '#2da98d';
        $secondaryColor = '#e37814';
        $tableHeadingColor = '#009aff';
        $buttonSuccessColor = '#7a6d3a';
        $buttonCancelColor = '#51f6c5';
        $variables = [];
        $variables['primaryColor'] = $primaryColor;
        $variables['secondaryColor'] = $secondaryColor;
        $variables['tableHeadingColor'] = $tableHeadingColor;
        $variables['buttonSuccessColor'] = $buttonSuccessColor;
        $variables['buttonCancelColor'] = $buttonCancelColor;

        $theme = new Theme();
        $theme->setThemeName($this->themeName);
        $theme->setVariables(json_encode($variables));
        $this->themeService->publishTheme($theme);
        require $this->resourceIncFile;
        $this->webResPath = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . sfConfig::get('ohrm_resource_dir') .
            DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . $this->themeName;
        $this->assertTrue(is_dir($this->webPath));
        $this->assertTrue(is_dir($this->webResPath));
        $this->assertEquals($this->themeName, OrangeConfig::getInstance()->getAppConfValue(ConfigService::KEY_THEME_NAME));

        if(is_dir($this->webPath)) {
            $this->removeDir($this->webPath);
        }
        if(is_dir($this->webResPath)) {
            $this->removeDir($this->webResPath);
        }

        OrangeConfig::getInstance()->setAppConfValue(ConfigService::KEY_THEME_NAME, 'default');
    }

    public function removeDir($dir) {
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->removeDir("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }

}
