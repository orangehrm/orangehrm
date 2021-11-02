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
 * Class ThemeService
 */
class ThemeService
{
    private $themeDao;

    /**
     * @return ThemeDao
     */
    public function getThemeDao()
    {
        if (!isset($this->themeDao)) {
            $this->themeDao = new ThemeDao();
        }
        return $this->themeDao;
    }

    /**
     * @param $themeName
     * @return $variables
     */
    public function getVariablesByThemeName($themeName)
    {
        return $this->getThemeDao()->getVariablesByThemeName($themeName);
    }

    /**
     * @param $themeName
     * @return $theme
     */
    public function getThemeByThemeName($themeName) {
        $theme = $this->getThemeDao()->getThemeByThemeName($themeName);

        if (!$theme) {
            $theme = new Theme();
            $theme->setId(2);
            $theme->setThemeName($themeName);
            return $theme;
        } else {
            return $theme;
        }
    }

    /**
     * @param Theme $theme
     * @return boolean
     */
    public function addTheme(Theme $theme)
    {
        return $this->getThemeDao()->addTheme($theme);
    }

    /**
     * @param Theme $theme
     *
     * @return boolean
     *
     * @throws Exception
     */

    public function publishTheme(Theme $theme) {
        $variablesArray = json_decode($theme->getVariables(), true);
        $variablesArray['imagesPath'] = '"../images/"';
        $variablesArray['login-social-links-display'] = $theme->getSocialMediaIcons();
        $loginVariables = [];
        $loginVariables['login-logo-inner-color'] = $variablesArray['primaryColor'];
        $loginVariables['login-logo-outer-color'] = $variablesArray['secondaryColor'];
        $loginVariables['imagesPath'] = '"../images/"';
        $css = Sass::instance()->compileSCSS($variablesArray);
        $loginCss = Sass::instance()->compileLoginSCSS($loginVariables);

        $this->createThemeFolder($theme->getThemeName(), $css, $loginCss, $theme);

        $oldUniqueResourceDir = sfConfig::get('ohrm_resource_dir');
        $uniqueResourceDir = orangehrmPublishAssetsTask::renameWebResourceDir($oldUniqueResourceDir, null);

        $propertiesToSet = array(
            'sf_web_css_dir_name' => $uniqueResourceDir . '/css',
            'sf_web_js_dir_name' => $uniqueResourceDir . '/js',
            'sf_web_images_dir_name' => $uniqueResourceDir . '/images',
            'ohrm_resource_dir' => $uniqueResourceDir,
        );

        foreach ($propertiesToSet as $key => $value) {
            sfConfig::set($key, $value);
        }


        try {
            $loginCssFile = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . $uniqueResourceDir . '/themes/' .
                $theme->getThemeName() . '/css/login.css';

            $this->writeFile($loginCss, $loginCssFile);

            $mainCssFile = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . $uniqueResourceDir . '/themes/' .
                $theme->getThemeName() . '/css/main.css';
            $this->writeFile($css, $mainCssFile);

            $themeLogoImagePath = sfConfig::get('sf_web_dir') . '/themes/' .
                $theme->getThemeName() . '/images/logo.png';
            $themeWebresLogoImagePath = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . $uniqueResourceDir . '/themes/' .
                $theme->getThemeName() . '/images/logo.png';

            $themeLoginBannerImagePath = sfConfig::get('sf_web_dir') . '/themes/' .
                $theme->getThemeName() . '/images/login/logo.png';
            $themeLoginBannerWebresLogoImagePath = sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . $uniqueResourceDir . '/themes/' .
                $theme->getThemeName() . '/images/login/logo.png';

            copy($themeLogoImagePath , $themeWebresLogoImagePath);
            copy($themeLoginBannerImagePath , $themeLoginBannerWebresLogoImagePath);

        } catch (Exception $exception){
            throw new Exception("Requires access to file permissions that are currently unavailable.");
        }

        OrangeConfig::getInstance()->setAppConfValue(ConfigService::KEY_THEME_NAME, $theme->getThemeName());

        return true;
    }

    /**
     * @param $themeName
     * @param $css
     * @param null $theme
     *
     * @throws Exception
     *
     * @return bool
     */
    private function createThemeFolder($themeName, $css, $loginCss, $theme = null) {

        $themePath = $this->getThemePath($themeName);

        $currentUmask = umask();
        umask(0002);

        $this->copyDir($this->getThemePath('default'), $themePath);
        if (!empty($css)) {
            $this->writeFile($css, $themePath . '/css/main.css');
        }
        if (!empty($loginCss)) {
            $this->writeFile($loginCss, $themePath . '/css/login.css');
        }
        // If theme is available, then create images as well

        if ($theme instanceof Theme && !is_null($theme->getMainLogo())) {

            $clientLogo = $theme->getMainLogo();
            if (!empty($clientLogo)) {
                $this->writeFile($clientLogo, $themePath . '/images/logo.png');
            }
        }

        if ($theme instanceof Theme && !is_null($theme->getLoginBanner())) {

            $loginBanner = $theme->getLoginBanner();
            if (!empty($loginBanner)) {
                $this->writeFile($loginBanner, $themePath . '/images/login/logo.png');
            }
        }

        umask($currentUmask);
        return true;
    }

    private function getThemePath($themeName, $webres = false) {

        $resourceDir = ($webres) ? sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . sfConfig::get('ohrm_resource_dir') : sfConfig::get('sf_web_dir');
        $themePath = $resourceDir . '/themes/' . $themeName;

        return $themePath;
    }

    private function copyDir($sourceDir, $destDir) {

        if (is_dir($destDir) || mkdir($destDir, 0775, true)) {

            if ($handle = opendir($sourceDir)) {
                while (false !== ($file = readdir($handle))) {
                    if ($file != "." && $file != "..") {

                        $sourceFile = $sourceDir . '/' . $file;
                        $destFile = $destDir . '/' . $file;

                        if (is_dir($sourceFile)) {
                            $this->copyDir($sourceFile, $destFile);
                        } else {
                            if (!copy($sourceFile, $destFile)) {
                                throw new Exception("Failed to copy $sourceFile -> $destFile");
                            }
                        }
                    }
                }
                closedir($handle);
            } else {
                throw new Exception("Failed to open directory: $sourceDir");
            }
        } else {
            throw new Exception('Unable to create folder. Check for permission in ' . $destDir);
        }
    }

    private function writeFile($data, $path) {

        if (!file_put_contents($path, $data)) {
            throw new Exception("Failed to write $path");
        }
    }
}
